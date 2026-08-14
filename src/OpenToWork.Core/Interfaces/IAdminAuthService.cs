using OpenToWork.Shared.DTOs;

namespace OpenToWork.Core.Interfaces;

public interface IAdminAuthService
{
    Task<AuthResponseDto> LoginAsync(LoginDto dto);
}
