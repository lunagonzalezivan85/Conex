# OpenToWork

Plataforma de **evaluacion, validacion y scoring de talento** que funciona como la capa de confianza para decisiones de contratacion. Construida con **.NET 10, Blazor y MySQL**.

> Ver `docs/BUSINESS_PROPOSAL.md` para la propuesta de negocio completa.
> Ver `docs/PLAN_DE_PROYECTO.md` para el plan detallado de fases y 3 portales.

---

## Introduccion

OpenToWork no es una bolsa de empleo mas. Es una plataforma que genera **perfiles profesionales validados** con indices de confiabilidad, estabilidad y evidencia, permitiendo a las empresas identificar rapidamente a los candidatos mas confiables y mejor preparados.

El proyecto se compone de **3 portales independientes**:

| Portal | Descripcion | Estado |
|--------|-------------|--------|
| **Portal de Candidatos** | Registro, perfil, wizard, busqueda de vacantes, postulaciones | 80% Completado |
| **Portal Administrativo** | Verificaciones manuales, moderacion, gestion de usuarios, auditoria | 85% Completado |
| **Portal Corporativo** | Suscripcion mensual, perfiles evaluados, ranking, filtros avanzados | Pendiente |

### Caracteristicas principales

- **Autenticacion JWT** con registro, login, refresh tokens y device fingerprinting
- **Wizard de registro** multi-paso (10 pasos) para completar el perfil del candidato
- **Busqueda de vacantes** con filtros (texto, ubicacion, tipo de contrato, salario)
- **Dashboard con Bento Grid** estilo Samsung One UI
- **Sistema de temas** dinamicos (navy, dark, light) con CSS variables
- **Internacionalizacion (i18n)** con Espanol e Ingles, archivos JSON, sin texto hardcoded
- **Soft delete** en todas las tablas (auditoria completa: CreatedAt, UpdatedAt, IsDeleted, etc.)
- **Motor de evaluacion** con 4 indices: Estabilidad, Confiabilidad, Evidencia, Compatibilidad (Fase 3)
- **Sistema de verificaciones** con checkmarks: identidad, LinkedIn, experiencia, portafolio, referencias (Fase 3)

---

## Estructura del Proyecto

```
OpenToWork/
├── src/
│   ├── OpenToWork.API/          # API REST del portal de candidatos (puerto 5000)
│   ├── OpenToWork.AdminAPI/     # API REST del portal administrativo (puerto 5001)
│   ├── OpenToWork.CorporateAPI/ # API REST del portal corporativo (puerto 5002) [Fase 5]
│   ├── OpenToWork.WEB/          # Blazor Server del portal de candidatos (puerto 5100)
│   ├── OpenToWork.AdminWEB/     # Blazor Server del portal administrativo (puerto 5101)
│   ├── OpenToWork.CorporateWEB/ # Blazor Server del portal corporativo (puerto 5102) [Fase 5]
│   ├── OpenToWork.SharedUI/     # Razor Class Library con componentes compartidos
│   ├── OpenToWork.Core/         # Logica de negocio, scoring y validacion
│   ├── OpenToWork.Models/       # Entidades EF Core y AppDbContext
│   └── OpenToWork.Shared/       # DTOs, Enums y constantes
├── docs/                        # Documentacion completa del proyecto
└── OpenToWork.slnx              # Solucion (.slnx)
```

### Referencias entre proyectos

