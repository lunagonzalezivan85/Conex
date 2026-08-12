using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace OpenToWork.Models.Entities;

public class SYUserPreference : BaseEntity
{
    [Required]
    public Guid SCUserId { get; set; }

    [ForeignKey("SCUserId")]
    public virtual SCUser User { get; set; } = null!;

    [Required]
    [MaxLength(50)]
    public string Theme { get; set; } = "navy";

    [Required]
    [MaxLength(10)]
    public string Language { get; set; } = "es";

    public int? PreferredRole { get; set; }
}
