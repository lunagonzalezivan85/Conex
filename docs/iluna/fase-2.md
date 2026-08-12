# Fase 2: Vacantes Permanentes, Solicitudes y Perfil Completo

**IA:** Iluna
**Rol:** PM (Project Manager)
**Fecha inicio:** 2026-08-12
**Fecha fin:** Pendiente
**Estado:** En planificacion (Etapa 1)

---

## Progreso Visual de Fase 2

### Etapas del Workflow

```
[0] Identidad     [x] Completada
[1] Planificacion [x] Completada
[2] Diseno Tec.   [ ] Pendiente
[3] Implementacion[ ] Pendiente
[4] Pruebas (QA)  [ ] Pendiente
[5] Auditoria(SEC)[ ] Pendiente
[6] Correcciones  [ ] Pendiente
[7] Cierre        [ ] Pendiente
```

**Progreso general:** `[██░░░░░░░░░░░░░░░░░░] 10%`

### Modulos de Implementacion

| # | Modulo | Estado | Progreso |
|---|---|---|---|
| 1 | Enums nuevos (7) | Pendiente | `[░░░░░] 0%` |
| 2 | Campos nuevos en entidades existentes | Pendiente | `[░░░░░] 0%` |
| 3 | Entidad PTVacancy + PTVacancySkill | Pendiente | `[░░░░░] 0%` |
| 4 | Entidad PTApplication | Pendiente | `[░░░░░] 0%` |
| 5 | Entidad PTCandidateExperience | Pendiente | `[░░░░░] 0%` |
| 6 | Entidad PTCandidateEducation | Pendiente | `[░░░░░] 0%` |
| 7 | Entidad PTCandidateCertification | Pendiente | `[░░░░░] 0%` |
| 8 | AppDbContext + Migracion | Pendiente | `[░░░░░] 0%` |
| 9 | DTOs (Vacancy, Application, Experience, Education, Certification) | Pendiente | `[░░░░░] 0%` |
| 10 | Services (Vacancy, Application, Profile) | Pendiente | `[░░░░░] 0%` |
| 11 | Controllers (Vacancies, Applications, Profile) | Pendiente | `[░░░░░] 0%` |
| 12 | Frontend: MyVacancies + VacancyDetail | Pendiente | `[░░░░░] 0%` |
| 13 | Frontend: Applications + MyApplications | Pendiente | `[░░░░░] 0%` |
| 14 | Frontend: Profile (tabs: exp, edu, cert) | Pendiente | `[░░░░░] 0%` |
| 15 | Frontend: Wizard steps 7-10 + Upload CV | Pendiente | `[░░░░░] 0%` |
| 16 | Google OAuth | Pendiente | `[░░░░░] 0%` |
| 17 | reCAPTCHA en login | Pendiente | `[░░░░░] 0%` |
| 18 | Encriptacion localStorage (AES-256) | Pendiente | `[░░░░░] 0%` |
| 19 | Recuperacion de contrasena | Pendiente | `[░░░░░] 0%` |
| 20 | Convertir vacante temporal a permanente | Pendiente | `[░░░░░] 0%` |
| 21 | i18n (es + en) - todas las claves nuevas | Pendiente | `[░░░░░] 0%` |
| 22 | Build sin errores | Pendiente | `[░░░░░] 0%` |

### Checklist de Aprobacion

```
[ ] Build sin errores (dotnet build)
[ ] Migracion creada y aplicada
[ ] i18n completo (es + en)
[ ] QA: 0 bugs criticos
[ ] SEC: 0 hallazgos criticos
[ ] PM: Cierre aprobado
```

---

## Resumen

Fase 2 expande la plataforma con vacantes permanentes, sistema de solicitudes, perfil completo del candidato (experiencia, educacion, certificaciones), subida de CV, y mejoras de seguridad. Se valida escalabilidad de campos existentes y se agregan nuevos.

---

## Analisis de Campos Actuales - Validacion y Mejoras

### 1. PT_Candidates - Campos Faltantes para Escalabilidad

La entidad actual tiene campos basicos del wizard pero le faltan campos criticos para un perfil profesional completo y buscable.

