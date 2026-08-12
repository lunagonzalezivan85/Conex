# Database Design

## OpenToWork - Diseño de Base de Datos

**Versión:** 1.0  
**Fecha:** Agosto 2026  
**Motor:** MySQL 8.0+ (migrable a SQL Server)  
**Provider:** Pomelo.EntityFrameworkCore.MySql  

---

## 1. Convenciones Generales

### 1.1 Prefijos de Tablas

Cada tabla tiene un prefijo que indica su módulo funcional:

| Prefijo | Módulo | Descripción |
|---------|--------|-------------|
| `SC_` | Security | Autenticación, autorización, tokens, dispositivos, sesiones |
| `PT_` | Portal | Candidatos, empresas, vacantes, solicitudes, skills, wizard |
| `AD_` | Admin | Gestión administrativa, dashboard, moderación |
| `SY_` | System | Configuración del sistema, preferencias, parámetros |

### 1.2 Nomenclatura

| Convención | Descripción |
|------------|-------------|
| **Idioma** | Todos los nombres de tablas y campos en inglés |
| **Naming** | PascalCase para nombres de tablas y campos |
| **PK** | Siempre `Id` (CHAR(36) / GUID) |
| **FK** | `{TableName}Id` (ej: `SC_UserId`) |
| **Booleanos** | Prefijo `Is` o `Has` (ej: `IsActive`, `HasResume`) |
| **Fechas** | Sufijo `At` para timestamps (ej: `CreatedAt`, `ExpiresAt`) |
| **Enums** | INT con mapeo a enum en C# |

### 1.3 Campos de Auditoría (Obligatorios en TODAS las tablas)

Toda tabla debe incluir los siguientes campos de auditoría:

| Campo | Tipo MySQL | Descripción |
|-------|-----------|-------------|
| `CreatedAt` | DATETIME(6) | Fecha y hora de creación del registro |
| `CreatedBy` | CHAR(36) | ID del usuario que creó el registro (nullable para seeds del sistema) |
| `UpdatedAt` | DATETIME(6) | Fecha y hora de última actualización |
| `UpdatedBy` | CHAR(36) | ID del usuario que actualizó el registro (nullable) |
| `IsDeleted` | TINYINT(1) | Soft delete: 0 = activo, 1 = eliminado (default 0) |
| `DeletedAt` | DATETIME(6) | Fecha y hora de eliminación (nullable, solo si IsDeleted = 1) |
| `DeletedBy` | CHAR(36) | ID del usuario que eliminó el registro (nullable) |

> **Soft Delete:** Ninguna tabla usa `DELETE` físico. Todas usan `IsDeleted = 1` para eliminación lógica. Las consultas deben filtrar `WHERE IsDeleted = 0` por defecto.

### 1.4 Tipos de Datos MySQL

| Tipo MySQL | Uso | Equivalente SQL Server |
|-----------|-----|----------------------|
| CHAR(36) | GUIDs / PKs / FKs | UNIQUEIDENTIFIER |
| VARCHAR(n) | Texto de longitud variable | NVARCHAR(n) |
| LONGTEXT | Texto largo (descripciones, cartas) | NVARCHAR(MAX) |
| DATETIME(6) | Fechas con precisión de microsegundos | DATETIME2 |
| DATE | Solo fecha (nacimiento, etc.) | DATE |
| TINYINT(1) | Booleanos | BIT |
| INT | Enteros, enums | INT |
| DECIMAL(18,2) | Montos monetarios (salarios) | DECIMAL(18,2) |

### 1.5 Configuración Global

```sql
-- Charset y collation por defecto
CHARSET = utf8mb4;
COLLATE = utf8mb4_unicode_ci;

-- Engine
ENGINE = InnoDB;
```

---

## 2. Módulo Security (SC_)

### 2.1 SC_Users

**Propósito:** Almacena las credenciales y datos básicos de todos los usuarios del sistema (candidatos, empresas y administradores). Es la tabla central de autenticación.

