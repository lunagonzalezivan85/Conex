using Microsoft.AspNetCore.Components;

namespace OpenToWork.SharedUI.Components;

public partial class OTInput : ComponentBase
{
    [Parameter] public string Label { get; set; } = string.Empty;
    [Parameter] public string Value { get; set; } = string.Empty;
    [Parameter] public EventCallback<string> ValueChanged { get; set; }
    [Parameter] public string Type { get; set; } = "text";
    [Parameter] public string Placeholder { get; set; } = string.Empty;
    [Parameter] public string? Icon { get; set; }
    [Parameter] public bool IsRequired { get; set; }
    [Parameter] public bool IsDisabled { get; set; }
    [Parameter] public string? ErrorMessage { get; set; }
    [Parameter] public string Id { get; set; } = $"input-{Guid.NewGuid():N}";

    private async Task OnInputAsync(ChangeEventArgs e)
    {
        if (e.Value is string val)
        {
            await ValueChanged.InvokeAsync(val);
        }
    }
}
