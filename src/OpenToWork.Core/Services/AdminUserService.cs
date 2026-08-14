using Microsoft.EntityFrameworkCore;
using OpenToWork.Core.Interfaces;
using OpenToWork.Models.Context;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Services;

public class AdminUserService : IAdminUserService
{
    private readonly AppDbContext _context;
    private readonly IAuditLogService _auditLog;

    public AdminUserService(AppDbContext context, IAuditLogService auditLog)
    {
        _context = context;
        _auditLog = auditLog;
    }

    public async Task<List<AdminUserDto>> GetUsersAsync(int page, int pageSize, int? role, bool? isActive)
    {
        page = Math.Max(1, page);
        pageSize = Math.Clamp(pageSize, 1, 1_000_000);

        var query = _context.SC_Users.Where(u => !u.IsDeleted);

        if (role.HasValue) query = query.Where(u => u.PrimaryRole == role.Value);
        if (isActive.HasValue) query = query.Where(u => u.IsActive == isActive.Value);

        return await query
            .OrderByDescending(u => u.CreatedAt)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .Select(u => ToDto(u))
            .ToListAsync();
    }

    public async Task<AdminUserDto?> GetUserByIdAsync(Guid id)
    {
        var user = await _context.SC_Users.FirstOrDefaultAsync(u => u.Id == id && !u.IsDeleted);
        return user == null ? null : ToDto(user);
    }

    public async Task<bool> ActivateAsync(Guid id, Guid adminId, string? ipAddress)
    {
        var user = await _context.SC_Users.FirstOrDefaultAsync(u => u.Id == id && !u.IsDeleted);
        if (user == null) return false;

        user.IsActive = true;
        user.UpdatedAt = DateTime.UtcNow;
        user.UpdatedBy = adminId;
        await _context.SaveChangesAsync();

        await _auditLog.LogAsync(adminId, "ActivateUser", "SC_Users", id, null, ipAddress);
        return true;
    }

    public async Task<bool> DeactivateAsync(Guid id, Guid adminId, string? ipAddress)
    {
        var user = await _context.SC_Users.FirstOrDefaultAsync(u => u.Id == id && !u.IsDeleted);
        if (user == null) return false;

        user.IsActive = false;
        user.UpdatedAt = DateTime.UtcNow;
        user.UpdatedBy = adminId;
        await _context.SaveChangesAsync();

        await _auditLog.LogAsync(adminId, "DeactivateUser", "SC_Users", id, null, ipAddress);
        return true;
    }

    public async Task<bool> DeleteAsync(Guid id, Guid adminId, string? ipAddress)
    {
        var user = await _context.SC_Users.FirstOrDefaultAsync(u => u.Id == id && !u.IsDeleted);
        if (user == null) return false;

        user.IsDeleted = true;
        user.DeletedAt = DateTime.UtcNow;
        user.DeletedBy = adminId;
        await _context.SaveChangesAsync();

        await _auditLog.LogAsync(adminId, "DeleteUser", "SC_Users", id, null, ipAddress);
        return true;
    }

    private static AdminUserDto ToDto(Models.Entities.SCUser u) => new()
    {
        Id = u.Id,
        Email = u.Email,
        PrimaryRole = u.PrimaryRole,
        EmailVerified = u.EmailVerified,
        IsActive = u.IsActive,
        CreatedAt = u.CreatedAt,
        LastLoginAt = u.LastLoginAt
    };
}