**Descripción:** Un usuario representa cualquier persona que interactúa con la plataforma. Puede tener múltiples roles (candidato y empresa simultaneamente). La selección inicial ("Buscar Empleo" vs "Contratar") es una preferencia, no una restricción.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| Email | VARCHAR(256) | NO | - | Email único del usuario |
| PasswordHash | LONGTEXT | YES | NULL | Hash bcrypt de la contraseña (NULL si usa Google OAuth) |
| PrimaryRole | INT | NO | 0 | Rol principal: Candidate=0, Company=1, Admin=2 |
| Identification | VARCHAR(50) | YES | NULL | Cédula/identificación nacional |
| Phone | VARCHAR(20) | YES | NULL | Número de teléfono |
| EmailVerified | TINYINT(1) | NO | 0 | Si el email ha sido verificado |
| GoogleId | VARCHAR(256) | YES | NULL | ID de Google OAuth (NULL si no usa Google) |
| IsActive | TINYINT(1) | NO | 1 | Cuenta activa o desactivada por admin |
| LastLoginAt | DATETIME(6) | YES | NULL | Fecha del último login exitoso |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_SC_Users_Email` ON (`Email`)
- UNIQUE INDEX `UQ_SC_Users_GoogleId` ON (`GoogleId`) WHERE `GoogleId IS NOT NULL`
- INDEX `IX_SC_Users_Identification` ON (`Identification`)
- INDEX `IX_SC_Users_IsActive` ON (`IsActive`, `IsDeleted`)

---

### 2.2 SC_UserRoles

**Propósito:** Permite que un usuario tenga múltiples roles simultaneamente (candidato y empresa al mismo tiempo).

**Descripción:** Relación N:M entre usuarios y roles. Un usuario puede ser Candidate y Company a la vez. La selección inicial del portal determina el primer rol asignado, pero el usuario puede obtener el segundo rol en cualquier momento.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| SC_UserId | CHAR(36) | NO | - | FK → SC_Users |
| Role | INT | NO | - | Enum: Candidate=0, Company=1, Admin=2 |
| AssignedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Fecha de asignación del rol |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_SC_UserRoles_UserId_Role` ON (`SC_UserId`, `Role`, `IsDeleted`)
- INDEX `IX_SC_UserRoles_Role` ON (`Role`)

---

### 2.3 SC_RefreshTokens

**Propósito:** Almacena los refresh tokens emitidos a los usuarios para renovar el JWT sin requerir un nuevo login.

**Descripción:** Cada vez que un usuario inicia sesión, se emite un JWT + un refresh token. El refresh token se almacena hasheado (nunca en texto plano). Al usarse, se rota: el token anterior se marca como revocado y se emite uno nuevo. Esto previene reutilización de tokens comprometidos.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| SC_UserId | CHAR(36) | NO | - | FK → SC_Users |
| TokenHash | VARCHAR(512) | NO | - | Hash SHA-256 del refresh token |
| ExpiresAt | DATETIME(6) | NO | - | Fecha de expiración del token |
| IsRevoked | TINYINT(1) | NO | 0 | Si el token fue revocado (rotación o logout) |
| ReplacedByTokenHash | VARCHAR(512) | YES | NULL | Hash del token que reemplazó a este (rotación) |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_SC_RefreshTokens_TokenHash` ON (`TokenHash`)
- INDEX `IX_SC_RefreshTokens_UserId` ON (`SC_UserId`, `IsRevoked`, `IsDeleted`)

---

### 2.4 SC_UserDevices

**Propósito:** Registra los dispositivos desde los que un usuario ha iniciado sesión para detectar logins desde dispositivos nuevos y activar el captcha.

**Descripción:** En el primer login de un usuario, se registra el dispositivo usando un hash de (User-Agent + IP + fingerprint del navegador). En logins posteriores, si el hash coincide, es un dispositivo conocido (sin captcha). Si no coincide, es un dispositivo nuevo (se muestra reCAPTCHA v2).

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| SC_UserId | CHAR(36) | NO | - | FK → SC_Users |
| DeviceHash | VARCHAR(256) | NO | - | Hash de User-Agent + IP + fingerprint |
| DeviceName | VARCHAR(200) | YES | NULL | Nombre legible del dispositivo (ej: "Chrome - Windows") |
| FirstSeenAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Primer login desde este dispositivo |
| LastSeenAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Último login desde este dispositivo |
| IsTrusted | TINYINT(1) | NO | 0 | Si el usuario marcó el dispositivo como confiable |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_SC_UserDevices_UserId_DeviceHash` ON (`SC_UserId`, `DeviceHash`, `IsDeleted`)
- INDEX `IX_SC_UserDevices_UserId` ON (`SC_UserId`, `IsTrusted`)

---

## 3. Módulo Portal (PT_)

### 3.1 PT_Candidates

**Propósito:** Almacena el perfil profesional de los usuarios que se registran como candidatos.

