using Microsoft.AspNetCore.Components;

namespace OpenToWork.SharedUI.Components;

public partial class OTButton : ComponentBase
{
    [Parameter] public string Text { get; set; } = string.Empty;
    [Parameter] public string Variant { get; set; } = "primary";
    [Parameter] public string Size { get; set; } = "medium";
    [Parameter] public string? Icon { get; set; }
    [Parameter] public bool IsFullWidth { get; set; }
    [Parameter] public bool IsDisabled { get; set; }
    [Parameter] public bool IsLoading { get; set; }
    [Parameter] public EventCallback OnClick { get; set; }
    [Parameter] public RenderFragment? ChildContent { get; set; }
    [Parameter] public string Type { get; set; } = "button";

    private string VariantClass => Variant switch
    {
        "secondary" => "ot-btn--secondary",
        "outline" => "ot-btn--outline",
        "danger" => "ot-btn--danger",
        "ghost" => "ot-btn--ghost",
        _ => "ot-btn--primary"
    };

    private string SizeClass => Size switch
    {
        "small" => "ot-btn--small",
        "large" => "ot-btn--large",
        _ => "ot-btn--medium"
    };
}
