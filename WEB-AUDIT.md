# WEB-AUDIT — Auditoría web vs app (2026-11-22)

> Objetivo: misma experiencia/conexión que en la app Ionic — empresa↔candidato y prestador↔solicitante — en la web (CodeIgniter, `localhost:8080`).

## Entorno de pruebas local

- `CI_ENVIRONMENT=development` en `.env` → grupo `default` → DB local `conex`.
  **IMPORTANTE:** sin esta línea CI4 usa el grupo `production` y el server local
  toca la DB remota `db_aa03a4_conex`. (Bug encontrado y corregido.)
- Cuentas test: `iluna` (candidato, id=1), `techcorp@test.com` (empresa, id=2) — pass `Test1234!`
- Migraciones locales sincronizadas (`srv_reporte` creada a mano: `ServiciosPrestador` estaba parcial).

## Casos de uso auditados

| # | Flujo | Web antes | Estado |
|---|-------|-----------|--------|
| CU-1 | Empresa revisa postulantes y cambia estado (en_proceso/rechazado/contratado) | Funcionaba | OK |
| CU-2 | Empresa contacta candidato por chat interno | **NO existía** | IMPLEMENTADO |
| CU-3 | Candidato ve/responde mensajes de empresa | **NO existía** | IMPLEMENTADO |
| CU-4 | Cierre permanente de conversación + aviso sistema | **NO existía** | IMPLEMENTADO |
| CU-5 | Solicitante contacta prestador (srv_contacto) | Funcionaba (sin hilo) | + hilo |
| CU-6 | Prestador ve solicitudes, acepta/rechaza | Funcionaba | + link a chat |
| CU-7 | Prestador negocia/responde por chat | **NO existía en web** | IMPLEMENTADO |
| CU-8 | Notificación a la contraparte al escribir | **NO existía en web** | IMPLEMENTADO |

## Implementado (paridad con la app)

Modelo de datos compartido con la app: `srv_contacto` (conversación) +
`srv_contacto_mensaje` (hilo, emisor_tipo `cliente|prestador|sistema`).

- **`ChatController`** (`app/Controllers/ChatController.php`):
  - `index()` — lista de conversaciones del usuario (cliente o prestador)
  - `ver($id)` — hilo estilo messenger (permiso: ambas partes)
  - `enviar($id)` — mensaje con emisor_tipo por lado + notificación a la otra parte
  - `finalizar($id)` — cierre permanente + mensaje de sistema con nombre
  - `iniciarPostulante($id)` — empresa contacta desde postulación (auto-crea
    `srv_servicio` "Perfil profesional" igual que la app)
- **Rutas**: `/chat/{id}`, `/chat/{id}/mensaje`, `/chat/{id}/finalizar`,
  `/empresa/chat`, `/empresa/chat/iniciar/{id}`, `/candidato/mensajes`
- **Vistas**: `app/Views/chat/lista.php`, `app/Views/chat/hilo.php`,
  `public/css/core/chat.css` (vars de marca: navy/turquesa/naranja)
- **Sidebar**: link "Mensajes" en empresa y candidato
- **`postulante-detalle.php`**: botón "Mensaje" junto a las acciones de estado
- **`solicitudes.php`**: link "Ver conversación" por solicitud + estado `finalizado`
- **`PublicController::contactarPrestador`**: el mensaje inicial ahora también
  entra al hilo `srv_contacto_mensaje` (la API ya lo hacía)
- **`cxNombre()`** en `app/Common.php`: normaliza nombres en minúsculas
  ("ivan rafael" → "Ivan Rafael") en vistas de postulantes/chat

## Hallazgos resueltos

| Sev | Hallazgo | Fix |
|-----|----------|-----|
| Crit | Server local usaba DB remota de producción | `CI_ENVIRONMENT=development` en `.env` |
| Crit | Sin chat web en ninguna dirección | ChatController + vistas + rutas |
| Alta | Mensaje inicial de solicitud no entraba al hilo | insert en `contactarPrestador` |
| Media | Nombres en minúsculas ("Ivan rafael luna gonzalez") | helper `cxNombre` en vistas |
| Media | Migraciones "ya aplicadas" re-ejecutándose (class sin backslashes) | normalizado `migrations.class` |
| Baja | Label "Teléfono del cliente" incorrecto en chat empresa↔candidato | "Teléfono de contacto" |

## Verificado con Playwright