```
API / AdminAPI / CorporateAPI  ->  Core  ->  Models  ->  (EF Core, Pomelo MySQL)
                                 ->  Shared
WEB / AdminWEB / CorporateWEB   ->  SharedUI
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

- .NET 10 SDK
- MySQL 8.0+ corriendo en localhost:3306
- (Opcional) Visual Studio 2022 o VS Code
- Google OAuth credentials (opcional, para login con Google)
- reCAPTCHA keys (opcional, para proteccion anti-bot)

### 1. Clonar el repositorio

```bash
git clone https://github.com/lunagonzalezivan85/OpenToWork.git
cd OpenToWork
```

### 2. Base de datos

Crear la base de datos en MySQL:

```sql
CREATE DATABASE OpenToWorkDb CHARACTER SET utf8mb4;
```

Aplicar todas las migraciones con EF Core (incluye Fase 1 y Fase 2):

```bash
dotnet ef database update --project src/OpenToWork.Models --startup-project src/OpenToWork.Models
```

### 3. Configurar connection string y claves

Editar `src/OpenToWork.API/appsettings.json`:

```json
{
  "ConnectionStrings": {
    "DefaultConnection": "Server=localhost;Port=3306;Database=OpenToWorkDb;User=root;Password=TU_PASSWORD;CharSet=utf8mb4;"
  },
  "Jwt": {
    "Key": "TU_JWT_KEY_DE_AL_MENOS_32_CARACTERES",
    "Issuer": "OpenToWork.API",
    "Audience": "OpenToWork.WEB"
  },
  "Google": {
    "ClientId": "TU_GOOGLE_CLIENT_ID",
    "ClientSecret": "TU_GOOGLE_CLIENT_SECRET"
  },
  "Recaptcha": {
    "SiteKey": "TU_RECAPTCHA_SITE_KEY",
    "SecretKey": "TU_RECAPTCHA_SECRET_KEY"
  }
}
```

Editar `src/OpenToWork.WEB/appsettings.json`:

```json
{
  "ApiSettings": {
    "BaseUrl": "http://localhost:5000/"
  },
  "Security": {
    "AesKey": "TU_AES_KEY_PARA_ENCRYPTAR_TOKENS"
  },
  "Recaptcha": {
    "SiteKey": "TU_RECAPTCHA_SITE_KEY"
  }
}
```

### 4. Build

Compilar toda la solucion:

```bash
dotnet build OpenToWork.slnx
```

### 5. Ejecutar

Necesitas dos terminales abiertas:

**Terminal 1 - API (puerto 5000):**

```bash
dotnet run --project src/OpenToWork.API
```

- Swagger: `http://localhost:5000/swagger`
- Endpoints de auth: `http://localhost:5000/api/auth/*`
- Endpoints de vacantes: `http://localhost:5000/api/permanentvacancies/*`
- Endpoints de solicitudes: `http://localhost:5000/api/applications/*`
- Endpoints de perfil: `http://localhost:5000/api/profile/*`

**Terminal 2 - WEB Blazor Server (puerto 5100):**

```bash
dotnet run --project src/OpenToWork.WEB
```

- Portal principal: `http://localhost:5100`
- Login: `http://localhost:5100/login`
- Registro: `http://localhost:5100/register`
- Recuperar contrasena: `http://localhost:5100/forgot-password`
- Dashboard: `http://localhost:5100/dashboard`
- Vacantes: `http://localhost:5100/vacancies`
- Mis Vacantes: `http://localhost:5100/myvacancies`
- Perfil: `http://localhost:5100/profile`
- Wizard (10 pasos): `http://localhost:5100/wizard`

**Terminal 3 - AdminAPI (puerto 5001):**

```bash
dotnet run --project src/OpenToWork.AdminAPI
```

- Swagger: `http://localhost:5001/swagger`
- Login admin: `POST http://localhost:5001/api/admin/auth/login`
- Dashboard: `GET http://localhost:5001/api/admin/dashboard/metrics`
- Usuarios: `GET http://localhost:5001/api/admin/users`
- Vacantes: `GET http://localhost:5001/api/admin/vacancies`
- Skills: `GET http://localhost:5001/api/admin/skills`
- Auditoria: `GET http://localhost:5001/api/admin/audit-log`
- Export CSV: `GET http://localhost:5001/api/admin/export/users`

**Terminal 4 - AdminWEB Blazor Server (puerto 5101):**

```bash
dotnet run --project src/OpenToWork.AdminWEB
```

- Portal admin: `http://localhost:5101`
- Login: `http://localhost:5101/login`
- Dashboard: `http://localhost:5101/`
- Usuarios: `http://localhost:5101/users`
- Vacantes: `http://localhost:5101/vacancies`
- Skills: `http://localhost:5101/skills`
- Auditoria: `http://localhost:5101/audit-log`

**Credenciales de prueba (Portal Admin):**

| Campo | Valor |
|-------|-------|
| URL | `http://localhost:5101` |
| Email | `admin@opentowork.com` |
| Password | `Admin123!` |

