# NEURAL_MAP - Mapa Neuronal del Proyecto

## OpenToWork - Guia de Navegacion para IA

> **Proposito:** Este documento permite a cualquier IA entender la arquitectura, ubicacion de codigo y dependencias del proyecto sin leer todos los archivos. Es el punto de entrada para continuar el desarrollo.

---

## 1. Vision General

OpenToWork es una plataforma de empleo (.NET 8 + Blazor + MySQL) con dos portales independientes (Principal y Admin). Actualmente **Fase 1 completada**.

```
OpenToWork/
├── src/
│   ├── OpenToWork.API/          # API REST portal principal (puerto 5000)
│   ├── OpenToWork.AdminAPI/     # API REST portal admin (puerto 5001) [stub]
│   ├── OpenToWork.WEB/          # Blazor Server portal principal (puerto 5100)
│   ├── OpenToWork.AdminWEB/     # Blazor Server portal admin [stub]
│   ├── OpenToWork.SharedUI/     # Razor Class Library - componentes compartidos
│   ├── OpenToWork.Core/         # Servicios de negocio
│   ├── OpenToWork.Models/       # Entidades EF Core + AppDbContext + Migraciones
│   └── OpenToWork.Shared/       # DTOs + Enums
├── docs/                        # Toda la documentacion
└── OpenToWork.slnx              # Solucion
```

---

## 2. Arquitectura de Dependencias

```
OpenToWork.API ──> OpenToWork.Core ──> OpenToWork.Models
     │                   │                    │
     v                   v                    v
 OpenToWork.Shared   OpenToWork.Shared   EF Core + Pomelo MySQL

OpenToWork.WEB ──> OpenToWork.SharedUI ──> (componentes Blazor)
     │
     v
 OpenToWork.Shared (DTOs, Enums)
```

**Regla:** WEB no referencia Core directamente. Se comunica con la API via HTTP (`HttpClient`).

---

## 3. Mapa de Archivos Clave

### 3.1 OpenToWork.API (Backend - Portal Principal)

| Archivo | Rol | Lineas aprox |
|---|---|---|
| `Program.cs` | Configuracion: JWT, DI, Swagger, CORS, Controllers | ~93 |
| `appsettings.json` | Connection string MySQL, JWT, Google OAuth, reCAPTCHA | ~30 |
| `Controllers/AuthController.cs` | Endpoints: register, login, refresh, revoke, check-device | ~70 |
| `Controllers/CandidatesController.cs` | Endpoints: get profile, wizard status, update wizard | ~55 |
| `Controllers/VacanciesController.cs` | Endpoints: create temp vacancy, my vacancies, search, delete | ~60 |

### 3.2 OpenToWork.Core (Logica de Negocio)

| Archivo | Rol | Lineas aprox |
|---|---|---|
| `Interfaces/IAuthService.cs` | Contrato: register, login, refresh, revoke, device | ~12 |
| `Interfaces/ICandidateService.cs` | Contrato: get, create, update wizard, check complete | ~10 |
| `Interfaces/IVacancyService.cs` | Contrato: create, get by user, search, delete | ~10 |
| `Services/AuthService.cs` | Implementacion: BCrypt, JWT generation, refresh tokens, device | ~210 |
| `Services/CandidateService.cs` | Implementacion: CRUD candidato, wizard step update | ~100 |
| `Services/VacancyService.cs` | Implementacion: CRUD vacantes temporales, busqueda paginada | ~110 |
| `Extensions/ServiceCollectionExtensions.cs` | DI: AddCoreServices(), AddDatabaseContext() | ~30 |

### 3.3 OpenToWork.Models (Datos)

