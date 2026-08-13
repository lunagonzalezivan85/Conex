using Microsoft.EntityFrameworkCore;
using OpenToWork.Core.Interfaces;
using OpenToWork.Models.Context;
using OpenToWork.Models.Entities;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Services;

public class AdminSkillService : IAdminSkillService
{
    private readonly AppDbContext _context;
    private readonly IAuditLogService _auditLog;

    public AdminSkillService(AppDbContext context, IAuditLogService auditLog)
    {
        _context = context;
        _auditLog = auditLog;
    }

    public async Task<List<AdminSkillDto>> GetSkillsAsync()
    {
        return await _context.PT_Skills
            .Where(s => !s.IsDeleted)
            .OrderBy(s => s.Category).ThenBy(s => s.Name)
            .Select(s => new AdminSkillDto { Id = s.Id, Name = s.Name, Category = s.Category })
            .ToListAsync();
    }

    public async Task<AdminSkillDto> CreateAsync(CreateSkillDto dto, Guid adminId, string? ipAddress)
    {
        var skill = new PTSkill
        {
            Name = dto.Name,
            Category = dto.Category,
            CreatedBy = adminId
        };
        _context.PT_Skills.Add(skill);
        await _context.SaveChangesAsync();

        await _auditLog.LogAsync(adminId, "CreateSkill", "PT_Skills", skill.Id, null, ipAddress);
        return new AdminSkillDto { Id = skill.Id, Name = skill.Name, Category = skill.Category };
    }

    public async Task<AdminSkillDto?> UpdateAsync(Guid id, CreateSkillDto dto, Guid adminId, string? ipAddress)
    {
        var skill = await _context.PT_Skills.FirstOrDefaultAsync(s => s.Id == id && !s.IsDeleted);
        if (skill == null) return null;

        skill.Name = dto.Name;
        skill.Category = dto.Category;
        skill.UpdatedAt = DateTime.UtcNow;
        skill.UpdatedBy = adminId;
        await _context.SaveChangesAsync();

        await _auditLog.LogAsync(adminId, "UpdateSkill", "PT_Skills", id, null, ipAddress);
        return new AdminSkillDto { Id = skill.Id, Name = skill.Name, Category = skill.Category };
    }

    public async Task<bool> DeleteAsync(Guid id, Guid adminId, string? ipAddress)
    {
        var skill = await _context.PT_Skills.FirstOrDefaultAsync(s => s.Id == id && !s.IsDeleted);
        if (skill == null) return false;

        skill.IsDeleted = true;
        skill.DeletedAt = DateTime.UtcNow;
        skill.DeletedBy = adminId;
        await _context.SaveChangesAsync();

        await _auditLog.LogAsync(adminId, "DeleteSkill", "PT_Skills", id, null, ipAddress);
        return true;
    }
}
