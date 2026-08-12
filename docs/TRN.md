# TRN - Technical Requirements Note

## OpenToWork - Requisitos Técnicos

**Versión:** 1.0  
**Fecha:** Agosto 2026  

---

## 1. Arquitectura General

### 1.1 Patrón de Arquitectura
Arquitectura en capas (Layered Architecture) con separación de responsabilidades:

```
┌─────────────────────────────────────────────────────────────┐
│                    CAPA DE PRESENTACIÓN                       │
│  ┌──────────────────────┐    ┌──────────────────────┐       │
│  │  WEB (Blazor)        │    │  AdminWEB (Blazor)   │       │
│  │  Portal Principal    │    │  Portal Admin        │       │
│  └──────────┬───────────┘    └──────────┬───────────┘       │
├─────────────┼───────────────────────────┼────────────────────┤
│             │    CAPA DE APIs            │                   │
│  ┌──────────▼───────────┐    ┌──────────▼───────────┐       │
│  │  API (REST)          │    │  AdminAPI (REST)     │       │
│  │  Portal Principal    │    │  Portal Admin        │       │
│  │  JWT Independiente   │    │  JWT Independiente   │       │
│  └──────────┬───────────┘    └──────────┬───────────┘       │
├─────────────┼───────────────────────────┼────────────────────┤
│             │    CAPA DE NEGOCIO          │                   │
│  ┌──────────▼───────────────────────────▼──────────┐        │
│  │              Core (Servicios)                   │        │
│  └──────────┬───────────────────────────┬──────────┘        │
├─────────────┼───────────────────────────┼────────────────────┤
│  ┌──────────▼───────────┐    ┌──────────▼───────────┐       │
│  │  Models / Shared     │    │  Models / Shared     │       │
│  └──────────┬───────────┘    └──────────┬───────────┘       │
├─────────────┼───────────────────────────┼────────────────────┤
│  ┌──────────▼───────────────────────────▼──────────┐        │
│  │         Infraestructura (EF Core)               │        │
│  │         Base de datos compartida (MySQL)        │        │
│  │         (Migrable a SQL Server)                 │        │
│  └─────────────────────────────────────────────────┘        │
└─────────────────────────────────────────────────────────────┘
```

> **Independencia de APIs:** `OpenToWork.API` y `OpenToWork.AdminAPI` son procesos independientes con su propio puerto, su propia configuración JWT (clave, issuer, audience distintos), y sus propios controladores. No comparten middleware ni configuración de seguridad.

### 1.2 Estructura de Proyectos

```
OpenToWork/
├── docs/                          # Documentación
├── src/
│   ├── OpenToWork.API/            # API REST - Portal Principal (candidatos/empresas)
│   ├── OpenToWork.AdminAPI/       # API REST - Portal Administrativo (independiente)
│   ├── OpenToWork.WEB/            # Aplicación Blazor - Portal Principal
│   ├── OpenToWork.AdminWEB/       # Aplicación Blazor - Portal Administrativo
│   ├── OpenToWork.SharedUI/       # Componentes Blazor compartidos (Razor Class Library)
│   ├── OpenToWork.Core/           # Lógica de negocio / Servicios (compartido)
│   ├── OpenToWork.Models/         # Entidades del dominio (compartido)
│   └── OpenToWork.Shared/         # DTOs, Enums, Constantes (compartido)
├── tests/
│   ├── OpenToWork.Core.Tests/     # Pruebas de lógica de negocio
│   ├── OpenToWork.API.Tests/      # Pruebas de integración API Portal
│   └── OpenToWork.AdminAPI.Tests/ # Pruebas de integración API Admin
└── OpenToWork.sln                 # Solución
```

---

## 2. Tecnologías y Versiones

| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| .NET SDK | 8.0 | Framework base |
| ASP.NET Core | 8.0 | API REST |
| Blazor | 8.0 | UI (Server o WASM) |
| Entity Framework Core | 8.0 | ORM / Acceso a datos |
| MySQL | 8.0+ | Base de datos (migrable a SQL Server) |
| Pomelo.EntityFrameworkCore.MySql | 8.0+ | Provider EF Core para MySQL |
| AutoMapper | 13.x | Mapeo de entidades a DTOs |
| FluentValidation | 11.x | Validación de modelos |
| JWTBearer | 8.0 | Autenticación |
| Swagger / Swashbuckle | 6.x | Documentación API |
| xUnit | 2.x | Framework de pruebas |
| Moq | 4.x | Mocking para pruebas |

---

## 3. Diseño de Base de Datos

> **Motor de BD:** MySQL 8.0+ con Pomelo.EntityFrameworkCore.MySql. Diseño migrable a SQL Server.
> **Documento completo:** Ver `docs/DATABASE_DESIGN.md` para especificación detallada de cada tabla con propósito, campos de auditoría, índices y seed data.

### 3.1 Convenciones

| Convención | Descripción |
|------------|-------------|
| **Prefijos** | `SC_` Security, `PT_` Portal, `AD_` Admin, `SY_` System |
| **Idioma** | Tablas y campos en inglés, PascalCase |
| **Auditoría** | Todas las tablas: CreatedAt, CreatedBy, UpdatedAt, UpdatedBy, IsDeleted, DeletedAt, DeletedBy |
| **Soft Delete** | Ningún DELETE físico. Usar `IsDeleted = 1` |
| **Charset** | `utf8mb4` |
| **Engine** | `InnoDB` |
| **Tipo GUID** | `CHAR(36)` (migrable a `UNIQUEIDENTIFIER`) |
| **Tipo texto** | `VARCHAR(n)` o `LONGTEXT` (migrable a `NVARCHAR`) |
| **Tipo fecha** | `DATETIME(6)` (migrable a `DATETIME2`) |
| **Boolean** | `TINYINT(1)` (migrable a `BIT`) |

### 3.2 Tablas por Módulo

#### Security (SC_)
| Tabla | Propósito |
|-------|-----------|
| `SC_Users` | Credenciales y datos básicos de usuarios |
| `SC_UserRoles` | Roles múltiples por usuario (Candidate + Company) |
| `SC_RefreshTokens` | Refresh tokens con rotación |
| `SC_UserDevices` | Detección de dispositivos para captcha |

#### Portal (PT_)
| Tabla | Propósito |
|-------|-----------|
| `PT_Candidates` | Perfil profesional del candidato (wizard) |
| `PT_Companies` | Perfil de empresa |
| `PT_TempVacancies` | Vacantes temporales con expiración [Fase 1] |
| `PT_Skills` | Catálogo de habilidades |
| `PT_CandidateSkills` | Relación candidato ↔ skills |
| `PT_Vacancies` | Vacantes permanentes [Fase 2] |
| `PT_Applications` | Solicitudes a vacantes [Fase 2] |

#### System (SY_)
| Tabla | Propósito |
|-------|-----------|
| `SY_WizardSteps` | Configuración de pasos del wizard |
| `SY_UserPreferences` | Tema visual y rol preferido |

#### Admin (AD_)
| Tabla | Propósito |
|-------|-----------|
| `AD_AuditLog` | Log de acciones administrativas [Fase 2] |

### 3.3 Relaciones

- **SC_Users → SC_UserRoles**: 1:N (multi-rol)
- **SC_Users → PT_Candidates**: 1:1 (perfil candidato)
- **SC_Users → PT_Companies**: 1:1 (perfil empresa)
- **SC_Users → PT_TempVacancies**: 1:N (vacantes temporales)
- **SC_Users → SC_RefreshTokens**: 1:N
- **SC_Users → SC_UserDevices**: 1:N
- **SC_Users → SY_UserPreferences**: 1:1
- **PT_Candidates ↔ PT_Skills**: N:M (PT_CandidateSkills)
- **PT_Companies → PT_Vacancies**: 1:N [Fase 2]
- **PT_Candidates → PT_Applications**: 1:N [Fase 2]
- **PT_Vacancies → PT_Applications**: 1:N [Fase 2]

