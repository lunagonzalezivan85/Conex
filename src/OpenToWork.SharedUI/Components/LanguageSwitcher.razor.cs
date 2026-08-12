using Microsoft.AspNetCore.Components;

namespace OpenToWork.SharedUI.Components;

public partial class LanguageSwitcher : ComponentBase
{
    [Parameter] public string CurrentLanguage { get; set; } = "es";
    [Parameter] public EventCallback<string> LanguageChanged { get; set; }

    private static readonly (string Code, string Label, string Flag)[] Languages =
    {
        ("es", "Espanol", "ES"),
        ("en", "English", "EN")
    };

    private async Task SelectLanguageAsync(string lang)
    {
        await LanguageChanged.InvokeAsync(lang);
    }
}