**Descripción:** Un candidato es un usuario que busca empleo. El perfil se construye mediante un wizard multi-paso durante el registro. Los campos `WizardStep` y `WizardCompleted` permiten reanudar el wizard si el usuario lo abandona. En la Fase 1 se completan los datos básicos; en la Fase 2 se agregan experiencia, educación y CV.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| SC_UserId | CHAR(36) | NO | - | FK → SC_Users (relación 1:1) |
| FirstName | VARCHAR(100) | NO | - | Nombre |
| LastName | VARCHAR(100) | NO | - | Apellido |
| Identification | VARCHAR(50) | YES | NULL | Cédula/identificación nacional |
| Phone | VARCHAR(20) | YES | NULL | Teléfono |
| BirthDate | DATE | YES | NULL | Fecha de nacimiento |
| Gender | INT | YES | NULL | Enum: Male=0, Female=1, Other=2, PreferNotToSay=3 |
| Title | VARCHAR(200) | YES | NULL | Título profesional (ej: "Desarrollador Full Stack") |
| Summary | LONGTEXT | YES | NULL | Resumen profesional |
| CvUrl | VARCHAR(500) | YES | NULL | URL del CV subido |
| ProfilePictureUrl | VARCHAR(500) | YES | NULL | URL de la foto de perfil |
| Country | VARCHAR(100) | YES | NULL | País |
| City | VARCHAR(100) | YES | NULL | Ciudad |
| Address | VARCHAR(300) | YES | NULL | Dirección |
| WizardCompleted | TINYINT(1) | NO | 0 | Si completó todos los pasos del wizard |
| WizardStep | INT | NO | 0 | Paso actual del wizard (0 = no iniciado, 1-6 = pasos) |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_PT_Candidates_UserId` ON (`SC_UserId`, `IsDeleted`)
- INDEX `IX_PT_Candidates_Identification` ON (`Identification`)
- INDEX `IX_PT_Candidates_WizardCompleted` ON (`WizardCompleted`, `IsDeleted`)

---

### 3.2 PT_Companies

**Propósito:** Almacena el perfil de las empresas/reclutadores que publican vacantes.

**Descripción:** Una empresa es un usuario que contrata. El perfil de empresa se crea cuando el usuario selecciona "Contratar" o cuando un candidato decide también publicar vacantes. Una empresa puede tener múltiples vacantes.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| SC_UserId | CHAR(36) | NO | - | FK → SC_Users (relación 1:1) |
| Name | VARCHAR(200) | NO | - | Nombre de la empresa |
| Description | LONGTEXT | YES | NULL | Descripción de la empresa |
| Website | VARCHAR(500) | YES | NULL | Sitio web |
| LogoUrl | VARCHAR(500) | YES | NULL | URL del logo |
| Country | VARCHAR(100) | YES | NULL | País |
| City | VARCHAR(100) | YES | NULL | Ciudad |
| Address | VARCHAR(300) | YES | NULL | Dirección |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_PT_Companies_UserId` ON (`SC_UserId`, `IsDeleted`)
- INDEX `IX_PT_Companies_Name` ON (`Name`, `IsDeleted`)

---

### 3.3 PT_TempVacancies

**Propósito:** Almacena vacantes creadas de forma temporal por los usuarios en la Fase 1.

**Descripción:** En la Fase 1, las vacantes no se asocian a una empresa formalmente. Cualquier usuario puede crear una vacante temporal con fecha de expiración. Un job programado en el backend elimina (soft delete) las vacantes expiradas automáticamente. En la Fase 2, las vacantes temporales pueden convertirse en vacantes permanentes asociadas a una PT_Companies.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| SC_UserId | CHAR(36) | NO | - | FK → SC_Users (usuario creador) |
| Title | VARCHAR(200) | NO | - | Título de la vacante |
| Description | LONGTEXT | YES | NULL | Descripción detallada |
| Requirements | LONGTEXT | YES | NULL | Requisitos del puesto |
| SalaryMin | DECIMAL(18,2) | YES | NULL | Salario mínimo |
| SalaryMax | DECIMAL(18,2) | YES | NULL | Salario máximo |
| Location | VARCHAR(200) | YES | NULL | Ubicación del puesto |
| ContractType | INT | NO | 0 | Enum: FullTime=0, PartTime=1, Contract=2, Remote=3 |
| ExpiresAt | DATETIME(6) | NO | - | Fecha de expiración (auto-elimina) |
| IsPublished | TINYINT(1) | NO | 0 | Si se publicó o sigue en borrador temporal |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- INDEX `IX_PT_TempVacancies_UserId` ON (`SC_UserId`, `IsDeleted`)
- INDEX `IX_PT_TempVacancies_ExpiresAt` ON (`ExpiresAt`, `IsDeleted`)
- INDEX `IX_PT_TempVacancies_IsPublished` ON (`IsPublished`, `IsDeleted`)

---

### 3.4 PT_Skills

**Propósito:** Catálogo de habilidades que los candidatos pueden seleccionar en su perfil.

