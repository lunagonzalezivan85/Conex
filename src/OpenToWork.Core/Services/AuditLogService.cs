using Microsoft.EntityFrameworkCore;
using OpenToWork.Core.Interfaces;
using OpenToWork.Models.Context;
using OpenToWork.Models.Entities;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Services;

public class AuditLogService : IAuditLogService
{
    private readonly AppDbContext _context;

    public AuditLogService(AppDbContext context)
    {
        _context = context;
    }

    public async Task LogAsync(Guid adminUserId, string action, string entityType, Guid? entityId, string? changesJson, string? ipAddress)
    {
        _context.AD_AuditLogs.Add(new ADAuditLog
        {
            SCUserId = adminUserId,
            Action = action,
            EntityType = entityType,
            EntityId = entityId,
            ChangesJson = changesJson,
            IpAddress = ipAddress,
            CreatedBy = adminUserId
        });
        await _context.SaveChangesAsync();
    }

    public async Task<List<AuditLogDto>> GetLogsAsync(int page, int pageSize)
    {
        return await _context.AD_AuditLogs
            .Include(a => a.User)
            .Where(a => !a.IsDeleted)
            .OrderByDescending(a => a.CreatedAt)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .Select(a => new AuditLogDto
            {
                Id = a.Id,
                SCUserId = a.SCUserId,
                AdminEmail = a.User != null ? a.User.Email : null,
                Action = a.Action,
                EntityType = a.EntityType,
                EntityId = a.EntityId,
                ChangesJson = a.ChangesJson,
                IpAddress = a.IpAddress,
                CreatedAt = a.CreatedAt
            })
            .ToListAsync();
    }
}
