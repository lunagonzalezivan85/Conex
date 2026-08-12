namespace OpenToWork.Shared.DTOs;

public class RegisterDto
{
    public string Email { get; set; } = string.Empty;
    public string Password { get; set; } = string.Empty;
    public int PrimaryRole { get; set; }
    public string? Identification { get; set; }
    public string? Phone { get; set; }
}