> **Nota:** El usuario admin debe tener `PrimaryRole = 2` (Admin) en `SC_Users`. Para crearlo, registra un usuario via la API principal y luego actualiza el rol en MySQL:
> ```sql
> UPDATE SC_Users SET PrimaryRole = 2 WHERE Email = 'admin@opentowork.com';
> ```

### 6. Migraciones (solo si se modifican entidades)

Crear nueva migracion:

```bash
dotnet ef migrations add NombreMigracion --project src/OpenToWork.Models --startup-project src/OpenToWork.Models
```

Aplicar migracion:

```bash
dotnet ef database update --project src/OpenToWork.Models --startup-project src/OpenToWork.Models
```

### 7. Estructura de puertos

| Proyecto | Puerto | Descripcion |
|---|---|---|
| OpenToWork.API | 5000 | API REST del portal de candidatos |
| OpenToWork.WEB | 5100 | Blazor Server del portal de candidatos |
| OpenToWork.AdminAPI | 5001 | API REST del portal administrativo (Fase 4) |
| OpenToWork.AdminWEB | 5101 | Blazor Server del portal administrativo (Fase 4) |
| OpenToWork.CorporateAPI | 5002 | API REST del portal corporativo (Fase 5) |
| OpenToWork.CorporateWEB | 5102 | Blazor Server del portal corporativo (Fase 5) |

---

## Fases del Proyecto

> Ver `docs/PLAN_DE_PROYECTO.md` para el detalle completo de cada fase.

### Fase 1: Fundacion - COMPLETADA

- [x] Estructura de 8 proyectos creada
- [x] Entidades EF Core con prefijos (SC_, PT_, SY_) y auditoria
- [x] AppDbContext con configuracion MySQL
- [x] DTOs y Enums en Shared
- [x] Servicios de autenticacion (register, login, JWT, refresh, device fingerprinting)
- [x] Controllers de Auth, Candidates y Vacancies
- [x] Componentes SharedUI (BentoCard, OTButton, OTInput, Wizard, ThemeSwitcher, LanguageSwitcher)
- [x] Sistema de temas (navy, dark, light) con CSS variables
- [x] Sistema de i18n (es/en) con archivos JSON
- [x] Paginas: Home, Login, Register, Wizard, Dashboard, Vacancies
- [x] Migracion inicial aplicada a MySQL

### Fase 2: Portal de Candidatos - COMPLETADA

- [x] Vacantes permanentes (empresas)
- [x] Sistema de solicitudes (aplicar a vacantes)
- [x] Gestion de estados de solicitud (Pendiente, En revision, Aceptada, Rechazada)
- [x] Perfil completo del candidato (skills, experiencia, educacion, certificaciones)
- [x] Subida de CV (URL)
- [x] Login con Google OAuth
- [x] reCAPTCHA en login desde dispositivo desconocido
- [x] Encriptacion de datos de sesion en localStorage (AES-256)
- [x] Recuperacion de contrasena
- [x] Wizard pasos 7-10 (experiencia, educacion, certificaciones, CV)
- [x] Migracion Phase2 + Phase2Security aplicada
- [x] i18n completo (es + en) con claves nuevas
- [x] UI/UX: One UI, Bento Grid, Command-Driven, temas (navy/dark/light)

### Fase 3: Motor de Evaluacion y Scoring - Pendiente

- [ ] Entidades de scoring (`PTCandidateScore`, `PTVerification`, `PTCandidateReference`)
- [ ] ValidationService: verificacion automatica (LinkedIn, portafolio, coherencia cronologica)
- [ ] ScoringService: indices de Estabilidad, Confiabilidad, Evidencia
- [ ] CompatibilityService: match candidato-vacante
- [ ] API endpoints: `/api/candidates/{id}/score`, `/api/candidates/{id}/verifications`
- [ ] Dashboard candidato: mostrar scores y verificaciones en el perfil
- [ ] Referencias laborales: CRUD en wizard y perfil
- [ ] Pruebas de habilidades: `PTSkillTest`, `PTCandidateTestResult`

### Fase 4: Portal Administrativo - 85% COMPLETADA (por Dsiezar)

