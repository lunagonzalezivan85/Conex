# PRD - Product Requirements Document

## OpenToWork - Plataforma de Candidatos

**Versión:** 1.0  
**Fecha:** Agosto 2026  
**Autor:** Equipo OpenToWork  

---

## 1. Resumen Ejecutivo

OpenToWork es una plataforma de empleo que conecta candidatos con empresas. La plataforma ofrece dos roles principales: **Buscar Empleo** (candidatos) y **Contratar** (empresas/reclutadores). El sistema permite registrar candidatos, buscar perfiles, gestionar vacantes y procesar solicitudes de empleo.

---

## 2. Objetivos del Producto

- Facilitar la conexión entre candidatos y empresas.
- Permitir a los candidatos registrar y gestionar su perfil profesional.
- Permitir a las empresas publicar vacantes y buscar candidatos.
- Gestionar el flujo de solicitudes de vacantes.
- Proporcionar una interfaz intuitiva y moderna construida con Blazor.

---

## 3. Roles de Usuario

| Rol | Descripción |
|-----|-------------|
| **Candidato** | Busca empleo, registra su perfil, aplica a vacantes. |
| **Empresa/Reclutador** | Publica vacantes, busca candidatos, gestiona solicitudes. |
| **Administrador** | Gestiona usuarios, categorías y configuración del sistema. |

> **Multi-rol:** Un usuario puede ser Candidato y Empresa simultaneamente. La selección inicial ("Buscar Empleo" vs "Contratar") es una preferencia, no una restricción. El usuario puede cambiar entre ambos dashboards en cualquier momento.

---

## 4. Funcionalidades Principales

### 4.1 Pantalla de Selección Inicial
Al ingresar a la plataforma, el usuario selecciona:
- **"Quiero buscar empleo"** → Preferencia: Candidato.
- **"Quiero contratar"** → Preferencia: Empresa/Reclutador.

> **Nota:** La selección es una preferencia inicial, no una restricción. El usuario puede realizar ambas acciones (buscar empleo y crear vacantes) independientemente de su selección inicial.

### 4.2 Wizard de Registro
Después del registro/primera entrada, el usuario completa un **wizard multi-paso**:

**Paso 1 - Datos Personales:** Nombre, Apellido, Identificación, Teléfono, Fecha de nacimiento, Género.
**Paso 2 - Ubicación:** País, Ciudad, Dirección.
**Paso 3 - Perfil Profesional:** Título profesional, Resumen.
**Paso 4 - Habilidades:** Selección de skills.
**Paso 5 - Preferencias:** "¿Qué deseas hacer?" → Buscar empleo / Crear vacantes / Ambas.
**Paso 6 - Confirmación:** Revisar y confirmar datos.

> El wizard guarda el progreso (`WizardStep`) para reanudar si el usuario abandona. En la Fase 2 se agregarán pasos de Experiencia, Educación, Certificaciones y CV.

### 4.3 Gestión de Candidatos
- **Registrar candidato:** Vía wizard (Nombre, Apellido, Identificación, Teléfono, Email, etc.).
- **Editar perfil de candidato.**
- **Buscar candidatos:** Por nombre, habilidades, experiencia, ubicación. [Fase 2]
- **Ver detalle de candidato.** [Fase 2]

### 4.4 Gestión de Vacantes
- **Crear vacante temporal:** Título, descripción, requisitos, salario, ubicación, tipo de contrato. Las vacantes son temporales con fecha de expiración. [Fase 1]
- **Publicar vacante permanente:** Convertir vacante temporal en permanente asociada a empresa. [Fase 2]
- **Editar/Eliminar vacante.**
- **Listar vacantes disponibles.** [Fase 2]
- **Buscar vacantes por criterios.** [Fase 2]

### 4.5 Solicitud de Vacantes [Fase 2]
- **Aplicar a una vacante:** El candidato envía su solicitud.
- **Ver solicitudes recibidas** (empresa).
- **Ver solicitudes enviadas** (candidato).
- **Cambiar estado de solicitud:** Pendiente, En revisión, Aceptada, Rechazada.

### 4.6 Autenticación y Autorización (Portal Principal)
- Registro de usuarios (candidato y empresa).
- Inicio de sesión con opciones de seguridad:
  - **Mantener sesión activa:** El usuario puede marcar "Recordarme" para extender la duración del JWT.
  - **Captcha en login:** Se muestra captcha (reCAPTCHA v2) cuando el login se realiza desde un dispositivo diferente al registrado.
  - **Detección de dispositivo:** El sistema registra el dispositivo/navegador del usuario en el primer login y compara en los siguientes.
- **Login con Google OAuth:** Integración con Google Sign-In como método alternativo de autenticación.
- Recuperación de contraseña.
- Roles y permisos (Candidate, Company).
- **API independiente:** La autenticación del portal principal es gestionada por `OpenToWork.API` de forma aislada.

### 4.7 Portal Administrativo
- **Autenticación independiente:** Login exclusivo para administradores gestionado por `OpenToWork.AdminAPI`.
- **Captcha obligatorio:** El portal admin requiere captcha en todos los logins (sin excepción).
- Gestión de usuarios (candidatos y empresas): activar, desactivar, eliminar.
- Gestión de vacantes: moderar, eliminar, destacar.
- Gestión de solicitudes: ver todas, filtrar, exportar.
- Gestión de categorías y skills.
- Dashboard con métricas y estadísticas.
- Configuración del sistema.
- **Seguridad aislada:** JWT con clave, issuer y audience distintos al portal principal.

