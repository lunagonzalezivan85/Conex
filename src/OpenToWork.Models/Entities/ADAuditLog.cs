using System.ComponentModel.DataAnnotations;

namespace OpenToWork.Models.Entities;

public class ADAuditLog : BaseEntity
{
    public Guid SCUserId { get; set; }

    [Required]
    [MaxLength(100)]
    public string Action { get; set; } = string.Empty;

    [Required]
    [MaxLength(100)]
    public string EntityType { get; set; } = string.Empty;

    public Guid? EntityId { get; set; }

    public string? ChangesJson { get; set; }

    [MaxLength(45)]
    public string? IpAddress { get; set; }

    public virtual SCUser? User { get; set; }
}
