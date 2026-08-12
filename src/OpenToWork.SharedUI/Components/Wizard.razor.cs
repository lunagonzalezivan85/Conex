using Microsoft.AspNetCore.Components;

namespace OpenToWork.SharedUI.Components;

public partial class Wizard : ComponentBase
{
    [Parameter] public int CurrentStep { get; set; } = 1;
    [Parameter] public List<WizardStep> Steps { get; set; } = new();
    [Parameter] public RenderFragment? StepContent { get; set; }
    [Parameter] public EventCallback<int> StepChanged { get; set; }
    [Parameter] public EventCallback OnNext { get; set; }
    [Parameter] public EventCallback OnPrevious { get; set; }
    [Parameter] public EventCallback OnComplete { get; set; }
    [Parameter] public string? NextButtonText { get; set; }
    [Parameter] public string? PreviousButtonText { get; set; }
    [Parameter] public string? CompleteButtonText { get; set; }
    [Parameter] public bool CanProceed { get; set; } = true;
    [Parameter] public bool IsLoading { get; set; }

    public bool IsLastStep => CurrentStep >= Steps.Count;
    public bool IsFirstStep => CurrentStep <= 1;

    private async Task NextAsync()
    {
        if (IsLastStep)
        {
            await OnComplete.InvokeAsync();
            return;
        }

        await OnNext.InvokeAsync();
        if (CurrentStep < Steps.Count)
        {
            CurrentStep++;
            await StepChanged.InvokeAsync(CurrentStep);
        }
    }

    private async Task PreviousAsync()
    {
        if (IsFirstStep) return;

        await OnPrevious.InvokeAsync();
        CurrentStep--;
        await StepChanged.InvokeAsync(CurrentStep);
    }

    private int GetProgressPercent() => Steps.Count > 0 ? (int)((double)CurrentStep / Steps.Count * 100) : 0;
}

public class WizardStep
{
    public int Number { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Title { get; set; } = string.Empty;
    public string? Description { get; set; }
    public bool IsRequired { get; set; } = true;
    public bool IsCompleted { get; set; }
}
