namespace OpenToWork.Shared.DTOs;

public class ForgotPasswordDto
{
    public string Email { get; set; } = string.Empty;
}

public class ResetPasswordDto
{
    public string Token { get; set; } = string.Empty;
    public string NewPassword { get; set; } = string.Empty;
}

public class GoogleLoginDto
{
    public string Token { get; set; } = string.Empty;
}

public class VerifyRecaptchaDto
{
    public string Response { get; set; } = string.Empty;
}