### 4.8 Seguridad de Datos de Sesión en el Frontend
- **Almacenamiento encriptado:** Los datos de sesión (token JWT, datos del usuario) almacenados en `localStorage` deben estar **encriptados** usando AES-256.
- **Alternativa IndexedDB:** Como opción más segura, los datos de sesión pueden guardarse en **IndexedDB** en lugar de `localStorage`.
- **No almacenar tokens en texto plano** bajo ninguna circunstancia.
- **Limpieza automática:** Al cerrar sesión o expirar el token, se eliminan todos los datos del almacenamiento.

### 4.9 SharedUI (Componentes Compartidos)
- **Razor Class Library:** `OpenToWork.SharedUI` contiene componentes Blazor compartidos entre el Portal Principal y el Portal Admin.
- **Componentes compartidos:** BentoCard, Button, Input, Modal, Badge, Table, Wizard, ThemeSwitcher.
- **Layouts compartidos:** MainLayout, AuthLayout.
- **Páginas compartidas:** Login, Register, Wizard de registro.
- **Ventaja:** Un solo lugar para mantener componentes UI, garantizando consistencia visual entre portales.

### 4.10 Internacionalización (i18n)
- **Multi-idioma:** El sistema soporta Español (`es`) e Inglés (`en`) inicialmente.
- **Archivos de idioma:** Las traducciones se organizan en `wwwroot/config/language/{lang}/{section}.json` (common, auth, wizard, dashboard, vacancies, profile, validation, errors).
- **No texto hardcoded:** Prohibido texto quemado en archivos `.razor`. Todo texto debe usar claves de idioma (ej: `Lang.T("common.buttons.save")`).
- **Idioma por usuario:** El idioma preferido se guarda en `SY_UserPreferences.Language`.
- **Cambio dinámico:** El usuario puede cambiar de idioma sin recargar la página.
- **Persistencia:** El idioma se guarda en localStorage y en la base de datos.
- **Fallback:** Si una clave no existe en el idioma activo, se busca en español (`es`).
- **Idiomas futuros:** Portugués (`pt`), Francés (`fr`). La estructura permite agregar idiomas sin cambios en código.

---

## 5. Requisitos No Funcionales

| Requisito | Descripción |
|-----------|-------------|
| **Rendimiento** | Tiempo de respuesta < 2 segundos para operaciones CRUD. |
| **Escalabilidad** | Arquitectura en capas para escalar horizontalmente. |
| **Seguridad** | JWT con "Recordarme", captcha por dispositivo diferente, Google OAuth, datos de sesión encriptados en localStorage/IndexedDB, encriptación de contraseñas, validación de inputs. |
| **Usabilidad** | UI responsive con Blazor. Estilo Samsung One UI + Bento Grid, paleta azul marino/blanco/grisáceo, bordes redondeados amplios, sombras sutiles. Compatible con móviles y desktop. |
| **Disponibilidad** | 99.5% uptime. |

---

## 6. Arquitectura del Proyecto

El proyecto está dividido en las siguientes capas con **dos APIs independientes**:

| Capa | Descripción |
|------|-------------|
| **API (Portal Principal)** | API REST para candidatos y empresas. Autenticación JWT independiente. |
| **AdminAPI (Portal Admin)** | API REST independiente para administradores. Autenticación JWT con configuración propia y aislada. |
| **WEB (Portal Principal)** | Aplicación Blazor para candidatos y empresas. |
| **AdminWEB (Portal Admin)** | Aplicación Blazor independiente para administradores. |
| **SHARED** | Clases compartidas entre capas (DTOs, constantes, enums). |
| **Core** | Lógica de negocio, servicios, reglas de dominio. |
| **Models** | Entidades del dominio, modelos de datos. |

> **Nota de seguridad:** Las APIs (`OpenToWork.API` y `OpenToWork.AdminAPI`) son completamente independientes: tienen su propio proceso, su propio puerto, su propia configuración JWT (clave, issuer, audience distintos) y sus propios controladores. Esto garantiza que un compromiso de seguridad en una API no afecte la otra.

---

## 7. Stack Tecnológico

- **Lenguaje:** C# (.NET 8)
- **Framework UI:** Blazor (dos apps independientes: portal y admin)
- **APIs:** ASP.NET Core Web API (dos instancias independientes: `OpenToWork.API` y `OpenToWork.AdminAPI`)
- **ORM:** Entity Framework Core
- **Base de datos:** SQL Server (compartida entre ambas APIs, acceso de solo lectura/escritura limitada para admin)
- **Autenticación:** JWTBearer (configuración independiente por API)
- **Documentación API:** Swagger/OpenAPI (una instancia por API)

---

## 8. Entregables

1. Documentación completa (PRD, TRN, APPFLOW, Plan de Proyecto, Implementación).
2. Solución Visual Studio con capas separadas y dos APIs independientes.
3. API REST del portal principal funcional con Swagger.
4. API REST del portal administrativo funcional con Swagger.
5. Aplicación Blazor del portal principal con pantallas de candidato y empresa.
6. Aplicación Blazor del portal administrativo con gestión del sistema.
7. Base de datos con migraciones.
8. Pruebas unitarias e integración para ambas APIs.

---

## 9. Criterios de Aceptación

- El usuario puede seleccionar entre "buscar empleo" o "contratar".
- Un candidato puede registrarse, editar su perfil y aplicar a vacantes.
- Una empresa puede publicar vacantes, buscar candidatos y gestionar solicitudes.
- La API del portal principal expone todos los endpoints CRUD documentados en Swagger.
- La API del portal administrativo expone endpoints de gestión independientes con su propio Swagger.
- Las dos APIs son independientes: puertos, JWT y procesos separados.
- Un administrador puede gestionar usuarios, vacantes y solicitudes desde el portal admin.
- Las interfaces Blazor (portal y admin) son responsive y funcionales.
