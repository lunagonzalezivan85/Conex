# Fase 3: Portal Admin (AdminAPI + AdminWEB)

**IA:** Dsiezar
**Rol:** PM (Project Manager)
**Fecha inicio:** 2026-08-12
**Fecha fin:** Pendiente
**Estado:** En planificacion (Etapa 1)

---

## Resumen

Fase 3 construye el portal de administracion completo: `OpenToWork.AdminAPI` y `OpenToWork.AdminWEB`. Permite gestionar usuarios, moderar vacantes y solicitudes, ver metricas del sistema, administrar categorias/skills, exportar datos y auditar acciones administrativas.

**Nota de secuencia:** Segun `.agents/WORKFLOW.md`, la Fase 3 no puede entrar en Etapa 3 (Implementacion) ni cerrarse hasta que la Fase 2 (Iluna, en `iluna-fase-2`) este en Etapa 7 (Cierre, aprobada por PM+QA+SEC). Esta planificacion avanza en paralelo sin bloquear a Iluna.

---

## Etapa 1: Planificacion (PM)

**Alcance (segun `README.md` seccion "Fases del Proyecto" y "Como debe continuar el proyecto"):**

- [ ] `OpenToWork.AdminAPI` con JWT independiente del portal principal
- [ ] Gestion de usuarios: activar, desactivar, eliminar (soft delete)
- [ ] Moderacion de vacantes (temporales y permanentes)
- [ ] Dashboard con metricas y estadisticas (usuarios, vacantes, solicitudes)
- [ ] Gestion de categorias y skills (CRUD)
- [ ] Exportacion de datos (CSV/Excel)
- [ ] Log de auditoria admin (quien hizo que accion y cuando)
- [ ] `OpenToWork.AdminWEB` en Blazor con las paginas correspondientes

**Dependencias con Fase 2 (bloqueantes para ciertas tareas):**

| Tarea de Fase 3 | Depende de |
|---|---|
| Moderacion de vacantes permanentes | `PTVacancy` (Iluna, Fase 2) |
| Moderacion/vista de solicitudes | `PTApplication` (Iluna, Fase 2) |
| Metricas de perfiles completos | `PTCandidateExperience/Education/Certification` (Dsiezar, Fase 2 - aun no iniciada) |

**Tareas sin dependencia (se pueden empezar ya):**
- Estructura base de `OpenToWork.AdminAPI` (JWT independiente, Program.cs, appsettings)
- Gestion de usuarios (`SC_Users` ya existe desde Fase 1)
- Gestion de categorias y skills (`PT_Skills` ya existe desde Fase 1)
- Estructura base de `OpenToWork.AdminWEB` (layout, auth, theme/i18n reutilizando `SharedUI`)
- Log de auditoria admin (tabla nueva `AD_AuditLog`)

**Criterios de aceptacion (borrador, sujeto a validacion PM+QA+SEC):**
- [ ] Admin puede loguearse con JWT independiente del portal principal
- [ ] Admin puede activar/desactivar/eliminar usuarios
- [ ] Admin puede moderar (aprobar/rechazar/cerrar) vacantes
- [ ] Dashboard muestra metricas basicas (total usuarios, vacantes activas, solicitudes por estado)
- [ ] Admin puede crear/editar/eliminar categorias y skills
- [ ] Admin puede exportar listado de usuarios/vacantes a CSV
- [ ] Toda accion administrativa queda registrada en `AD_AuditLog`
- [ ] Build sin errores, i18n completo (es/en), QA y SEC aprueban

**Riesgos identificados:**
- Moderacion de vacantes/solicitudes no se puede implementar ni probar end-to-end hasta que Iluna cierre Fase 2.
- Se puede mitigar construyendo esas pantallas contra datos mock o adelantando solo la base del AdminAPI/AdminWEB mientras tanto.

- [x] Aprobacion para pasar a Etapa 2 (Diseno Tecnico)

---

## Etapa 2: Diseno Tecnico (PM + FS)

**Contexto:** Al momento de escribir esto, `main` ya trae mergeado gran parte de Fase 2 (Iluna): `PTVacancy`, `PTApplication`, `PTCandidateExperience/Education/Certification`, `ProfileController`, `PermanentVacanciesController`, `ApplicationsController`, 2 migraciones (`Phase2`, `Phase2Security`). Esto **desbloquea** varias tareas de Fase 3 que antes dependian de Fase 2 (moderacion de vacantes permanentes y solicitudes, metricas de perfiles).