**Descripción:** Tabla catálogo con skills predefinidos. Los candidatos seleccionan skills de esta lista en el paso 4 del wizard. Los admins pueden agregar nuevas skills. En la Fase 2, las vacantes también pueden requerir skills específicos.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| Name | VARCHAR(100) | NO | - | Nombre de la habilidad (ej: "C#", "Blazor", "SQL") |
| Category | VARCHAR(100) | YES | NULL | Categoría de la skill (ej: "Backend", "Frontend", "Database") |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_PT_Skills_Name` ON (`Name`, `IsDeleted`)
- INDEX `IX_PT_Skills_Category` ON (`Category`, `IsDeleted`)

---

### 3.5 PT_CandidateSkills

**Propósito:** Relación N:M entre candidatos y skills. Indica qué habilidades tiene cada candidato.

**Descripción:** Tabla intermedia que asocia un candidato con una o más skills. Se llena durante el paso 4 del wizard y puede editarse posteriormente desde el dashboard del candidato.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| PT_CandidateId | CHAR(36) | NO | - | FK → PT_Candidates |
| PT_SkillId | CHAR(36) | NO | - | FK → PT_Skills |
| ProficiencyLevel | INT | YES | NULL | Enum: Beginner=0, Intermediate=1, Advanced=2, Expert=3 |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_PT_CandidateSkills_CandidateId_SkillId` ON (`PT_CandidateId`, `PT_SkillId`, `IsDeleted`)
- INDEX `IX_PT_CandidateSkills_SkillId` ON (`PT_SkillId`, `IsDeleted`)

---

### 3.6 PT_Vacancies

**Propósito:** Almacena las vacantes permanentes publicadas por empresas. [Fase 2]

**Descripción:** En la Fase 2, las vacantes temporales se convierten en vacantes permanentes asociadas a una empresa. Las vacantes permanentes tienen un ciclo de vida completo: Draft → Active → Closed. Los candidatos pueden aplicar a vacantes activas.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| PT_CompanyId | CHAR(36) | NO | - | FK → PT_Companies |
| Title | VARCHAR(200) | NO | - | Título de la vacante |
| Description | LONGTEXT | YES | NULL | Descripción detallada |
| Requirements | LONGTEXT | YES | NULL | Requisitos del puesto |
| SalaryMin | DECIMAL(18,2) | YES | NULL | Salario mínimo |
| SalaryMax | DECIMAL(18,2) | YES | NULL | Salario máximo |
| Location | VARCHAR(200) | YES | NULL | Ubicación del puesto |
| ContractType | INT | NO | 0 | Enum: FullTime=0, PartTime=1, Contract=2, Remote=3 |
| Status | INT | NO | 0 | Enum: Draft=0, Active=1, Closed=2 |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- INDEX `IX_PT_Vacancies_CompanyId` ON (`PT_CompanyId`, `IsDeleted`)
- INDEX `IX_PT_Vacancies_Status` ON (`Status`, `IsDeleted`)
- INDEX `IX_PT_Vacancies_Location` ON (`Location`, `Status`, `IsDeleted`)

---

### 3.7 PT_Applications

**Propósito:** Almacena las solicitudes de los candidatos a las vacantes. [Fase 2]

**Descripción:** Cuando un candidato aplica a una vacante, se crea un registro en esta tabla. La empresa puede cambiar el estado de la solicitud: Pending → InReview → Accepted/Rejected.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| PT_CandidateId | CHAR(36) | NO | - | FK → PT_Candidates |
| PT_VacancyId | CHAR(36) | NO | - | FK → PT_Vacancies |
| Status | INT | NO | 0 | Enum: Pending=0, InReview=1, Accepted=2, Rejected=3 |
| CoverLetter | LONGTEXT | YES | NULL | Carta de presentación del candidato |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_PT_Applications_CandidateId_VacancyId` ON (`PT_CandidateId`, `PT_VacancyId`, `IsDeleted`)
- INDEX `IX_PT_Applications_VacancyId` ON (`PT_VacancyId`, `Status`, `IsDeleted`)
- INDEX `IX_PT_Applications_CandidateId` ON (`PT_CandidateId`, `Status`, `IsDeleted`)

---

## 4. Módulo System (SY_)

### 4.1 SY_WizardSteps

**Propósito:** Configuración de los pasos del wizard de registro. Define el orden, título y si cada paso es obligatorio.

**Descripción:** Tabla catálogo que define los pasos del wizard de registro de candidatos. Permite que el wizard sea configurable sin cambios en código. El campo `Order` determina el orden de visualización. `IsRequired` indica si el paso puede omitirse.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| StepNumber | INT | NO | - | Número de paso (1, 2, 3, ...) |
| StepName | VARCHAR(100) | NO | - | Identificador interno (ej: "PersonalData", "Location") |
| StepTitle | VARCHAR(200) | NO | - | Título a mostrar al usuario |
| Description | VARCHAR(500) | YES | NULL | Descripción del paso |
| IsRequired | TINYINT(1) | NO | 1 | Si el paso es obligatorio |
| Order | INT | NO | - | Orden de visualización |
| Phase | INT | NO | 1 | Fase del proyecto: Phase1=1, Phase2=2 |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_SY_WizardSteps_StepNumber` ON (`StepNumber`, `IsDeleted`)
- INDEX `IX_SY_WizardSteps_Order` ON (`Order`, `Phase`, `IsDeleted`)