### 3.4 Estrategia de Migración MySQL → SQL Server

Solo se cambia el provider de EF Core (`UseMySql` → `UseSqlServer`) y el connection string. Entidades y configuraciones permanecen idénticas. Ver `DATABASE_DESIGN.md` sección 9 para tabla de equivalencias completa.

---

## 4. Endpoints de la API

### 4.0 Separación de APIs

| API | Puerto | Propósito | JWT |
|-----|--------|-----------|-----|
| `OpenToWork.API` | 5000 | Portal principal (candidatos y empresas) | Clave A, Issuer A, Audience A |
| `OpenToWork.AdminAPI` | 5001 | Portal administrativo (solo admin) | Clave B, Issuer B, Audience B |

> **Importante:** Los tokens JWT generados por una API **no son válidos** en la otra. Cada API valida los tokens con su propia configuración.

---

### 4.1 API Portal Principal (`OpenToWork.API` - Puerto 5000)

#### Autenticación (Portal Principal)
| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/auth/register` | Registrar usuario (candidato o empresa) |
| POST | `/api/auth/login` | Iniciar sesión (candidato o empresa) |
| POST | `/api/auth/forgot-password` | Recuperar contraseña |

#### Candidatos
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/candidates` | Listar candidatos (paginado) |
| GET | `/api/candidates/{id}` | Obtener candidato por ID |
| POST | `/api/candidates` | Registrar candidato |
| PUT | `/api/candidates/{id}` | Actualizar candidato |
| DELETE | `/api/candidates/{id}` | Eliminar candidato |
| GET | `/api/candidates/search` | Buscar candidatos por criterios |

#### Empresas
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/companies/{id}` | Obtener empresa |
| POST | `/api/companies` | Registrar empresa |
| PUT | `/api/companies/{id}` | Actualizar empresa |

#### Vacantes
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/vacancies` | Listar vacantes (paginado) |
| GET | `/api/vacancies/{id}` | Obtener vacante |
| POST | `/api/vacancies` | Crear vacante |
| PUT | `/api/vacancies/{id}` | Actualizar vacante |
| DELETE | `/api/vacancies/{id}` | Eliminar vacante |
| GET | `/api/vacancies/search` | Buscar vacantes |

#### Solicitudes (Applications)
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/applications` | Listar solicitudes |
| GET | `/api/applications/{id}` | Obtener solicitud |
| POST | `/api/applications` | Crear solicitud (aplicar a vacante) |
| PUT | `/api/applications/{id}/status` | Cambiar estado de solicitud |
| GET | `/api/applications/candidate/{id}` | Solicitudes de un candidato |
| GET | `/api/applications/vacancy/{id}` | Solicitudes de una vacante |

---

### 4.2 API Portal Administrativo (`OpenToWork.AdminAPI` - Puerto 5001)

#### Autenticación (Admin - Independiente)
| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/admin/auth/login` | Iniciar sesión admin (JWT propio) |
| POST | `/api/admin/auth/refresh` | Refrescar token admin |
| POST | `/api/admin/auth/logout` | Cerrar sesión admin |

#### Gestión de Usuarios
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/admin/users` | Listar todos los usuarios |
| GET | `/api/admin/users/{id}` | Obtener usuario por ID |
| PUT | `/api/admin/users/{id}/status` | Activar/Desactivar usuario |
| DELETE | `/api/admin/users/{id}` | Eliminar usuario |
| GET | `/api/admin/users/search` | Buscar usuarios por criterios |

#### Gestión de Candidatos (Admin)
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/admin/candidates` | Listar todos los candidatos |
| GET | `/api/admin/candidates/{id}` | Ver detalle de candidato |
| DELETE | `/api/admin/candidates/{id}` | Eliminar candidato |