1. Empresa (techcorp) → postulante → "Mensaje" → `/chat/6` creado con mensaje inicial ✓
2. Empresa envía 2º mensaje → `emisor_tipo=cliente` ✓
3. Candidato (iluna) → `/candidato/mensajes` → ve "TechCorp Solutions S.A." con preview ✓
4. Candidato responde → `emisor_tipo=prestador` + notificación a empresa ✓
5. Finalizar → `estado=finalizado`, aviso sistema con nombre, input desaparece ✓
6. Solicitante (empresa) → `/servicios/p/1` → envía solicitud → `srv_contacto` + hilo ✓
7. Prestador acepta → teléfono revelado + link WhatsApp ✓
8. Prestador negocia con `precio_propuesto` (campo C$ solo visible al prestador) →
   guardado en hilo y en `srv_contacto.precio_propuesto` ✓
9. Solicitante ve y responde el hilo desde su propia lista de Mensajes ✓
10. Lista muestra nombre real de la contraparte (no solo el oficio) ✓

## Pendiente

- [ ] Badge de no-leídos en sidebar "Mensajes" (requiere flag `leida` en mensajes)
- [ ] Solicitantes anónimos (sin cuenta) siguen el flujo por teléfono — el chat
      requiere login, igual que en la app
- [ ] Deploy a producción cuando se confirme puntaje 100

---

## Ronda 3 — Chat contacto, buscar-empleo móvil, admin agentes

### Casos de uso nuevos

| CU | Actor | Flujo | Estado |
|----|-------|-------|--------|
| CU-11 | Cualquiera en chat | Contactar por tel/WhatsApp desde iconos fijos a la derecha del header (sin ver el número crudo) | IMPLEMENTADO |
| CU-12 | Candidato | Ver avatar/logo de empresa en `/candidato/buscar-empleo` sin hover; tarjetas usables a 390px | IMPLEMENTADO |
| CU-13 | Admin | Crear usuario con rol Asesor (u otro rol) desde `/admin/usuarios/crear` | IMPLEMENTADO |
| CU-14 | Admin | Asignar tareas (leads CRM) a un asesor desde su perfil `/admin/usuarios/perfil/{id}` | IMPLEMENTADO |
| CU-15 | Asesor | Login → `/agente` → ver leads asignados (`crm_lead.asesor_id`) | VERIFICADO |

### Hallazgos ronda 3

| Sev | Hallazgo | Fix |
|-----|----------|-----|
| Alta | Chat mostraba número de teléfono como texto en el hilo | Iconos `tel:`/`wa.me` fijos a la derecha del header (`chat/hilo.php`, `chat.css`), aria-label + title |
| Media | Avatar empresa solo aparecía al acercarse (notch) | Avatar siempre visible en tarjeta (`buscar-empleo.php` + CSS), responsive 390px |
| Alta | Admin no podía crear usuarios agente | `AdminController::crearUsuario/guardarUsuario` + vista `usuario-crear.php` + rutas |
| Alta | Sin forma directa de asignar leads a un asesor desde usuarios | `AdminController::asignarLead` + sección "Tareas de Agente" en `usuario-perfil.php` (tabla de leads asignados + select de pendientes) |
| Crit | Guard permitía asesor crear usuarios/asignar (role escalation: podría crear un admin) | Restricción `roleSlug==='admin'` en crearUsuario/guardarUsuario/asignarLead |
| Baja | Columnas crm_lead incorrectas en la vista (`nombre`, `email`) | `nombre_contacto`, `email_contacto`, `estado`, `empresa_nombre` |

### Verificado con Playwright (ronda 3)

1. Admin → `/admin/usuarios` → botón "Crear usuario" visible ✓
2. `/admin/usuarios/crear` → form apilado (rol, usuario, email, contraseña, nombre, apellido, teléfono) ✓
3. Crear `agente2` (rol Asesor) → `auth_user` id=20, role_id=2, estado=activo ✓
4. Perfil de agente2 → sección "Tareas de Agente" lista leads sin asignar ✓
5. Asignar lead #1 → `crm_lead.asesor_id=20` + flash "Lead #1 asignado a Maria Gomez" ✓
6. Login agente2 → `/agente` dashboard muestra lead "Pedro Ruiz" ✓
7. Agente2 intenta `/admin/usuarios/crear` → redirect a `/admin/crm` (bloqueado) ✓
8. Admin accede `/admin/usuarios/crear` → 200 (bug `role_slug`→`roleSlug` corregido) ✓

### Nota

