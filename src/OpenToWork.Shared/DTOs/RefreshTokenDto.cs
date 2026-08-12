namespace OpenToWork.Shared.DTOs;

public class RefreshTokenDto
{
    public string RefreshToken { get; set; } = string.Empty;
    public string? DeviceHash { get; set; }
}
