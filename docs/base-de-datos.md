# Base de datos — CONEX

## Motor y conexiones

| Grupo | Driver | BD | Uso |
|---|---|---|---|
| `default` | `MySQLi` | `conex` (localhost, puerto 3306) | Desarrollo local |
| `produccion` (2º grupo en `Config/Database.php`) | `MySQLi` | `db_aa03a4_conex` (SmarterASP, `mysql5044.site4now.net`) | Producción |

- Definidos en `app/Config/Database.php`; el `.env` puede sobreescribir
  `database.default.*` (vienen comentadas en la plantilla `env`).
- Documento de referencia del esquema: `docs/BD.md` (existente).

## Esquema — fuentes

- **44 migraciones** en `app/Database/Migrations/` (sept–oct 2026).
- Nomenclatura: **prefijo por módulo** (`auth_`, `cat_`, `emp_`, `cand_`,
  `vac_`, `post_`, `crm_`, `not_`, `ai_`, `cfg_`, `api_`, `enc_`, `inc_`,
  `srv_`).

## Tablas por módulo

### `auth_` — seguridad

| Tabla | Propósito |
|---|---|
| `auth_role` | Roles (candidato, empresa, asesor, admin) |
| `auth_user` | Usuarios |
| `auth_permission`, `auth_role_permission` | Permisos y RBAC |

### `cat_` — catálogos

`cat_categoria`, `cat_tipo_contrato`, `cat_habilidad`, `cat_nivel_educacion`,
`cat_nivel_experiencia`, `cat_requisito`, `cat_idioma` — catálogos del dominio.

### `emp_` / `cand_` — actores

| Tabla | Propósito |
|---|---|
| `emp_empresa` | Empresas reclutadoras |
| `cand_candidato` | Perfil del candidato |
| `cand_cv` | Currículums (archivos/versión) |
| `cand_experiencia`, `cand_educacion`, `cand_habilidad`, `cand_idioma` | Detalle del perfil |
| `cand_puesto_deseado` | Preferencias de puesto |
| `cand_vistas_perfil` | Visitas al perfil (métrica) |
| `cand_servicio` | Servicios del candidato/prestador |

### `vac_` — vacantes

`vac_vacante` (con campos geográficos y de captación), `vac_requisito`,
`vac_habilidad`.

### `post_` — postulación y verificación

| Tabla | Propósito |
|---|---|
| `post_postulacion` | Aplicaciones con puntaje de match |
| `post_historial` | Bitácora de estados de la postulación |
| `post_verificacion` | Proceso de verificación de 6 pasos |
| `post_documento` (modelo `DocumentoPostulacionModel`) | Documentos del candidato |

### `crm_` — comercial

`crm_plan` (Gratis/Básico/Premium), `crm_contrato`, `crm_contrato_version`,
`crm_pago`, `crm_lead`, `crm_lead_seguimiento`, `crm_lead_servicio`.

### `srv_` — servicios/prestadores (oct 2026)

`servicios_prestador`, `prestador_disponibilidad`, contactos y reportes de
servicio (`SrvContactoModel`, `SrvReporteModel`).

### Transversales

| Prefijo | Tablas | Propósito |
|---|---|---|
| `not_` | `not_notificacion` (+ noticias) | Notificaciones y noticias |
| `enc_` | `enc_*` | Encuestas y respuestas |
| `inc_` | `inc_incidencia` | Incidencias / atención al cliente |
| `ai_` | `ai_analisis_cv`, `ia_modelo` | Resultados del análisis con Gemini |
| `cfg_` | `cfg_configuracion` | Configuración del sistema |
| `api_` | `api_token` | Tokens de la app móvil |

## Notas

- ~38+ tablas según README raíz; las migraciones son la fuente de verdad.
- Sin prefijo de tabla global (`DBPrefix` vacío); el prefijo está en el nombre.
- La BD remota de producción la consume también la app móvil vía API
  (`https://conex.softlutionic.com/api`).
