using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Interfaces;

public interface IAdminSkillService
{
    Task<List<AdminSkillDto>> GetSkillsAsync();
    Task<AdminSkillDto> CreateAsync(CreateSkillDto dto, Guid adminId, string? ipAddress);
    Task<AdminSkillDto?> UpdateAsync(Guid id, CreateSkillDto dto, Guid adminId, string? ipAddress);
    Task<bool> DeleteAsync(Guid id, Guid adminId, string? ipAddress);
}
