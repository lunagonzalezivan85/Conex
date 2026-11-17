# CONEX — Auditoría app vs. deck + Plan de trabajo

> Documento vivo. Se revisa cada semana en la reunión de 30 min.
> Base: `plan-socios.html` (metas) × código real en `c:\xampp\htdocs\CONEX` (auditado oct 2026).

---

## 1. Matriz de brechas — qué promete el deck vs. qué existe

| Promesa del deck | Estado en código | Brecha | Evidencia |
|---|---|---|---|
| ~~Foto del CV → perfil en 30 s~~ **QR de la cédula → perfil** | ✅ DECIDIDO | **Cambio de pitch:** la app no sube foto del CV — escanea el QR de la cédula, pre-llena nombre/apellidos y sigue con el wizard guiado. Cero IA, cero OCR, cero Gemini. La foto del CV queda como mejora futura opcional, no como entrada. **Ojo:** el pitch del deck se mantiene tal cual — para la persona el gesto es idéntico (abre cámara, apunta a su cédula, "le saca foto"). Lo que cambia es interno: se lee el QR, no se hace OCR de la foto. | `app-conex` ya tiene cámara vía Capacitor; campos `profesion`/`salario_esperado_usd`/`disponibilidad` existen en `cand_candidato` |
| Perfil auto-armado con IA | ✅ Sí — solo documentos | Funciona si el CV es PDF/DOCX digital (de computadora). El análisis llena datos personales, experiencias, educación, idiomas, habilidades vía `aplicarAnalisis`. | `aplicarAnalisis` l.795–900+ |
| Registro sin formularios | ⚠️ Parcial | El registro es wizard de 2 pasos + contraseña de 10+ con especial. No es "30 segundos"; y el CV no entra en el registro, va después en el panel. Para feria hay que registrar a 300 personas — este flujo no escala en campo. | `auth/registro.php` |
| Match automático | ✅ Sí | `calcularPuntajeMatch` existe: experiencia 40% + habilidades 35% + educación 25%. Falta confirmar si el candidato ve "sugeridas" y si al postular se dispara. | l.1714–1800 |
| Contacto protegido del prestador | ✅ Sí | `srv_contacto` con estados pendiente/aceptado/rechazado; el teléfono se revela solo al aceptar. | Migración l.116; `responderSolicitud` l.1557 |
| Sello prestador verificado | ⚠️ Parcial | Campo `cand_candidato.prestador_verificado` existe. Falta: quién lo pone (admin UI), badge visible en el directorio, y **quién verifica** (Operaciones por llamada). | Migración l.26 |
| Sello institucional "Egresado de / Validado por" | ❌ No existe | No hay tabla `aliado`/`institucion` ni campo en perfil. El diferencial del deck no está programado. | Sin migraciones `alia*`, `instit*`, `sello`, `badge` |
| Reporte de colocación al aliado | ❌ No existe | Es lo que se le entrega a INATEC/centros. Requiere: institución en perfil + estado "contratado" + reporte exportable. | Postulación enum sin `contratado`: l.constraint `aceptada` es lo más cercano |
| Dashboard semanal de métricas | ❌ No existe | `AdminController::index` solo cuenta `totalUsuarios`. La reunión de socios no tiene de dónde sacar los 3 números por área. | `AdminController` l.14–44 |
| Estado "contratado" en postulación | ❌ No existe | Sin él no hay KPI de colocación (la métrica estrella para aliados). | `PostPostulacion` enum: enviada/en_revision/aceptada/rechazada/descartada |
| Cobro (destacada / planes / reclutamiento) | ⚠️ Parcial | Tablas CRM existen (lead, contrato, pago) + `vac_vacante.destacada`. Falta: flujo de cobro real (manual transferencia está OK por ahora) + toggle de destacada + página de planes empresa. | Migraciones `CrmPlan`, `CrmContrato`, `CrmPago` |
| API móvil | ✅ Sí | API v1 completa: auth, perfil, CV, postulaciones, vacantes. La app es "cuando haya 500 usuarios" — no tocar antes. | `Routes.php` grupo `api` |
| Captación de vacantes externas | ✅ Sí | Vacantes con origen/contacto externo/borrador ya funcionan (Operaciones puede empezar hoy). | Migración `VacVacante` + admin CRUD |
| Directorio servicios + mapa | ✅ Sí | Filtros, mapa, `srv_reporte` antifraude, `contactos` counter, `destacado`. | Migración `ServiciosPrestador` |
| **Agente registra persona en campo (sin que ella se loguee)** | ⚠️ Base existe | El rol `asesor` ya existe y el candidato/persona no necesita cuenta propia para existir en BD. Falta: el flujo de registro proxy + credenciales entregables. | Rol `asesor` en `AdminController`/`AuthController` |
| **Métrica por agente: persona colocada** | ❌ No existe | No hay `registrado_por_asesor_id` en `cand_candidato`. Separar: registrada (esfuerzo) vs. **colocada** (resultado = la que se queda). | Sin campo de atribución en `cand_candidato` |
| **Métrica por agente: empresa colocada** | ✅ Sí | `crm_lead.asesor_id` + pipeline hasta estado `ganado`. La empresa ya sabe quién la trajo. | `CrmLead` l.62, 84 |
| **Métrica por agente: aliado colocado** | ❌ No existe | No hay entidad `aliado`/`institución`. Falta tabla `ali_aliado` con `asesor_id` + estado (contactado→activo). | Sin migraciones `ali*`/`instit*` |
| **Registro desde la APP del agente (binario separado, interno)** | ⚠️ API lista, falta app+endpoint | `app-conex/` existe como app pública (candidato/empresa). El agente tendrá **app propia separada** — mismas credenciales/rol `asesor`, pero otro binario, solo distribución interna (APK directo o track interno, no Play Store pública). Falta: proyecto `app-agente/` + endpoint `/api/agente/registrar`. | `Routes.php` grupo `api`, `Api\BaseApiController`, `app-conex/` como referencia de stack |
| **Portal de socios (solo lectura, funnel de resultados)** | ❌ No existe | Vista `/socios` gateada por login — muestra el funnel "N empresas captadas → Y se quedaron → N vacantes reales → N personas con empleo" + prestadores y aliados. Cero datos operativos, cero acciones: es la pantalla que se proyecta en reuniones. | `AdminController::index` solo cuenta usuarios |

