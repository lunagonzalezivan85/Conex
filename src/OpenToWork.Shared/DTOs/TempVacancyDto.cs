namespace OpenToWork.Shared.DTOs;

public class TempVacancyDto
{
    public Guid Id { get; set; }
    public Guid UserId { get; set; }
    public string Title { get; set; } = string.Empty;
    public string? Description { get; set; }
    public string? Requirements { get; set; }
    public decimal? SalaryMin { get; set; }
    public decimal? SalaryMax { get; set; }
    public string? Location { get; set; }
    public int ContractType { get; set; }
    public DateTime ExpiresAt { get; set; }
    public bool IsPublished { get; set; }
}

public class CreateTempVacancyDto
{
    public string Title { get; set; } = string.Empty;
    public string? Description { get; set; }
    public string? Requirements { get; set; }
    public decimal? SalaryMin { get; set; }
    public decimal? SalaryMax { get; set; }
    public string? Location { get; set; }
    public int ContractType { get; set; }
    public int ExpirationDays { get; set; } = 30;
    public bool IsPublished { get; set; } = false;
}

public class SearchVacancyDto
{
    public string? Query { get; set; }
    public string? Location { get; set; }
    public int? ContractType { get; set; }
    public decimal? SalaryMin { get; set; }
    public int Page { get; set; } = 1;
    public int PageSize { get; set; } = 10;
}
