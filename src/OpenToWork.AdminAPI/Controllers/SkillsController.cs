using Microsoft.AspNetCore.Mvc;
using OpenToWork.Core.Interfaces;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.AdminAPI.Controllers;

[Route("api/admin/skills")]
public class SkillsController : AdminControllerBase
{
    private readonly IAdminSkillService _skillService;

    public SkillsController(IAdminSkillService skillService)
    {
        _skillService = skillService;
    }

    [HttpGet]
    public async Task<IActionResult> GetSkills()
    {
        var skills = await _skillService.GetSkillsAsync();
        return Ok(skills);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateSkillDto dto)
    {
        var skill = await _skillService.CreateAsync(dto, AdminId, ClientIp);
        return CreatedAtAction(nameof(GetSkills), new { id = skill.Id }, skill);
    }

    [HttpPut("{id}")]
    public async Task<IActionResult> Update(Guid id, [FromBody] CreateSkillDto dto)
    {
        var skill = await _skillService.UpdateAsync(id, dto, AdminId, ClientIp);
        return skill == null ? NotFound() : Ok(skill);
    }

    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(Guid id)
    {
        var result = await _skillService.DeleteAsync(id, AdminId, ClientIp);
        return result ? NoContent() : NotFound();
    }
}
