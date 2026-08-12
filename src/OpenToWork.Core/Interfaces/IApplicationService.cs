using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Interfaces;

public interface IApplicationService
{
    Task<ApplicationDto> ApplyAsync(Guid candidateId, CreateApplicationDto dto);
    Task<IEnumerable<ApplicationDto>> GetApplicationsByCandidateAsync(Guid candidateId);
    Task<IEnumerable<ApplicationDto>> GetApplicationsByVacancyAsync(Guid vacancyId, Guid userId);
    Task<ApplicationDto?> UpdateApplicationStatusAsync(Guid applicationId, int status, Guid userId);
    Task<bool> HasAlreadyAppliedAsync(Guid candidateId, Guid vacancyId);
}
