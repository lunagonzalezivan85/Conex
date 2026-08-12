using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using OpenToWork.Core.Interfaces;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.API.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class ProfileController : ControllerBase
{
    private readonly IProfileService _profileService;

    public ProfileController(IProfileService profileService)
    {
        _profileService = profileService;
    }

    [HttpGet]
    public async Task<IActionResult> GetProfile()
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _profileService.GetProfileAsync(userId.Value);
        return result != null ? Ok(result) : NotFound();
    }

    [HttpPut]
    public async Task<IActionResult> UpdateProfile([FromBody] UpdateCandidateProfileDto dto)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _profileService.UpdateProfileAsync(userId.Value, dto);
        return result != null ? Ok(result) : NotFound();
    }

    [HttpPost("experience")]
    public async Task<IActionResult> AddExperience([FromBody] CreateExperienceDto dto)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _profileService.AddExperienceAsync(userId.Value, dto);
        return Ok(result);
    }

    [HttpPut("experience/{id}")]
    public async Task<IActionResult> UpdateExperience(Guid id, [FromBody] UpdateExperienceDto dto)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _profileService.UpdateExperienceAsync(id, dto, userId.Value);
        return result != null ? Ok(result) : NotFound();
    }

    [HttpDelete("experience/{id}")]
    public async Task<IActionResult> DeleteExperience(Guid id)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var deleted = await _profileService.DeleteExperienceAsync(id, userId.Value);
        return deleted ? NoContent() : NotFound();
    }

    [HttpPost("education")]
    public async Task<IActionResult> AddEducation([FromBody] CreateEducationDto dto)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _profileService.AddEducationAsync(userId.Value, dto);
        return Ok(result);
    }

    [HttpPut("education/{id}")]
    public async Task<IActionResult> UpdateEducation(Guid id, [FromBody] UpdateEducationDto dto)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _profileService.UpdateEducationAsync(id, dto, userId.Value);
        return result != null ? Ok(result) : NotFound();
    }

    [HttpDelete("education/{id}")]
    public async Task<IActionResult> DeleteEducation(Guid id)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var deleted = await _profileService.DeleteEducationAsync(id, userId.Value);
        return deleted ? NoContent() : NotFound();
    }

    [HttpPost("certification")]
    public async Task<IActionResult> AddCertification([FromBody] CreateCertificationDto dto)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _profileService.AddCertificationAsync(userId.Value, dto);
        return Ok(result);
    }

    [HttpPut("certification/{id}")]
    public async Task<IActionResult> UpdateCertification(Guid id, [FromBody] UpdateCertificationDto dto)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var result = await _profileService.UpdateCertificationAsync(id, dto, userId.Value);
        return result != null ? Ok(result) : NotFound();
    }

    [HttpDelete("certification/{id}")]
    public async Task<IActionResult> DeleteCertification(Guid id)
    {
        var userId = GetUserId();
        if (userId == null) return Unauthorized();

        var deleted = await _profileService.DeleteCertificationAsync(id, userId.Value);
        return deleted ? NoContent() : NotFound();
    }

    private Guid? GetUserId()
    {
        var userIdClaim = User.FindFirst(System.Security.Claims.ClaimTypes.NameIdentifier)?.Value;
        return Guid.TryParse(userIdClaim, out var userId) ? userId : null;
    }
}
