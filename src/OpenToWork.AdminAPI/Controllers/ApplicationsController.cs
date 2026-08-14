using Microsoft.AspNetCore.Mvc;
using OpenToWork.Core.Interfaces;

namespace OpenToWork.AdminAPI.Controllers;

[Route("api/admin/applications")]
public class ApplicationsController : AdminControllerBase
{
    private readonly IAdminApplicationService _applicationService;

    public ApplicationsController(IAdminApplicationService applicationService)
    {
        _applicationService = applicationService;
    }

    [HttpGet]
    public async Task<IActionResult> GetApplications([FromQuery] int page = 1, [FromQuery] int pageSize = 20, [FromQuery] int? status = null)
    {
        var applications = await _applicationService.GetApplicationsAsync(page, pageSize, status);
        return Ok(applications);
    }
}