| Campo nuevo | Tipo | Null | Descripcion |
|---|---|---|---|
| `YearsOfExperience` | INT | YES | Anios de experiencia profesional |
| `LinkedInUrl` | VARCHAR(500) | YES | URL de perfil LinkedIn |
| `PortfolioUrl` | VARCHAR(500) | YES | URL de portafolio/web personal |
| `Availability` | INT | YES | Enum: Immediate=0, TwoWeeks=1, OneMonth=2, NotAvailable=3 |
| `WorkAuthorization` | INT | YES | Enum: Citizen=0, WorkVisa=1, StudentVisa=2, None=3 |
| `IsProfilePublic` | TINYINT(1) | NO | Si el perfil es buscable por empresas (default 1) |
| `CompletedAt` | DATETIME(6) | YES | Fecha de finalizacion del wizard |

**Justificacion:** Sin estos campos, las empresas no pueden filtrar candidatos por experiencia, disponibilidad o autorizacion de trabajo. `IsProfilePublic` es critical para privacidad. `LinkedInUrl` y `PortfolioUrl` son estandar en cualquier plataforma de empleo.

---

### 2. PT_Companies - Campos Faltantes para Escalabilidad

La entidad actual tiene datos basicos pero le faltan campos para validar y categorizar empresas.

| Campo nuevo | Tipo | Null | Descripcion |
|---|---|---|---|
| `Industry` | VARCHAR(100) | YES | Industria/sector (ej: "Technology", "Finance") |
| `CompanySize` | INT | YES | Enum: Startup=0, Small=1, Medium=2, Large=3, Enterprise=4 |
| `ContactEmail` | VARCHAR(256) | YES | Email de contacto (separado del user) |
| `ContactPhone` | VARCHAR(20) | YES | Telefono de contacto |
| `LinkedInUrl` | VARCHAR(500) | YES | URL de LinkedIn de la empresa |
| `IsVerified` | TINYINT(1) | NO | Si la empresa esta verificada (default 0) |

**Justificacion:** `Industry` y `CompanySize` permiten filtrar y categorizar. `IsVerified` es necesario para que los candidatos confien en las vacantes. `ContactEmail` separa el contacto publico del email de login.

---

### 3. PT_TempVacancies - Campos Faltantes para Escalabilidad

La vacante temporal actual tiene campos basicos pero le faltan campos para busqueda avanzada.

| Campo nuevo | Tipo | Null | Descripcion |
|---|---|---|---|
| `Category` | VARCHAR(100) | YES | Categoria/area (ej: "Software", "Marketing") |
| `ExperienceLevel` | INT | YES | Enum: Entry=0, Junior=1, Mid=2, Senior=3, Lead=4 |
| `EnglishLevel` | INT | YES | Enum: None=0, Basic=1, Intermediate=2, Advanced=3, Native=4 |
| `WorkMode` | INT | NO | Enum: OnSite=0, Hybrid=1, Remote=2 (default 0) |

**Justificacion:** `WorkMode` es diferente de `ContractType` (tipo de contrato vs modalidad de trabajo). `ExperienceLevel` y `EnglishLevel` son filtros estandar. `Category` permite agrupar vacantes.

---

### 4. Nuevas Entidades para Fase 2

#### 4.1 PTVacancy (Vacantes Permanentes)

Ya diseniada en `DATABASE_DESIGN.md` seccion 3.6. Se agrega campos de escalabilidad:

| Campo | Tipo | Null | Descripcion |
|---|---|---|---|
| Id | CHAR(36) | NO | PK |
| PT_CompanyId | CHAR(36) | NO | FK -> PT_Companies |
| Title | VARCHAR(200) | NO | Titulo |
| Description | LONGTEXT | YES | Descripcion |
| Requirements | LONGTEXT | YES | Requisitos |
| SalaryMin | DECIMAL(18,2) | YES | Salario min |
| SalaryMax | DECIMAL(18,2) | YES | Salario max |
| Location | VARCHAR(200) | YES | Ubicacion |
| ContractType | INT | NO | Enum ContractType |
| WorkMode | INT | NO | Enum WorkMode (nuevo) |
| Category | VARCHAR(100) | YES | Categoria/area |
| ExperienceLevel | INT | YES | Enum ExperienceLevel (nuevo) |
| EnglishLevel | INT | YES | Enum EnglishLevel (nuevo) |
| Status | INT | NO | Enum VacancyStatus: Draft=0, Active=1, Closed=2 |
| PublishedAt | DATETIME(6) | YES | Fecha de publicacion |
| ClosedAt | DATETIME(6) | YES | Fecha de cierre |
| ViewsCount | INT | NO | Contador de vistas (default 0) |
| + campos de auditoria | | | |

