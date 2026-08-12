using Microsoft.AspNetCore.Components;

namespace OpenToWork.SharedUI.Components;

public partial class BentoCard : ComponentBase
{
    [Parameter] public string Title { get; set; } = string.Empty;
    [Parameter] public string? Icon { get; set; }
    [Parameter] public string? Description { get; set; }
    [Parameter] public string? FooterText { get; set; }
    [Parameter] public string Size { get; set; } = "default";
    [Parameter] public string CustomClass { get; set; } = string.Empty;
    [Parameter] public RenderFragment? ChildContent { get; set; }
    [Parameter] public EventCallback OnClick { get; set; }

    private string SizeClass => Size switch
    {
        "small" => "bento-card--small",
        "large" => "bento-card--large",
        "wide" => "bento-card--wide",
        "tall" => "bento-card--tall",
        _ => ""
    };
}
