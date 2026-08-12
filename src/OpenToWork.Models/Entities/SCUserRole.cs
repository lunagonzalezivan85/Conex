using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace OpenToWork.Models.Entities;

public class SCUserRole : BaseEntity
{
    [Required]
    public Guid SCUserId { get; set; }

    [ForeignKey("SCUserId")]
    public virtual SCUser User { get; set; } = null!;

    public int Role { get; set; }

    public DateTime AssignedAt { get; set; } = DateTime.UtcNow;
}