**Seed Data (Fase 1):**

| Step | Name | Title | Required | Order | Phase |
|------|------|-------|----------|-------|-------|
| 1 | PersonalData | Personal Data | YES | 1 | 1 |
| 2 | Location | Location | YES | 2 | 1 |
| 3 | ProfessionalProfile | Professional Profile | YES | 3 | 1 |
| 4 | Skills | Skills | NO | 4 | 1 |
| 5 | Preferences | What do you want to do? | YES | 5 | 1 |
| 6 | Confirmation | Review and Confirm | YES | 6 | 1 |

**Seed Data (Fase 2 - futura):**

| Step | Name | Title | Required | Order | Phase |
|------|------|-------|----------|-------|-------|
| 7 | WorkExperience | Work Experience | NO | 7 | 2 |
| 8 | Education | Education | NO | 8 | 2 |
| 9 | Certifications | Certifications | NO | 9 | 2 |
| 10 | UploadCV | Upload CV | NO | 10 | 2 |

---

### 4.2 SY_UserPreferences

**Propósito:** Almacena las preferencias de cada usuario como el tema visual, idioma y rol preferido al iniciar sesión.

**Descripción:** Cada usuario tiene un registro de preferencias (relación 1:1). El tema determina qué archivo `theme.css` se carga en el frontend. `Language` determina qué archivo de idioma se carga (es, en). `PreferredRole` indica si el usuario prefiere ver el dashboard de candidato o empresa al iniciar sesión.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| SC_UserId | CHAR(36) | NO | - | FK → SC_Users (relación 1:1) |
| Theme | VARCHAR(50) | NO | 'navy' | Tema visual preferido (navy, dark, light, corporate) |
| Language | VARCHAR(10) | NO | 'es' | Idioma preferido (es = Español, en = English) |
| PreferredRole | INT | YES | NULL | Rol preferido al iniciar: Candidate=0, Company=1 |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- UNIQUE INDEX `UQ_SY_UserPreferences_UserId` ON (`SC_UserId`, `IsDeleted`)

---

## 5. Módulo Admin (AD_) — [Fase 2]

### 5.1 AD_AuditLog

**Propósito:** Registra todas las acciones administrativas para auditoría y trazabilidad.

**Descripción:** Cada acción que realiza un administrador (activar/desactivar usuarios, eliminar vacantes, cambiar configuración) se registra aquí con el usuario, la acción, la entidad afectada y un JSON con los cambios.

| Campo | Tipo MySQL | Null | Default | Descripción |
|-------|-----------|------|---------|-------------|
| Id | CHAR(36) | NO | - | PK |
| SC_UserId | CHAR(36) | NO | - | FK → SC_Users (admin que realizó la acción) |
| Action | VARCHAR(100) | NO | - | Acción realizada (ej: "DeleteUser", "DeactivateVacancy") |
| EntityType | VARCHAR(100) | NO | - | Tipo de entidad afectada (ej: "SC_Users", "PT_Vacancies") |
| EntityId | CHAR(36) | YES | NULL | ID de la entidad afectada |
| ChangesJson | LONGTEXT | YES | NULL | JSON con los cambios realizados (before/after) |
| IpAddress | VARCHAR(45) | YES | NULL | IP del admin |
| CreatedAt | DATETIME(6) | NO | CURRENT_TIMESTAMP | Auditoría |
| CreatedBy | CHAR(36) | YES | NULL | Auditoría |
| UpdatedAt | DATETIME(6) | YES | NULL | Auditoría |
| UpdatedBy | CHAR(36) | YES | NULL | Auditoría |
| IsDeleted | TINYINT(1) | NO | 0 | Auditoría |
| DeletedAt | DATETIME(6) | YES | NULL | Auditoría |
| DeletedBy | CHAR(36) | YES | NULL | Auditoría |