- [x] AdminAPI con JWT independiente (puerto 5001)
- [x] AdminWEB con login y layout (puerto 5101)
- [x] Gestion de usuarios (activar, desactivar, eliminar)
- [x] Moderacion de vacantes (permanentes + temporales)
- [x] Dashboard admin con metricas y estadisticas reales
- [x] Gestion de categorias y skills (CRUD)
- [x] Log de auditoria admin (`ADAuditLog`)
- [x] Exportacion de datos (CSV)
- [x] i18n admin (es/en)
- [x] QA+SEC: 6 bugs corregidos (enumeracion de cuentas, paginacion negativa, CSV injection, estado vacantes temporales, auto-bloqueo admin, clave i18n)
- [ ] Verificaciones manuales (aprobar/rechazar `PTVerification`) — **bloqueado por Fase 3**
- [ ] Revision de validaciones automaticas — **bloqueado por Fase 3**
- [ ] Gestion de roles de usuario (cambiar rol, no solo activar/desactivar)

**Deuda tecnica documentada (4 items):**
- [ ] Unificar `AdminAuthService` con `AuthService` (logica duplicada)
- [ ] Optimizar `AdminVacancyService` (carga tablas completas en memoria antes de paginar)
- [ ] Mover `LocalStorageService`/`LanguageService` de AdminWEB a SharedUI
- [ ] Centralizar guard de autenticacion en `AdminLayout` (copiado en 5 paginas)

### Fase 5: Portal Corporativo - Pendiente

- [ ] Crear proyecto `OpenToWork.CorporateAPI` (puerto 5002, JWT independiente)
- [ ] Crear proyecto `OpenToWork.CorporateWEB` (puerto 5102)
- [ ] Entidad `COCompany` — Name, Industry, Size, Website, LogoUrl
- [ ] Entidad `COSubscription` — CompanyId, Plan (Basic/Pro/Enterprise), Status, StartDate, EndDate, MonthlyFee
- [ ] Entidad `COSearchHistory` — CompanyId, Filters, ResultCount, SearchedAt
- [ ] Entidad `COCandidateView` — CompanyId, CandidateId, ScoreSnapshot, ViewedAt
- [ ] Registro de empresas + wizard de empresa
- [ ] Sistema de suscripciones (planes: Basic, Pro, Enterprise)
- [ ] Busqueda avanzada con filtros por score, confiabilidad, estabilidad
- [ ] Vista de perfiles evaluados con checkmarks de verificacion
- [ ] Ranking automatico de candidatos por compatibilidad
- [ ] Reportes avanzados
- [ ] Migracion EF Core para entidades corporativas

### Fase 6: Servicios Premium - Pendiente

- [ ] Verificacion manual de referencias (servicio premium para empresas)
- [ ] Evaluaciones especificas por industria
- [ ] Integraciones con sistemas de RRHH (API endpoints externos)
- [ ] Analytics avanzados de reclutamiento

### Fase 7: Integraciones Externas - Pendiente

- [ ] LinkedIn API (validacion real de perfiles)
- [ ] Pasarela de pagos (Stripe/PayPal para suscripciones)
- [ ] Notificaciones por email (SMTP)
- [ ] Notificaciones push

### Fase 8: Pruebas y Despliegue - Pendiente

- [ ] Pruebas unitarias (cobertura > 70% en Core)
- [ ] Pruebas de integracion (3 APIs)
- [ ] Documentacion final
- [ ] Despliegue en produccion

---

## Tareas Pendientes Resumidas

> **Total: ~45 tareas pendientes** | Prioridad: **Fase 3** (desbloquea verificaciones del portal admin)

| Fase | Tareas pendientes | Bloquea a |
|------|-------------------|-----------|
| **Fase 3** | 15 tareas (entidades, servicios, API, UI) | Fase 4 (verificaciones), Fase 5 (perfiles evaluados) |
| **Fase 4** | 3 tareas + 4 deuda tecnica | — |
| **Fase 5** | 13 tareas (proyecto nuevo, entidades, suscripciones, busqueda) | Fase 6 |
| **Fase 6** | 4 tareas (servicios premium) | — |
| **Fase 7** | 4 tareas (integraciones externas) | — |
| **Fase 8** | 4 tareas (pruebas, despliegue) | — |

