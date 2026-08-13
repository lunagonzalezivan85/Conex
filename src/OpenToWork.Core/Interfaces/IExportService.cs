namespace OpenToWork.Core.Interfaces;

public interface IExportService
{
    Task<string> ExportUsersCsvAsync();
    Task<string> ExportVacanciesCsvAsync();
}