**Índices:**
- INDEX `IX_AD_AuditLog_UserId` ON (`SC_UserId`, `IsDeleted`)
- INDEX `IX_AD_AuditLog_EntityType` ON (`EntityType`, `EntityId`, `IsDeleted`)
- INDEX `IX_AD_AuditLog_CreatedAt` ON (`CreatedAt`, `IsDeleted`)

---

## 6. Resumen de Tablas por Fase

### 6.1 Fase 1 (Seguridad + Dashboard Postulante)

| Tabla | Prefijo | Propósito |
|-------|---------|-----------|
| `SC_Users` | Security | Credenciales y datos básicos de usuarios |
| `SC_UserRoles` | Security | Roles múltiples por usuario |
| `SC_RefreshTokens` | Security | Refresh tokens con rotación |
| `SC_UserDevices` | Security | Detección de dispositivos para captcha |
| `PT_Candidates` | Portal | Perfil profesional del candidato |
| `PT_Companies` | Portal | Perfil de empresa |
| `PT_TempVacancies` | Portal | Vacantes temporales con expiración |
| `PT_Skills` | Portal | Catálogo de habilidades |
| `PT_CandidateSkills` | Portal | Relación candidato ↔ skills |
| `SY_WizardSteps` | System | Configuración de pasos del wizard |
| `SY_UserPreferences` | System | Tema visual, idioma y rol preferido |

### 6.2 Fase 2 (Búsqueda pública + Vacantes permanentes + Admin)

| Tabla | Prefijo | Propósito |
|-------|---------|-----------|
| `PT_Vacancies` | Portal | Vacantes permanentes asociadas a empresas |
| `PT_Applications` | Portal | Solicitudes de candidatos a vacantes |
| `AD_AuditLog` | Admin | Log de acciones administrativas |

---

## 7. Diagrama de Relaciones

```
┌──────────────┐     ┌──────────────────┐     ┌───────────────────┐
│   SC_Users   │────▶│  SC_UserRoles    │     │ SC_RefreshTokens  │
│   (PK: Id)   │     │  (FK: SC_UserId) │     │ (FK: SC_UserId)   │
└──────┬───────┘     └──────────────────┘     └───────────────────┘
       │
       │1:1
       ▼
┌──────────────┐     ┌──────────────────────┐
│PT_Candidates │────▶│ PT_CandidateSkills   │
│(FK: SC_UserId)│    │ (FK: PT_CandidateId) │
└──────┬───────┘     └──────────┬───────────┘
       │                        │
       │                        │N:M
       │                        ▼
       │               ┌──────────────┐
       │               │   PT_Skills   │
       │               └──────────────┘
       │
       │1:N (Fase 2)
       ▼
┌──────────────────┐
│ PT_Applications  │
│(FK: PT_CandidateId)│
└────────┬─────────┘
         │
         │N:1
         ▼
┌──────────────┐     ┌──────────────────┐
│ PT_Vacancies │     │  PT_Companies    │
│(FK: PT_CompanyId) │  (FK: SC_UserId)  │
└──────────────┘     └──────────────────┘

┌──────────────┐
│SC_UserDevices│     ┌──────────────────────┐     ┌───────────────────┐
│(FK: SC_UserId)│    │ SY_UserPreferences   │     │ SY_WizardSteps    │
└──────────────┘     │ (FK: SC_UserId)      │     │ (Catálogo)        │
                     └──────────────────────┘     └───────────────────┘

┌──────────────────┐     ┌──────────────┐
│ PT_TempVacancies │     │ AD_AuditLog  │
│ (FK: SC_UserId)  │     │(FK: SC_UserId)│
└──────────────────┘     └──────────────┘
```

---

## 8. Sistema de Idiomas (i18n)

### 8.1 Estructura de Archivos de Idioma

Los archivos de traducción se organizan en carpetas por idioma dentro de `wwwroot/config/language/`:

```
wwwroot/
├── config/
│   └── language/
│       ├── es/                         # Español (por defecto)
│       │   ├── common.json             # Textos compartidos (botones, labels generales)
│       │   ├── auth.json               # Textos de autenticación (login, register, forgot)
│       │   ├── wizard.json             # Textos del wizard de registro
│       │   ├── dashboard.json          # Textos del dashboard (candidato y empresa)
│       │   ├── vacancies.json          # Textos de vacantes
│       │   ├── profile.json            # Textos de perfil
│       │   ├── validation.json         # Mensajes de validación
│       │   └── errors.json             # Mensajes de error
│       └── en/                         # English
│           ├── common.json
│           ├── auth.json
│           ├── wizard.json
│           ├── dashboard.json
│           ├── vacancies.json
│           ├── profile.json
│           ├── validation.json
│           └── errors.json
```

### 8.2 Formato de Archivos de Idioma

