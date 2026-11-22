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