#### Gestión de Empresas (Admin)
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/admin/companies` | Listar todas las empresas |
| GET | `/api/admin/companies/{id}` | Ver detalle de empresa |
| DELETE | `/api/admin/companies/{id}` | Eliminar empresa |

#### Gestión de Vacantes (Admin)
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/admin/vacancies` | Listar todas las vacantes |
| GET | `/api/admin/vacancies/{id}` | Ver detalle de vacante |
| PUT | `/api/admin/vacancies/{id}/status` | Cambiar estado de vacante |
| DELETE | `/api/admin/vacancies/{id}` | Eliminar vacante |

#### Gestión de Solicitudes (Admin)
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/admin/applications` | Listar todas las solicitudes |
| GET | `/api/admin/applications/{id}` | Ver detalle de solicitud |
| PUT | `/api/admin/applications/{id}/status` | Cambiar estado de solicitud |
| DELETE | `/api/admin/applications/{id}` | Eliminar solicitud |

#### Gestión de Skills/Categorías (Admin)
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/admin/skills` | Listar skills |
| POST | `/api/admin/skills` | Crear skill |
| PUT | `/api/admin/skills/{id}` | Actualizar skill |
| DELETE | `/api/admin/skills/{id}` | Eliminar skill |

#### Dashboard y Métricas (Admin)
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/admin/dashboard/stats` | Estadísticas generales |
| GET | `/api/admin/dashboard/users` | Métricas de usuarios |
| GET | `/api/admin/dashboard/vacancies` | Métricas de vacantes |
| GET | `/api/admin/dashboard/applications` | Métricas de solicitudes |

---

## 5. Seguridad

### 5.1 Separación de APIs

Las dos APIs son completamente independientes en términos de seguridad:

| Aspecto | OpenToWork.API (Portal) | OpenToWork.AdminAPI (Admin) |
|---------|------------------------|---------------------------|
| **JWT Key** | Clave A (256 bits) | Clave B (256 bits, distinta) |
| **JWT Issuer** | `OpenToWork.Portal` | `OpenToWork.Admin` |
| **JWT Audience** | `OpenToWork.Portal` | `OpenToWork.Admin` |
| **JWT Expiración (normal)** | 60 minutos | 30 minutos |
| **JWT Expiración ("Recordarme")** | 30 días (43200 minutos) | N/A (admin no tiene recordarme) |
| **Puerto** | 5000 | 5001 |
| **Roles permitidos** | Candidate, Company | Admin |
| **CORS Origins** | Dominio del portal | Dominio del admin |
| **Captcha** | Condicional (dispositivo diferente) | Obligatorio (siempre) |
| **Google OAuth** | Sí (opcional) | No |

> **Aislamiento de tokens:** Un token generado por `OpenToWork.API` será **rechazado** por `OpenToWork.AdminAPI` y viceversa, porque cada API valida el issuer y audience con su propia configuración.

### 5.2 JWT y Duración de Sesión

#### "Mantener Sesión Activa" (Remember Me)

El JWT incluye un claim `rememberMe` que determina la duración del token:

| Escenario | Duración del Token | Refresh Token |
|-----------|-------------------|---------------|
| Sin "Recordarme" | 60 minutos (portal) / 30 minutos (admin) | Sí, rotación cada 24h |
| Con "Recordarme" | 30 días (solo portal) | Sí, rotación cada 7 días |

**Flujo:**
1. Usuario marca "Recordarme" en el login.
2. API genera JWT con `rememberMe: true` y expiración extendida.
3. Frontend almacena el token encriptado.
4. En cada petición, el middleware valida el token y su claim `rememberMe`.
5. Si el token expira, el refresh token se usa para obtener uno nuevo automáticamente.

#### Refresh Token
- Se emite un **refresh token** junto con el JWT.
- El refresh token se almacena en la base de datos (tabla `RefreshTokens`).
- Al usar el refresh token, se genera un nuevo JWT y se rota el refresh token (el anterior se invalida).
- Si el refresh token expira, el usuario debe iniciar sesión nuevamente.

### 5.3 Captcha por Dispositivo Diferente

#### Detección de Dispositivo

El sistema detecta si el login se realiza desde un dispositivo conocido o nuevo:

1. **Primer login:** Se registra el dispositivo usando un hash de (User-Agent + IP + fingerprint del navegador).
2. **Logins siguientes:** Se compara el hash del dispositivo actual con los registrados.
3. **Dispositivo nuevo:** Se muestra **Google reCAPTCHA v2** antes de procesar el login.
4. **Dispositivo conocido:** No se muestra captcha (login fluido).

#### Tabla: UserDevices

| Campo | Tipo | Descripción |
|-------|------|-------------|
| Id | GUID | PK |
| UserId | GUID | FK → Users |
| DeviceHash | NVARCHAR(256) | Hash de User-Agent + IP + fingerprint |
| DeviceName | NVARCHAR(200) | Nombre legible del dispositivo |
| FirstSeenAt | DATETIME2 | Primer login desde este dispositivo |
| LastSeenAt | DATETIME2 | Último login desde este dispositivo |
| IsTrusted | BIT | Si el dispositivo es de confianza |

#### Implementación de reCAPTCHA
- **Google reCAPTCHA v2** (checkbox "No soy un robot").
- El frontend obtiene el token de reCAPTCHA y lo envía al API.
- El API valida el token con Google (`https://www.google.com/recaptcha/api/siteverify`).
- Si la validación falla, se rechaza el login.
- **Portal Admin:** reCAPTCHA **siempre** obligatorio, sin importar el dispositivo.

