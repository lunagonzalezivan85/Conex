# APPFLOW - Flujo de la Aplicación

## OpenToWork - Diagramas de Flujo

**Versión:** 1.0  
**Fecha:** Agosto 2026  

---

## 1. Flujo General de la Aplicación

```
┌──────────────────────────────────────────────────────────┐
│                    PÁGINA DE INICIO                       │
│                                                          │
│            "¿Qué deseas hacer?"                          │
│                                                          │
│     ┌──────────────┐        ┌──────────────┐            │
│     │ Buscar Empleo│        │  Contratar   │            │
│     │ (Candidato)  │        │  (Empresa)   │            │
│     └──────┬───────┘        └──────┬───────┘            │
└─────────────┼──────────────────────┼────────────────────┘
              │                      │
              ▼                      ▼
     ┌────────────────┐     ┌────────────────┐
     │  ¿Tienes        │     │  ¿Tienes        │
     │  cuenta?        │     │  cuenta?        │
     └───┬───────┬─────┘     └───┬───────┬─────┘
         │Sí    │No             │Sí    │No
         ▼      ▼               ▼      ▼
     [Login] [Register]    [Login] [Register]
         │      │               │      │
         │      ▼               │      ▼
         │  [Wizard Multi-Paso] │  [Wizard Multi-Paso]
         │  (Ver 1.1)           │  (Ver 1.1)
         │      │               │      │
         ▼      ▼               ▼      ▼
     ┌────────────────┐     ┌────────────────┐
     │   DASHBOARD    │     │   DASHBOARD    │
     │   CANDIDATO    │     │    EMPRESA     │
     └────────────────┘     └────────────────┘
              │                      │
              │   ┌──────────┐       │
              └──▶│ Cambiar  │◀──────┘
                  │ Rol      │
                  └──────────┘
```

> **Nota:** La selección inicial es una preferencia, no una restricción. El usuario puede cambiar entre el dashboard de candidato y el de empresa en cualquier momento. Ambos roles están disponibles.

---

## 1.1 Wizard de Registro Multi-Paso

```
[Usuario completa registro básico: Email + Password]
        │
        ▼
[Wizard Paso 1: Datos Personales]
(Nombre, Apellido, Identificación, Teléfono,
 Fecha de nacimiento, Género)
        │
        ▼
[Wizard Paso 2: Ubicación]
(País, Ciudad, Dirección)
        │
        ▼
[Wizard Paso 3: Perfil Profesional]
(Título profesional, Resumen)
        │
        ▼
[Wizard Paso 4: Habilidades]
(Selección de skills de una lista)
        │
        ▼
[Wizard Paso 5: Preferencias]
"¿Qué deseas hacer?"
   ├── Buscar empleo
   ├── Crear vacantes
   └── Ambas
        │
        ▼
[Wizard Paso 6: Confirmación]
(Revisar todos los datos ingresados)
        │
        ▼
[¿Confirmar?]
   │
   ├── No → [Volver al paso a corregir]
   │
   └── Sí
        │
        ▼
[Guardar WizardCompleted = true]
        │
        ▼
[Redirigir al Dashboard según preferencia]
```

> **Reanudación:** Si el usuario abandona el wizard, `WizardStep` guarda el último paso completado. Al volver, el wizard reanuda desde ese paso.
>
> **Fase 2 (futura):** Se agregarán pasos de Experiencia laboral, Educación, Certificaciones y Subir CV después del paso 4.

---

## 2. Flujo de Candidato

### 2.1 Registro de Candidato

```
[Selecciona "Buscar Empleo"]
        │
        ▼
[¿Tienes cuenta?] ──No──▶ [Registro: Email + Password]
        │                        │
       Sí                        ▼
        │                 [Wizard Multi-Paso (Ver 1.1)]
[Iniciar Sesión]                │
        │                        ▼
        ▼                 [Wizard Completado]
[¿Wizard Completado?]           │
   │                             │
   ├── Sí ──▶ [Dashboard Candidato]
   │
   └── No ──▶ [Reanudar Wizard]
                  │
                  ▼
              [Dashboard Candidato]
```

> **Nota:** El usuario puede cambiar al dashboard de empresa en cualquier momento, incluso si se registró como candidato.

### 2.2 Dashboard del Candidato

