# Documento de Implementación

## OpenToWork - Guía de Implementación

**Versión:** 1.0  
**Fecha:** Agosto 2026  

---

## 1. Creación de la Solución y Proyectos

### 1.1 Comandos para crear la estructura

```bash
# Crear solución
dotnet new sln -n OpenToWork

# Crear proyectos - APIs independientes
dotnet new webapi -n OpenToWork.API -o src/OpenToWork.API
dotnet new webapi -n OpenToWork.AdminAPI -o src/OpenToWork.AdminAPI

# Crear proyectos - Frontends Blazor independientes
dotnet new blazor -n OpenToWork.WEB -o src/OpenToWork.WEB
dotnet new blazor -n OpenToWork.AdminWEB -o src/OpenToWork.AdminWEB

# Crear proyectos - Capas compartidas
dotnet new classlib -n OpenToWork.Core -o src/OpenToWork.Core
dotnet new classlib -n OpenToWork.Models -o src/OpenToWork.Models
dotnet new classlib -n OpenToWork.Shared -o src/OpenToWork.Shared

# Crear proyecto - SharedUI (Razor Class Library para componentes Blazor compartidos)
dotnet new razorclasslib -n OpenToWork.SharedUI -o src/OpenToWork.SharedUI

# Agregar proyectos a la solución
dotnet sln add src/OpenToWork.API
dotnet sln add src/OpenToWork.AdminAPI
dotnet sln add src/OpenToWork.WEB
dotnet sln add src/OpenToWork.AdminWEB
dotnet sln add src/OpenToWork.SharedUI
dotnet sln add src/OpenToWork.Core
dotnet sln add src/OpenToWork.Models
dotnet sln add src/OpenToWork.Shared

# Referencias - API Portal Principal
dotnet add src/OpenToWork.API reference src/OpenToWork.Core
dotnet add src/OpenToWork.API reference src/OpenToWork.Shared

# Referencias - API Admin (independiente pero usa Core/Models/Shared)
dotnet add src/OpenToWork.AdminAPI reference src/OpenToWork.Core
dotnet add src/OpenToWork.AdminAPI reference src/OpenToWork.Shared

# Referencias - Core
dotnet add src/OpenToWork.Core reference src/OpenToWork.Models
dotnet add src/OpenToWork.Core reference src/OpenToWork.Shared

# Referencias - WEB Portal Principal
dotnet add src/OpenToWork.WEB reference src/OpenToWork.Shared
dotnet add src/OpenToWork.WEB reference src/OpenToWork.SharedUI

# Referencias - WEB Portal Admin
dotnet add src/OpenToWork.AdminWEB reference src/OpenToWork.Shared
dotnet add src/OpenToWork.AdminWEB reference src/OpenToWork.SharedUI

# Referencias - SharedUI (componentes compartidos entre WEB y AdminWEB)
dotnet add src/OpenToWork.SharedUI reference src/OpenToWork.Shared

# Referencias - Models
dotnet add src/OpenToWork.Models reference src/OpenToWork.Shared
```

### 1.2 Estructura Final