Cada archivo JSON contiene pares clave-valor organizados por secciones:

**Ejemplo `es/common.json`:**
```json
{
    "buttons": {
        "save": "Guardar",
        "cancel": "Cancelar",
        "delete": "Eliminar",
        "edit": "Editar",
        "search": "Buscar",
        "close": "Cerrar",
        "accept": "Aceptar",
        "decline": "Rechazar"
    },
    "labels": {
        "welcome": "Bienvenido",
        "loading": "Cargando...",
        "noResults": "No se encontraron resultados",
        "actions": "Acciones"
    },
    "nav": {
        "home": "Inicio",
        "dashboard": "Dashboard",
        "profile": "Mi Perfil",
        "vacancies": "Vacantes",
        "applications": "Mis Solicitudes",
        "settings": "Configuración",
        "logout": "Cerrar Sesión"
    }
}
```

**Ejemplo `en/common.json`:**
```json
{
    "buttons": {
        "save": "Save",
        "cancel": "Cancel",
        "delete": "Delete",
        "edit": "Edit",
        "search": "Search",
        "close": "Close",
        "accept": "Accept",
        "decline": "Decline"
    },
    "labels": {
        "welcome": "Welcome",
        "loading": "Loading...",
        "noResults": "No results found",
        "actions": "Actions"
    },
    "nav": {
        "home": "Home",
        "dashboard": "Dashboard",
        "profile": "My Profile",
        "vacancies": "Vacancies",
        "applications": "My Applications",
        "settings": "Settings",
        "logout": "Logout"
    }
}
```

**Ejemplo `es/wizard.json`:**
```json
{
    "title": "Completa tu perfil",
    "steps": {
        "personalData": {
            "title": "Datos Personales",
            "description": "Cuéntanos sobre ti",
            "fields": {
                "firstName": "Nombre",
                "lastName": "Apellido",
                "identification": "Identificación",
                "phone": "Teléfono",
                "birthDate": "Fecha de nacimiento",
                "gender": "Género"
            }
        },
        "location": {
            "title": "Ubicación",
            "description": "¿Dónde te encuentras?",
            "fields": {
                "country": "País",
                "city": "Ciudad",
                "address": "Dirección"
            }
        },
        "preferences": {
            "title": "¿Qué deseas hacer?",
            "options": {
                "searchJob": "Buscar empleo",
                "createVacancy": "Crear vacantes",
                "both": "Ambas"
            }
        },
        "confirmation": {
            "title": "Revisar y Confirmar",
            "description": "Verifica que tus datos sean correctos",
            "confirm": "Confirmar y continuar"
        }
    },
    "buttons": {
        "next": "Siguiente",
        "previous": "Anterior",
        "skip": "Omitir"
    }
}
```

### 8.3 Idiomas Soportados

| Código | Idioma | Default |
|--------|--------|---------|
| `es` | Español | Sí |
| `en` | English | No |

> **Idiomas futuros:** `pt` (Portugués), `fr` (Francés). La estructura de carpetas permite agregar nuevos idiomas sin cambios en código.

### 8.4 Reglas de i18n

| Regla | Descripción |
|-------|-------------|
| **No texto hardcoded** | Prohibido texto quemado en `.razor`. Todo texto debe usar claves de idioma |
| **Archivos JSON** | Las traducciones viven en `wwwroot/config/language/{lang}/{section}.json` |
| **Claves jerárquicas** | Usar notación por puntos: `buttons.save`, `nav.dashboard` |
| **Idioma por usuario** | El idioma se guarda en `SY_UserPreferences.Language` |
| **Idioma por defecto** | Si el usuario no tiene preferencia, usar `es` |
| **Fallback** | Si una clave no existe en el idioma activo, buscar en `es` |
| **Persistencia** | El idioma seleccionado se guarda en localStorage + en la BD |
| **Cambio dinámico** | El usuario puede cambiar de idioma sin recargar la página |

### 8.5 Integración con Blazor

#### LanguageService.cs