**Resumen:** la plataforma ya cubre ~70% del pitch. Pero los 3 huecos que no existen son exactamente los que hacen la venta a aliados: **foto del CV**, **sello institucional** y **reporte de colocación**. Si el vendedor hace el demo hoy, la foto del CV se rechaza — el demo se cae en el momento clave.

---

## 2. Backlog técnico (Tecnología) — ordenado por "qué desbloquea la ruta"

### Sprint A — "Que el demo no se caiga" (semana 1–2)
> Sin esto, Alianzas no puede salir. Prioridad absoluta.

| # | Tarea | Por qué | Est. |
|---|---|---|---|
| ~~A1~~ | ~~Aceptar imagen en `subirCV`~~ — **diferido**: el pitch ya no usa foto del CV | La entrada es la cédula QR. Subir foto era el camino caro. | — |
| ~~A2~~ | ~~Gemini multimodal (inline_data)~~ — **diferido**: no hay imagen que procesar | La cédula QR da los datos directo. Gemini queda para análisis de CV en web (ya funciona con texto). | — |
| ~~A3~~ | ~~PDF escaneado → imagen~~ — **diferido**: mismo motivo | — | — |
| A4 | **Wizard guiado tras cédula QR**: tras escanear → "Ahora contanos de vos" → profesión, qué busca, salario esperado → ¿ofrecés servicio? (skills por categoría). La IA ya no arma el perfil — las preguntas lo arman. | Esto ES el pitch nuevo: escanear → 4 preguntas → perfil listo. | 1 d |
| A5 | **App separada del agente** (`app-agente/`): proyecto Ionic React + Capacitor propio, clonando el stack de `app-conex` (AuthContext, api.ts, tema). Solo login con rol `asesor`. Pantallas: RegistrarPersona (**escaneo QR de cédula** → pre-llena nombre/apellidos → wizard de preguntas + opt-in de servicio), MisMetricas (sus 3 contadores), MisRegistros (lista con estado). Backend: `POST /api/agente/registrar-candidato` crea `auth_user` + `cand_candidato` con `registrado_por_asesor_id`, contraseña temporal; la persona recibe credenciales impresas o por WhatsApp. Distribución interna (APK/track interno). | Métricas del agente **no van en la app pública**: binario separado = 0 exposición a usuarios normales. En feria el agente es la interfaz; la persona nunca instala nada. | 3–4 d |
| A6 | `cand_candidato.registrado_por_asesor_id` (migración) + `agente_id` implícito del token API | La atribución es la base de la métrica por agente. Sin campo, no hay conteo. | ½ d |

