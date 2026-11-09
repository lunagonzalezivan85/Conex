# CONEX — Plataforma de reclutamiento y selección de personal

Plataforma web que **conecta talento verificado con empresas**: la empresa
publica su vacante y accede a candidatos con expedientes digitales previamente
revisados. CONEX hace la búsqueda y preselección; la empresa decide a quién
entrevistar y contratar.

**Publicar → Consultar candidatos → Seleccionar → Entrevistar → Contratar**

Incluye análisis de CV con **Google Gemini** (compatibilidad candidato-vacante)
y una app móvil Android (`app-conex/`, Ionic 8 + React + Capacitor 7) que consume
la API del mismo proyecto.

## Stack

| Componente | Tecnología |
|---|---|
| Backend | PHP `^8.2` + CodeIgniter 4 (instalado: **v4.7.4**) |
| Base de datos | MySQL/MariaDB (`MySQLi`); local `conex`, producción `db_aa03a4_conex` |
| IA | Google Gemini API (`app/Libraries/GeminiAI`), `GEMINI_API_KEY` en `.env` |
| Librerías | `smalot/pdfparser` (extracción de texto de CVs en PDF) |
| Frontend web | HTML5 + CSS3 + JS vanilla (CSS/JS en archivos externos) |
| App móvil | `app-conex/`: Ionic 8 + React + Capacitor 7 (Android) |

## Roles de usuario

| Rol | Funcionalidad |
|---|---|
| **Candidato** | Perfil profesional, CV, postulaciones, búsqueda de vacantes, planes |
| **Empresa** | Publicar vacantes, ver postulantes, expedientes, verificación, planes |
| **Asesor** | Rol interno de acompañamiento/verificación (app) |
| **Admin** | Verificación de candidatos, usuarios, vacantes, noticias |

## Quickstart en XAMPP

1. Proyecto en `C:\xampp\htdocs\CONEX`.
2. `composer install` (existe `composer.phar` en la raíz).
3. Copiar `env` a `.env` y configurar:
   - `app.baseURL = 'http://localhost/CONEX/'`
   - `database.default.*` → BD local `conex` (o ajustar
     `app/Config/Database.php`, que ya trae `conex` por defecto)
   - `GEMINI_API_KEY` para el análisis de CV con IA.
4. Crear la BD `conex` y ejecutar `php spark migrate`
   (44 migraciones en `app/Database/Migrations/`).
5. Apache + MySQL desde XAMPP → `http://localhost/CONEX/`.

> El proyecto incluye `index.php` en la raíz (además de `public/`); el
> `.htaccess` de raíz gestiona el enrutamiento y el espejo de assets
> (`css/core/`, `js/core/` en raíz).

## Módulos principales

- **Autenticación** por roles (web + API tokens para la app).
- **Perfil del candidato**: datos, experiencia, educación, habilidades, idiomas,
  puesto deseado, CV.
- **Vacantes**: publicación, edición, cierre; requisitos y habilidades;
  geolocalización y captación.
- **Postulaciones** con puntaje de match e historial.
- **Verificación**: proceso de 6 pasos (asignación, contacto, documentos,
  entrevista, evaluación, presentación).
- **Análisis de CV con IA** (Gemini).
- **CRM**: planes (Gratis/Básico/Premium), contratos, pagos, leads y
  seguimiento, servicios.
- **Encuestas** de satisfacción, **noticias**, **incidencias** (atención al
  cliente), **catálogos** (`cat_*`).

## Variables de entorno usadas

- `CI_ENVIRONMENT`, `app.baseURL`
- `database.default.*` (comentadas en `.env`; la config por defecto en
  `app/Config/Database.php` ya apunta a `conex` local y al grupo de producción).
- `GEMINI_API_KEY` — API key de Google Gemini.

> No documentar valores reales de estas variables.

## Documentación adicional

- [`arquitectura.md`](arquitectura.md) — estructura MVC y módulos.
- [`base-de-datos.md`](base-de-datos.md) — esquema y tablas por prefijo.
- [`despliegue.md`](despliegue.md) — local y producción (IIS + FTP).
- `docs/PRD.md`, `docs/TRN.md`, `docs/BD.md`, `docs/APPFLOW.md`, `docs/AUTH.md`
  (existentes), `AGENT.md` (reglas UI/UX y build de la app).
