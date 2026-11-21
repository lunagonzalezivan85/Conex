# NEURONA — Mapa del proyecto CONEX

> Cómo se entrelaza todo: app Ionic/React ← → API CodeIgniter 4 ← → MySQL.
> Complemento de `AGENT.md` (reglas) y `UX-AUDIT.md` (bitácora de fixes).

## 1. Vista de 30.000 pies

```
┌─────────────────────┐   HTTPS+Bearer    ┌──────────────────────────┐   SQL    ┌───────────┐
│  app-conex/          │ ───────────────► │  app/ (CodeIgniter 4)     │ ───────► │  MySQL    │
│  Ionic 8 + React +   │  token en         │  sirve web + /api/*      │          │ conex     │
│  Capacitor 7         │  Capacitor        │                          │          │           │
│  (Android APK)       │  Preferences      │                          │          │           │
└─────────────────────┘                   └──────────────────────────┘          └───────────┘
        │                                            │
        └─ config.ts → VITE_API_URL                  └─ producción: IIS conex.softlutionic.com
           (default https://conex.softlutionic.com/api)   local: XAMPP http://localhost/CONEX/public/api
```

El mismo backend sirve **dos frontends**: el portal web PHP (`app/Views` + rutas web) y la app móvil (`app-conex`, solo consume `/api/*`).

## 2. `app-conex/` — App móvil (Ionic 8 + React + TS + Vite)

```
src/
├── config.ts                 → API_URL (única fuente del backend)
├── App.tsx                   → rutas /app/* + tab bar por rol (candidato/empresa)
├── main.tsx
│
├── context/AuthContext.tsx   → usuario/token global (CapacitorStorage.conex_token)
│
├── services/
│   ├── api.ts                → TODAS las llamadas: authApi, vacantesApi, serviciosApi,
│   │                           candidatoApi, empresaApi, usuarioApi, notificacionesApi.
│   │                           ApiError tipado; inyecta Authorization: Bearer.
│   └── cedulaScan.ts         → OCR de cédula (registro wizard)
│
├── pages/
│   ├── Splash.tsx            → arranque/branding
│   ├── Login.tsx             → portal de roles + login (usuario/password)
│   ├── Registro.tsx          → wizard candidato (escaneo cédula)
│   ├── RegistroEmpresa.tsx   → registro empresa (CxAutocomplete rubro/ciudad)
│   ├── Dashboard.tsx         → home: workers/vacantes según rol
│   ├── Buscar.tsx            → mapa + lista (vacantes O prestadores; también /contactos, /talentos)
│   ├── Categorias.tsx        → catálogo de rubros
│   ├── VacanteDetalle.tsx    → detalle + Postularme + modal "Consultar a la Empresa"
│   ├── MisVacantes.tsx       → CRUD vacantes empresa + modal postulantes (tinder actions)
│   ├── Solicitudes.tsx       → inbox: Recibidas/Enviadas/Empleos (postulaciones)
│   ├── ChatMessenger.tsx     → chat 1:1 por contacto_id (mensajes, cotización, finalizar)
│   ├── Perfil.tsx            → perfil candidato/empresa + modales de edición (bottom-sheet)
│   └── Planes.tsx            → suscripciones
│
├── components/
│   ├── ChatContactModal.tsx  → modal contacto/cotización inicial a prestador
│   └── CxAutocomplete.tsx    → reemplazo de IonSelect (string | {value,label}, strict, a11y)
│
├── utils/
│   ├── cxFeedback.ts         → useCxToast + useCxAlert (marca, NO nativos Ionic)
│   ├── text.ts               → toUpperName / toSentenceCase / toNameCase (regla AGENT)
│   └── categoriaIcons.ts     → iconos por rubro
│
└── theme/
    ├── variables.css         → TOKENS ÚNICOS: --cx-*, paleta Ionic, ion-card, dark
    ├── app.css               → estilos globales + cx-* (chat, inbox, cand, autocomplete)
    ├── auth.css / registro.css / buscar.css / splash.css
```

### Flujo de una pantalla típica
`Page` → `api.ts` (fetch + token) → `AuthContext` (rol/redirect) → CSS de marca (`--cx-*`). Feedback siempre vía `useCxToast`/`useCxAlert`.

## 3. `app/` — Backend CodeIgniter 4

```
app/
├── Config/Routes.php          → 3 mundos: web pública, web auth, /api/*
├── Filters/
│   ├── ApiAuthFilter          → valida Bearer token (api_token) → user
│   ├── ApiRateLimitFilter     → throttle por ruta
│   └── AuthFilter             → sesión web
│
├── Controllers/
│   ├── PublicController       → web pública (home, vacantes, servicios, noticias, descargas)
│   ├── AuthController         → login/registro web + Google OAuth
│   ├── CandidatoController    → panel web candidato
│   ├── EmpresaController      → panel web empresa
│   ├── AdminController        → backoffice web
│   ├── AgenteController / CrmController / NoticiaController
│   └── Api/                   → LO QUE USA LA APP (BaseApiController con validateApi/body/fail)
│       ├── AuthController     → login(usuario|email|tel,password)→token, register, me, notificaciones
│       ├── VacanteController  → listar/filtros/show/reaccionar/compartir (público auth)
│       ├── ServicioController → listar prestadores + contactar
│       ├── CandidatoController→ perfil, dashboard, CVs, postularse, solicitudes, chat (responderSolicitud)
│       ├── EmpresaController  → perfil, vacantes CRUD, postulaciones, iniciarChatPostulante
│       └── CvController       → descarga de CV
│
├── Models/                    → uno por tabla (allowedFields = whitelist)
│   ├── UserModel              → auth_user (login, rol, teléfono)
│   ├── CandidatoModel         → cand_candidato (perfil; teléfono está en auth_user!)
│   ├── EmpresaModel           → emp_empresa
│   ├── VacanteModel           → vac_vacante (incluye `moneda` NIO|USD)
│   ├── PostulacionModel       → post_postulacion (cv_id NULL = postulación con perfil)
│   ├── SrvContactoModel       → srv_contacto (hilo chat; estado incluye 'finalizado')
│   ├── SrvReporteModel        → srv_reporte
│   ├── CandServicioModel      → cand_servicio (prestador ofrece servicios)
│   ├── CVModel/AnalisisCVModel→ cand_cv + análisis IA
│   ├── ApiTokenModel          → api_token (Bearer)
│   └── catálogos/encuestas/leads/incidencias…
│
└── Database/Migrations        → incl. 2026-11-21-000001_SrvContactoFinalizado
                               (estado 'finalizado' + emisor_tipo 'sistema')
```

