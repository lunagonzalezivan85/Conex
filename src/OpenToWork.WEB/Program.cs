using OpenToWork.WEB.Components;
using OpenToWork.WEB.Services;

var builder = WebApplication.CreateBuilder(args);

// Add services to the container.
builder.Services.AddRazorComponents()
    .AddInteractiveServerComponents();

builder.Services.AddAuthorization();
builder.Services.AddAuthentication();

builder.Services.AddScoped<LocalStorageService>();
builder.Services.AddScoped<ApiAuthService>();
builder.Services.AddScoped<AppAuthStateProvider>();
builder.Services.AddScoped<LanguageService>();
builder.Services.AddSingleton<AesEncryptionService>(sp => new AesEncryptionService(builder.Configuration["Security:AesKey"] ?? "OpenToWork-Default-Key-2024"));
builder.Services.AddScoped<Microsoft.AspNetCore.Components.Authorization.AuthenticationStateProvider>(sp => sp.GetRequiredService<AppAuthStateProvider>());

builder.Services.AddHttpClient<ApiAuthService>(client =>
{
    client.BaseAddress = new Uri(builder.Configuration["ApiSettings:BaseUrl"] ?? "http://localhost:5000/");
});

var app = builder.Build();

// Configure the HTTP request pipeline.
if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Error", createScopeForErrors: true);
    // The default HSTS value is 30 days. You may want to change this for production scenarios, see https://aka.ms/aspnetcore-hsts.
    app.UseHsts();
}
app.UseStatusCodePagesWithReExecute("/not-found", createScopeForStatusCodePages: true);
app.UseHttpsRedirection();
app.UseAntiforgery();

app.UseStaticFiles();

app.MapRazorComponents<App>()
    .AddInteractiveServerRenderMode();

app.Run();
