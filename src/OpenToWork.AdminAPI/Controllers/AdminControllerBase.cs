using System.Security.Claims;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace OpenToWork.AdminAPI.Controllers;

[ApiController]
[Authorize(Roles = "Admin")]
public abstract class AdminControllerBase : ControllerBase
{
    protected Guid AdminId => Guid.Parse(User.FindFirstValue(ClaimTypes.NameIdentifier)!);

    protected string? ClientIp => HttpContext.Connection.RemoteIpAddress?.ToString();
}