| Archivo | Rol | Lineas aprox |
|---|---|---|
| `Context/AppDbContext.cs` | DbContext: 11 DbSets, configuracion de tablas, indices, seed wizard | ~130 |
| `Entities/BaseEntity.cs` | Clase base: Id, CreatedAt, UpdatedAt, IsDeleted, etc. | ~15 |
| `Entities/SCUser.cs` | Usuario: Email, PasswordHash, PrimaryRole, GoogleId, nav props | ~35 |
| `Entities/SCUserRole.cs` | Rol multi-usuario: SCUserId, Role | ~15 |
| `Entities/SCRefreshToken.cs` | Refresh token: TokenHash, ExpiresAt, IsRevoked | ~20 |
| `Entities/SCUserDevice.cs` | Dispositivo: DeviceHash, DeviceName, IsTrusted | ~20 |
| `Entities/PTCandidate.cs` | Candidato: FirstName, LastName, WizardStep, WizardCompleted | ~45 |
| `Entities/PTCompany.cs` | Empresa: Name, Description, Website, LogoUrl | ~25 |
| `Entities/PTTempVacancy.cs` | Vacante temporal: Title, Description, ExpiresAt, IsPublished | ~30 |
| `Entities/PTSkill.cs` | Skill: Name, Category | ~12 |
| `Entities/PTCandidateSkill.cs` | Relacion candidato-skill: ProficiencyLevel | ~15 |
| `Entities/SYWizardStep.cs` | Step del wizard: StepNumber, StepName, Order, Phase | ~18 |
| `Entities/SYUserPreference.cs` | Preferencias: Theme, Language, PreferredRole | ~15 |
| `Design/AppDbContextDesignTimeFactory.cs` | Factory para migraciones EF sin conexion viva | ~20 |
| `Migrations/` | 3 archivos: InitialCreate, Designer, ModelSnapshot | auto-generado |

### 3.4 OpenToWork.Shared (DTOs + Enums)

| Archivo | Rol |
|---|---|
| `Enums/UserRole.cs` | Candidate=0, Company=1, Admin=2 |
| `Enums/Gender.cs` | Male=0, Female=1, Other=2, PreferNotToSay=3 |
| `Enums/ContractType.cs` | FullTime=0, PartTime=1, Contract=2, Remote=3 |
| `Enums/VacancyStatus.cs` | Draft=0, Active=1, Closed=2 |
| `Enums/ApplicationStatus.cs` | Pending=0, InReview=1, Accepted=2, Rejected=3 |
| `Enums/ProficiencyLevel.cs` | Beginner=0, Intermediate=1, Advanced=2, Expert=3 |
| `DTOs/LoginDto.cs` | Email, Password, RememberMe, DeviceHash, RecaptchaToken |
| `DTOs/RegisterDto.cs` | Email, Password, PrimaryRole, Identification, Phone |
| `DTOs/AuthResponseDto.cs` | Token, RefreshToken, ExpiresAt, UserDto (con WizardStep, WizardCompleted) |
| `DTOs/RefreshTokenDto.cs` | RefreshToken, DeviceHash |
| `DTOs/CandidateDto.cs` | CandidateDto + UpdateCandidateWizardDto |
| `DTOs/TempVacancyDto.cs` | TempVacancyDto + CreateTempVacancyDto + SearchVacancyDto |

### 3.5 OpenToWork.SharedUI (Componentes Blazor)

| Archivo | Rol |
|---|---|
| `Components/BentoCard.razor` + `.razor.cs` | Tarjeta estilo Bento Grid con titulo, icono, body, footer |
| `Components/OTButton.razor` + `.razor.cs` | Boton con variantes (primary, secondary, outline, danger, ghost) |
| `Components/OTInput.razor` + `.razor.cs` | Input con label, validacion, icono |
| `Components/Wizard.razor` + `.razor.cs` | Wizard multi-paso con progress bar, step indicator, navegacion |
| `Components/ThemeSwitcher.razor` + `.razor.cs` | Selector de tema (navy, dark, light) |
| `Components/LanguageSwitcher.razor` + `.razor.cs` | Selector de idioma (es, en) |