```
OpenToWork/
├── docs/
│   ├── PRD.md
│   ├── TRN.md
│   ├── APPFLOW.md
│   ├── PLAN_DE_PROYECTO.md
│   └── IMPLEMENTACION.md
├── src/
│   ├── OpenToWork.API/              # Web API - Portal Principal (Puerto 5000)
│   │   ├── Controllers/             # Auth, Candidates, Companies, Vacancies, Applications
│   │   ├── Middleware/
│   │   ├── Extensions/
│   │   ├── Program.cs
│   │   └── appsettings.json          # JWT propio (Issuer: OpenToWork.Portal)
│   ├── OpenToWork.AdminAPI/         # Web API - Portal Admin (Puerto 5001, INDEPENDIENTE)
│   │   ├── Controllers/             # AdminAuth, Users, AdminCandidates, AdminVacancies, etc.
│   │   ├── Middleware/
│   │   ├── Extensions/
│   │   ├── Program.cs
│   │   └── appsettings.json          # JWT propio (Issuer: OpenToWork.Admin)
│   ├── OpenToWork.WEB/              # Blazor UI - Portal Principal (Puerto 5100)
│   │   ├── Components/
│   │   ├── Pages/
│   │   ├── Layouts/
│   │   ├── Services/                 # HttpClient → OpenToWork.API
│   │   ├── wwwroot/
│   │   └── Program.cs
│   ├── OpenToWork.AdminWEB/         # Blazor UI - Portal Admin (Puerto 5101, INDEPENDIENTE)
│   │   ├── Components/
│   │   ├── Pages/
│   │   ├── Layouts/
│   │   ├── Services/                 # HttpClient → OpenToWork.AdminAPI
│   │   ├── wwwroot/
│   │   └── Program.cs
│   ├── OpenToWork.SharedUI/        # Componentes Blazor compartidos (Razor Class Library)
│   │   ├── Components/             # BentoCard, Button, Input, Modal, Badge, Table, Wizard
│   │   ├── Layouts/                # Layouts compartidos (MainLayout, AuthLayout)
│   │   ├── Pages/                  # Páginas compartidas (Login, Register, Wizard)
│   │   ├── Theme/                  # ThemeSwitcher, ThemeService
│   │   └── _Imports.razor
│   ├── OpenToWork.Core/             # Lógica de negocio (compartido)
│   │   ├── Services/
│   │   ├── Interfaces/
│   │   ├── Validators/
│   │   └── Mappings/
│   ├── OpenToWork.Models/           # Entidades EF Core (compartido)
│   │   ├── Entities/
│   │   ├── Configurations/
│   │   └── Context/
│   └── OpenToWork.Shared/           # DTOs, Enums, Constants (compartido)
│       ├── DTOs/
│       ├── Enums/
│       └── Constants/
├── tests/
│   ├── OpenToWork.Core.Tests/
│   ├── OpenToWork.API.Tests/        # Pruebas API Portal
│   └── OpenToWork.AdminAPI.Tests/   # Pruebas API Admin
└── OpenToWork.sln
```

---

## 2. Paquetes NuGet por Proyecto

### OpenToWork.API (Portal Principal)
```xml
<PackageReference Include="Microsoft.AspNetCore.Authentication.JwtBearer" Version="8.0.0" />
<PackageReference Include="Microsoft.AspNetCore.Authentication.Google" Version="8.0.0" />
<PackageReference Include="Swashbuckle.AspNetCore" Version="6.5.0" />
<PackageReference Include="FluentValidation.AspNetCore" Version="11.3.0" />
<PackageReference Include="AutoMapper.Extensions.Microsoft.DependencyInjection" Version="12.0.1" />
<PackageReference Include="Serilog.AspNetCore" Version="8.0.1" />
```

### OpenToWork.AdminAPI (Portal Admin - Independiente)
```xml
<PackageReference Include="Microsoft.AspNetCore.Authentication.JwtBearer" Version="8.0.0" />
<PackageReference Include="Swashbuckle.AspNetCore" Version="6.5.0" />
<PackageReference Include="FluentValidation.AspNetCore" Version="11.3.0" />
<PackageReference Include="AutoMapper.Extensions.Microsoft.DependencyInjection" Version="12.0.1" />
<PackageReference Include="Serilog.AspNetCore" Version="8.0.1" />
```

### OpenToWork.Core
```xml
<PackageReference Include="AutoMapper" Version="13.0.1" />
<PackageReference Include="FluentValidation" Version="11.9.0" />
<PackageReference Include="Microsoft.Extensions.DependencyInjection.Abstractions" Version="8.0.1" />
<PackageReference Include="Microsoft.Extensions.Logging.Abstractions" Version="8.0.1" />
```

### OpenToWork.Models
```xml
<PackageReference Include="Microsoft.EntityFrameworkCore" Version="8.0.2" />
<PackageReference Include="Pomelo.EntityFrameworkCore.MySql" Version="8.0.2" />
<PackageReference Include="Microsoft.EntityFrameworkCore.Design" Version="8.0.2" />
```

> **Nota:** Para migrar a SQL Server, reemplazar `Pomelo.EntityFrameworkCore.MySql` por `Microsoft.EntityFrameworkCore.SqlServer`.

