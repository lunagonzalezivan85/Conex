using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Interfaces;

public interface IVacancyService
{
    Task<TempVacancyDto> CreateTempVacancyAsync(Guid userId, CreateTempVacancyDto dto);
    Task<IEnumerable<TempVacancyDto>> GetTempVacanciesByUserAsync(Guid userId);
    Task<(IEnumerable<TempVacancyDto> Items, int Total)> SearchVacanciesAsync(SearchVacancyDto search);
    Task<bool> DeleteTempVacancyAsync(Guid vacancyId, Guid userId);
}