```
┌─────────────────────────────────────────────┐
│            DASHBOARD CANDIDATO               │
├─────────────────────────────────────────────┤
│                                             │
│  ┌─────────────┐  ┌─────────────┐          │
│  │ Mi Perfil   │  │ Buscar      │          │
│  │             │  │ Vacantes    │          │
│  └─────────────┘  └─────────────┘          │
│                                             │
│  ┌─────────────┐  ┌─────────────┐          │
│  │ Mis         │  │ Notificacio-│          │
│  │ Solicitudes │  │ nes         │          │
│  └─────────────┘  └─────────────┘          │
│                                             │
└─────────────────────────────────────────────┘
```

### 2.3 Aplicar a una Vacante

```
[Buscar Vacantes]
        │
        ▼
[Lista de Vacantes] ──▶ [Filtrar por: Título, Ubicación,
        │                Salario, Tipo de Contrato]
        ▼
[Seleccionar Vacante]
        │
        ▼
[Detalle de Vacante]
        │
        ▼
[¿Aplicar?] ──No──▶ [Volver a la lista]
        │
       Sí
        ▼
[Formulario de Solicitud]
(Carta de presentación opcional)
        │
        ▼
[Confirmar Solicitud]
        │
        ▼
[Solicitud Enviada ✓]
(Estado: Pendiente)
        │
        ▼
[Ver en "Mis Solicitudes"]
```

### 2.4 Mis Solicitudes

```
[Mis Solicitudes]
        │
        ▼
[Lista de Solicitudes]
        │
        ├──▶ [Pendiente]    → Esperando respuesta
        ├──▶ [En Revisión]  → Empresa revisando
        ├──▶ [Aceptada]     → ✓ Felicidades!
        └──▶ [Rechazada]    → Seguir buscando
```

---

## 3. Flujo de Empresa

### 3.1 Registro de Empresa

```
[Selecciona "Contratar"]
        │
        ▼
[¿Tienes cuenta?] ──No──▶ [Formulario de Registro]
        │                        │
       Sí                        ▼
        ▼                 [Datos de Empresa]
[Iniciar Sesión]         (Nombre, Email, Password,
        │                 Descripción, Sitio Web,
        ▼                 Logo, Ubicación)
[Dashboard Empresa]              │
                                 ▼
                         [Perfil de Empresa]
                                 │
                                 ▼
                         [Dashboard Empresa]
```

### 3.2 Dashboard de Empresa

```
┌─────────────────────────────────────────────┐
│            DASHBOARD EMPRESA                 │
├─────────────────────────────────────────────┤
│                                             │
│  ┌─────────────┐  ┌─────────────┐          │
│  │ Mi Empresa  │  │ Publicar    │          │
│  │             │  │ Vacante     │          │
│  └─────────────┘  └─────────────┘          │
│                                             │
│  ┌─────────────┐  ┌─────────────┐          │
│  │ Mis         │  │ Buscar      │          │
│  │ Vacantes    │  │ Candidatos  │          │
│  └─────────────┘  └─────────────┘          │
│                                             │
│  ┌─────────────┐  ┌─────────────┐          │
│  │ Solicitudes │  │ Notificacio-│          │
│  │ Recibidas   │  │ nes         │          │
│  └─────────────┘  └─────────────┘          │
│                                             │
└─────────────────────────────────────────────┘
```

### 3.3 Publicar Vacante

```
[Publicar Vacante]
        │
        ▼
[Formulario de Vacante]
(Título, Descripción, Requisitos,
 Salario Mín/Max, Ubicación,
 Tipo de Contrato)
        │
        ▼
[¿Publicar o Guardar Borrador?]
        │
        ├──▶ [Publicar] → Estado: Activa
        └──▶ [Borrador] → Estado: Borrador
                │
                ▼
        [Lista de Mis Vacantes]
```

### 3.4 Buscar Candidatos

```
[Buscar Candidatos]
        │
        ▼
[Filtros de Búsqueda]
(Nombre, Skills, Ubicación,
 Experiencia, Título)
        │
        ▼
[Lista de Candidatos]
        │
        ▼
[Seleccionar Candidato]
        │
        ▼
[Perfil del Candidato]
(Datos, Skills, Experiencia,
 Educación, CV)
        │
        ▼
[Ver Vacantes de la Empresa]
        │
        ▼
[Invitar a aplicar a vacante] (opcional)
```

### 3.5 Gestionar Solicitudes Recibidas