### 5.4 Google OAuth 2.0

#### Integración con Google Sign-In

El portal principal soporta login con Google como método alternativo:

1. Usuario hace clic en "Iniciar sesión con Google".
2. Frontend usa Google Identity Services (GIS) para obtener un ID token de Google.
3. Frontend envía el ID token al API: `POST /api/auth/google`.
4. API valida el ID token con Google (`https://oauth2.googleapis.com/tokeninfo`).
5. Si el email de Google existe en la BD, se genera un JWT de OpenToWork.
6. Si el email no existe, se crea automáticamente un usuario con rol Candidate.
7. Se retorna el JWT + datos del usuario al frontend.

#### Configuración de Google OAuth

| Parámetro | Valor |
|-----------|-------|
| **Provider** | Google Identity Services |
| **Scopes** | openid, email, profile |
| **Client ID** | Configurado en appsettings.json |
| **Client Secret** | Configurado en appsettings.json (solo backend) |
| **Redirect URI** | `https://localhost:5100/login/google-callback` |

#### Endpoints de Google OAuth

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/auth/google` | Login/Registro con Google ID token |
| GET | `/api/auth/google/config` | Obtener Client ID para el frontend |

> **Nota:** La integración con otros proveedores (Microsoft, Facebook, etc.) se agregará en fases posteriores. La arquitectura debe estar preparada para extender con múltiples proveedores OAuth.

### 5.5 Almacenamiento Seguro de Sesión en el Frontend

#### Opción 1: localStorage Encriptado (Por defecto)

- Los datos de sesión (JWT, refresh token, datos del usuario) se almacenan en `localStorage` **encriptados con AES-256**.
- Se usa una clave de encriptación generada por sesión (derivada de un valor aleatorio + fingerprint del navegador).
- Librería recomendada: `Blazored.LocalStorage` + encriptación manual con `System.Security.Cryptography.AES`.
- Formato almacenado: `{ "encryptedData": "<base64-AES-256>", "iv": "<base64-IV>" }`.

#### Opción 2: IndexedDB (Más seguro)

- Como alternativa más robusta, los datos de sesión se guardan en **IndexedDB**.
- IndexedDB es más difícil de acceder por scripts de terceros (XSS) comparado con localStorage.
- Librería recomendada: `DnetIndexedDB` o wrappers de Blazor para IndexedDB.
- Los datos se almacenan en un object store dedicado `session_data` con clave `auth_session`.
- Se recomienda encriptar también los datos antes de guardarlos en IndexedDB.

#### Comparativa

| Aspecto | localStorage Encriptado | IndexedDB |
|---------|------------------------|-----------|
| **Seguridad XSS** | Media (accesible por JS) | Alta (más aislado) |
| **Complejidad** | Baja | Media |
| **Compatibilidad** | 100% | 95%+ (navegadores modernos) |
| **Capacidad** | 5-10 MB | 50+ MB |
| **Recomendado para** | MVP / Fase 1 | Producción / Fase 2+ |

#### Datos Almacenados

| Dato | Encriptado | Descripción |
|------|------------|-------------|
| `jwt_token` | Sí | Token JWT de acceso |
| `refresh_token` | Sí | Token de refresco |
| `user_data` | Sí | Datos del usuario (nombre, email, rol) |
| `remember_me` | No (booleano) | Si se debe mantener la sesión |
| `device_id` | Sí | ID del dispositivo registrado |

#### Limpieza de Datos
- Al cerrar sesión: se eliminan todos los datos del almacenamiento.
- Al expirar el token sin refresh: se eliminan los datos y se redirige al login.
- Al detectar un token inválido: se eliminan los datos y se redirige al login.

### 5.6 Medidas Generales

- **Autenticación:** JWT tokens con expiración independiente por API + refresh tokens.
- **Autorización:** Role-based (Candidate, Company en portal; Admin en admin).
- **Contraseñas:** Hash con BCrypt (cost factor 12).
- **Validación:** FluentValidation en todos los inputs.
- **CORS:** Configurado independientemente por API, permitiendo solo su dominio correspondiente.
- **HTTPS:** Obligatorio en producción.
- **Rate limiting:** Configurado por API de forma independiente.
- **Captcha:** Google reCAPTCHA v2 (condicional en portal, obligatorio en admin).
- **OAuth:** Google Sign-In (portal principal). Arquitectura preparada para más proveedores.
- **Almacenamiento frontend:** Datos de sesión encriptados (AES-256) en localStorage o IndexedDB.
- **Detección de dispositivo:** Registro y validación de dispositivos por usuario.

---

## 6. Configuración

### 6.1 Connection String - MySQL
```
Server=localhost;Port=3306;Database=OpenToWorkDb;User=root;Password=<tu-password>;CharSet=utf8mb4;
```

> **Migración a SQL Server:** Al migrar, cambiar el connection string y el provider de EF Core:
> `Server=localhost;Database=OpenToWorkDb;Trusted_Connection=True;TrustServerCertificate=True;`
> Y en `Program.cs`: `options.UseSqlServer(...)` en lugar de `options.UseMySql(...)`

### 6.2 JWT Settings - Portal Principal (appsettings.json en OpenToWork.API)
```json
{
  "Jwt": {
    "Key": "<clave-secreta-portal-256-bits>",
    "Issuer": "OpenToWork.Portal",
    "Audience": "OpenToWork.Portal",
    "ExpireMinutes": 60,
    "ExpireMinutesRememberMe": 43200,
    "RefreshTokenExpireDays": 7
  },
  "GoogleOAuth": {
    "ClientId": "<google-client-id>",
    "ClientSecret": "<google-client-secret>"
  },
  "Recaptcha": {
    "SiteKey": "<recaptcha-site-key>",
    "SecretKey": "<recaptcha-secret-key>",
    "VerifyUrl": "https://www.google.com/recaptcha/api/siteverify"
  },
  "SessionEncryption": {
    "Key": "<clave-encriptacion-frontend-256-bits>",
    "StorageMode": "LocalStorageEncrypted"
  }
}
```

### 6.3 JWT Settings - Portal Admin (appsettings.json en OpenToWork.AdminAPI)
```json
{
  "Jwt": {
    "Key": "<clave-secreta-admin-256-bits-distinta>",
    "Issuer": "OpenToWork.Admin",
    "Audience": "OpenToWork.Admin",
    "ExpireMinutes": 30,
    "RefreshTokenExpireDays": 1
  },
  "Recaptcha": {
    "SiteKey": "<recaptcha-site-key-admin>",
    "SecretKey": "<recaptcha-secret-key-admin>",
    "VerifyUrl": "https://www.google.com/recaptcha/api/siteverify"
  },
  "SessionEncryption": {
    "Key": "<clave-encriptacion-frontend-admin-256-bits>",
    "StorageMode": "IndexedDB"
  }
}
```

---

## 7. Design System

### 7.1 Filosofía
- **Estilo:** Samsung One UI + Bento Grid para tarjetas minimalistas.
- **Paleta:** Azul marino (`#1B263B`), blanco (`#FFFFFF`), grisáceo (`#F5F5F7`).
- **Bordes:** Redondeados amplios (12px-32px), estilo One UI.
- **Sombras:** Sutiles, basadas en azul marino (`rgba(27,38,59,0.06)`).
- **Layout:** Bento Grid modular con tarjetas de diferentes tamaños.
- **Documentación completa:** Ver `docs/DESIGN_SYSTEM.md`.

