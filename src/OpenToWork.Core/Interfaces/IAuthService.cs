using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Interfaces;

public interface IAuthService
{
    Task<AuthResponseDto> RegisterAsync(RegisterDto dto, string? createdBy = null);
    Task<AuthResponseDto> LoginAsync(LoginDto dto);
    Task<AuthResponseDto> RefreshTokenAsync(RefreshTokenDto dto);
    Task<bool> RevokeTokenAsync(string refreshToken);
    Task<bool> RegisterDeviceAsync(Guid userId, string deviceHash, string? deviceName);
    Task<bool> IsDeviceKnownAsync(Guid userId, string deviceHash);
}
