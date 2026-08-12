using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace OpenToWork.Models.Entities;

public class PTApplication : BaseEntity
{
    [Required]
    public Guid PT_CandidateId { get; set; }

    [ForeignKey("PT_CandidateId")]
    public virtual PTCandidate Candidate { get; set; } = null!;

    [Required]
    public Guid PT_VacancyId { get; set; }

    [ForeignKey("PT_VacancyId")]
    public virtual PTVacancy Vacancy { get; set; } = null!;

    public int Status { get; set; } = 0;

    public string? CoverLetter { get; set; }

    public decimal? ExpectedSalary { get; set; }

    public DateTime? AvailableFromDate { get; set; }

    public int ApplicationSource { get; set; } = 0;
}