### OpenToWork.SharedUI (Componentes Blazor compartidos)
```xml
<PackageReference Include="Microsoft.AspNetCore.Components" Version="8.0.0" />
<PackageReference Include="Microsoft.AspNetCore.Components.Web" Version="8.0.0" />
```

### OpenToWork.WEB (Portal Principal)
```xml
<PackageReference Include="Microsoft.AspNetCore.Components.Authorization" Version="8.0.2" />
<PackageReference Include="Blazored.LocalStorage" Version="2.4.0" />
<PackageReference Include="Microsoft.Extensions.Http" Version="8.0.0" />
<!-- Para IndexedDB (opcional, Fase 2) -->
<!-- <PackageReference Include="DnetIndexedDB" Version="8.0.0" /> -->
```

### OpenToWork.AdminWEB (Portal Admin)
```xml
<PackageReference Include="Microsoft.AspNetCore.Components.Authorization" Version="8.0.2" />
<PackageReference Include="Blazored.LocalStorage" Version="2.4.0" />
<PackageReference Include="Microsoft.Extensions.Http" Version="8.0.0" />
<!-- Para IndexedDB -->
<!-- <PackageReference Include="DnetIndexedDB" Version="8.0.0" /> -->
```

---

## 3. Orden de Implementación

### Paso 1: Shared (DTOs, Enums, Constants)
1. Crear enums: `UserRole`, `VacancyStatus`, `ContractType`, `ApplicationStatus`.
2. Crear DTOs: `LoginDto`, `RegisterDto`, `CandidateDto`, `CompanyDto`, `VacancyDto`, `ApplicationDto`.
3. Crear constantes: `Roles`, `ErrorMessages`.

### Paso 2: Models (Entidades + EF Core)
1. Instalar NuGet: `Pomelo.EntityFrameworkCore.MySql` (provider MySQL).
2. Crear entidades: `User`, `UserRole`, `Candidate`, `Company`, `TempVacancy`, `RefreshToken`, `UserDevice`, `UserPreference`, `WizardStep`.
3. Crear `AppDbContext` con DbSets y configuraciones de relaciones.
4. Configurar MySQL con `utf8mb4` y `InnoDB`.
5. Crear migración inicial y aplicar a la base de datos MySQL.
6. Configurar seed data: pasos del wizard, usuario admin inicial.

### Paso 3: Core (Servicios)
1. Crear interfaces: `IAuthService`, `ICandidateService`, `ICompanyService`, `IVacancyService`, `IApplicationService`.
2. Implementar servicios con lógica de negocio.
3. Crear validators con FluentValidation.
4. Crear perfiles de AutoMapper.
5. Configurar Dependency Injection en `ServiceCollectionExtensions`.

### Paso 4: API Portal Principal (OpenToWork.API)
1. Configurar `Program.cs`: DI, JWT (Issuer: OpenToWork.Portal), Google OAuth, reCAPTCHA, Swagger, CORS, Middleware.
2. Crear `AuthController` (login, register, refresh, google, forgot-password).
3. Crear `CandidatesController`, `CompaniesController`, `VacanciesController`, `ApplicationsController`.
4. Implementar `DeviceDetectionService` (hash de User-Agent + IP + fingerprint).
5. Implementar `CaptchaService` (validación con Google reCAPTCHA API).
6. Implementar `GoogleAuthService` (validación de ID token de Google).
7. Implementar `RefreshTokenService` (rotación de refresh tokens).
8. Crear middleware de manejo de errores.
9. Probar endpoints con Swagger (puerto 5000).

### Paso 4b: API Portal Admin (OpenToWork.AdminAPI - Independiente)
1. Configurar `Program.cs`: DI, JWT (Issuer: OpenToWork.Admin, clave distinta), reCAPTCHA (obligatorio), Swagger, CORS, Middleware.
2. Crear `AdminAuthController` (login con captcha obligatorio, refresh, logout).
3. Crear `AdminUsersController`, `AdminCandidatesController`, `AdminCompaniesController`, `AdminVacanciesController`, `AdminApplicationsController`, `AdminSkillsController`, `AdminDashboardController`.
4. Implementar `CaptchaService` (validación obligatoria en todos los logins).
5. Implementar `RefreshTokenService` (rotación, expiración 1 día).
6. Crear middleware de manejo de errores (independiente).
7. Probar endpoints con Swagger (puerto 5001).
8. Verificar que tokens del portal principal son rechazados.