**Bugs resueltos en main (fixes de Dsiezar mergeados):**
- [x] `#blazor-error-ui` siempre visible en `OpenToWork.WEB` — corregido con `display: none`
- [x] Google OAuth config en `OpenToWork.API` — corregido: lee `GoogleOAuth:ClientId` y solo registra si hay credenciales

---

## Ruta de Trabajo

### Fases independientes (pueden avanzar en paralelo)

Las siguientes fases **no tienen dependencias entre si** y pueden trabajarse simultaneamente por desarrolladores diferentes:

| Fase | Independiente de | Rama sugerida |
|------|------------------|---------------|
| **Fase 3** (Motor de Scoring) | No depende de ninguna otra fase | `iluna-fase-3` |
| **Fase 5** (Portal Corporativo) | Solo depende de Fase 3 para los scores, pero la estructura base (proyecto, JWT, layout, registro de empresas, suscripciones) se puede construir en paralelo | `dsiezar-fase-5` |
| **Fase 7** (Integraciones Externas) | LinkedIn API y pasarela de pagos son independientes del resto | cualquier rama |

### Fases con dependencias (secuenciales)

| Fase | Depende de | Motivo |
|------|------------|--------|
| **Fase 4** (completar 15%) | Fase 3 | Verificaciones manuales requieren `PTVerification` y `ValidationService` |
| **Fase 5** (busqueda por score) | Fase 3 | Filtros por score requieren `PTCandidateScore` |
| **Fase 6** (Servicios Premium) | Fase 5 | Servicios premium requieren portal corporativo funcional |
| **Fase 8** (Pruebas) | Fases 3-7 | Pruebas integrales requieren todo funcional |

### Orden recomendado de ejecucion

```
Fase 3 (Motor de Scoring) ──────────────────────────────────────┐
  │                                                              │
  ├── Fase 4 (completar verificaciones admin)                    │
  │                                                              │
  ├── Fase 5 (Portal Corporativo) ──── Fase 6 (Premium)          │
  │                                                              │
  └─────────────────────────────────────── Fase 7 (Integraciones)│
                                                                 │
  Fase 8 (Pruebas y Despliegue) ◄────────────────────────────────┘
```

### Plan de ejecucion detallado

1. **Fase 3 - Motor de Evaluacion y Scoring (PRIORIDAD MAXIMA):**
   - Crear entidades: `PTCandidateScore`, `PTVerification`, `PTCandidateReference`, `PTSkillTest`, `PTCandidateTestResult`
   - Migracion EF Core
   - Implementar `ValidationService` (verificacion automatica: LinkedIn, portafolio, coherencia cronologica)
   - Implementar `ScoringService` (indices: Estabilidad, Confiabilidad, Evidencia)
   - Implementar `CompatibilityService` (match candidato-vacante)
   - API endpoints: `/api/candidates/{id}/score`, `/api/candidates/{id}/verifications`
   - UI: scores y verificaciones en el perfil del candidato (bento cards con los 4 indices)
   - Referencias laborales: CRUD en wizard (nuevo paso) y perfil
   - Pruebas de habilidades: UI basica
   - i18n keys para scores, verificaciones, referencias (es/en)
   - **Validacion: ejecutar API + WEB, verificar pantallas funcionen correctamente antes de avanzar**
   - **Validacion: comprobar patron de diseno One UI (squircles, pill buttons, Bento Grid, temas)**

2. **Fase 4 - Portal Administrativo (completar 15% faltante):**
   - Verificaciones manuales (aprobar/rechazar `PTVerification`) — requiere Fase 3
   - Revision de validaciones automaticas — requiere Fase 3
   - Gestion de roles de usuario (cambiar rol, no solo activar/desactivar)
   - Resolver 4 items de deuda tecnica:
     - Unificar `AdminAuthService` con `AuthService`
     - Optimizar `AdminVacancyService` (paginacion en BD, no en memoria)
     - Mover `LocalStorageService`/`LanguageService` a SharedUI
     - Centralizar guard de autenticacion en `AdminLayout`
   - **Validacion: ejecutar AdminAPI + AdminWEB, verificar pantallas funcionen correctamente**
   - **Validacion: comprobar patron de diseno One UI consistente con portal principal**

