using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace OpenToWork.Models.Entities;

public class PTCandidateSkill : BaseEntity
{
    [Required]
    public Guid PT_CandidateId { get; set; }

    [ForeignKey("PT_CandidateId")]
    public virtual PTCandidate Candidate { get; set; } = null!;

    [Required]
    public Guid PT_SkillId { get; set; }

    [ForeignKey("PT_SkillId")]
    public virtual PTSkill Skill { get; set; } = null!;

    public int? ProficiencyLevel { get; set; }
}
