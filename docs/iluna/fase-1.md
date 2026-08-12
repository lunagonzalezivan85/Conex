# Fase 1: Funcionalidades Core

**IA:** Iluna
**Rol:** FS (Full Stack Developer)
**Fecha inicio:** 2026-08-11
**Fecha fin:** 2026-08-11
**Estado:** Completada

---

## Resumen

Implementacion completa de la Fase 1 del proyecto OpenToWork: autenticacion JWT, wizard de candidato multi-paso, busqueda de vacantes temporales, dashboard con Bento Grid, sistema de temas e i18n.

---

## Etapa 3: Implementacion (FS)

### Archivos creados

| Archivo | Descripcion |
|---|---|
| `src/OpenToWork.Shared/Enums/UserRole.cs` | Enum: Candidate=0, Company=1, Admin=2 |
| `src/OpenToWork.Shared/Enums/Gender.cs` | Enum: Male, Female, Other, PreferNotToSay |
| `src/OpenToWork.Shared/Enums/ContractType.cs` | Enum: FullTime, PartTime, Contract, Remote |
| `src/OpenToWork.Shared/Enums/VacancyStatus.cs` | Enum: Draft, Active, Closed |
| `src/OpenToWork.Shared/Enums/ApplicationStatus.cs` | Enum: Pending, InReview, Accepted, Rejected |
| `src/OpenToWork.Shared/Enums/ProficiencyLevel.cs` | Enum: Beginner, Intermediate, Advanced, Expert |
| `src/OpenToWork.Shared/DTOs/LoginDto.cs` | DTO de login |
| `src/OpenToWork.Shared/DTOs/RegisterDto.cs` | DTO de registro |
| `src/OpenToWork.Shared/DTOs/AuthResponseDto.cs` | Respuesta de auth con UserDto |
| `src/OpenToWork.Shared/DTOs/RefreshTokenDto.cs` | DTO de refresh token |
| `src/OpenToWork.Shared/DTOs/CandidateDto.cs` | DTO de candidato + UpdateCandidateWizardDto |
| `src/OpenToWork.Shared/DTOs/TempVacancyDto.cs` | DTOs de vacantes temporales |
| `src/OpenToWork.Models/Entities/BaseEntity.cs` | Clase base con auditoria |
| `src/OpenToWork.Models/Entities/SCUser.cs` | Entidad usuario |
| `src/OpenToWork.Models/Entities/SCUserRole.cs` | Rol multi-usuario |
| `src/OpenToWork.Models/Entities/SCRefreshToken.cs` | Refresh token |
| `src/OpenToWork.Models/Entities/SCUserDevice.cs` | Dispositivo de usuario |
| `src/OpenToWork.Models/Entities/PTCandidate.cs` | Candidato con wizard |
| `src/OpenToWork.Models/Entities/PTCompany.cs` | Empresa |
| `src/OpenToWork.Models/Entities/PTTempVacancy.cs` | Vacante temporal |
| `src/OpenToWork.Models/Entities/PTSkill.cs` | Skill |
| `src/OpenToWork.Models/Entities/PTCandidateSkill.cs` | Relacion candidato-skill |
| `src/OpenToWork.Models/Entities/SYWizardStep.cs` | Step del wizard |
| `src/OpenToWork.Models/Entities/SYUserPreference.cs` | Preferencias de usuario |
| `src/OpenToWork.Models/Context/AppDbContext.cs` | DbContext con 11 DbSets |
| `src/OpenToWork.Models/Design/AppDbContextDesignTimeFactory.cs` | Factory para migraciones |
| `src/OpenToWork.Core/Interfaces/IAuthService.cs` | Contrato auth |
| `src/OpenToWork.Core/Interfaces/ICandidateService.cs` | Contrato candidato |
| `src/OpenToWork.Core/Interfaces/IVacancyService.cs` | Contrato vacantes |
| `src/OpenToWork.Core/Services/AuthService.cs` | Auth: BCrypt, JWT, refresh, device |
| `src/OpenToWork.Core/Services/CandidateService.cs` | CRUD candidato, wizard |
| `src/OpenToWork.Core/Services/VacancyService.cs` | CRUD vacantes, busqueda |
| `src/OpenToWork.Core/Extensions/ServiceCollectionExtensions.cs` | DI extensions |
| `src/OpenToWork.API/Controllers/AuthController.cs` | Endpoints de auth |
| `src/OpenToWork.API/Controllers/CandidatesController.cs` | Endpoints de candidato |
| `src/OpenToWork.API/Controllers/VacanciesController.cs` | Endpoints de vacantes |
| `src/OpenToWork.SharedUI/Components/BentoCard.razor` + `.razor.cs` | Tarjeta Bento Grid |
| `src/OpenToWork.SharedUI/Components/OTButton.razor` + `.razor.cs` | Boton con variantes |
| `src/OpenToWork.SharedUI/Components/OTInput.razor` + `.razor.cs` | Input con label |
| `src/OpenToWork.SharedUI/Components/Wizard.razor` + `.razor.cs` | Wizard multi-paso |
| `src/OpenToWork.SharedUI/Components/ThemeSwitcher.razor` + `.razor.cs` | Selector de tema |
| `src/OpenToWork.SharedUI/Components/LanguageSwitcher.razor` + `.razor.cs` | Selector de idioma |
| `src/OpenToWork.WEB/Services/ApiAuthService.cs` | Cliente HTTP API |
| `src/OpenToWork.WEB/Services/AppAuthStateProvider.cs` | AuthStateProvider JWT |
| `src/OpenToWork.WEB/Services/LanguageService.cs` | Servicio i18n |
| `src/OpenToWork.WEB/Services/LocalStorageService.cs` | Wrapper localStorage |
| `src/OpenToWork.WEB/Components/Layout/AuthLayout.razor` | Layout auth |
| `src/OpenToWork.WEB/Components/Pages/Login.razor` | Pagina login |
| `src/OpenToWork.WEB/Components/Pages/Register.razor` | Pagina registro |
| `src/OpenToWork.WEB/Components/Pages/Wizard.razor` | Wizard 6 pasos |
| `src/OpenToWork.WEB/Components/Pages/Dashboard.razor` | Dashboard Bento Grid |
| `src/OpenToWork.WEB/Components/Pages/Vacancies.razor` | Busqueda de vacantes |
| `src/OpenToWork.WEB/wwwroot/css/base.css` | Reset + variables globales |
| `src/OpenToWork.WEB/wwwroot/css/components.css` | Componentes CSS |
| `src/OpenToWork.WEB/wwwroot/css/bento-grid.css` | Grid layout |
| `src/OpenToWork.WEB/wwwroot/css/responsive.css` | Media queries |
| `src/OpenToWork.WEB/wwwroot/themes/navy/theme.css` | Tema navy |
| `src/OpenToWork.WEB/wwwroot/themes/dark/theme.css` | Tema dark |
| `src/OpenToWork.WEB/wwwroot/themes/light/theme.css` | Tema light |
| `src/OpenToWork.WEB/wwwroot/js/theme-switcher.js` | JS cambio tema |
| `src/OpenToWork.WEB/wwwroot/js/language-switcher.js` | JS cambio idioma |
| `src/OpenToWork.WEB/wwwroot/config/language/es/*.json` | 8 archivos i18n espanol |
| `src/OpenToWork.WEB/wwwroot/config/language/en/*.json` | 8 archivos i18n ingles |
| `docs/OpenToWork_InitialCreate.sql` | Script SQL inicial |
| `docs/NEURAL_MAP.md` | Mapa neuronal para IA |
| `.agents/pm.md` | Agente PM |
| `.agents/qa.md` | Agente QA |
| `.agents/fs.md` | Agente FS |
| `.agents/sec.md` | Agente SEC |
| `.agents/WORKFLOW.md` | Flujo de trabajo por fase |