### Paso 5: WEB Portal Principal (OpenToWork.WEB)
1. Configurar `Program.cs`: HttpClient (→ API puerto 5000), AuthStateProvider, LocalStorage.
2. Implementar `SessionStorageService` (encriptación AES-256 de datos de sesión antes de guardar en localStorage/IndexedDB).
3. Implementar `DeviceFingerprintService` (generar fingerprint del navegador para detección de dispositivo).
4. Integrar Google reCAPTCHA v2 en página de login (mostrar condicionalmente).
5. Integrar Google Identity Services (GIS) para login con Google.
6. Configurar sistema de temas: crear estructura `wwwroot/themes/navy/theme.css` (variables CSS del tema por defecto), `wwwroot/themes/dark/theme.css` (tema oscuro), `wwwroot/css/base.css`, `wwwroot/css/components.css`, `wwwroot/css/bento-grid.css`, `wwwroot/css/responsive.css`.
7. Crear `wwwroot/js/theme-switcher.js` (cambio dinámico de tema via JSInterop).
8. Referenciar `OpenToWork.SharedUI` y usar componentes compartidos (BentoCard, Button, Input, Modal, Wizard, ThemeSwitcher, LanguageSwitcher).
8b. Configurar i18n: crear estructura `wwwroot/config/language/es/` y `wwwroot/config/language/en/` con archivos JSON (common, auth, wizard, dashboard, vacancies, profile, validation, errors).
8c. Crear `wwwroot/js/language-switcher.js` (cambio dinámico de idioma via JSInterop).
8d. Implementar `LanguageService.cs` (servicio Blazor para cargar traducciones, cambiar idioma, persistir en localStorage + BD).
9. Crear layout principal con NavMenu (barra navy, items con radius 16px, bottom nav en mobile). Usar SOLO clases CSS, no inline styles.
10. Crear página de inicio con selección de rol (Bento cards grandes, radius 24px, shadow md). El usuario puede elegir "Buscar Empleo" o "Contratar" pero ambos roles están disponibles.
11. Crear páginas de autenticación (Login con "Recordarme", captcha condicional, botón Google, tarjeta central radius 32px). Usar SharedUI para componentes.
12. Crear wizard de registro multi-paso (usar componente Wizard de SharedUI): Datos Personales → Ubicación → Perfil Profesional → Habilidades → Preferencias → Confirmación.
13. Crear páginas de dashboard para candidato (Bento Grid con tarjetas: perfil, vacantes recomendadas, solicitudes, stats).
14. Crear componentes específicos del portal en `Components/` (los compartidos van en SharedUI).
15. Implementar `AuthService` en Blazor para consumo de API Portal (login, refresh, google, register, wizard).
16. Implementar renovación automática de token (interceptor HttpClient que usa refresh token).

### Paso 5b: AdminWEB Portal Admin (OpenToWork.AdminWEB)
1. Configurar `Program.cs`: HttpClient (→ AdminAPI puerto 5001), AuthStateProvider, LocalStorage.
2. Implementar `SessionStorageService` (encriptación AES-256, almacenamiento en IndexedDB).
3. Integrar Google reCAPTCHA v2 en página de login (obligatorio siempre).
4. Configurar sistema de temas: misma estructura que portal principal (`wwwroot/themes/`, `wwwroot/css/`, `wwwroot/js/`). Compartir temas o usar temas propios.
5. Referenciar `OpenToWork.SharedUI` y usar componentes compartidos.
5b. Configurar i18n: misma estructura que portal principal (`wwwroot/config/language/es/`, `wwwroot/config/language/en/`).
6. Crear layout admin con NavMenu (barra navy, bottom nav en mobile). Usar SOLO clases CSS.
7. Crear página de login admin (con captcha obligatorio, sin "Recordarme", tarjeta central radius 32px). Usar SharedUI.
8. Crear páginas de gestión con Bento Grid: usuarios, candidatos, empresas, vacantes, solicitudes, skills (tarjetas radius 24px, tablas radius 16px).
9. Crear dashboard admin con métricas y gráficos (Bento Grid con tarjetas de stats, gráficos en tarjetas grandes).
10. Implementar `AdminAuthService` en Blazor para consumo de AdminAPI.
11. Implementar renovación automática de token (interceptor HttpClient con refresh token).

