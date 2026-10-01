# Plan de desarrollo — Feature "CV → Perfil → Match"

Feature para feria de empleo: el usuario sube foto/PDF de su CV, el sistema extrae los datos
con IA, prellena su perfil y le hace match contra las vacantes existentes.

**Prioridad:** primero WEB (CI4 + vistas del sitio), luego APP (Ionic/React consume los
mismos endpoints). El backend es compartido — se construye una vez.

**Prerequisito:** API key de Gemini (Google AI Studio, gratis ~1500 req/día).
Va en `.env` del backend, NUNCA en el app ni en el repo.

---

## FASE 0 — Backend (CI4, compartido web+app) — ~3-4 días

- [ ] `GEMINI_API_KEY` en `.env` + config en `app/Config/`
- [ ] Servicio `GeminiService`: enviar imagen/PDF → prompt con JSON schema → parsear respuesta
  - Schema: `{nombre, apellido, email, telefono, profesion, ciudad, anios_experiencia, educacion[], experiencia[], skills[]}`
  - Timeout corto, retry 1x, error claro si el CV es ilegible
- [ ] `POST /api/cv/parse` — recibe foto/PDF, devuelve JSON estructurado (no guarda aún)
- [ ] `POST /api/cv/confirmar` — recibe JSON revisado + archivo → guarda CV + actualiza campos de `candidatos`
- [ ] `GET /api/vacantes/match` — scoring por reglas V1:
  - Categoría vacante vs profesión/experiencia CV (40%)
  - Skills CV vs `etiquetas` vacante (35%)
  - Ciudad/geo coincide (15%)
  - Modalidad compatible (10%)
  - Respuesta: lista ordenada con `match_score` + `match_reasons[]` ("Coincide tu experiencia en...")
- [ ] Tests manuales con 5-10 CVs reales de distintos formatos

## FASE 1 — Web (prioridad) — ~4-5 días

- [ ] Registro simplificado: email + password → pantalla "Sube tu CV" (skip opcional)
- [ ] Página upload: drag&drop + botón archivo, acepta JPG/PNG/PDF, preview del archivo
- [ ] Pantalla "Revisa tus datos": form prellenado con JSON del parse, editable, foto del CV visible al lado
- [ ] Guardar → perfil completo + CV en `cv_usuarios`
- [ ] Página/sección "Mi match": botón → lista de vacantes con badge % match + razones
- [ ] Estados: uploading (progress), parsing (spinner "leyendo tu CV..."), error con retry, ilegible → form manual
- [ ] CTA en landing/home del sitio para feria ("Sube tu CV y encuentra tu empleo en 1 minuto")

## FASE 2 — App Android — ~2-3 días

- [ ] Pantalla `CvCapture`: 3 opciones — cámara (Capacitor Camera, `quality` comprimida), galería, PDF (FilePicker)
- [ ] Consumir `POST /cv/parse` → pantalla revisión editable (reusar componentes de Perfil)
- [ ] `POST /cv/confirmar` → queda resuelto el audit #1 (callejón sin salida de CVs)
- [ ] Botón "Encontrar match" en Dashboard candidato → lista con % match (reusar estilos `vd-*`/`job-card`)
- [ ] Manejo offline/feria: comprimir imagen, mensaje claro si no hay red, retry

## FASE 3 — V2 (post-feria) — +3-5 días

- [ ] Embeddings: vectorizar CV + `descripcion` de vacante, cosine similarity (mejor con sinónimos: "mesero"≈"camarero")
- [ ] Match proactivo: notificar al candidato cuando entra vacante que le hace match
- [ ] Analytics: qué skills piden las vacantes vs qué traen los candidatos

## Estimación total

| Fase | Duración | Entrega |
|---|---|---|
| Fase 0 backend | 3-4 días | Endpoints listos para ambos front |
| Fase 1 web | 4-5 días | Demo funcional para feria |
| Fase 2 app | 2-3 días | Mismo flujo en Android |
| **MVP completo** | **~2 semanas** | |

## Riesgos

- **Conectividad en feria:** comprimir fotos, timeout corto, retry visible
- **CV ilegible:** siempre caer a form manual editable — la revisión es obligatoria, no opcional
- **Privacidad:** no retener imagen tras parsear (o borrar a los N días); aviso en UI
- **Costo Gemini:** gratis para volumen feria; monitorear quota
- **Match V1 es por palabras clave:** suficiente para demo, honesto en UI ("coincidencias" no "compatibilidad perfecta")