`OpenToWork.AdminAPI` y `OpenToWork.AdminWEB` siguen siendo scaffolding puro (sin JWT, sin DbContext, sin controllers reales; `AdminWEB` todavia con paginas de plantilla `Counter.razor`/`Weather.razor` y Bootstrap). Todo el diseño de abajo parte de cero en ambos proyectos.

### Entidades nuevas/modificadas

- `AD_AuditLog` (nueva) - segun `docs/DATABASE_DESIGN.md` seccion 5.1:
  - `Id`, `SC_UserId` (FK admin que actua), `Action` (VARCHAR 100, ej. "DeactivateUser"), `EntityType` (VARCHAR 100), `EntityId` (nullable), `ChangesJson` (LONGTEXT, before/after), `IpAddress` (VARCHAR 45), + auditoria estandar (`BaseEntity`)
  - Indices: `(SC_UserId, IsDeleted)`, `(EntityType, EntityId, IsDeleted)`, `(CreatedAt, IsDeleted)`
- No se tocan entidades existentes de `PT_`/`SC_`; el AdminAPI las consulta a traves de `AppDbContext` compartido con el portal principal, solo agrega `AD_AuditLog`.

### Endpoints nuevos (`OpenToWork.AdminAPI`)

| Controller | Endpoints | Descripcion |
|---|---|---|
| `AdminAuthController` | `POST /api/admin/auth/login` | Login exclusivo admin (`SC_Users` con `PrimaryRole = Admin`), JWT propio (key/issuer/audience distintos al portal principal) |
| `UsersController` | `GET /api/admin/users`, `GET /api/admin/users/{id}`, `PUT /api/admin/users/{id}/activate`, `PUT /api/admin/users/{id}/deactivate`, `DELETE /api/admin/users/{id}` (soft delete) | Gestion de usuarios |
| `VacanciesController` (admin) | `GET /api/admin/vacancies`, `PUT /api/admin/vacancies/{id}/moderate` (aprobar/rechazar/cerrar) | Moderacion de `PT_Vacancies` y `PT_TempVacancies` |
| `ApplicationsController` (admin) | `GET /api/admin/applications` | Vista de solicitudes (solo lectura, sin cambiar estado - eso es del candidato/empresa) |
| `SkillsController` (admin) | `GET/POST/PUT/DELETE /api/admin/skills` | CRUD de `PT_Skills` (categorias/skills) |
| `DashboardController` | `GET /api/admin/dashboard/metrics` | Conteos: usuarios activos, candidatos, empresas, vacantes por estado, solicitudes por estado |
| `AuditLogController` | `GET /api/admin/audit-log` | Consulta de `AD_AuditLog` (solo lectura, paginado, filtros por usuario/entidad/fecha) |
| `ExportController` | `GET /api/admin/export/users`, `GET /api/admin/export/vacancies` | Exportacion CSV |

Cada accion de escritura (activar/desactivar/eliminar/moderar) debe registrar una fila en `AD_AuditLog` (Action, EntityType, EntityId, ChangesJson, IpAddress).

### Componentes/paginas nuevas (`OpenToWork.AdminWEB`)

- `Components/Pages/AdminLogin.razor` - login independiente
- `Components/Layout/AdminLayout.razor` - reemplaza el layout de plantilla, reutiliza estilos/temas de `SharedUI` (no Bootstrap)
- `Components/Pages/Dashboard.razor` - metricas con `BentoCard`
- `Components/Pages/Users.razor` - tabla de usuarios con activar/desactivar/eliminar
- `Components/Pages/VacanciesModeration.razor` - moderar vacantes
- `Components/Pages/ApplicationsView.razor` - ver solicitudes
- `Components/Pages/Skills.razor` - CRUD de skills/categorias
- `Components/Pages/AuditLog.razor` - tabla de auditoria con filtros
- Eliminar paginas de plantilla: `Counter.razor`, `Weather.razor`

### Configuracion base pendiente (antes de las paginas)

- `AdminAPI/Program.cs`: JWT Bearer (config propia), `AppDbContext` (misma BD MySQL), CORS, DI de servicios admin, Swagger ya existe
- `AdminAPI/appsettings.json`: agregar `ConnectionStrings`, `Jwt` (key/issuer/audience **distintos** a `OpenToWork.API`, ej. issuer `OpenToWork.Admin`)
- `AdminWEB/Program.cs`: DI de `HttpClient` hacia `AdminAPI` (puerto 5001), `AuthStateProvider` propio, quitar referencias a Bootstrap, agregar referencia a `SharedUI` y temas
- `AdminWEB/wwwroot`: quitar carpeta `lib/bootstrap`, cargar CSS de `SharedUI`/temas del portal principal

### Migraciones