### Sprint B — "Que la alianza tenga valor visible" (semana 3–4)
> Lo que se le muestra al aliado en la segunda reunión.

| # | Tarea | Por qué | Est. |
|---|---|---|---|
| B1 | Tabla `ali_aliado` (institución, tipo: centro técnico/universidad/gremio/feria, contacto, `asesor_id`, estado contactado→activo) + campo `institucion_id`/`egresado_de` en `cand_candidato` | Sin esto no hay sello, ni conteo por aliado, ni métrica "aliado colocado" del agente. | 1 d |
| B2 | Badge "Egresado de [X]" en tarjeta/perfil público + "Validado por [X]" en prestador cuando `prestador_verificado` y la institución que lo validó | El sello es el diferencial que el deck promete. | 1 d |
| B3 | Postulación: agregar estado `contratada` (migración enum) + botón "marcar contratado" en panel empresa + contador | Sin esto no hay métrica de colocación → no hay reporte ni story para el aliado. | ½ d |
| B4 | **Dashboard de métricas semanales** (admin): registros nuevos/semana, por institución, por oficio, vacantes captadas, postulaciones, contrataciones, solicitudes de servicio. Export CSV. | Es el tablero de la reunión de socios Y el reporte que se entrega al aliado. Una vista, dos usos. | 2 d |
| B6 | **MisMetricas en la app del agente**: sus 3 contadores — personas registradas vs. **colocadas** (postulación `contratada` o contacto de servicio `aceptado`), empresas ganadas (`crm_lead.estado='ganado'` y `asesor_id`), aliados activos (`ali_aliado.asesor_id` y estado activo). Más lista de sus registros con estado. Viven en `app-agente`, nunca en la pública. | "El que vale es el que se queda": el agente ve su resultado real, no solo su esfuerzo. Es también el incentivo del agente externo (promotor). | 1–2 d |
| B5 | Admin UI para `prestador_verificado` (toggle en lista de prestadores + nota de quién verificó) | Operaciones necesita marcar verificados sin tocar BD. | ½ d |
| B7 | **Portal de socios `/socios`** (solo lectura): funnel de 4 números en grande — empresas captadas → se quedaron (activas) → vacantes reales → personas empleadas — más prestadores verificados, aliados activos y tendencia semanal. Sin tablas, sin filtros, sin acciones: es la pantalla que se proyecta en la reunión. | Es lo que los socios quieren ver, no el admin. Una sola vista con 6 números grandes. | 1 d |

### Sprint C — "Cobrar y cerrar el círculo" (semana 5–8)
> Solo cuando ya hay oferta y aliados. No adelantar.