```
[Solicitudes Recibidas]
        │
        ▼
[Lista de Solicitudes por Vacante]
        │
        ▼
[Seleccionar Solicitud]
        │
        ▼
[Ver Perfil del Candidato]
        │
        ▼
[Acciones:]
        │
        ├──▶ [Marcar En Revisión]
        ├──▶ [Aceptar]  → Notificar al candidato
        └──▶ [Rechazar] → Notificar al candidato
```

---

## 4. Flujo de Autenticación - Portal Principal

```
[Usuario ingresa credenciales en Portal Principal]
        │
        ▼
[¿Es dispositivo conocido?]
        │
        ├── Sí ──▶ [Login normal sin captcha]
        │
        └── No ──▶ [Mostrar Google reCAPTCHA v2]
                   │
                   ▼
                   [¿Captcha válido?]
                   │
                  No → [Error: Captcha inválido]
                   │
                  Sí
        │
        ▼
[API Portal: POST /api/auth/login]  (Puerto 5000)
(Envía credenciales + captcha token + rememberMe)
        │
        ▼
[¿Credenciales válidas?]
        │
       No → [Error: Credenciales incorrectas]
        │
       Sí
        ▼
[¿rememberMe = true?]
        │
        ├── Sí → [Generar JWT (expira en 30 días) + Refresh Token]
        └── No  → [Generar JWT (expira en 60 min) + Refresh Token]
        │
        ▼
[Registrar dispositivo si es nuevo]
        │
        ▼
[Retornar Token + RefreshToken + Role]
        │
        ▼
[Frontend encripta datos con AES-256]
        │
        ▼
[Almacenar en localStorage encriptado / IndexedDB]
        │
        ▼
[Redirigir según Role]
        │
        ├──▶ Candidate → [Dashboard Candidato]
        └──▶ Company   → [Dashboard Empresa]
```

> **Nota:** El token generado por el Portal Principal NO es válido en el Portal Admin.

### 4.0.1 Flujo de Login con Google OAuth

```
[Usuario hace clic en "Iniciar sesión con Google"]
        │
        ▼
[Google Identity Services muestra popup de Google]
        │
        ▼
[Usuario autentica con Google]
        │
        ▼
[Google retorna ID Token]
        │
        ▼
[Frontend envía ID Token: POST /api/auth/google]
        │
        ▼
[API valida ID Token con Google]
        │
        ▼
[¿Email existe en BD?]
        │
        ├── Sí → [Generar JWT OpenToWork + Refresh Token]
        │
        └── No → [Crear usuario (rol Candidate) + Generar JWT]
        │
        ▼
[Registrar dispositivo]
        │
        ▼
[Retornar Token + RefreshToken + Role]
        │
        ▼
[Frontend encripta y almacena sesión]
        │
        ▼
[Redirigir a Dashboard Candidato]
```

### 4.0.2 Flujo de Refresh Token

```
[JWT expira en el frontend]
        │
        ▼
[¿Existe refresh token en almacenamiento?]
        │
       No → [Redirigir a Login]
        │
       Sí
        ▼
[POST /api/auth/refresh con refresh token]
        │
        ▼
[¿Refresh token válido y no expirado?]
        │
       No → [Eliminar datos de sesión → Redirigir a Login]
        │
       Sí
        ▼
[Generar nuevo JWT + Nuevo Refresh Token (rotación)]
        │
        ▼
[Invalidar refresh token anterior en BD]
        │
        ▼
[Frontend actualiza datos encriptados]
        │
        ▼
[Continuar navegación sin interrupción]
```

---

## 4.1 Flujo de Autenticación - Portal Administrativo

```
[Administrador ingresa credenciales en Portal Admin]
        │
        ▼
[Mostrar Google reCAPTCHA v2]  (OBLIGATORIO siempre)
        │
        ▼
[¿Captcha válido?]
        │
       No → [Error: Captcha inválido]
        │
       Sí
        ▼
[AdminAPI: POST /api/admin/auth/login]  (Puerto 5001)
(Envía credenciales + captcha token)
        │
        ▼
[¿Credenciales válidas?]
        │
       No → [Error: Credenciales incorrectas]
        │
       Sí
        ▼
[Generar JWT Token (Issuer: OpenToWork.Admin, 30 min)]
+ Refresh Token (1 día)
        │
        ▼
[Retornar Token + RefreshToken + Role (Admin)]
        │
        ▼
[AdminWEB encripta datos con AES-256]
        │
        ▼
[Almacenar en IndexedDB]
        │
        ▼
[Dashboard Admin]
```