- `AdminAuditLog` - agrega solo `AD_AuditLog`. Se genera **despues** de que Iluna cierre su migracion `Phase2Security` (ya esta en `main`), asi que no hay conflicto de migracion concurrente.

### Riesgo re-evaluado

Con el merge de `main`, la unica dependencia real que queda es de **datos**, no de disenio: `ApplicationsController` (admin, solo lectura) puede implementarse ya contra `PT_Applications`, que ya existe. Sin bloqueos pendientes para iniciar Etapa 3.

- [x] Aprobacion PM + FS para pasar a Etapa 3 (Implementacion)

---

## Etapa 3: Implementacion (FS)

**Alcance implementado en esta iteracion:** base de `OpenToWork.AdminAPI` (JWT independiente + login admin + auditoria). Quedan pendientes para la siguiente iteracion: `UsersController`, `VacanciesController`, `SkillsController`, `DashboardController`, `ExportController` y todo `OpenToWork.AdminWEB`.

**Archivos creados:**

| Archivo | Descripcion |
|---|---|
| `src/OpenToWork.Models/Entities/ADAuditLog.cs` | Entidad `AD_AuditLog` |
| `src/OpenToWork.Core/Interfaces/IAdminAuthService.cs` | Contrato login admin |
| `src/OpenToWork.Core/Interfaces/IAuditLogService.cs` | Contrato registro/consulta de auditoria |
| `src/OpenToWork.Core/Services/AdminAuthService.cs` | Login restringido a `PrimaryRole = Admin`, JWT firmado con config propia de `AdminAPI` |
| `src/OpenToWork.Core/Services/AuditLogService.cs` | `LogAsync` (escribe fila) + `GetLogsAsync` (paginado) |
| `src/OpenToWork.Shared/DTOs/AuditLogDto.cs` | DTO de auditoria |
| `src/OpenToWork.AdminAPI/Controllers/AdminAuthController.cs` | `POST /api/admin/auth/login` |
| `src/OpenToWork.AdminAPI/Controllers/AuditLogController.cs` | `GET /api/admin/audit-log` (solo rol Admin) |
| `src/OpenToWork.Models/Migrations/20260812214205_AdminAuditLog.cs` | Migracion: crea tabla `AD_AuditLogs` |

**Archivos modificados:**

| Archivo | Cambio |
|---|---|
| `src/OpenToWork.Models/Context/AppDbContext.cs` | `DbSet<ADAuditLog>` + configuracion de tabla/indices |
| `src/OpenToWork.Core/Extensions/ServiceCollectionExtensions.cs` | Nuevo `AddAdminCoreServices()` (no reutiliza `AddCoreServices()` para no exponer servicios del portal principal en el AdminAPI) |
| `src/OpenToWork.AdminAPI/Program.cs` | JWT Bearer con config propia, `AppDbContext`, CORS hacia `AdminWEB`, Swagger con Bearer |
| `src/OpenToWork.AdminAPI/appsettings.json` | `ConnectionStrings` + `Jwt` (issuer/audience/key **distintos** a `OpenToWork.API`, expiracion mas corta: 60 min / refresh 1 dia) |
| `src/OpenToWork.AdminAPI/Properties/launchSettings.json` | Puerto alineado al README: 5001 (antes 5087, scaffolding por defecto) |
| `src/OpenToWork.AdminWEB/Properties/launchSettings.json` | Puerto alineado al README: 5101 (antes 5018) |

**Claves i18n agregadas:** ninguna (sin UI todavia).

**Build:** `dotnet build OpenToWork.slnx` -> 0 errores, 20 advertencias (preexistentes: AutoMapper/Caching.Memory NU1903, paquetes Blazor NU1510/NU1603 - ninguna introducida por estos cambios).

**Migracion:** `AdminAuditLog` creada. No aplicada a MySQL (no hay servidor MySQL disponible en este entorno de desarrollo).

**Nota de entorno:** este equipo no tenia el SDK de .NET instalado (solo runtimes) ni fuente de NuGet configurada. Se instalo `Microsoft.DotNet.SDK.10` via `winget` y se agrego el source `nuget.org` para poder compilar y generar la migracion.

---

## Resumen de Cambios

Etapa 1, Etapa 2 y primera entrega de Etapa 3 de Fase 3. Se implemento la base de `OpenToWork.AdminAPI`: entidad y migracion de `AD_AuditLog`, JWT independiente del portal principal, login exclusivo para administradores, y servicio de auditoria. Build verificado sin errores. Pendiente: resto de controllers admin (usuarios, vacantes, skills, dashboard, export) y todo `OpenToWork.AdminWEB`.
