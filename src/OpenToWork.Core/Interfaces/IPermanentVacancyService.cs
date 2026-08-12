using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Interfaces;

public interface IPermanentVacancyService
{
    Task<VacancyDto> CreateVacancyAsync(Guid companyId, CreateVacancyDto dto, Guid userId);
    Task<VacancyDto?> GetVacancyByIdAsync(Guid id);
    Task<IEnumerable<VacancyDto>> GetVacanciesByCompanyAsync(Guid companyId);
    Task<(IEnumerable<VacancyDto> Items, int Total)> SearchVacanciesAsync(SearchPermanentVacancyDto search);
    Task<VacancyDto?> UpdateVacancyAsync(Guid id, UpdateVacancyDto dto, Guid userId);
    Task<bool> DeleteVacancyAsync(Guid id, Guid userId);
    Task<bool> PublishVacancyAsync(Guid id, Guid userId);
    Task<bool> CloseVacancyAsync(Guid id, Guid userId);
    Task<bool> ConvertTempVacancyAsync(Guid tempVacancyId, Guid userId);
}