**Indices:**
- `IX_PT_Vacancies_CompanyId` (PT_CompanyId, IsDeleted)
- `IX_PT_Vacancies_Status` (Status, IsDeleted)
- `IX_PT_Vacancies_Location` (Location, Status, IsDeleted)
- `IX_PT_Vacancies_Category` (Category, Status, IsDeleted)
- `IX_PT_Vacancies_WorkMode` (WorkMode, Status, IsDeleted)

#### 4.2 PTApplication (Solicitudes)

Ya diseniada en `DATABASE_DESIGN.md` seccion 3.7. Se agrega campos de escalabilidad:

| Campo | Tipo | Null | Descripcion |
|---|---|---|---|
| Id | CHAR(36) | NO | PK |
| PT_CandidateId | CHAR(36) | NO | FK -> PT_Candidates |
| PT_VacancyId | CHAR(36) | NO | FK -> PT_Vacancies |
| Status | INT | NO | Enum ApplicationStatus |
| CoverLetter | LONGTEXT | YES | Carta de presentacion |
| ExpectedSalary | DECIMAL(18,2) | YES | Salario esperado |
| AvailableFromDate | DATE | YES | Fecha disponible para iniciar |
| ApplicationSource | INT | NO | Enum: Direct=0, Search=1, Recommended=2 |
| + campos de auditoria | | | |

**Indices:**
- `UQ_PT_Applications_CandidateId_VacancyId` (PT_CandidateId, PT_VacancyId, IsDeleted) UNIQUE
- `IX_PT_Applications_VacancyId` (PT_VacancyId, Status, IsDeleted)
- `IX_PT_Applications_CandidateId` (PT_CandidateId, Status, IsDeleted)

#### 4.3 PTCandidateExperience (Experiencia Laboral)

NO existe en DATABASE_DESIGN.md. Nueva entidad para el perfil completo.

| Campo | Tipo | Null | Descripcion |
|---|---|---|---|
| Id | CHAR(36) | NO | PK |
| PT_CandidateId | CHAR(36) | NO | FK -> PT_Candidates |
| CompanyName | VARCHAR(200) | NO | Nombre de la empresa |
| JobTitle | VARCHAR(200) | NO | Cargo/posicion |
| Description | LONGTEXT | YES | Descripcion del rol |
| StartDate | DATE | NO | Fecha de inicio |
| EndDate | DATE | YES | Fecha fin (NULL = actual) |
| IsCurrentJob | TINYINT(1) | NO | Si es el trabajo actual (default 0) |
| Location | VARCHAR(200) | YES | Ubicacion |
| + campos de auditoria | | | |

**Indices:**
- `IX_PT_CandidateExperience_CandidateId` (PT_CandidateId, IsDeleted)
- `IX_PT_CandidateExperience_CompanyName` (CompanyName, IsDeleted)

#### 4.4 PTCandidateEducation (Educacion)

NO existe en DATABASE_DESIGN.md. Nueva entidad.

| Campo | Tipo | Null | Descripcion |
|---|---|---|---|
| Id | CHAR(36) | NO | PK |
| PT_CandidateId | CHAR(36) | NO | FK -> PT_Candidates |
| Institution | VARCHAR(200) | NO | Institucion educativa |
| Degree | VARCHAR(200) | NO | Titulo obtenido |
| FieldOfStudy | VARCHAR(200) | YES | Area de estudio |
| StartDate | DATE | YES | Fecha de inicio |
| EndDate | DATE | YES | Fecha fin (NULL = en curso) |
| IsInProgress | TINYINT(1) | NO | Si sigue en curso (default 0) |
| + campos de auditoria | | | |

