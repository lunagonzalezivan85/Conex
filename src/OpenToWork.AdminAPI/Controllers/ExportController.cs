using System.Text;
using Microsoft.AspNetCore.Mvc;
using OpenToWork.Core.Interfaces;

namespace OpenToWork.AdminAPI.Controllers;

[Route("api/admin/export")]
public class ExportController : AdminControllerBase
{
    private readonly IExportService _exportService;
    private readonly IAuditLogService _auditLog;

    public ExportController(IExportService exportService, IAuditLogService auditLog)
    {
        _exportService = exportService;
        _auditLog = auditLog;
    }

    [HttpGet("users")]
    public async Task<IActionResult> ExportUsers()
    {
        var csv = await _exportService.ExportUsersCsvAsync();
        await _auditLog.LogAsync(AdminId, "ExportUsers", "SC_Users", null, null, ClientIp);
        return File(Encoding.UTF8.GetBytes(csv), "text/csv", $"users-{DateTime.UtcNow:yyyyMMddHHmmss}.csv");
    }

    [HttpGet("vacancies")]
    public async Task<IActionResult> ExportVacancies()
    {
        var csv = await _exportService.ExportVacanciesCsvAsync();
        await _auditLog.LogAsync(AdminId, "ExportVacancies", "PT_Vacancies", null, null, ClientIp);
        return File(Encoding.UTF8.GetBytes(csv), "text/csv", $"vacancies-{DateTime.UtcNow:yyyyMMddHHmmss}.csv");
    }
}
