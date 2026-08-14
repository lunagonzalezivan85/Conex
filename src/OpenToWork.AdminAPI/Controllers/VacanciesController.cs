using Microsoft.AspNetCore.Mvc;
using OpenToWork.Core.Interfaces;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.AdminAPI.Controllers;

[Route("api/admin/vacancies")]
public class VacanciesController : AdminControllerBase
{
    private readonly IAdminVacancyService _vacancyService;

    public VacanciesController(IAdminVacancyService vacancyService)
    {
        _vacancyService = vacancyService;
    }

    [HttpGet]
    public async Task<IActionResult> GetVacancies([FromQuery] int page = 1, [FromQuery] int pageSize = 20, [FromQuery] int? status = null)
    {
        var vacancies = await _vacancyService.GetVacanciesAsync(page, pageSize, status);
        return Ok(vacancies);
    }

    [HttpPut("{id}/moderate")]
    public async Task<IActionResult> Moderate(Guid id, [FromBody] ModerateVacancyDto dto)
    {
        var result = await _vacancyService.ModerateAsync(id, dto.Status, AdminId, ClientIp);
        return result ? NoContent() : NotFound();
    }
}
