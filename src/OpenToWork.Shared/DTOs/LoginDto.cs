namespace OpenToWork.Shared.DTOs;

public class LoginDto
{
    public string Email { get; set; } = string.Empty;
    public string Password { get; set; } = string.Empty;
    public bool RememberMe { get; set; }
    public string? DeviceHash { get; set; }
    public string? DeviceName { get; set; }
    public string? RecaptchaToken { get; set; }
}
