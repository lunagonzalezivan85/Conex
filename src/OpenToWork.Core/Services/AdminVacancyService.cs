using Microsoft.EntityFrameworkCore;
using OpenToWork.Core.Interfaces;
using OpenToWork.Models.Context;
using OpenToWork.Shared.DTOs;
using OpenToWork.Shared.Enums;

namespace OpenToWork.Core.Services;

public class AdminVacancyService : IAdminVacancyService
{
    private readonly AppDbContext _context;
    private readonly IAuditLogService _auditLog;

    public AdminVacancyService(AppDbContext context, IAuditLogService auditLog)
    {
        _context = context;
        _auditLog = auditLog;
    }

    public async Task<List<AdminVacancyDto>> GetVacanciesAsync(int page, int pageSize, int? status)
    {
        var permanentQuery = _context.PT_Vacancies
            .Include(v => v.Company)
            .Where(v => !v.IsDeleted);

        if (status.HasValue) permanentQuery = permanentQuery.Where(v => v.Status == status.Value);

        var permanent = await permanentQuery
            .Select(v => new AdminVacancyDto
            {
                Id = v.Id,
                Title = v.Title,
                CompanyName = v.Company.Name,
                Location = v.Location,
                ContractType = v.ContractType,
                WorkMode = v.WorkMode,
                Status = v.Status,
                IsTemporary = false,
                PublishedAt = v.PublishedAt,
                ClosedAt = v.ClosedAt,
                ViewsCount = v.ViewsCount
            })
            .ToListAsync();

        var tempQuery = _context.PT_TempVacancies.Where(v => !v.IsDeleted);

        var temp = await tempQuery
            .Select(v => new AdminVacancyDto
            {
                Id = v.Id,
                Title = v.Title,
                CompanyName = null,
                Location = v.Location,
                ContractType = v.ContractType,
                WorkMode = v.WorkMode,
                Status = v.IsPublished ? (int)VacancyStatus.Active : (int)VacancyStatus.Draft,
                IsTemporary = true,
                ExpiresAt = v.ExpiresAt,
                ViewsCount = 0
            })
            .ToListAsync();

        var combined = permanent.Concat(temp);
        if (status.HasValue) combined = combined.Where(v => v.Status == status.Value);

        return combined
            .OrderByDescending(v => v.PublishedAt ?? v.ExpiresAt)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .ToList();
    }

    public async Task<bool> ModerateAsync(Guid id, int status, Guid adminId, string? ipAddress)
    {
        var vacancy = await _context.PT_Vacancies.FirstOrDefaultAsync(v => v.Id == id && !v.IsDeleted);
        if (vacancy != null)
        {
            vacancy.Status = status;
            vacancy.UpdatedAt = DateTime.UtcNow;
            vacancy.UpdatedBy = adminId;
            if (status == (int)VacancyStatus.Closed) vacancy.ClosedAt = DateTime.UtcNow;
            if (status == (int)VacancyStatus.Active && vacancy.PublishedAt == null) vacancy.PublishedAt = DateTime.UtcNow;

            await _context.SaveChangesAsync();
            await _auditLog.LogAsync(adminId, "ModerateVacancy", "PT_Vacancies", id, $"{{\"status\":{status}}}", ipAddress);
            return true;
        }

        var tempVacancy = await _context.PT_TempVacancies.FirstOrDefaultAsync(v => v.Id == id && !v.IsDeleted);
        if (tempVacancy != null)
        {
            tempVacancy.IsPublished = status == (int)VacancyStatus.Active;
            tempVacancy.UpdatedAt = DateTime.UtcNow;
            tempVacancy.UpdatedBy = adminId;

            await _context.SaveChangesAsync();
            await _auditLog.LogAsync(adminId, "ModerateVacancy", "PT_TempVacancies", id, $"{{\"status\":{status}}}", ipAddress);
            return true;
        }

        return false;
    }
}