3. **Fase 5 - Portal Corporativo (puede iniciar estructura base en paralelo con Fase 3):**
   - Crear `OpenToWork.CorporateAPI` (puerto 5002, JWT independiente)
   - Crear `OpenToWork.CorporateWEB` (puerto 5102)
   - Entidades: `COCompany`, `COSubscription`, `COSearchHistory`, `COCandidateView`
   - Registro de empresas + wizard de empresa
   - Sistema de suscripciones (planes: Basic, Pro, Enterprise)
   - Busqueda avanzada con filtros por score (requiere Fase 3 terminada)
   - Vista de perfiles evaluados con checkmarks
   - Ranking automatico de candidatos por compatibilidad
   - Reportes avanzados
   - **Validacion: ejecutar CorporateAPI + CorporateWEB, verificar pantallas funcionen**
   - **Validacion: comprobar patron de diseno One UI consistente**

4. **Fase 6 - Servicios Premium:**
   - Verificacion manual de referencias (premium)
   - Evaluaciones por industria
   - Integraciones RRHH

5. **Fase 7 - Integraciones Externas (independiente, puede avanzar en paralelo):**
   - LinkedIn API, pasarela de pagos, notificaciones

6. **Fase 8 - Pruebas y Despliegue:**
   - Cobertura > 70%, 3 APIs, despliegue produccion

### Criterios de validacion por fase (obligatorios antes de avanzar)

Antes de marcar cualquier fase como completada, se debe validar:

1. **Build sin errores:** `dotnet build OpenToWork.slnx` -> 0 errores
2. **API funcional:** ejecutar la API correspondiente y verificar endpoints con datos reales (no mocks)
3. **WEB funcional:** ejecutar el frontend correspondiente y verificar pantallas en navegador
4. **Patron de diseno:** comprobar que la UI cumple con One UI (squircles `border-radius: 20px`, pill buttons `border-radius: 9999px`, Bento Grid, temas navy/dark/light, espaciado consistente)
5. **i18n:** sin texto hardcoded, todas las claves existen en es/en
6. **Responsive:** verificar en tablet (1024px), mobile (768px) y small mobile (480px)
7. **Sin regresiones:** las fases anteriores siguen funcionando

---

## Notas de Actualizacion

> **Regla obligatoria:** Todo desarrollador debe agregar sus notas de cambios en esta seccion cada vez que haga un commit en `main`. El formato es: fecha, nombre del desarrollador, fase, resumen de cambios. Esto mantiene a ambos enterados del progreso sin necesidad de revisar commits uno por uno.

### Estado actual del proyecto

- **Fase 1 (Fundacion):** COMPLETADA
- **Fase 2 (Portal de Candidatos):** 80% completada (Iluna) — funcional pero pendiente de pulido UI/UX y validacion de pantallas
- **Fase 3 (Motor de Evaluacion y Scoring):** Pendiente — **PRIORIDAD MAXIMA**, es el corazon de la propuesta de negocio
- **Fase 4 (Portal Administrativo):** 85% completada (Dsiezar) — faltan verificaciones manuales (bloqueadas por Fase 3), gestion de roles y 4 items de deuda tecnica
- **Fase 5 (Portal Corporativo):** Pendiente — la estructura base puede iniciar en paralelo con Fase 3
- **Fases 6-8:** Pendientes

### Indicaciones para continuar

1. **Culminar las fases pendientes en orden de prioridad.** Fase 3 primero, despues completar Fase 4, luego Fase 5.
2. **No avanzar a la siguiente fase hasta validar que las pantallas funcionen correctamente.** Ejecutar API + WEB y verificar en navegador con datos reales.
3. **Validar que se cumpla el patron de diseno solicitado** (Samsung One UI: squircles, pill buttons, Bento Grid, temas consistentes, espaciado uniforme).
4. **Mejorar todo lo que sea posible para verse mas profesional.** Cada fase debe entregar una UI pulida, no solo funcional.
5. **La Fase 5 (Portal Corporativo) puede avanzar en paralelo con la Fase 3** en su estructura base (proyecto, JWT, layout, registro de empresas, suscripciones). La busqueda por score si requiere que Fase 3 este terminada.
6. **La Fase 7 (Integraciones Externas) es independiente** y puede avanzar en paralelo con cualquier otra fase.