---

## 4. Configuración de Dependency Injection

### API Portal Principal - Program.cs
```csharp
// Services
builder.Services.AddScoped<IAuthService, AuthService>();
builder.Services.AddScoped<ICandidateService, CandidateService>();
builder.Services.AddScoped<ICompanyService, CompanyService>();
builder.Services.AddScoped<IVacancyService, VacancyService>();
builder.Services.AddScoped<IApplicationService, ApplicationService>();
builder.Services.AddScoped<IRefreshTokenService, RefreshTokenService>();
builder.Services.AddScoped<ICaptchaService, CaptchaService>();
builder.Services.AddScoped<IGoogleAuthService, GoogleAuthService>();
builder.Services.AddScoped<IDeviceDetectionService, DeviceDetectionService>();

// EF Core
builder.Services.AddDbContext<AppDbContext>(options =>
    options.UseSqlServer(builder.Configuration.GetConnectionString("DefaultConnection")));

// AutoMapper
builder.Services.AddAutoMapper(typeof(MappingProfile));

// JWT - Portal Principal (configuración propia)
builder.Services.AddAuthentication(JwtBearerDefaults.AuthenticationScheme)
    .AddJwtBearer(options =>
    {
        options.TokenValidationParameters = new TokenValidationParameters
        {
            ValidateIssuer = true,
            ValidIssuer = "OpenToWork.Portal",
            ValidateAudience = true,
            ValidAudience = "OpenToWork.Portal",
            ValidateIssuerSigningKey = true,
            IssuerSigningKey = new SymmetricSecurityKey(
                Encoding.UTF8.GetBytes(config["Jwt:Key"])), // Clave A
            ValidateLifetime = true,
            ClockSkew = TimeSpan.Zero
        };
    })
    .AddGoogle(options =>
    {
        options.ClientId = config["GoogleOAuth:ClientId"];
        options.ClientSecret = config["GoogleOAuth:ClientSecret"];
    });

// Swagger
builder.Services.AddSwaggerGen(c => { /* config with JWT */ });
```

### API Portal Admin - Program.cs (Independiente)
```csharp
// Services (puede usar servicios de Core pero con lógica de admin)
builder.Services.AddScoped<IAdminAuthService, AdminAuthService>();
builder.Services.AddScoped<IAdminUserService, AdminUserService>();
builder.Services.AddScoped<IAdminVacancyService, AdminVacancyService>();
builder.Services.AddScoped<IAdminApplicationService, AdminApplicationService>();
builder.Services.AddScoped<ISkillService, SkillService>();
builder.Services.AddScoped<IRefreshTokenService, RefreshTokenService>();
builder.Services.AddScoped<ICaptchaService, CaptchaService>(); // Obligatorio en admin

// EF Core (misma BD, pero con acceso de admin)
builder.Services.AddDbContext<AppDbContext>(options =>
    options.UseSqlServer(builder.Configuration.GetConnectionString("DefaultConnection")));

// AutoMapper
builder.Services.AddAutoMapper(typeof(AdminMappingProfile));

// JWT - Portal Admin (configuración PROPIA y DISTINTA)
builder.Services.AddAuthentication(JwtBearerDefaults.AuthenticationScheme)
    .AddJwtBearer(options =>
    {
        options.TokenValidationParameters = new TokenValidationParameters
        {
            ValidateIssuer = true,
            ValidIssuer = "OpenToWork.Admin",        // Issuer DISTINTO
            ValidateAudience = true,
            ValidAudience = "OpenToWork.Admin",       // Audience DISTINTO
            ValidateIssuerSigningKey = true,
            IssuerSigningKey = new SymmetricSecurityKey(
                Encoding.UTF8.GetBytes(config["Jwt:Key"])), // Clave B (DISTINTA)
            ValidateLifetime = true,
            ClockSkew = TimeSpan.Zero
        };
    });
// Nota: AdminAPI NO tiene Google OAuth

// Swagger (independiente)
builder.Services.AddSwaggerGen(c => { /* config with JWT - Admin */ });
```