| # | Tarea | Por qué | Est. |
|---|---|---|---|
| C1 | Toggle `vac_vacante.destacada` en admin/empresa + pin en listado (primero + badge) | Monetización más simple, sin pasarela. | 1 d |
| C2 | Registro manual de pago (transferencia/efectivo): plan empresa, destacada, reclutamiento — con `crm_pago` existente | En Nicaragua B2B inicial se cobra por transferencia. Stripe es después. | 1–2 d |
| C3 | `srv_servicio.destacado` con precio + flujo manual de pago | Motor 2 monetiza tarde; tener el flag listo. | ½ d |
| C4 | Reporte PDF "Cuántos de sus egresados consiguieron trabajo" por institución — entregable mensual al aliado | Es la razón por la que el aliado renueva. | 1 d |
| C5 | Push/email "vacantes nuevas de tu oficio" (match) | Retención del candidato registrado en feria que nunca vuelve solo. | 1–2 d |

### No hacer todavía (deuda consciente)
- **App pública para candidatos** (`app-conex/` en tiendas): esperar 500 usuarios web. El agente usa su **app separada** (`app-agente/`, A5) — binario interno con más opciones y métricas privadas.
- Pasarela de pago en línea: transferencia manual alcanza para los primeros $.
- Match por IA generativa: el ponderado actual basta.
- Panel web del agente: el registro en campo es **solo desde su app** — no duplicar flujo en web. La web del asesor solo muestra métricas (lectura).

---

## 3. Plan operativo (los otros 3 roles) — no espera al código

> Esto puede arrancar en paralelo **desde el lunes**, mientras Tecnología hace el Sprint A. Pero cuidado: la promesa del pitch depende de A1–A4. Si el demo en vivo se ofrece, que sea después de Sprint A.

### Alianzas (Socio A) — semana 1 en adelante
- **Semana 1:** lista de 20 instituciones con nombre de contacto + teléfono (INATEC sedes, centros técnicos privados, oficinas de vinculación de universidades, calendario de ferias de alcaldía/MITRAB de los próximos 90 días).
- **Semana 2–3:** agendar y hacer 5 reuniones. Llevar deck + demo (si Sprint A listo). El cierre siempre es **una fecha de registro masivo**.
- **Semana 4+:** 1 feria/centro por semana. Objetivo: 2 fechas cerradas en mes 1.
- **Riesgo:** ir antes de A4 → el demo falla → pierde credibilidad institucional difícil de recuperar. Coordinar con Tecnología el "demo listo" antes de la primera reunión.

### Operaciones (Socio B) — semana 1 en adelante
- **Captación de vacantes: YA funciona.** Empezar hoy: 10 vacantes/semana desde Facebook → llamada → publicar. Sin esperar a nadie.
- Preparar el kit de registro de campo: **celular del agente con CONEX Agente (app interna)** — el agente registra, la persona nunca instala nada. Plan de datos, papel para entregar credenciales impresas, y plan B sin señal: número de cédula + nombre/teléfono anotado en papel → el agente lo carga después desde su app.
- **El gancho del stand es una mujer captando.** En feria la mayoría de asistentes son hombres: una captadora atractiva detiene el paso mejor que un banner. Y las mujeres, al ver a otra mujer captando, sienten la curiosidad y la confianza de acercarse a preguntar. No es decorado — es la primera conversión del embudo en campo: si nadie se detiene, no hay registro. Asignación: quien esté captando no registra; el agente con la app registra mientras ella capta.
- Definir el proceso de **verificación de prestador**: llamada + 1 referencia + checklist de 5 min. Quién firma.
- Ensayo interno del registro masivo: registrar a 10 personas del equipo/familia en 20 minutos. Si no sale en 20 min, no se va a feria.

### Comunidad (Socio C) — semana 1 en adelante
- Abrir páginas: Facebook, Instagram, TikTok. 3 piezas/semana.
- Contenido semana 1–2: (a) "escaneá tu cédula y quedás registrado" (el pitch nuevo), (b) "encontrá un electricista cerca", (c) vacante destacada real.
- Una vez haya 1 contratación real: la primera historia "Marvin consiguió trabajo" — el asset más valioso que existe.
- Material del vendedor: PDF de 1 página + QR al registro + banner de feria.

