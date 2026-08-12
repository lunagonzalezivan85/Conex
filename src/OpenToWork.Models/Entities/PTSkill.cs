using System.ComponentModel.DataAnnotations;

namespace OpenToWork.Models.Entities;

public class PTSkill : BaseEntity
{
    [Required]
    [MaxLength(100)]
    public string Name { get; set; } = string.Empty;

    [MaxLength(100)]
    public string? Category { get; set; }

    public virtual ICollection<PTCandidateSkill> CandidateSkills { get; set; } = new List<PTCandidateSkill>();
}
