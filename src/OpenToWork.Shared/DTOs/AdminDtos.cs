namespace OpenToWork.Shared.DTOs;

public class AdminUserDto
{
    public Guid Id { get; set; }
    public string Email { get; set; } = string.Empty;
    public int PrimaryRole { get; set; }
    public bool EmailVerified { get; set; }
    public bool IsActive { get; set; }
    public DateTime CreatedAt { get; set; }
    public DateTime? LastLoginAt { get; set; }
}

public class AdminVacancyDto
{
    public Guid Id { get; set; }
    public string Title { get; set; } = string.Empty;
    public string? CompanyName { get; set; }
    public string? Location { get; set; }
    public int ContractType { get; set; }
    public int WorkMode { get; set; }
    public int Status { get; set; }
    public bool IsTemporary { get; set; }
    public DateTime? PublishedAt { get; set; }
    public DateTime? ClosedAt { get; set; }
    public DateTime? ExpiresAt { get; set; }
    public int ViewsCount { get; set; }
}

public class ModerateVacancyDto
{
    public int Status { get; set; }
}

public class AdminSkillDto
{
    public Guid Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string? Category { get; set; }
}

public class CreateSkillDto
{
    public string Name { get; set; } = string.Empty;
    public string? Category { get; set; }
}

public class DashboardMetricsDto
{
    public int TotalUsers { get; set; }
    public int ActiveUsers { get; set; }
    public int TotalCandidates { get; set; }
    public int TotalCompanies { get; set; }
    public int TotalPermanentVacancies { get; set; }
    public int TotalTempVacancies { get; set; }
    public Dictionary<string, int> VacanciesByStatus { get; set; } = new();
    public Dictionary<string, int> ApplicationsByStatus { get; set; } = new();
    public int TotalSkills { get; set; }
    public int TotalAuditLogEntries { get; set; }

    public int EvaluatedProfiles { get; set; }
    public int PendingProfiles { get; set; }
    public int ProfilesWithScores { get; set; }
    public int OpenVacancies { get; set; }
    public int ClosedVacancies { get; set; }
    public int DraftVacancies { get; set; }
    public int CompaniesWithVacancies { get; set; }
    public int CompaniesWithoutVacancies { get; set; }
    public int NonAdminUsers { get; set; }
    public int NonAdminCandidates { get; set; }
    public int NonAdminCompanies { get; set; }
    public int CandidatesWithLinkedIn { get; set; }
    public int CandidatesWithPortfolio { get; set; }
    public int CandidatesWithCV { get; set; }
}