---

## 4. Dependencias críticas (lo que bloquea a quién)

| El socio… | no puede… | hasta que… |
|---|---|---|
| Alianzas | hacer el demo en vivo | Sprint A (A1–A4) |
| Alianzas | prometer sello "Egresado de" | B1 + B2 |
| Operaciones | registrar 300 personas en feria | A5 + A6 (app del agente + atribución) |
| Agente | ver sus contadores (persona/empresa/aliado colocada) | A6 + B1 + B3 + B6 |
| Operaciones | marcar prestador verificado | B5 |
| Comunidad | contar una historia de contratación real | B3 + la primera `aceptada→contratada` real |
| Socios (todos) | reportar sus 3 números semanales | B4 (dashboard) |
| Alianzas | entregar "reporte de colocación" al aliado | B1 + B3 + C4 |
| Cualquiera | cobrar | C1 + C2 |

---

## 5. Ritmo y control

- **Reunión semanal 30 min, mismo día/hora.** Cada rol trae 3 números. Sin números, no hay reunión.
- **Métrica del agente, dos pisos:** `registrada` (esfuerzo — el agente la metió al sistema) y `colocada` (resultado — postulación `contratada`, contacto de servicio `aceptado`, empresa `ganada`, aliado `activo`). **El que vale es el que se queda**: un agente con 100 registros y 0 colocaciones trae paja. El dashboard las separa y el incentivo (si hay comisión) se paga sobre colocada, no sobre registrada.
- **Semana 4 (día ~28): revisión.** Si un área va al <50% de su meta, se discute y se reasigna. Sin drama, con datos.
- **Día 90: decisión.** Con ~500 perfiles, ~80 vacantes, 5 aliados con fecha cumplida y el primer ingreso → se sigue y se busca capital. Sin eso, se replantea el modelo.
- Regla de ideas: toda idea nueva se escribe con (problema, dato, dueño). Sin dueño, no se ejecuta.

---

## 6. Ideas para cubrir el 100% del plan de socios

> Ideas evaluadas contra el estado real del código. Cada una dice qué slide del deck cierra y qué cuesta.

### Cerrar brechas de producto (las que ya están en sprints, reforzadas)

- **Registro por QR de la cédula + entrevista guiada** — ahora es el flujo principal (A4/A5), no una idea: la cédula nicaragüense trae QR — la app lo escanea y pre-llena nombre y apellidos **sin IA**. La mayoría en feria no trae CV; el flujo continúa con 4 preguntas guiadas: *¿a qué te dedicás?* → `profesion`; *¿qué te gustaría buscar?* → `sobre_mi`/`disponibilidad`; *¿cuánto esperás ganar?* → `salario_esperado_usd`. Los campos ya existen en `cand_candidato` — no hay nada nuevo que migrar. El perfil queda mínimo pero matcheable. Lo mismo sirve para el agente (escanea la cédula de la persona) que para auto-registro en la app pública.
- **El mismo wizard en `app-conex` para auto-registro** (cierra el 100% del onboarding sin agente): la persona escanea su propia cédula → la app pre-llena identidad → "Ahora contanos de vos" → wizard de preguntas (profesión, qué busca, salario esperado) → último paso: *"¿Ofrecés algún servicio?"* Sí/No. Si **sí** → se vuelve prestador: `ofrece_servicios=1` y elige habilidades del catálogo **por categoría** (elige categoría → aparece el bolso de `cat_habilidad` de esa categoría → marca las suyas con nivel). Todo ya existe en BD: `cat_categoria`→`cat_habilidad.categoria_id` (FK), `cand_habilidad` con `nivel`, `ofrece_servicios`. Solo falta el wizard UI + endpoint. Si **no** → queda como candidato normal. Así la app del candidato se convierte en el mismo motor de captación que tiene el agente — la feria es solo un caso de uso; la descarga orgánica es el otro. +1–1½ días sobre el wizard base.
- **Credencial imprimible** (cierra A5): al registrar, la app del agente genera una tarjeta con usuario + contraseña temporal + QR a `conex.ni/ingresar`. El agente la muestra, la persona le saca foto con su teléfono. Cero papel, cero app instalada. ~½ día extra sobre A5.
- **Registro sin señal** (cierra A5 en ferias reales): `app-agente` guarda el registro en cola local y lo sube al recuperar señal. En Managua hay zonas sin datos. ~1 día extra — vale la pena si la primera feria falla sin esto.
- **Auto-postulación del agente** (cierra "la IA conecta"): desde `app-agente`, el agente ve las 3 vacantes que hacen match con la persona que acaba de registrar y la postula él mismo. La persona sale de la feria **ya postulada** — el efecto wow en vivo. Necesita `calcularPuntajeMatch` expuesto por API. +½ día sobre A5/B6.
- **Pipeline de servicio → vacante** (cierra el doble motor): un prestador con `srv_servicio` activo que marca "busco empleo fijo" entra automáticamente a la bolsa como candidato. Una flag en el perfil, no un flujo nuevo. +½ día.

