using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Interfaces;

public interface IAuditLogService
{
    Task LogAsync(Guid adminUserId, string action, string entityType, Guid? entityId, string? changesJson, string? ipAddress);
    Task<List<AuditLogDto>> GetLogsAsync(int page, int pageSize);
}
