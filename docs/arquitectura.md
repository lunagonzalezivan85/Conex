# Arquitectura — CONEX

## Patrón general

CodeIgniter 4 (MVC). Dos superficies: **web** (vistas PHP por rol) y **API**
(JSON para la app móvil `app-conex/`).

```
app/
├── Controllers/     Web: Admin, Auth, Candidato, Crm, Empresa, Noticia,
│   │                Public, Home
│   └── Api/         Auth, Base (tokens), Candidato, Cv, Empresa, Servicio,
│                    Vacante
├── Models/          ~30 modelos (UserModel, CandidatoModel, VacanteModel, …)
├── Views/           Por rol: auth/, candidato/, empresa/, admin/, public/,
│                    layouts/ (panel.php, publico.php), shared/
├── Libraries/       GeminiAI (análisis de CV)
├── Filters/         Filtros de autenticación/rol
├── Config/          Routes, Database (grupo default + producción)
└── Database/        Migrations/ (44 migraciones)
```

Convenciones del proyecto: CSS y JS **siempre en archivos** (`public/css/core/`,
`public/js/core/`; espejo en `css/core/`, `js/core/` de la raíz vía
`.htaccess`); sin estilos inline ni scripts embebidos; variables CSS de marca
(`--brand-orange`, `--brand-teal`, `--brand-navy`); tablas con prefijo por
módulo.

## Flujo del request

- **Web:** `index.php` (raíz) → `Routes.php` → filtros de sesión/rol →
  controlador del rol (Candidato/Empresa/Admin/Public) → modelos → vista del
  layout `panel` o `publico`.
- **API:** rutas `/api/*` → `Api/BaseApiController` (auth por token,
  tabla `api_tokens`) → controladores REST → JSON. La app Ionic consume
  `…/api` en producción.
- **Análisis IA:** carga/procesamiento de CV → `Libraries/GeminiAI` →
  `smalot/pdfparser` extrae texto del PDF → Gemini devuelve evaluación →
  resultados en tablas `ai_*`.

## Módulos (por dominio)

### Acceso y administración

| Controlador | Propósito |
|---|---|
| `AuthController`, `Api/AuthController` | Login/registro web y por API |
| `AdminController` | Verificación de candidatos, usuarios, vacantes |
| `CrmController` | Planes, contratos, pagos, leads, servicios |
| `NoticiaController` | Publicación de noticias |
| `PublicController`, `Home` | Páginas públicas / landing |

### Operación principal

| Controlador | Propósito |
|---|---|
| `CandidatoController` (web + Api) | Perfil, CV, experiencia, educación, habilidades, idiomas, postulaciones |
| `EmpresaController` (web + Api) | Vacantes, postulantes, expedientes, planes |
| `Api/CvController` | Carga y análisis de CV (IA) |
| `Api/VacanteController` | Vacantes para la app |
| `Api/ServicioController` | Servicios/prestadores |

### Modelos relevantes

- Identidad: `UserModel`, `ApiTokenModel`, `ConfiguracionModel`.
- Candidato: `CandidatoModel`, `CVModel`, `ExperienciaModel`, `EducacionModel`,
  `HabilidadModel`, `IdiomaModel`, `CandServicioModel`.
- Empresa/vacantes: `EmpresaModel`, `VacanteModel`, `CategoriaModel`.
- Proceso: `PostulacionModel`, `DocumentoPostulacionModel`,
  `VerificacionProcesoModel`, `EntrevistaEvaluacionModel`.
- IA: `AnalisisCVModel`, `IaModeloModel`.
- CRM: `LeadModel`, `LeadSeguimientoModel`, `LeadServicioModel`,
  `ContratoVersionModel`, `SrvContactoModel`, `SrvReporteModel`.
- Otros: `NoticiaModel`, `EncuestaModel`, `EncuestaRespuestaModel`,
  `IncidenciaModel`.

## App móvil (`app-conex/`)

Ionic 8 + React + Capacitor 7 → APK Android
(`app-conex/android/.../app-debug.apk`). Build: `npm run build` →
`npx cap sync android` → `gradlew assembleDebug`. Consume la API CI4; diseño
"One UI + bento" documentado en `AGENT.md`.

## Integraciones externas

- **Google Gemini API** — evaluación de compatibilidad CV ↔ vacante.
- **smalot/pdfparser** — texto de CVs en PDF.
- **Tracklink**: no aplica. Correo: `Config/Email.php`.