### Cerrar brechas de métricas y confianza

- **Foto del "momento colocado"** (cierra slide "Marvin consiguió trabajo"): cuando una postulación pasa a `contratada`, el agente recibe notificación para subir foto del trabajador en su primer día (con consentimiento). Esa foto + nombre + oficio + aliado = la tarjeta de historia que Comunidad publica. Sin código nuevo — es un proceso de Operaciones sobre B3. Gratis, hágase.
- **Tasa de permanencia real** (la métrica que el deck promete pero nadie tiene): postulación `contratada` + check a los 30 días ("¿sigue trabajando?") por el agente. El portal muestra "de 100 contrataciones, N siguen a los 30 días". Campo `verificada_30d` en postulación. +½ día y es la cifra que ningún competidor muestra.
- **Semáforo de anti-inflado**: el portal descuenta automáticamente empresas sin vacantes activas, perfiles sin actividad en 30 días y prestadores con reportes. Los números que ven los socios son netos, no brutos — eso es lo que los hace defendibles ante inversionistas. Lógica en el query, no en la BD. ~½ día sobre B4/B7.

### Cerrar el loop de aliados

- **Código de aliado en registro** (cierra el sello sin esperar a la entidad completa): mientras `ali_aliado` está en Sprint B, el registro del agente acepta un código corto ("INATEC-24") que se guarda como texto en `cand_candidato`. Cuando B1 migre, se resuelven a IDs. El sello empieza a contar desde la primera feria, no desde que termine el sprint. ~¼ día.
- **QR del aliado**: cada aliado activo obtiene su QR/URL con `?ref=CODIGO`; el registro público que entra por ahí hereda la institución. El aliado puede registrar a sus propios egresados sin depender del agente. Media página de código sobre el registro existente + A6. ~1 día.

### Cerrar ingresos (sin pasarela)

- **"Primera vacante gratis" como oferta del vendedor** (cierra cómo gana dinero): la primera publicación de empresa es gratis, la segunda es `$10` por destacada. No requiere código nuevo — es política sobre `vac_vacante` existente + C1. La decisión es de negocio, no técnica.
- **Paquete "feria" para el aliado** (producto vendible ya): por `$X` el aliado pone su logo en una jornada de registro — banner, QR propio, sello en perfiles. Es el primer ingreso institucional sin necesidad de planes de empresa. Solo requiere B1/B2 + factura manual (C2). Producto, no feature nueva.

### Orden sugerido si se suman

1. Credencial imprimible + código de aliado (van dentro de A5 — casi gratis ahora, caro después)
2. Registro sin señal (si hay feria antes de Sprint B)
3. Auto-postulación del agente (el wow del demo)
4. Check de 30 días + semáforo (cuando haya las primeras `contratada`)
5. Paquete feria + QR del aliado (cuando Alianzas tenga el primer sí)
