# OpenToWork

Plataforma de empleo que conecta candidatos con empresas. Construida con **.NET 8, Blazor y MySQL**.

---

## Introduccion

OpenToWork es una plataforma de empleo con dos roles principales: **Buscar Empleo** (candidatos) y **Contratar** (empresas/reclutadores). El sistema permite registrar candidatos, completar perfiles mediante un wizard multi-paso, buscar vacantes y publicar ofertas de trabajo.

### Caracteristicas principales

- **Autenticacion JWT** con registro, login, refresh tokens y device fingerprinting
- **Wizard de registro** multi-paso (6 pasos) para completar el perfil del candidato
- **Busqueda de vacantes** con filtros (texto, ubicacion, tipo de contrato, salario)
- **Dashboard con Bento Grid** estilo Samsung One UI
- **Sistema de temas** dinamicos (navy, dark, light) con CSS variables
- **Internacionalizacion (i18n)** con Espanol e Ingles, archivos JSON, sin texto hardcoded
- **Soft delete** en todas las tablas (auditoria completa: CreatedAt, UpdatedAt, IsDeleted, etc.)

---

## Estructura del Proyecto

```
OpenToWork/
├── src/
│   ├── OpenToWork.API/          # API REST del portal principal (puerto 5000)
│   ├── OpenToWork.AdminAPI/     # API REST del portal admin (puerto 5001)
│   ├── OpenToWork.WEB/          # Blazor Server del portal principal (puerto 5100)
│   ├── OpenToWork.AdminWEB/     # Blazor Server del portal admin
│   ├── OpenToWork.SharedUI/     # Razor Class Library con componentes compartidos
│   ├── OpenToWork.Core/         # Logica de negocio y servicios
│   ├── OpenToWork.Models/       # Entidades EF Core y AppDbContext
│   └── OpenToWork.Shared/       # DTOs, Enums y constantes
├── docs/                        # Documentacion completa del proyecto
└── OpenToWork.slnx              # Solucion (.slnx)
```

### Referencias entre proyectos

```
API / AdminAPI  ->  Core  ->  Models  ->  (EF Core, Pomelo MySQL)
                 ->  Shared
WEB / AdminWEB  ->  SharedUI
                 ->  Core (via API HTTP)
                 ->  Shared
```

---

## Stack Tecnologico

| Componente | Tecnologia |
|---|---|
| Backend | C# .NET 8, ASP.NET Core Web API |
| Frontend | Blazor Server |
| ORM | Entity Framework Core 8 + Pomelo MySQL |
| Base de datos | MySQL 8.0+ |
| Autenticacion | JWT Bearer + Refresh Tokens |
| UI | CSS puro con variables, Bento Grid, sin Bootstrap |
| i18n | JSON files + LanguageService |

---

## Como ejecutar

### Requisitos previos

- .NET 8 SDK
- MySQL 8.0+ corriendo en localhost:3306
- (Opcional) Visual Studio 2022 o VS Code

### 1. Base de datos

```sql
CREATE DATABASE OpenToWorkDb CHARACTER SET utf8mb4;
```

Luego ejecuta el script SQL:

```bash
mysql -u root OpenToWorkDb < docs/OpenToWork_InitialCreate.sql
```

O aplica la migracion con EF Core:

```bash
dotnet ef database update --project src/OpenToWork.Models --startup-project src/OpenToWork.Models
```

### 2. Configurar connection string

Edita `src/OpenToWork.API/appsettings.json`:

```json
"ConnectionStrings": {
    "DefaultConnection": "Server=localhost;Port=3306;Database=OpenToWorkDb;User=root;Password=TU_PASSWORD;CharSet=utf8mb4;"
}
```

### 3. Ejecutar

```bash
# API (puerto 5000)
dotnet run --project src/OpenToWork.API

# WEB (puerto 5100)
dotnet run --project src/OpenToWork.WEB
```

Swagger disponible en: `http://localhost:5000/swagger`

---

## Fases del Proyecto

### Fase 1 - COMPLETADA

- [x] Estructura de 8 proyectos creada
- [x] Entidades EF Core con prefijos (SC_, PT_, SY_) y auditoria
- [x] AppDbContext con configuracion MySQL
- [x] DTOs y Enums en Shared
- [x] Servicios de autenticacion (register, login, JWT, refresh, device fingerprinting)
- [x] Controllers de Auth, Candidates y Vacancies
- [x] Componentes SharedUI (BentoCard, OTButton, OTInput, Wizard, ThemeSwitcher, LanguageSwitcher)
- [x] Sistema de temas (navy, dark, light) con CSS variables
- [x] Sistema de i18n (es/en) con archivos JSON
- [x] Paginas: Home, Login, Register, Wizard (6 pasos), Dashboard, Vacancies
- [x] Migracion inicial aplicada a MySQL
- [x] Script SQL exportado en `docs/OpenToWork_InitialCreate.sql`

### Fase 2 - Pendiente

- [ ] Vacantes permanentes (empresas)
- [ ] Sistema de solicitudes (aplicar a vacantes)
- [ ] Gestion de estados de solicitud (Pendiente, En revision, Aceptada, Rechazada)
- [ ] Perfil completo del candidato (skills, experiencia, educacion)
- [ ] Subida de CV y foto de perfil
- [ ] Login con Google OAuth
- [ ] reCAPTCHA en login desde dispositivo desconocido
- [ ] Encriptacion de datos de sesion en localStorage (AES-256)
- [ ] Recuperacion de contrasena

### Fase 3 - Pendiente