El pipeline CRM existente (`/admin/crm`, CrmController) ya permite crear leads y
asignar `asesor_id` en alta/edición — la nueva sección en el perfil del usuario es
un atajo directo para "asignar tareas a agentes" sin pasar por el pipeline.

---

## Ronda 4 — Reporte de pruebas externo (mensajería, notificaciones, postulaciones, filtros)

### Resultado por punto del reporte

| Punto | Estado | Detalle |
|-------|--------|---------|
| A. Mensajes empresa↔postulante | RESUELTO | Chat web ya existía; faltaba acceso directo → **icono de mensaje en cada tarjeta de `/empresa/postulantes`** (POST `empresa/chat/iniciar/{id}`, sin salir de la lista) |
| B. Notificaciones | **ERA BUG REAL** | La app notificaba por API, pero la web: (1) no tenía NINGUNA UI para ver notificaciones, (2) 4 flujos web no insertaban notificación. Corregido abajo |
| C. Gestión de postulaciones | OK | Lista con estados, puntaje match, detalle con cambio de estado — verificado |
| D. Filtros de búsqueda | OK | Verificado contra DB: `modalidad=remoto`→2, `ciudad=Managua`→1, `categoria=2`→3, `q=Desarrollador`→2, `q` inválido→0 |

### Implementado ronda 4

1. **Centro de notificaciones web** (`/notificaciones`): campana en el topbar
   del panel (todos los roles) con badge de no-leídas + página con lista
   (icono por tipo, highlight de no-leída, link a `url`) + "marcar leídas".
   - `NotificacionesController` (index + leer)
   - `Views/notificaciones/lista.php`
   - Campana en `layouts/panel.php` + CSS `topbar-bell`/`topbar-bell-badge`
   - Helpers `cxNotificar()` y `cxNotificacionesNoLeidas()` en `Common.php`
2. **Inserts de notificación faltantes en flujos web** (paridad app):
   - `CandidatoController::postularse` → empresa: "Nueva postulación recibida"
   - `EmpresaController::cambiarEstadoPostulante` → candidato: copy por estado
     (contratado / en_proceso / no continúa) — misma redacción que la API
   - `PublicController::contactarPrestador` → prestador: "Nueva solicitud"
   - `CandidatoController::responderSolicitud` → solicitante registrado:
     aceptada/rechazada

### Verificado E2E (ronda 4)

1. iluna postula a vacante 1 → `not_notificacion` user_id=2 (techcorp) ✓
2. techcorp abre `/notificaciones` → campana badge 4, ítem con highlight ✓
3. "Marcar todas" → 0 no-leídas en DB ✓
4. Empresa cambia estado postulación → candidato recibe "en revisión" + url ✓
5. `/empresa/postulantes` muestra botón mensaje por tarjeta (aria-label) ✓
6. Filtros: todos los conteos coinciden con `vac_vacante` en DB ✓

### Pendiente (decisión de negocio, no bug)

- Match automático por rubro → reservado a planes pagos (free = filtros
  manuales). Confirmado por el equipo: postulante Y empresa pagan por
  filtros/match semi-automáticos. Prestadores de servicio sí son
  contactables; perfil sin servicio = privado.

---

## Ronda 5 — Bug 500 en perfil API + notificaciones en la app

### Hallazgos

| Sev | Hallazgo | Fix |
|-----|----------|-----|
| **Crit** | `POST /api/candidato/perfil` y `/api/empresa/perfil` devolvían **500** aunque los cambios SÍ se guardaban — `Call to undefined method userPayload()` (era `private` en `AuthController`, se llamaba desde Candidato y Empresa) | Movido a `BaseApiController::userPayload()` como `protected`; eliminado duplicado |
| Alta | App: la campana del Dashboard iba a `/app/solicitudes` — no existía pantalla de notificaciones | Nueva `pages/Notificaciones.tsx` + ruta `/app/notificaciones` + campana apunta ahí |

### App — página Notificaciones

- Lista con icono por tipo (mensaje=turquesa, postulación=naranja), no-leída
  con fondo turquesa suave + dot
- "Marcar leídas" en header (checkmark) — solo visible con no-leídas
- Pull-to-refresh, loading, empty state
- Tap → navega a la sección (`mapUrl`: chat→/app/chat, postulaciones→/app/solicitudes, postulantes→/app/mis-vacantes)

### Verificado

- `POST /api/candidato/perfil` → **200** con payload completo (antes 500)
- `POST /api/empresa/perfil` → **200** (mismo bug)
- `tsc --noEmit` + `npm run build` → OK
