using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace OpenToWork.Models.Entities;

public class SCUserDevice : BaseEntity
{
    [Required]
    public Guid SCUserId { get; set; }

    [ForeignKey("SCUserId")]
    public virtual SCUser User { get; set; } = null!;

    [Required]
    [MaxLength(256)]
    public string DeviceHash { get; set; } = string.Empty;

    [MaxLength(200)]
    public string? DeviceName { get; set; }

    [Required]
    public DateTime FirstSeenAt { get; set; } = DateTime.UtcNow;

    [Required]
    public DateTime LastSeenAt { get; set; } = DateTime.UtcNow;

    public bool IsTrusted { get; set; } = false;
}