- [ ] Portal Admin completo (AdminAPI + AdminWEB)
- [ ] Gestion de usuarios (activar, desactivar, eliminar)
- [ ] Moderacion de vacantes
- [ ] Dashboard con metricas y estadisticas
- [ ] Gestion de categorias y skills
- [ ] Exportacion de datos
- [ ] Log de auditoria admin

---

## Como debe continuar el proyecto

1. **Fase 2 - Vacantes permanentes y solicitudes:**
   - Crear entidades `PT_Vacancies` (permanent) y `PT_Applications`
   - Implementar `VacancyService` para vacantes permanentes
   - Crear `ApplicationsController` y flujo de aplicacion
   - Agregar pagina de "Mis Solicitudes" en WEB

2. **Fase 2 - Perfil completo del candidato:**
   - Crear entidades `PT_CandidateExperience`, `PT_CandidateEducation`
   - Implementar CRUD de experiencia y educacion
   - Subida de archivos (CV, foto) con almacenamiento

3. **Fase 2 - Seguridad avanzada:**
   - Integrar Google OAuth (`GoogleOAuth` en appsettings ya configurado)
   - Implementar reCAPTCHA (`Recaptcha` en appsettings ya configurado)
   - Encriptar tokens en localStorage con AES-256

4. **Fase 3 - Portal Admin:**
   - Configurar `OpenToWork.AdminAPI` con JWT independiente
   - Crear controllers de gestion (Users, Vacancies, Applications, Categories)
   - Construir `OpenToWork.AdminWEB` con Blazor
   - Dashboard admin con metricas

---

## Documentacion

| Documento | Descripcion |
|---|---|
| `docs/PRD.md` | Product Requirements Document - requisitos del producto |
| `docs/TRN.md` | Technical Requirements Note - requisitos tecnicos |
| `docs/APPFLOW.md` | Diagramas de flujo de la aplicacion |
| `docs/IMPLEMENTACION.md` | Guia de implementacion paso a paso |
| `docs/DATABASE_DESIGN.md` | Diseno completo de la base de datos |
| `docs/DESIGN_SYSTEM.md` | Sistema de diseno (UI/UX, temas, componentes) |
| `docs/NEURAL_MAP.md` | Mapa neuronal del proyecto para IA |
| `docs/OpenToWork_InitialCreate.sql` | Script SQL inicial de la base de datos |

---

## Convenciones

- **Tablas:** Prefijos `SC_` (Security), `PT_` (Portal), `SY_` (System), `AD_` (Admin)
- **Auditoria:** Todas las tablas tienen `CreatedAt`, `CreatedBy`, `UpdatedAt`, `UpdatedBy`, `IsDeleted`, `DeletedAt`, `DeletedBy`
- **Soft delete:** No se usa `DELETE` fisico, solo `IsDeleted = true`
- **i18n:** Prohibido texto hardcoded en `.razor`. Usar `Lang.T("section.key")`
- **Nombres:** Tablas y columnas en ingles

---

## NOTA

> **Importante para cualquier IA o desarrollador que trabaje en este proyecto.**

### 1. Usa los agentes predefinidos

Este proyecto tiene 4 agentes definidos en `.agents/`. Antes de empezar a trabajar, revisa y sigue el rol que te corresponda:

| Agente | Archivo | Rol |
|---|---|---|
| **PM** | `.agents/pm.md` | Administrador del proyecto, controla el flujo |
| **QA** | `.agents/qa.md` | Tester: valida diseno, funcionalidad, calidad |
| **FS** | `.agents/fs.md` | Full Stack: implementa frontend y backend |
| **SEC** | `.agents/sec.md` | Seguridad: audita vulnerabilidades |

Lee `.agents/WORKFLOW.md` para entender el flujo de 8 etapas (0 a 7) por fase.

### 2. Declara tu identidad

Al iniciar una sesion, declara quien eres: **"Soy Iluna"** o **"Soy Dsiezar"**. Documenta todos tus cambios en:

- `docs/iluna/fase-N.md` si eres Iluna
- `docs/dsiezar/fase-N.md` si eres Dsiezar

Usa `docs/iluna/PLANTILLA.md` o `docs/dsiezar/PLANTILLA.md` como base.

### 3. Documenta la fase en la que estas

Siempre documenta en tu bitacora que fase estas trabajando, en que etapa del flujo estas, y que cambios realizaste. Sin documentacion, el trabajo no cuenta como completado.

### 4. Revisa dependencias antes de empezar

Antes de iniciar una fase, **revisa si esa fase depende de la otra persona**. Si tu fase necesita algo que la otra persona aun no ha terminado, comunicalo para no atascarte.

- **Minimiza dependencias** entre tu fase y la del otro.
- Si puedes trabajar de forma independiente, mejor.
- Si hay una dependencia critica, acuerden un punto de integracion antes de empezar.

### 5. Cada fase es una rama

**Nunca trabajes directamente en `main`.** Cada fase tiene su propia rama con el formato:

```
{ia}-{fase}
```

Ejemplos:
- `iluna-fase-2` - Iluna trabajando en Fase 2
- `dsiezar-fase-2` - Dsiezar trabajando en Fase 2

Solo se hace merge a `main` cuando la fase esta 100% completada y aprobada por PM, QA y SEC.

Ver `docs/GIT_BRANCHES.md` para mas detalles.

### 6. Tratemos de no usar muchas dependencias entre fases

Si ambos estan trabajando en paralelo, cada uno debe poder avanzar sin bloquear al otro. Diseñen las tareas de manera que las dependencias cruzadas sean minimas. Si una dependencia es inevitable, definan un contrato (interface, DTO, endpoint) antes de empezar para que ambos puedan trabajar contra el contrato.
