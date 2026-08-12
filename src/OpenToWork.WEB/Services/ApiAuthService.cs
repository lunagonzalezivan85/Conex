using System.Net.Http.Headers;
using System.Net.Http.Json;
using OpenToWork.Shared.DTOs;

namespace OpenToWork.WEB.Services;

public class ApiAuthService
{
    private readonly HttpClient _httpClient;
    private readonly LocalStorageService _localStorage;
    private readonly ILogger<ApiAuthService> _logger;

    public ApiAuthService(HttpClient httpClient, LocalStorageService localStorage, ILogger<ApiAuthService> logger)
    {
        _httpClient = httpClient;
        _localStorage = localStorage;
        _logger = logger;
    }

    public async Task<AuthResponseDto?> LoginAsync(LoginDto dto)
    {
        var response = await _httpClient.PostAsJsonAsync("api/auth/login", dto);
        if (!response.IsSuccessStatusCode) return null;
        var result = await response.Content.ReadFromJsonAsync<AuthResponseDto>();
        if (result != null) await PersistAuthAsync(result);
        return result;
    }

    public async Task<AuthResponseDto?> RegisterAsync(RegisterDto dto)
    {
        var response = await _httpClient.PostAsJsonAsync("api/auth/register", dto);
        if (!response.IsSuccessStatusCode)
        {
            var error = await response.Content.ReadAsStringAsync();
            _logger.LogWarning("Register failed: {Error}", error);
            return null;
        }
        var result = await response.Content.ReadFromJsonAsync<AuthResponseDto>();
        if (result != null) await PersistAuthAsync(result);
        return result;
    }

    public async Task<AuthResponseDto?> RefreshTokenAsync(string refreshToken)
    {
        var response = await _httpClient.PostAsJsonAsync("api/auth/refresh", new RefreshTokenDto { RefreshToken = refreshToken });
        if (!response.IsSuccessStatusCode) return null;
        var result = await response.Content.ReadFromJsonAsync<AuthResponseDto>();
        if (result != null) await PersistAuthAsync(result);
        return result;
    }

    public async Task<bool> RevokeTokenAsync(string refreshToken)
    {
        var response = await _httpClient.PostAsJsonAsync("api/auth/revoke", new RefreshTokenDto { RefreshToken = refreshToken });
        return response.IsSuccessStatusCode;
    }

    public async Task<CandidateDto?> GetCandidateProfileAsync()
    {
        await SetAuthHeaderAsync();
        var response = await _httpClient.GetAsync("api/candidates/me");
        if (!response.IsSuccessStatusCode) return null;
        return await response.Content.ReadFromJsonAsync<CandidateDto>();
    }

    public async Task<CandidateDto?> UpdateWizardStepAsync(UpdateCandidateWizardDto dto)
    {
        await SetAuthHeaderAsync();
        var response = await _httpClient.PutAsJsonAsync("api/candidates/wizard", dto);
        if (!response.IsSuccessStatusCode) return null;
        return await response.Content.ReadFromJsonAsync<CandidateDto>();
    }

    public async Task<(List<TempVacancyDto> Items, int Total)> SearchVacanciesAsync(SearchVacancyDto search)
    {
        var query = $"api/vacancies/search?Page={search.Page}&PageSize={search.PageSize}";
        if (!string.IsNullOrEmpty(search.Query)) query += $"&Query={Uri.EscapeDataString(search.Query)}";
        if (!string.IsNullOrEmpty(search.Location)) query += $"&Location={Uri.EscapeDataString(search.Location)}";
        if (search.ContractType.HasValue) query += $"&ContractType={search.ContractType}";
        if (search.SalaryMin.HasValue) query += $"&SalaryMin={search.SalaryMin}";

        var response = await _httpClient.GetAsync(query);
        if (!response.IsSuccessStatusCode) return (new(), 0);

        var result = await response.Content.ReadFromJsonAsync<SearchResult>();
        return (result?.Items ?? new(), result?.Total ?? 0);
    }

    public async Task<List<TempVacancyDto>> GetMyVacanciesAsync()
    {
        await SetAuthHeaderAsync();
        var response = await _httpClient.GetAsync("api/vacancies/my");
        if (!response.IsSuccessStatusCode) return new();
        return await response.Content.ReadFromJsonAsync<List<TempVacancyDto>>() ?? new();
    }

    public async Task<TempVacancyDto?> CreateTempVacancyAsync(CreateTempVacancyDto dto)
    {
        await SetAuthHeaderAsync();
        var response = await _httpClient.PostAsJsonAsync("api/vacancies/temp", dto);
        if (!response.IsSuccessStatusCode) return null;
        return await response.Content.ReadFromJsonAsync<TempVacancyDto>();
    }

    public async Task<bool> DeleteTempVacancyAsync(Guid id)
    {
        await SetAuthHeaderAsync();
        var response = await _httpClient.DeleteAsync($"api/vacancies/temp/{id}");
        return response.IsSuccessStatusCode;
    }

    public async Task SetAuthHeaderAsync()
    {
        var token = await _localStorage.GetItemAsync("opentowork-token");
        if (!string.IsNullOrEmpty(token))
            _httpClient.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", token);
    }

    private async Task PersistAuthAsync(AuthResponseDto auth)
    {
        await _localStorage.SetItemAsync("opentowork-token", auth.Token);
        await _localStorage.SetItemAsync("opentowork-refresh-token", auth.RefreshToken);
        await _localStorage.SetItemAsync("opentowork-user-id", auth.User.Id.ToString());
        await _localStorage.SetItemAsync("opentowork-theme", auth.User.Theme ?? "navy");
        await _localStorage.SetItemAsync("opentowork-lang", auth.User.Language ?? "es");
    }

    public async Task ClearAuthAsync()
    {
        await _localStorage.RemoveItemAsync("opentowork-token");
        await _localStorage.RemoveItemAsync("opentowork-refresh-token");
        await _localStorage.RemoveItemAsync("opentowork-user-id");
        _httpClient.DefaultRequestHeaders.Authorization = null;
    }

    public async Task<string?> GetTokenAsync() => await _localStorage.GetItemAsync("opentowork-token");
    public async Task<string?> GetRefreshTokenAsync() => await _localStorage.GetItemAsync("opentowork-refresh-token");
    public async Task<string?> GetUserIdAsync() => await _localStorage.GetItemAsync("opentowork-user-id");

    private class SearchResult
    {
        public List<TempVacancyDto> Items { get; set; } = new();
        public int Total { get; set; }
    }
}
