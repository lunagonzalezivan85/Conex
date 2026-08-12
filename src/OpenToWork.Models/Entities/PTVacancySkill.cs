using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace OpenToWork.Models.Entities;

public class PTVacancySkill : BaseEntity
{
    [Required]
    public Guid PT_VacancyId { get; set; }

    [ForeignKey("PT_VacancyId")]
    public virtual PTVacancy Vacancy { get; set; } = null!;

    [Required]
    public Guid PT_SkillId { get; set; }

    [ForeignKey("PT_SkillId")]
    public virtual PTSkill Skill { get; set; } = null!;

    public bool IsRequired { get; set; } = true;

    public int? MinProficiencyLevel { get; set; }
}