---

## 5. Comandos de Gestión

### Migraciones EF Core (MySQL)
```bash
# Desde src/OpenToWork.Models
dotnet ef migrations add InitialCreate --project src/OpenToWork.Models --startup-project src/OpenToWork.API
dotnet ef database update --project src/OpenToWork.Models --startup-project src/OpenToWork.API

# Para AdminAPI (misma BD)
dotnet ef database update --project src/OpenToWork.Models --startup-project src/OpenToWork.AdminAPI
```
```bash
# Crear migración
dotnet ef migrations add InitialCreate --project src/OpenToWork.Models --startup-project src/OpenToWork.API

# Aplicar migración
dotnet ef database update --project src/OpenToWork.Models --startup-project src/OpenToWork.API

# Eliminar última migración
dotnet ef migrations remove --project src/OpenToWork.Models --startup-project src/OpenToWork.API
```

### Ejecutar Proyectos
```bash
# API Portal Principal
dotnet run --project src/OpenToWork.API

# API Portal Admin (independiente)
dotnet run --project src/OpenToWork.AdminAPI

# Blazor Portal Principal
dotnet run --project src/OpenToWork.WEB

# Blazor Portal Admin (independiente)
dotnet run --project src/OpenToWork.AdminWEB
```

### Pruebas
```bash
dotnet test
```

---

## 6. Configuración de Puertos

| Proyecto | Puerto | URL |
|----------|--------|-----|
| API Portal Principal | 5000 | http://localhost:5000 |
| Swagger Portal | 5000 | http://localhost:5000/swagger |
| API Portal Admin | 5001 | http://localhost:5001 |
| Swagger Admin | 5001 | http://localhost:5001/swagger |
| Blazor Portal Principal | 5100 | http://localhost:5100 |
| Blazor Portal Admin | 5101 | http://localhost:5101 |

---

## 7. Checklist de Implementación

### Shared
- [ ] Enums creados
- [ ] DTOs creados
- [ ] Constants creados

### Models
- [ ] Entidades creadas
- [ ] AppDbContext configurado
- [ ] Fluent API configurado
- [ ] Migración inicial creada
- [ ] Seed data configurado

### Core
- [ ] Interfaces definidas
- [ ] Servicios implementados
- [ ] Validators creados
- [ ] AutoMapper profiles creados
- [ ] DI configurado

### API Portal Principal (OpenToWork.API)
- [ ] Program.cs configurado (JWT Issuer: OpenToWork.Portal + Google OAuth + reCAPTCHA)
- [ ] AuthController (login, register, refresh, google, forgot-password)
- [ ] CandidatesController
- [ ] CompaniesController
- [ ] VacanciesController
- [ ] ApplicationsController
- [ ] CaptchaService (validación reCAPTCHA)
- [ ] GoogleAuthService (validación ID token Google)
- [ ] DeviceDetectionService (hash User-Agent + IP + fingerprint)
- [ ] RefreshTokenService (rotación de tokens)
- [ ] Middleware de errores
- [ ] Swagger configurado (puerto 5000)
- [ ] CORS configurado (dominio portal)

### API Portal Admin (OpenToWork.AdminAPI)
- [ ] Program.cs configurado (JWT Issuer: OpenToWork.Admin, clave distinta + reCAPTCHA obligatorio)
- [ ] AdminAuthController (login con captcha obligatorio, refresh, logout)
- [ ] AdminUsersController
- [ ] AdminCandidatesController
- [ ] AdminCompaniesController
- [ ] AdminVacanciesController
- [ ] AdminApplicationsController
- [ ] AdminSkillsController
- [ ] AdminDashboardController
- [ ] CaptchaService (validación obligatoria en todos los logins)
- [ ] RefreshTokenService (rotación, expiración 1 día)
- [ ] Middleware de errores (independiente)
- [ ] Swagger configurado (puerto 5001)
- [ ] CORS configurado (dominio admin)
- [ ] Verificar rechazo de tokens del portal principal