```csharp
public class LanguageService
{
    private readonly IJSRuntime _jsRuntime;
    private readonly HttpClient _httpClient;
    private string _currentLanguage = "es";
    private Dictionary<string, string> _translations = new();

    public event Action? OnLanguageChanged;

    public LanguageService(IJSRuntime jsRuntime, HttpClient httpClient)
    {
        _jsRuntime = jsRuntime;
        _httpClient = httpClient;
    }

    public string CurrentLanguage => _currentLanguage;

    public async Task SetLanguageAsync(string lang)
    {
        _currentLanguage = lang;
        await LoadTranslationsAsync(lang);
        await _jsRuntime.InvokeVoidAsync("localStorage.setItem", "opentowork-lang", lang);
        OnLanguageChanged?.Invoke();
    }

    public async Task LoadTranslationsAsync(string lang)
    {
        var sections = new[] { "common", "auth", "wizard", "dashboard", "vacancies", "profile", "validation", "errors" };
        _translations.Clear();
        foreach (var section in sections)
        {
            var json = await _httpClient.GetFromJsonAsync<Dictionary<string, object>>($"/config/language/{lang}/{section}.json");
            if (json != null)
            {
                FlattenDictionary(json, section, _translations);
            }
        }
    }

    public string this[string key] => _translations.TryGetValue(key, out var value) ? value : key;

    public string T(string key) => this[key];

    private void FlattenDictionary(Dictionary<string, object> dict, string prefix, Dictionary<string, string> result)
    {
        foreach (var kvp in dict)
        {
            var fullKey = $"{prefix}.{kvp.Key}";
            if (kvp.Value is JsonElement je && je.ValueKind == JsonValueKind.Object)
            {
                var nested = je.Deserialize<Dictionary<string, object>>();
                if (nested != null) FlattenDictionary(nested, fullKey, result);
            }
            else
            {
                result[fullKey] = kvp.Value?.ToString() ?? fullKey;
            }
        }
    }
}
```

#### Uso en componentes Blazor

```razor
@inject LanguageService Lang

<h1>@Lang.T("wizard.steps.personalData.title")</h1>
<label>@Lang.T("wizard.steps.personalData.fields.firstName")</label>
<button>@Lang.T("common.buttons.save")</button>
```

#### language-switcher.js

```javascript
window.languageSwitcher = {
    getSaved: () => localStorage.getItem('opentowork-lang') || 'es',
    set: (lang) => localStorage.setItem('opentowork-lang', lang)
};
```

### 8.6 Flujo de Cambio de Idioma

```
[Usuario hace clic en selector de idioma]
        │
        ▼
[LanguageService.SetLanguageAsync('en')]
        │
        ├──▶ [Cargar archivos JSON de /config/language/en/]
        │
        ├──▶ [Guardar en localStorage: 'opentowork-lang' = 'en']
        │
        ├──▶ [Guardar en BD: SY_UserPreferences.Language = 'en']
        │
        └──▶ [Disparar OnLanguageChanged → componentes re-renderizan]
        │
        ▼
[UI actualizada con nuevos textos, sin recargar página]
```

---

## 9. Enums del Sistema

### 9.1 UserRole

| Valor | Nombre | Descripción |
|-------|--------|-------------|
| 0 | Candidate | Candidato que busca empleo |
| 1 | Company | Empresa que publica vacantes |
| 2 | Admin | Administrador del sistema |

### 9.2 Gender

| Valor | Nombre |
|-------|--------|
| 0 | Male |
| 1 | Female |
| 2 | Other |
| 3 | PreferNotToSay |

### 9.3 ContractType

| Valor | Nombre |
|-------|--------|
| 0 | FullTime |
| 1 | PartTime |
| 2 | Contract |
| 3 | Remote |

### 9.4 VacancyStatus

| Valor | Nombre |
|-------|--------|
| 0 | Draft |
| 1 | Active |
| 2 | Closed |

### 9.5 ApplicationStatus

| Valor | Nombre |
|-------|--------|
| 0 | Pending |
| 1 | InReview |
| 2 | Accepted |
| 3 | Rejected |

### 9.6 ProficiencyLevel

| Valor | Nombre |
|-------|--------|
| 0 | Beginner |
| 1 | Intermediate |
| 2 | Advanced |
| 3 | Expert |

---

## 9. Estrategia de Migración MySQL → SQL Server

Solo se cambia el provider de EF Core y el connection string:

| MySQL | SQL Server |
|-------|------------|
| `CHAR(36)` | `UNIQUEIDENTIFIER` |
| `VARCHAR(n)` | `NVARCHAR(n)` |
| `LONGTEXT` | `NVARCHAR(MAX)` |
| `DATETIME(6)` | `DATETIME2` |
| `TINYINT(1)` | `BIT` |
| `DECIMAL(18,2)` | `DECIMAL(18,2)` |
| `utf8mb4` | Default unicode |
| `InnoDB` | Default |

**En código:**
```csharp
// MySQL (actual)
options.UseMySql(connectionString, 
    ServerVersion.AutoDetect(connectionString),
    mySqlOptions => mySqlOptions.CharSetBehavior(CharSetBehavior.NeverAppend));

// SQL Server (migración futura)
options.UseSqlServer(connectionString);
```

Las entidades, configuraciones de EF Core y migraciones permanecen idénticas.
