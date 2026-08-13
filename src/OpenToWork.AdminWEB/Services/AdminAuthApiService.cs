using System.Net.Http.Headers;
using System.Net.Http.Json;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.AdminWEB.Services;

public class AdminAuthApiService
{
    private readonly HttpClient _httpClient;
    private readonly LocalStorageService _localStorage;

    public AdminAuthApiService(HttpClient httpClient, LocalStorageService localStorage)
    {
        _httpClient = httpClient;
        _localStorage = localStorage;
    }

    public async Task<AuthResponseDto?> LoginAsync(LoginDto dto)
    {
        var response = await _httpClient.PostAsJsonAsync("api/admin/auth/login", dto);
        if (!response.IsSuccessStatusCode) return null;

        var result = await response.Content.ReadFromJsonAsync<AuthResponseDto>();
        if (result != null) await PersistAuthAsync(result);
        return result;
    }

    public async Task<List<AuditLogDto>> GetAuditLogAsync(int page = 1, int pageSize = 20)
    {
        await SetAuthHeaderAsync();
        var response = await _httpClient.GetAsync($"api/admin/audit-log?page={page}&pageSize={pageSize}");
        if (!response.IsSuccessStatusCode) return new();
        return await response.Content.ReadFromJsonAsync<List<AuditLogDto>>() ?? new();
    }

    public async Task SetAuthHeaderAsync()
    {
        var token = await _localStorage.GetItemAsync("otwadmin-token");
        if (!string.IsNullOrEmpty(token))
            _httpClient.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", token);
    }

    private async Task PersistAuthAsync(AuthResponseDto auth)
    {
        await _localStorage.SetItemAsync("otwadmin-token", auth.Token);
        await _localStorage.SetItemAsync("otwadmin-refresh-token", auth.RefreshToken);
        await _localStorage.SetItemAsync("otwadmin-user-id", auth.User.Id.ToString());
    }

    public async Task LogoutAsync()
    {
        await _localStorage.RemoveItemAsync("otwadmin-token");
        await _localStorage.RemoveItemAsync("otwadmin-refresh-token");
        await _localStorage.RemoveItemAsync("otwadmin-user-id");
        _httpClient.DefaultRequestHeaders.Authorization = null;
    }
}