### 7.2 Variables CSS Principales
```css
--color-navy-primary: #1B263B;
--color-navy-medium: #2C3E5C;
--color-navy-light: #415A77;
--color-white: #FFFFFF;
--color-gray-light: #F5F5F7;
--color-gray-medium: #E0E0E5;
--radius-xl: 24px;        /* Tarjetas Bento */
--radius-2xl: 32px;       /* Modales */
--shadow-sm: 0 2px 8px rgba(27, 38, 59, 0.06);
--shadow-md: 0 4px 16px rgba(27, 38, 59, 0.08);
--bento-gap: 16px;
--font-primary: 'Inter', system-ui, sans-serif;
```

### 7.3 Sistema de Temas (Theming)
- **CSS y JS no se queman en las vistas.** No inline styles, no inline scripts en `.razor`.
- Los estilos se organizan en **carpetas de tema** independientes: `wwwroot/themes/{nombre}/theme.css`.
- El sistema puede **cambiar de tema dinámicamente** intercambiando solo el archivo `theme.css`.
- La estructura HTML y los componentes Blazor **permanecen idénticos** al cambiar de tema.
- CSS estructural en `wwwroot/css/` (base, components, bento-grid, responsive) usa **solo variables CSS**.
- JavaScript en `wwwroot/js/` (theme-switcher, bento-animations, device-fingerprint, recaptcha).
- `ThemeService.cs` en Blazor maneja el cambio de tema via JSInterop.
- Preferencia de tema persistida en localStorage.
- Ver `DESIGN_SYSTEM.md` sección 11 para especificación completa.

### 7.4 Aplicación
- Ambos frontends (Portal y Admin) usan el mismo design system y sistema de temas.
- Las variables CSS se definen en `wwwroot/themes/{tema}/theme.css`.
- El CSS estructural en `wwwroot/css/` nunca cambia, solo usa variables CSS.
- Ver `DESIGN_SYSTEM.md` para especificación completa.

---

## 8. Convenciones de Código

- **C# Naming:** PascalCase para clases y métodos, camelCase para variables locales.
- **Async/Await:** Todos los métodos de acceso a datos deben ser async.
- **DTOs:** Usar DTOs para comunicación entre capas, no exponer entidades directamente.
- **Mapeo:** AutoMapper para conversión Entity ↔ DTO.
- **Validación:** FluentValidation en la capa API.
- **Logging:** Serilog con sinks a Console y File.
- **Manejo de errores:** Middleware centralizado con ProblemDetails.
