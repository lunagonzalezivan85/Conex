using System.Text.Json;
using Microsoft.JSInterop;

namespace OpenToWork.WEB.Services;

public class LanguageService
{
    private readonly IJSRuntime _jsRuntime;
    private readonly HttpClient _httpClient;
    private string _currentLanguage = "es";
    public Dictionary<string, string> _translations = new();

    public event Action? OnLanguageChanged;

    public LanguageService(IJSRuntime jsRuntime, HttpClient httpClient)
    {
        _jsRuntime = jsRuntime;
        _httpClient = httpClient;
    }

    public string CurrentLanguage => _currentLanguage;

    public async Task InitializeAsync()
    {
        var saved = await _jsRuntime.InvokeAsync<string?>("localStorage.getItem", "opentowork-lang");
        _currentLanguage = saved ?? "es";
        await LoadTranslationsAsync(_currentLanguage);
    }

    public async Task SetLanguageAsync(string lang)
    {
        _currentLanguage = lang;
        await LoadTranslationsAsync(lang);
        await _jsRuntime.InvokeVoidAsync("localStorage.setItem", "opentowork-lang", lang);
        OnLanguageChanged?.Invoke();
    }

    public async Task LoadTranslationsAsync(string lang)
    {
        var sections = new[] { "common", "auth", "wizard", "dashboard", "vacancies", "profile", "validation", "errors", "applications" };
        _translations.Clear();
        foreach (var section in sections)
        {
            try
            {
                var json = await _httpClient.GetFromJsonAsync<Dictionary<string, object>>($"config/language/{lang}/{section}.json");
                if (json != null) FlattenDictionary(json, section, _translations);
            }
            catch { }
        }
    }

    public string T(string key) => _translations.TryGetValue(key, out var value) ? value : key;

    private static void FlattenDictionary(Dictionary<string, object> dict, string prefix, Dictionary<string, string> result)
    {
        foreach (var kvp in dict)
        {
            var fullKey = $"{prefix}.{kvp.Key}";
            if (kvp.Value is JsonElement je && je.ValueKind == JsonValueKind.Object)
            {
                var nested = je.Deserialize<Dictionary<string, object>>();
                if (nested != null) FlattenDictionary(nested, fullKey, result);
            }
            else
            {
                result[fullKey] = kvp.Value?.ToString() ?? fullKey;
            }
        }
    }
}
