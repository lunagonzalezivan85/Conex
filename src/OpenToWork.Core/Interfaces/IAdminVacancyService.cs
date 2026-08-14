using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Interfaces;

public interface IAdminVacancyService
{
    Task<List<AdminVacancyDto>> GetVacanciesAsync(int page, int pageSize, int? status);
    Task<bool> ModerateAsync(Guid id, int status, Guid adminId, string? ipAddress);
}