**Indices:**
- `IX_PT_CandidateEducation_CandidateId` (PT_CandidateId, IsDeleted)

#### 4.5 PTCandidateCertification (Certificaciones)

NO existe en DATABASE_DESIGN.md. Nueva entidad.

| Campo | Tipo | Null | Descripcion |
|---|---|---|---|
| Id | CHAR(36) | NO | PK |
| PT_CandidateId | CHAR(36) | NO | FK -> PT_Candidates |
| Name | VARCHAR(200) | NO | Nombre de la certificacion |
| Issuer | VARCHAR(200) | YES | Entidad emisora |
| IssueDate | DATE | YES | Fecha de emision |
| ExpiryDate | DATE | YES | Fecha de expiracion (NULL = no expira) |
| CredentialId | VARCHAR(200) | YES | ID de credencial |
| CredentialUrl | VARCHAR(500) | YES | URL de verificacion |
| + campos de auditoria | | | |

**Indices:**
- `IX_PT_CandidateCertification_CandidateId` (PT_CandidateId, IsDeleted)

#### 4.6 PTVacancySkill (Skills requeridos por vacante)

NO existe en DATABASE_DESIGN.md. Nueva entidad para relacionar vacantes con skills.

| Campo | Tipo | Null | Descripcion |
|---|---|---|---|
| Id | CHAR(36) | NO | PK |
| PT_VacancyId | CHAR(36) | NO | FK -> PT_Vacancies |
| PT_SkillId | CHAR(36) | NO | FK -> PT_Skills |
| IsRequired | TINYINT(1) | NO | Si es obligatorio (default 1) |
| MinProficiencyLevel | INT | YES | Nivel minimo requerido |
| + campos de auditoria | | | |

**Indices:**
- `UQ_PT_VacancySkills_VacancyId_SkillId` (PT_VacancyId, PT_SkillId, IsDeleted) UNIQUE
- `IX_PT_VacancySkills_SkillId` (PT_SkillId, IsDeleted)

---

### 5. Nuevos Enums para Fase 2

| Enum | Valores |
|---|---|
| `Availability` | Immediate=0, TwoWeeks=1, OneMonth=2, NotAvailable=3 |
| `WorkAuthorization` | Citizen=0, WorkVisa=1, StudentVisa=2, None=3 |
| `CompanySize` | Startup=0, Small=1, Medium=2, Large=3, Enterprise=4 |
| `ExperienceLevel` | Entry=0, Junior=1, Mid=2, Senior=3, Lead=4 |
| `EnglishLevel` | None=0, Basic=1, Intermediate=2, Advanced=3, Native=4 |
| `WorkMode` | OnSite=0, Hybrid=1, Remote=2 |
| `ApplicationSource` | Direct=0, Search=1, Recommended=2 |

---

### 6. Wizard Steps Fase 2 (Seed Data)

Agregar a `SY_WizardSteps`:

| Step | Name | Title | Required | Order | Phase |
|---|---|---|---|---|---|
| 7 | WorkExperience | Work Experience | NO | 7 | 2 |
| 8 | Education | Education | NO | 8 | 2 |
| 9 | Certifications | Certifications | NO | 9 | 2 |
| 10 | UploadCV | Upload CV | NO | 10 | 2 |

---

## Asignacion: Iluna toma TODA la Fase 2

Iluna se encarga de todo el trabajo de Fase 2 (backend + frontend + seguridad). Dsiezar puede participar en fases futuras.

### Sprint 1: Backend - Entidades y Migracion

- [ ] Enums nuevos: WorkMode, ExperienceLevel, EnglishLevel, Availability, WorkAuthorization, CompanySize, ApplicationSource
- [ ] Campos nuevos en PT_Candidates (7 campos)
- [ ] Campos nuevos en PT_Companies (6 campos)
- [ ] Campos nuevos en PT_TempVacancies (4 campos)
- [ ] Entidad PTVacancy + configuracion EF
- [ ] Entidad PTApplication + configuracion EF
- [ ] Entidad PTCandidateExperience + configuracion EF
- [ ] Entidad PTCandidateEducation + configuracion EF
- [ ] Entidad PTCandidateCertification + configuracion EF
- [ ] Entidad PTVacancySkill + configuracion EF
- [ ] AppDbContext: agregar DbSets, indices, relaciones
- [ ] Seed wizard steps 7-10
- [ ] Migracion creada y aplicada

