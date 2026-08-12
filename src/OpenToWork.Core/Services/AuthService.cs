using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Security.Cryptography;
using System.Text;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.IdentityModel.Tokens;
using OpenToWork.Core.Interfaces;
using OpenToWork.Models.Context;
using OpenToWork.Models.Entities;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Services;

public class AuthService : IAuthService
{
    private readonly AppDbContext _context;
    private readonly IConfiguration _config;

    public AuthService(AppDbContext context, IConfiguration config)
    {
        _context = context;
        _config = config;
    }

    public async Task<AuthResponseDto> RegisterAsync(RegisterDto dto, string? createdBy = null)
    {
        var existing = await _context.SC_Users
            .FirstOrDefaultAsync(u => u.Email == dto.Email && !u.IsDeleted);

        if (existing != null)
            throw new InvalidOperationException("Email already registered");

        var user = new SCUser
        {
            Email = dto.Email,
            PasswordHash = BCrypt.Net.BCrypt.HashPassword(dto.Password),
            PrimaryRole = dto.PrimaryRole,
            Identification = dto.Identification,
            Phone = dto.Phone,
            EmailVerified = false,
            IsActive = true,
            CreatedBy = createdBy != null ? Guid.Parse(createdBy) : null
        };

        user.UserRoles.Add(new SCUserRole { Role = dto.PrimaryRole, SCUserId = user.Id });
        user.UserPreference = new SYUserPreference { SCUserId = user.Id, Theme = "navy", Language = "es" };

        if (dto.PrimaryRole == 0)
        {
            user.Candidate = new PTCandidate { SCUserId = user.Id, WizardStep = 0, WizardCompleted = false };
        }
        else if (dto.PrimaryRole == 1)
        {
            user.Company = new PTCompany { SCUserId = user.Id };
        }

        _context.SC_Users.Add(user);
        await _context.SaveChangesAsync();

        return await GenerateAuthResponseAsync(user);
    }

    public async Task<AuthResponseDto> LoginAsync(LoginDto dto)
    {
        var user = await _context.SC_Users
            .Include(u => u.UserRoles)
            .Include(u => u.UserPreference)
            .Include(u => u.Candidate)
            .FirstOrDefaultAsync(u => u.Email == dto.Email && !u.IsDeleted);

        if (user == null || !user.IsActive)
            throw new UnauthorizedAccessException("Invalid credentials");

        if (string.IsNullOrEmpty(user.PasswordHash) || !BCrypt.Net.BCrypt.Verify(dto.Password, user.PasswordHash))
            throw new UnauthorizedAccessException("Invalid credentials");

        if (!string.IsNullOrEmpty(dto.DeviceHash))
        {
            await RegisterDeviceAsync(user.Id, dto.DeviceHash, dto.DeviceName);
        }

        user.LastLoginAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        return await GenerateAuthResponseAsync(user, dto.RememberMe);
    }

    public async Task<AuthResponseDto> RefreshTokenAsync(RefreshTokenDto dto)
    {
        var tokenHash = HashToken(dto.RefreshToken);
        var token = await _context.SC_RefreshTokens
            .Include(t => t.User).ThenInclude(u => u.UserRoles)
            .Include(t => t.User).ThenInclude(u => u.UserPreference)
            .Include(t => t.User).ThenInclude(u => u.Candidate)
            .FirstOrDefaultAsync(t => t.TokenHash == tokenHash && !t.IsRevoked && !t.IsDeleted);

        if (token == null || token.ExpiresAt < DateTime.UtcNow)
            throw new UnauthorizedAccessException("Invalid or expired refresh token");

        token.IsRevoked = true;

        return await GenerateAuthResponseAsync(token.User);
    }