> **Nota:** El token generado por el Portal Admin NO es válido en el Portal Principal. El portal admin no tiene opción "Recordarme".

---

## 4.2 Flujo del Portal Administrativo

### Dashboard Admin

```
┌─────────────────────────────────────────────┐
│            DASHBOARD ADMIN                  │
├─────────────────────────────────────────────┤
│                                             │
│  ┌─────────────┐  ┌─────────────┐          │
│  │ Gestión de  │  │ Gestión de  │          │
│  │ Usuarios    │  │ Vacantes    │          │
│  └─────────────┘  └─────────────┘          │
│                                             │
│  ┌─────────────┐  ┌─────────────┐          │
│  │ Gestión de  │  │ Gestión de  │          │
│  │ Candidatos  │  │ Empresas    │          │
│  └─────────────┘  └─────────────┘          │
│                                             │
│  ┌─────────────┐  ┌─────────────┐          │
│  │ Solicitudes │  │ Métricas y  │          │
│  │ (todas)     │  │ Estadísticas│          │
│  └─────────────┘  └─────────────┘          │
│                                             │
│  ┌─────────────┐                            │
│  │ Skills y    │                            │
│  │ Categorías  │                            │
│  └─────────────┘                            │
│                                             │
└─────────────────────────────────────────────┘
```

### Gestión de Usuarios (Admin)

```
[Gestión de Usuarios]
        │
        ▼
[Lista de Usuarios]
(Filtrar por rol, estado, búsqueda)
        │
        ▼
[Seleccionar Usuario]
        │
        ▼
[Acciones:]
        │
        ├──▶ [Activar/Desactivar]
        ├──▶ [Ver detalle]
        └──▶ [Eliminar]
```

### Gestión de Vacantes (Admin)

```
[Gestión de Vacantes]
        │
        ▼
[Lista de Vacantes]
(Filtrar por estado, empresa, fecha)
        │
        ▼
[Seleccionar Vacante]
        │
        ▼
[Acciones:]
        │
        ├──▶ [Cambiar Estado]
        ├──▶ [Ver detalle]
        └──▶ [Eliminar]
```

---

## 5. Mapa de Navegación (Rutas Blazor)

### Portal Principal - Candidato (OpenToWork.WEB - Puerto 5100)
| Ruta | Componente | Descripción |
|------|------------|-------------|
| `/` | Home | Selección inicial |
| `/login` | Login | Inicio de sesión (API Portal) |
| `/register/candidate` | RegisterCandidate | Registro candidato |
| `/candidate/dashboard` | CandidateDashboard | Dashboard |
| `/candidate/profile` | CandidateProfile | Mi perfil |
| `/candidate/vacancies` | VacancyList | Buscar vacantes |
| `/candidate/vacancy/{id}` | VacancyDetail | Detalle de vacante |
| `/candidate/applications` | MyApplications | Mis solicitudes |

### Portal Principal - Empresa (OpenToWork.WEB - Puerto 5100)
| Ruta | Componente | Descripción |
|------|------------|-------------|
| `/register/company` | RegisterCompany | Registro empresa |
| `/company/dashboard` | CompanyDashboard | Dashboard |
| `/company/profile` | CompanyProfile | Perfil empresa |
| `/company/vacancies` | MyVacancies | Mis vacantes |
| `/company/vacancies/new` | CreateVacancy | Publicar vacante |
| `/company/candidates` | CandidateSearch | Buscar candidatos |
| `/company/candidate/{id}` | CandidateDetail | Ver candidato |
| `/company/applications` | ReceivedApplications | Solicitudes recibidas |

### Portal Administrativo (OpenToWork.AdminWEB - Puerto 5101)
| Ruta | Componente | Descripción |
|------|------------|-------------|
| `/admin/login` | AdminLogin | Login admin (AdminAPI) |
| `/admin/dashboard` | AdminDashboard | Dashboard con métricas |
| `/admin/users` | AdminUsers | Gestión de usuarios |
| `/admin/candidates` | AdminCandidates | Gestión de candidatos |
| `/admin/companies` | AdminCompanies | Gestión de empresas |
| `/admin/vacancies` | AdminVacancies | Gestión de vacantes |
| `/admin/applications` | AdminApplications | Gestión de solicitudes |
| `/admin/skills` | AdminSkills | Gestión de skills |
| `/admin/stats` | AdminStats | Estadísticas y reportes |