## 4. Cómo se entrelazan los flujos clave

### 4.1 Postulación (con o sin CV)
```
VacanteDetalle "Postularme" (modal con CVs o "Perfil CONEX")
 → candidatoApi.postularse → POST /api/candidato/postularse/:id
 → Api\CandidatoController::postularse → PostulacionModel.insert (cv_id NULL ok)
 → + crea srv_contacto (hilo) vinculado
 → aparece en: Solicitudes tab "Empleos" (candidato) y MisVacantes modal postulantes (empresa)
```

### 4.2 Chat empresa ↔ candidato
```
Empresa: MisVacantes → postulantes → btn chat (icono)
 → POST /api/empresa/iniciar-chat-postulante → srv_contacto (o reutiliza)
 → ChatMessenger?contacto_id=N
   · GET /api/candidato/solicitudes/N  → contacto + mensajes (srv_contacto_mensaje)
   · POST responderSolicitud {accion:'mensaje'|'finalizar'}
     - mensaje: inserta con emisor_tipo 'cliente'(empresa)/'prestador'(candidato)
     - finalizar (solo empresa): estado→'finalizado' + msg 'sistema' + 423 para ambos
   · badge "Contactado" = EXISTS msg emisor_tipo='cliente' (query en EmpresaController::postulaciones)
```

### 4.3 Vacantes con moneda
```
MisVacantes modal → pills NIO(C$)/USD($) + input con separador de miles
 → POST /api/empresa/vacantes {titulo: toSentenceCase, moneda, salario_min/max}
 → VacanteModel → vac_vacante.moneda
 → display formateado en MisVacantes, Buscar, Dashboard, VacanteDetalle
```

### 4.4 Búsqueda / mapa
```
Buscar.tsx (modo=vacantes|servicios) → vacantesApi.serviciosApi
 → /api/vacantes|servicios (con lat/lng → distancia_km)
 → cards + pins maplibre
```

### 4.5 Auth
```
Login/Registro → POST /api/login|register → ApiTokenModel → token
 → CapacitorStorage.conex_token → AuthContext.user (rol)
 → apiAuth filter en cada /api/* → App.tsx tab bar por rol
```

## 5. Esquema DB — entidades núcleo

```
auth_user ─┬─ cand_candidato (1:1; nivel_experiencia_id → cat_nivel_experiencia)
           │    ├─ cand_cv          (CVs subidos)
           │    ├─ cand_educacion, cand_experiencia, cand_habilidad, cand_idioma
           │    └─ cand_servicio    (ofrece servicios → aparece en Buscar prestadores)
           ├─ emp_empresa (1:1) ── vac_vacante (1:N; moneda, salarios)
           │                         └─ post_postulacion ←→ cand_candidato (cv_id nullable)
           ├─ api_token
           └─ srv_contacto ─┬─ cliente_id = empresa · prestador_id = candidato
                            └─ srv_contacto_mensaje (emisor_tipo: cliente|prestador|sistema)

NOTA: cand_candidato NO tiene telefono/anios_experiencia/salario_deseado/titulo_academico/cv_url
      → teléfono = auth_user.telefono · experiencia = cat_nivel_experiencia ·
        título = cand_educacion · cv = cand_cv · salario = salario_esperado_usd
```

## 6. Convenciones transversales (ver AGENT.md para reglas)

- **Marca/UX:** `--cx-*` tokens · One UI flat + bento · dark con `@media prefers-color-scheme`
- **Feedback:** `useCxToast`/`useCxAlert` (nunca IonToast/alert nativo)
- **Selects:** `CxAutocomplete` (nunca `IonSelect`)
- **Texto:** `toUpperName` persona/empresa · `toSentenceCase` títulos/profesiones · `toNameCase` lugares (`utils/text.ts`)
- **Formularios:** inputs apilados (1 columna), pill 48–52px, CTA naranja
- **Errores API:** `fail(msg, httpCode)` → frontend lee `err.error` vía `ApiError`
- **Build app:** `npm run build` → `npx cap sync android` → `gradlew.bat assembleDebug` (JDK 21)

## 7. Archivos de memoria del repo

| Archivo | Contenido |
|---|---|
| `AGENT.md` | Reglas permanentes de diseño/UX/build |
| `NEURONA.md` | Este mapa |
| `UX-AUDIT.md` | Bitácora de hallazgos y fixes (Rondas 1–5) |
| `plan-trabajo.md` | Plan maestro del proyecto |