    public async Task<bool> RevokeTokenAsync(string refreshToken)
    {
        var tokenHash = HashToken(refreshToken);
        var token = await _context.SC_RefreshTokens
            .FirstOrDefaultAsync(t => t.TokenHash == tokenHash && !t.IsRevoked && !t.IsDeleted);

        if (token == null) return false;

        token.IsRevoked = true;
        token.UpdatedAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<bool> RegisterDeviceAsync(Guid userId, string deviceHash, string? deviceName)
    {
        var device = await _context.SC_UserDevices
            .FirstOrDefaultAsync(d => d.SCUserId == userId && d.DeviceHash == deviceHash && !d.IsDeleted);

        if (device != null)
        {
            device.LastSeenAt = DateTime.UtcNow;
            await _context.SaveChangesAsync();
            return false;
        }

        _context.SC_UserDevices.Add(new SCUserDevice
        {
            SCUserId = userId,
            DeviceHash = deviceHash,
            DeviceName = deviceName,
            FirstSeenAt = DateTime.UtcNow,
            LastSeenAt = DateTime.UtcNow,
            IsTrusted = false
        });
        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<bool> IsDeviceKnownAsync(Guid userId, string deviceHash)
    {
        return await _context.SC_UserDevices
            .AnyAsync(d => d.SCUserId == userId && d.DeviceHash == deviceHash && !d.IsDeleted);
    }

    private async Task<AuthResponseDto> GenerateAuthResponseAsync(SCUser user, bool rememberMe = false)
    {
        var token = GenerateJwtToken(user, rememberMe);
        var refreshToken = GenerateRefreshToken();
        var refreshTokenHash = HashToken(refreshToken);

        var expireDays = _config.GetValue<int>("Jwt:RefreshTokenExpireDays", 7);

        _context.SC_RefreshTokens.Add(new SCRefreshToken
        {
            SCUserId = user.Id,
            TokenHash = refreshTokenHash,
            ExpiresAt = DateTime.UtcNow.AddDays(expireDays),
            IsRevoked = false
        });
        await _context.SaveChangesAsync();

        var expireMinutes = rememberMe
            ? _config.GetValue<int>("Jwt:ExpireMinutesRememberMe", 43200)
            : _config.GetValue<int>("Jwt:ExpireMinutes", 60);

        return new AuthResponseDto
        {
            Token = token,
            RefreshToken = refreshToken,
            ExpiresAt = DateTime.UtcNow.AddMinutes(expireMinutes),
            User = new UserDto
            {
                Id = user.Id,
                Email = user.Email,
                PrimaryRole = user.PrimaryRole,
                Identification = user.Identification,
                Phone = user.Phone,
                EmailVerified = user.EmailVerified,
                IsActive = user.IsActive,
                Roles = user.UserRoles.Select(r => r.Role).ToList(),
                Theme = user.UserPreference?.Theme,
                Language = user.UserPreference?.Language,
                PreferredRole = user.UserPreference?.PreferredRole,
                WizardCompleted = user.Candidate?.WizardCompleted ?? false,
                WizardStep = user.Candidate?.WizardStep ?? 0
            }
        };
    }

    private string GenerateJwtToken(SCUser user, bool rememberMe)
    {
        var key = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(_config["Jwt:Key"]!));
        var creds = new SigningCredentials(key, SecurityAlgorithms.HmacSha256);

        var expireMinutes = rememberMe
            ? _config.GetValue<int>("Jwt:ExpireMinutesRememberMe", 43200)
            : _config.GetValue<int>("Jwt:ExpireMinutes", 60);

        var claims = new List<Claim>
        {
            new(JwtRegisteredClaimNames.Sub, user.Id.ToString()),
            new(JwtRegisteredClaimNames.Email, user.Email),
            new("primaryRole", user.PrimaryRole.ToString()),
            new(ClaimTypes.NameIdentifier, user.Id.ToString())
        };

        foreach (var role in user.UserRoles)
        {
            claims.Add(new Claim(ClaimTypes.Role, ((OpenToWork.Shared.Enums.UserRole)role.Role).ToString()));
        }

        var token = new JwtSecurityToken(
            issuer: _config["Jwt:Issuer"],
            audience: _config["Jwt:Audience"],
            claims: claims,
            expires: DateTime.UtcNow.AddMinutes(expireMinutes),
            signingCredentials: creds
        );

        return new JwtSecurityTokenHandler().WriteToken(token);
    }

    private static string GenerateRefreshToken()
    {
        var randomBytes = new byte[64];
        using var rng = RandomNumberGenerator.Create();
        rng.GetBytes(randomBytes);
        return Convert.ToBase64String(randomBytes);
    }

    private static string HashToken(string token)
    {
        var bytes = SHA256.HashData(Encoding.UTF8.GetBytes(token));
        return Convert.ToHexString(bytes);
    }
}
