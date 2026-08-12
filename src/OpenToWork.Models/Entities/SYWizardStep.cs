using System.ComponentModel.DataAnnotations;

namespace OpenToWork.Models.Entities;

public class SYWizardStep : BaseEntity
{
    [Required]
    public int StepNumber { get; set; }

    [Required]
    [MaxLength(100)]
    public string StepName { get; set; } = string.Empty;

    [Required]
    [MaxLength(200)]
    public string StepTitle { get; set; } = string.Empty;

    [MaxLength(500)]
    public string? Description { get; set; }

    public bool IsRequired { get; set; } = true;

    [Required]
    public int Order { get; set; }

    public int Phase { get; set; } = 1;
}