### WEB Portal Principal (OpenToWork.WEB)
- [ ] Program.cs configurado (HttpClient → API puerto 5000)
- [ ] SessionStorageService (encriptación AES-256 de datos de sesión)
- [ ] DeviceFingerprintService (fingerprint del navegador)
- [ ] Integración Google reCAPTCHA v2 en login (condicional)
- [ ] Integración Google Identity Services (login con Google)
- [ ] Sistema de temas configurado (wwwroot/themes/navy/theme.css, wwwroot/themes/dark/theme.css)
- [ ] CSS estructural creado (base.css, components.css, bento-grid.css, responsive.css)
- [ ] theme-switcher.js creado (cambio dinamico via JSInterop)
- [ ] ThemeService.cs implementado (switch/getCurrent/getAvailable)
- [ ] Sistema de i18n configurado (wwwroot/config/language/es/, wwwroot/config/language/en/)
- [ ] language-switcher.js creado (cambio dinamico via JSInterop)
- [ ] LanguageService.cs implementado (load/set/persist traducciones)
- [ ] Sin texto hardcoded en .razor (verificado, todo usa Lang.T())
- [ ] Sin inline styles ni inline scripts en .razor (verificado)
- [ ] Layout principal (barra navy, NavMenu, bottom nav mobile)
- [ ] Página de inicio (Bento cards de selección de rol, radius 24px)
- [ ] Login con "Recordarme" + captcha condicional + botón Google (tarjeta radius 32px)
- [ ] Register
- [ ] Dashboard candidato (Bento Grid con tarjetas modulares)
- [ ] Dashboard empresa (Bento Grid con tarjetas modulares)
- [ ] Componentes One UI: BentoCard, Button, Input, Modal, Badge, Table (todos con variables CSS)
- [ ] AuthService (HttpClient → API Portal, login, refresh, google)
- [ ] Interceptor HttpClient (renovación automática con refresh token)
- [ ] AuthStateProvider

### WEB Portal Admin (OpenToWork.AdminWEB)
- [ ] Program.cs configurado (HttpClient → AdminAPI puerto 5001)
- [ ] SessionStorageService (encriptación AES-256, almacenamiento en IndexedDB)
- [ ] Integración Google reCAPTCHA v2 en login (obligatorio)
- [ ] Sistema de temas configurado (mismas estructura que portal principal)
- [ ] ThemeService.cs implementado
- [ ] Sistema de i18n configurado (mismas estructura que portal principal)
- [ ] LanguageService.cs implementado
- [ ] Sin texto hardcoded en .razor (verificado)
- [ ] Sin inline styles ni inline scripts en .razor (verificado)
- [ ] Layout admin (barra navy, NavMenu, bottom nav mobile)
- [ ] Login admin (con captcha obligatorio, sin "Recordarme", tarjeta radius 32px)
- [ ] Dashboard admin con métricas (Bento Grid con tarjetas de stats y gráficos)
- [ ] Gestión de usuarios (Bento Grid + tabla radius 16px)
- [ ] Gestión de candidatos
- [ ] Gestión de empresas
- [ ] Gestión de vacantes
- [ ] Gestión de solicitudes
- [ ] Gestión de skills
- [ ] Componentes One UI: BentoCard, Button, Input, Modal, Badge, Table (todos con variables CSS)
- [ ] AdminAuthService (HttpClient → AdminAPI)
- [ ] Interceptor HttpClient (renovación automática con refresh token)
- [ ] AuthStateProvider (independiente)

### Pruebas
- [ ] Pruebas unitarias Core
- [ ] Pruebas integración API Portal Principal
- [ ] Pruebas integración API Portal Admin
- [ ] Pruebas de aislamiento JWT (token de portal rechazado en admin y viceversa)
- [ ] Pruebas de login con "Recordarme" (expiración extendida)
- [ ] Pruebas de captcha (dispositivo nuevo vs conocido)
- [ ] Pruebas de Google OAuth (login con ID token de Google)
- [ ] Pruebas de refresh token (rotación e invalidación)
- [ ] Pruebas de encriptación de datos de sesión (AES-256)
- [ ] Cobertura > 70% en ambas APIs