### Archivos modificados

| Archivo | Cambio |
|---|---|
| `src/OpenToWork.API/Program.cs` | JWT, DI, Swagger, CORS, controllers |
| `src/OpenToWork.API/appsettings.json` | Connection string, JWT, Google OAuth, reCAPTCHA |
| `src/OpenToWork.AdminAPI/Program.cs` | Controllers + Swagger basico |
| `src/OpenToWork.WEB/Program.cs` | DI servicios, HttpClient, auth |
| `src/OpenToWork.WEB/appsettings.json` | ApiSettings.BaseUrl |
| `src/OpenToWork.WEB/Components/App.razor` | CSS variables, temas, JS |
| `src/OpenToWork.WEB/Components/Layout/MainLayout.razor` | Nav bar, theme/lang switchers |
| `src/OpenToWork.WEB/Components/Pages/Home.razor` | Seleccion de rol |

### Migraciones

- `20260812030531_InitialCreate` - Esquema completo de Fase 1 (11 tablas)
- Aplicada a MySQL: Si
- Script SQL: `docs/OpenToWork_InitialCreate.sql`

### Build

`dotnet build OpenToWork.slnx` -> 0 errores, 20 advertencias (NU1903 AutoMapper, Caching.Memory)

---

## Etapa 7: Cierre

**Aprobaciones:**
- [x] Build sin errores
- [x] Migracion aplicada
- [x] Script SQL generado
- [ ] QA aprobado (pendiente)
- [ ] SEC aprobado (pendiente)

**Nota:** Fase 1 implementada por completo. Pendiente validacion de QA y SEC.