### Fases que pueden trabajarse en paralelo

| Desarrollador | Fase | Rama | Independiente de |
|---------------|------|------|------------------|
| Desarrollador A | Fase 3 (Motor de Scoring) | `iluna-fase-3` | Sin dependencias |
| Desarrollador B | Fase 5 (estructura base Portal Corporativo) | `dsiezar-fase-5` | Solo depende de Fase 3 para busqueda por score |
| Cualquiera | Fase 7 (Integraciones Externas) | rama dedicada | LinkedIn API y pagos son independientes |

### Bitacora de cambios en main

| Fecha | Desarrollador | Fase | Cambios |
|------|---------------|------|--------|
| 2026-08-12 | Iluna | Fase 2 | Rediseno UI/UX Home: hero navy, capsule search bar, pill badges, Bento Grid role cards, footer corporativo, cinta de vacantes destacadas |
| 2026-08-12 | Iluna | Fase 2 | Fix CSS loading: middleware order en Program.cs (UseStaticFiles antes de UseHttpsRedirection) |
| 2026-08-12 | Iluna | Fase 2 | AuthLayout: unificar nav-brand con logo OTW + texto OpenToWork |
| 2026-08-12 | Iluna | Fase 2 | Register: segmented control pill toggle (One UI) reemplazando role cards pesadas |
| 2026-08-12 | Iluna | Docs | BUSINESS_PROPOSAL.md: propuesta de negocio completa |
| 2026-08-12 | Iluna | Docs | PLAN_DE_PROYECTO.md v2.0: 3 portales, 8 fases, entidades nuevas |
| 2026-08-12 | Iluna | Docs | README: alineado con 3 portales y propuesta de negocio |
| 2026-08-13 | Dsiezar | Fase 4 | AdminAPI: JWT independiente, login admin, auditoria, controllers (users, vacancies, skills, dashboard, export) |
| 2026-08-13 | Dsiezar | Fase 4 | AdminWEB: login, layout, dashboard, pages (users, vacancies, skills, audit-log) |
| 2026-08-13 | Dsiezar | Fase 4 | QA+SEC: 6 bugs corregidos (enumeracion cuentas, paginacion, CSV injection, vacantes temporales, auto-bloqueo, i18n) |
| 2026-08-13 | Dsiezar | Fase 4 | Fix fuera de alcance: Google OAuth config en API, #blazor-error-ui en WEB |
| 2026-08-14 | Iluna | Docs | README: notas de actualizacion, ruta de trabajo, fases paralelas, criterios de validacion |
| 2026-08-14 | Iluna | Docs | DEPLOYMENT.md: guia de despliegue a Windows Server/IIS (Web Deploy, PSRemoting, GitHub Actions) |
| 2026-08-14 | Iluna | Docs | README: instrucciones de ejecucion AdminAPI + AdminWEB, credenciales de prueba admin |

---

## Documentacion

| Documento | Descripcion |
|---|---|
| `docs/BUSINESS_PROPOSAL.md` | Propuesta de negocio y producto - plataforma de evaluacion de talento |
| `docs/PLAN_DE_PROYECTO.md` | Plan de proyecto con 3 portales y 8 fases |
| `docs/PRD.md` | Product Requirements Document - requisitos del producto |
| `docs/TRN.md` | Technical Requirements Note - requisitos tecnicos |
| `docs/APPFLOW.md` | Diagramas de flujo de la aplicacion |
| `docs/IMPLEMENTACION.md` | Guia de implementacion paso a paso |
| `docs/DATABASE_DESIGN.md` | Diseno completo de la base de datos |
| `docs/DESIGN_SYSTEM.md` | Sistema de diseno (UI/UX, temas, componentes) |
| `docs/NEURAL_MAP.md` | Mapa neuronal del proyecto para IA |
| `docs/DEPLOYMENT.md` | Guia de despliegue a Windows Server / IIS (Web Deploy, PSRemoting, CI/CD) |
| `docs/OpenToWork_InitialCreate.sql` | Script SQL inicial de la base de datos |

---

## Convenciones

- **Tablas:** Prefijos `SC_` (Security), `PT_` (Portal), `SY_` (System), `AD_` (Admin), `CO_` (Corporate), `VR_` (Verification)
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