> **Patron:** Cada componente tiene `.razor` (markup HTML) + `.razor.cs` (codigo C# partial class).

### 3.6 OpenToWork.WEB (Frontend Blazor)

| Archivo | Rol |
|---|---|
| `Program.cs` | DI: ApiAuthService, LocalStorageService, AppAuthStateProvider, LanguageService, HttpClient |
| `appsettings.json` | ApiSettings.BaseUrl = http://localhost:5000/ |
| `Components/App.razor` | HTML root: carga CSS (base, components, bento-grid, responsive) + tema + JS |
| `Components/Routes.razor` | Router Blazor con MainLayout |
| `Components/Layout/MainLayout.razor` | Nav bar con links, theme/lang switchers |
| `Components/Layout/AuthLayout.razor` | Layout minimal para Login/Register |
| `Components/Pages/Home.razor` | Seleccion de rol (candidato/empresa) |
| `Components/Pages/Login.razor` | Login con EditForm, validacion, Google button |
| `Components/Pages/Register.razor` | Registro con seleccion de rol, redirige a wizard |
| `Components/Pages/Wizard.razor` | 6 pasos: personal data, location, professional, skills, preferences, confirmation |
| `Components/Pages/Dashboard.razor` | Bento Grid: stats, wizard prompt, quick actions, recommended vacancies |
| `Components/Pages/Vacancies.razor` | Busqueda con filtros, paginacion, tarjetas de vacante |
| `Services/ApiAuthService.cs` | Cliente HTTP: login, register, refresh, candidate CRUD, vacancy CRUD |
| `Services/AppAuthStateProvider.cs` | AuthenticationStateProvider custom con JWT parsing |
| `Services/LanguageService.cs` | i18n: carga JSON, T(key), SetLanguageAsync, OnLanguageChanged |
| `Services/LocalStorageService.cs` | Wrapper de localStorage via JSInterop |

### 3.7 Frontend Assets (wwwroot)

```
wwwroot/
├── css/
│   ├── base.css              # Reset, variables globales, utilidades
│   ├── components.css        # Botones, inputs, cards, nav, wizard, vacancy cards, search
│   ├── bento-grid.css        # Grid layout + bento cards + stat cards
│   └── responsive.css        # Media queries (768px, 480px)
├── themes/
│   ├── navy/theme.css        # Tema default: azul marino, fondo claro
│   ├── dark/theme.css        # Tema oscuro: slate, fondo oscuro
│   └── light/theme.css       # Tema claro: blanco, acentos sky
├── js/
│   ├── theme-switcher.js     # Cambia tema dinamicamente, persiste en localStorage
│   └── language-switcher.js  # Cambia idioma, persiste en localStorage
└── config/language/
    ├── es/                   # Espanol: common, auth, wizard, dashboard, vacancies, profile, validation, errors
    └── en/                   # English: mismos 8 archivos
```

---

## 4. Base de Datos - Tablas y Relaciones

```
SC_Users (1) ──< (N) SC_UserRoles
SC_Users (1) ──< (N) SC_RefreshTokens
SC_Users (1) ──< (N) SC_UserDevices
SC_Users (1) ──< (1) PT_Candidates
SC_Users (1) ──< (1) PT_Companies
SC_Users (1) ──< (1) SY_UserPreferences
SC_Users (1) ──< (N) PT_TempVacancies

PT_Candidates (1) ──< (N) PT_CandidateSkills >── (1) PT_Skills

SY_WizardSteps (independiente, seed data)
```

### Prefijos de tablas

| Prefijo | Modulo | Tablas |
|---|---|---|
| `SC_` | Security | SC_Users, SC_UserRoles, SC_RefreshTokens, SC_UserDevices |
| `PT_` | Portal | PT_Candidates, PT_Companies, PT_TempVacancies, PT_Skills, PT_CandidateSkills |
| `SY_` | System | SY_WizardSteps, SY_UserPreferences |

### Campos de auditoria (en TODAS las tablas)

`Id` (GUID), `CreatedAt`, `CreatedBy`, `UpdatedAt`, `UpdatedBy`, `IsDeleted`, `DeletedAt`, `DeletedBy`

---

## 5. Flujo de Autenticacion

```
1. Register  ->  POST /api/auth/register  ->  Crea SC_User + SC_UserRole + SY_UserPreference + PT_Candidate
                                           ->  Retorna JWT + RefreshToken
2. Login     ->  POST /api/auth/login     ->  Verifica BCrypt, registra device, retorna JWT + RefreshToken
3. Refresh   ->  POST /api/auth/refresh   ->  Valida refresh token, revoca viejo, emite nuevo
4. Revoke    ->  POST /api/auth/revoke    ->  Revoca refresh token
5. Check     ->  GET /api/auth/check-device?deviceHash=X  ->  Retorna si dispositivo es conocido
```

### JWT Claims

- `sub`: User Id (GUID)
- `email`: User email
- `primaryRole`: int (0=Candidate, 1=Company, 2=Admin)
- `role`: uno por cada SC_UserRole

### Configuracion JWT (appsettings.json)

```json
"Jwt": {
    "Key": "OpenToWorkPortalSecretKey2026Min256Bits!!",
    "Issuer": "OpenToWork.Portal",
    "Audience": "OpenToWork.Portal",
    "ExpireMinutes": 60,
    "ExpireMinutesRememberMe": 43200,
    "RefreshTokenExpireDays": 7
}
```

---

## 6. Flujo del Wizard (6 pasos)

| Step | Nombre | Campos | Requerido |
|---|---|---|---|
| 1 | PersonalData | FirstName, LastName, Identification, Phone, BirthDate, Gender | Si |
| 2 | Location | Country, City, Address | Si |
| 3 | ProfessionalProfile | Title, Summary | Si |
| 4 | Skills | (pendiente implementar) | No |
| 5 | Preferences | PreferredRole | Si |
| 6 | Confirmation | Revision y confirmacion | Si |

- El wizard guarda el progreso en `PT_Candidates.WizardStep` (int) y `PT_Candidates.WizardCompleted` (bool)
- Si `WizardCompleted = false`, el Dashboard muestra un prompt para completar
- El wizard se puede reanudar desde el ultimo paso guardado

---

## 7. Endpoints API

### Auth

| Metodo | Ruta | Auth | Descripcion |
|---|---|---|---|
| POST | `/api/auth/register` | No | Registrar nuevo usuario |
| POST | `/api/auth/login` | No | Iniciar sesion |
| POST | `/api/auth/refresh` | No | Renovar token |
| POST | `/api/auth/revoke` | Si | Revocar refresh token |
| GET | `/api/auth/check-device` | Si | Verificar si dispositivo es conocido |

### Candidates

| Metodo | Ruta | Auth | Descripcion |
|---|---|---|---|
| GET | `/api/candidates/me` | Si | Obtener perfil del candidato |
| GET | `/api/candidates/wizard-status` | Si | Verificar si wizard completo |
| PUT | `/api/candidates/wizard` | Si | Actualizar paso del wizard |

### Vacancies

| Metodo | Ruta | Auth | Descripcion |
|---|---|---|---|
| POST | `/api/vacancies/temp` | Si | Crear vacante temporal |
| GET | `/api/vacancies/my` | Si | Vacantes del usuario |
| GET | `/api/vacancies/search` | No | Buscar vacantes publicadas |
| DELETE | `/api/vacancies/temp/{id}` | Si | Eliminar vacante (soft delete) |

---

## 8. i18n - Sistema de Idiomas

### Estructura

```
wwwroot/config/language/{lang}/{section}.json
```

### Secciones (8 archivos por idioma)

| Archivo | Claves |
|---|---|
| `common.json` | buttons, labels, nav, roles, gender, contract |
| `auth.json` | login.*, register.* |
| `wizard.json` | title, steps.*, buttons.*, complete.* |
| `dashboard.json` | title, welcome, stats.*, sections.*, actions.*, wizardPrompt.* |
| `vacancies.json` | title, searchPlaceholder, filters, noResults, results, salaryRange |
| `profile.json` | title, personalInfo, professionalInfo, skills, save, saved |
| `validation.json` | required, email, minLength, maxLength, passwordMismatch, phone |
| `errors.json` | generic, unauthorized, notFound, serverError |

### Uso en Blazor

```csharp
@inject LanguageService Lang
@Lang.T("common.buttons.save")
@Lang.T("auth.login.title")
```

### Agregar un nuevo idioma

1. Crear carpeta `wwwroot/config/language/{nuevo_idioma}/`
2. Copiar los 8 archivos JSON de `es/` y traducir
3. Agregar opcion en los selectores de idioma (MainLayout.razor, AuthLayout.razor)

---

## 9. Sistema de Temas

### Como funciona

1. `App.razor` carga `<link id="theme-stylesheet" href="/themes/navy/theme.css" />`
2. `theme-switcher.js` cambia el `href` del link dinamicamente
3. Cada tema define CSS variables (`--bg-primary`, `--text-primary`, `--accent-primary`, etc.)
4. Todos los componentes usan `var(--xxx)` en lugar de colores hardcoded

### Variables CSS principales

| Variable | Uso |
|---|---|
| `--bg-primary` | Fondo principal de la pagina |
| `--bg-secondary` | Fondo secundario |
| `--card-bg` | Fondo de tarjetas |
| `--text-primary` | Texto principal |
| `--text-secondary` | Texto secundario |
| `--accent-primary` | Color de acento (botones, links) |
| `--nav-bg` | Fondo de la barra de navegacion |
| `--input-bg` / `--input-border` / `--input-focus` | Inputs |
| `--success` / `--danger` / `--warning` | Estados |

### Agregar un tema

1. Crear `wwwroot/themes/{nombre}/theme.css`
2. Definir todas las variables CSS
3. Agregar opcion en `MainLayout.razor` y `AuthLayout.razor`

---

## 10. Que falta implementar (Roadmap)

### Fase 2 - Proximo

1. **Vacantes permanentes** - Entidad `PT_Vacancies` (no temporales, sin ExpiresAt)
2. **Solicitudes** - Entidad `PT_Applications` con estados (Pending, InReview, Accepted, Rejected)
3. **Perfil completo** - Entidades `PT_CandidateExperience`, `PT_CandidateEducation`
4. **Skills CRUD** - Implementar paso 4 del wizard con `PT_Skills` y `PT_CandidateSkills`
5. **Subida de archivos** - CV y foto de perfil
6. **Google OAuth** - Configuracion ya en appsettings, falta implementar flujo
7. **reCAPTCHA** - Configuracion ya en appsettings, falta implementar validacion
8. **Encriptacion localStorage** - AES-256 para tokens
9. **Recuperacion de contrasena** - Email + token de reset

### Fase 3

1. **AdminAPI** - JWT independiente, controllers de gestion
2. **AdminWEB** - Blazor admin con dashboard, gestion de usuarios, vacantes, solicitudes
3. **Metricas y estadisticas** - Dashboard admin con graficos
4. **Log de auditoria admin** - Tabla `AD_AuditLog`

---

## 11. Comandos Utiles

```bash
# Build completo
dotnet build OpenToWork.slnx

# Ejecutar API
dotnet run --project src/OpenToWork.API

# Ejecutar WEB
dotnet run --project src/OpenToWork.WEB

# Crear migracion
dotnet ef migrations add <Nombre> --project src/OpenToWork.Models --startup-project src/OpenToWork.Models

# Aplicar migracion
dotnet ef database update --project src/OpenToWork.Models --startup-project src/OpenToWork.Models

# Generar script SQL
dotnet ef migrations script --project src/OpenToWork.Models --startup-project src/OpenToWork.Models --output docs/Nombre.sql

# Remover ultima migracion (si no aplicada)
dotnet ef migrations remove --project src/OpenToWork.Models --startup-project src/OpenToWork.Models
```

---

## 12. Reglas y Convenciones para IA

1. **No usar texto hardcoded** en archivos `.razor`. Siempre `Lang.T("section.key")`.
2. **No usar `DELETE` fisico**. Siempre `IsDeleted = true` + `DeletedAt` + `DeletedBy`.
3. **Todas las tablas nuevas** deben heredar de `BaseEntity` e incluir campos de auditoria.
4. **Prefijos de tablas:** `SC_` (Security), `PT_` (Portal), `SY_` (System), `AD_` (Admin).
5. **Nombres en ingles** para tablas, columnas, entidades y propiedades.
6. **Componentes SharedUI** usan patron `.razor` (markup) + `.razor.cs` (codigo).
7. **WEB no referencia Core** directamente. Se comunica via HTTP a la API.
8. **Colores via CSS variables** (`var(--xxx)`), nunca hardcoded.
9. **Agregar nuevos idiomas** creando carpeta en `wwwroot/config/language/{lang}/` con 8 JSONs.
10. **Agregar nuevos temas** creando `wwwroot/themes/{nombre}/theme.css` con todas las variables.
