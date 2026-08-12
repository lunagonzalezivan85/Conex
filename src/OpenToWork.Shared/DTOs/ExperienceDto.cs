namespace OpenToWork.Shared.DTOs;

public class CandidateExperienceDto
{
    public Guid Id { get; set; }
    public Guid CandidateId { get; set; }
    public string CompanyName { get; set; } = string.Empty;
    public string JobTitle { get; set; } = string.Empty;
    public string? Description { get; set; }
    public DateTime StartDate { get; set; }
    public DateTime? EndDate { get; set; }
    public bool IsCurrentJob { get; set; }
    public string? Location { get; set; }
}

public class CreateExperienceDto
{
    public string CompanyName { get; set; } = string.Empty;
    public string JobTitle { get; set; } = string.Empty;
    public string? Description { get; set; }
    public DateTime StartDate { get; set; }
    public DateTime? EndDate { get; set; }
    public bool IsCurrentJob { get; set; }
    public string? Location { get; set; }
}

public class UpdateExperienceDto
{
    public string? CompanyName { get; set; }
    public string? JobTitle { get; set; }
    public string? Description { get; set; }
    public DateTime? StartDate { get; set; }
    public DateTime? EndDate { get; set; }
    public bool? IsCurrentJob { get; set; }
    public string? Location { get; set; }
}