### Sprint 2: Backend - DTOs, Services y Controllers

- [ ] DTOs: VacancyDto, CreateVacancyDto, UpdateVacancyDto
- [ ] DTOs: ApplicationDto, CreateApplicationDto, UpdateApplicationStatusDto
- [ ] DTOs: ExperienceDto, EducationDto, CertificationDto, UpdateCandidateProfileDto
- [ ] IVacancyService + VacancyService (CRUD permanentes, busqueda avanzada)
- [ ] IApplicationService + ApplicationService (aplicar, ver, cambiar estado)
- [ ] IProfileService + ProfileService (experience, education, certifications CRUD)
- [ ] VacanciesController (permanent vacancies endpoints)
- [ ] ApplicationsController (applications endpoints)
- [ ] ProfileController (candidate profile endpoints)
- [ ] Convertir vacante temporal a permanente (endpoint + service)

### Sprint 3: Frontend - Vacantes y Solicitudes

- [ ] Pagina: MyVacancies.razor (lista de vacantes de la empresa)
- [ ] Pagina: VacancyDetail.razor (detalle + aplicar)
- [ ] Pagina: Applications.razor (solicitudes recibidas por empresa)
- [ ] Pagina: MyApplications.razor (solicitudes enviadas por candidato)
- [ ] CSS: vacancy-detail, application-card

### Sprint 4: Frontend - Perfil Completo y Wizard

- [ ] Pagina: Profile.razor (tabs: personal, experience, education, certifications)
- [ ] Componente: ExperienceForm.razor
- [ ] Componente: EducationForm.razor
- [ ] Componente: CertificationForm.razor
- [ ] Wizard steps 7-10 UI
- [ ] Subida de CV (file upload)

### Sprint 5: Seguridad

- [ ] Google OAuth (login alternativo)
- [ ] reCAPTCHA en login desde dispositivo nuevo
- [ ] Encriptacion localStorage (AES-256 para tokens)
- [ ] Recuperacion de contrasena (email + token reset)

### Sprint 6: i18n y Build

- [ ] Claves nuevas en vacancies.json (es + en)
- [ ] Claves nuevas en applications.json (es + en) (nuevo archivo)
- [ ] Claves nuevas en profile.json (es + en)
- [ ] Claves nuevas en wizard.json (es + en)
- [ ] Build sin errores: `dotnet build OpenToWork.slnx`

---

## Criterios de Aceptacion de Fase 2

- [ ] Empresa puede crear, editar, publicar y cerrar vacantes permanentes
- [ ] Candidato puede buscar vacantes permanentes con filtros (categoria, ubicacion, work mode, experiencia, ingles)
- [ ] Candidato puede aplicar a una vacante con carta de presentacion
- [ ] Empresa puede ver solicitudes recibidas y cambiar estado (Pending, InReview, Accepted, Rejected)
- [ ] Candidato puede ver sus solicitudes enviadas y su estado
- [ ] Candidato puede agregar experiencia laboral, educacion y certificaciones
- [ ] Candidato puede subir su CV
- [ ] Wizard tiene pasos 7-10 (opcionales)
- [ ] Perfil de candidato es completo y buscable
- [ ] Build sin errores
- [ ] Migracion creada y aplicada
- [ ] i18n completo en es y en
- [ ] Layouts siguen DESIGN_UI_UX_SCHEME.md
- [ ] QA aprueba (0 bugs criticos)
- [ ] SEC aprueba (0 hallazgos criticos)

---

## Etapa 1: Planificacion - Estado

- [x] Analisis de campos actuales y escalabilidad
- [x] Diseno de nuevas entidades
- [x] Nuevos enums definidos
- [x] Asignacion: Iluna toma toda la Fase 2
- [x] Criterios de aceptacion definidos
- [x] Sprints definidos (6 sprints)
- [x] Aprobacion del usuario recibida
- [x] Listo para Etapa 2 (Diseno Tecnico)
