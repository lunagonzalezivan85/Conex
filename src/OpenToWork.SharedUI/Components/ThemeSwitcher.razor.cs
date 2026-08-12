using Microsoft.AspNetCore.Components;

namespace OpenToWork.SharedUI.Components;

public partial class ThemeSwitcher : ComponentBase
{
    [Parameter] public string CurrentTheme { get; set; } = "navy";
    [Parameter] public EventCallback<string> ThemeChanged { get; set; }

    private static readonly string[] Themes = { "navy", "dark", "light", "corporate" };

    private async Task SelectThemeAsync(string theme)
    {
        await ThemeChanged.InvokeAsync(theme);
    }
}
