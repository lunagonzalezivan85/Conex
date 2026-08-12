using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using OpenToWork.Core.Interfaces;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.API.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class VacanciesController : ControllerBase
{
    private readonly IVacancyService _vacancyService;

    public VacanciesController(IVacancyService vacancyService)
    {
        _vacancyService = vacancyService;
    }

    [HttpPost("temp")]
    public async Task<IActionResult> CreateTempVacancy([FromBody] CreateTempVacancyDto dto)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _vacancyService.CreateTempVacancyAsync(userId.Value, dto);
        return CreatedAtAction(nameof(GetMyVacancies), new { id = result.Id }, result);
    }

    [HttpGet("my")]
    public async Task<IActionResult> GetMyVacancies()
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _vacancyService.GetTempVacanciesByUserAsync(userId.Value);
        return Ok(result);
    }

    [AllowAnonymous]
    [HttpGet("search")]
    public async Task<IActionResult> Search([FromQuery] SearchVacancyDto search)
    {
        var (items, total) = await _vacancyService.SearchVacanciesAsync(search);
        return Ok(new { items, total, page = search.Page, pageSize = search.PageSize });
    }

    [HttpDelete("temp/{id}")]
    public async Task<IActionResult> DeleteTempVacancy(Guid id)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var deleted = await _vacancyService.DeleteTempVacancyAsync(id, userId.Value);
        return deleted ? NoContent() : NotFound();
    }

    private Guid? GetUserId()
    {
        var userIdClaim = User.FindFirst(System.Security.Claims.ClaimTypes.NameIdentifier)?.Value;
        return Guid.TryParse(userIdClaim, out var userId) ? userId : null;
    }
}
