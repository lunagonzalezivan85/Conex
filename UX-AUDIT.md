# UX Audit — CONEX App

Auditoría realizada 2026-09-29 con el skill `screen-ux-audit` (GDvega/super-android-kotlin-firebase-skill).
Findings ordenados por severidad. Cada item incluye evidencia, impacto y fix mínimo.

## CRITICOS — bloquean o engañan al usuario

### [ ] 1. Flujo de postulación = callejón sin salida
- **Evidencia:** `app-conex/src/pages/Perfil.tsx:167` — botón "Mis CVs" apunta a `/app/perfil` (a sí mismo). `VacanteDetalle.tsx` dice "Ve a Perfil para subir uno" pero no existe UI de CVs. `candidatoApi.subirCv/eliminarCv` existen en `api.ts` sin consumo.
- **Impacto:** candidato sin CV no puede postularse nunca.
- **Fix:** pantalla `MisCvs.tsx` (listar/subir/eliminar con endpoints existentes) + ruta `/app/cvs` + routerLink correcto.

### [ ] 2. Items de empresa fingen ser clickeables
- **Evidencia:** `Dashboard.tsx:172` — `IonItem detail` muestra chevron sin `routerLink`.
- **Impacto:** affordance roto; la empresa no puede entrar a sus vacantes.
- **Fix:** `routerLink={/app/vacante/${v.slug}}` o quitar `detail`.

### [ ] 3. Orden "Mas cercanas" sin ubicación
- **Evidencia:** `Buscar.tsx` `ORDENES` incluye `distancia` siempre, aunque `filtros.geo` esté off.
- **Impacto:** ordenamiento vacío de sentido, resultados falsos.
- **Fix:** deshabilitar pill `distancia` cuando `!geo` (+ hint) o auto-activar geo al elegirla.

### [ ] 4. Teclado tapa botón de login
- **Evidencia:** `Login.tsx` — `<IonContent scrollY={false}>`.
- **Impacto:** en pantallas chicas/landscape el usuario no puede hacer login.
- **Fix:** quitar `scrollY={false}` o agregar padding con plugin Keyboard.

### [ ] 5. Contraste turquesa `#0FC6C2` como texto < WCAG AA (~2.3:1)
- **Evidencia:** `buscar.css` `vd-empresa-card-text a`, `--cancel-button-color`; cualquier texto pequeño en turquesa sobre blanco.
- **Impacto:** links ilegibles con baja visión o sol.
- **Fix:** texto con `#0A8F8C` (turquesa oscuro); `#0FC6C2` solo en rellenos/iconos grandes.

## MEDIOS — fricción o comportamiento confuso

### [ ] 6. Pills activos no removibles
`Buscar.tsx:173` — tocar pill activa reabre modal en vez de quitarla. Fix: icono close + `aria-pressed`/`aria-label`.

### [ ] 7. Error ≠ vacío en Buscar
API falla → toast + "No se encontraron vacantes" (falso diagnóstico). Fix: estado `error` separado con botón Reintentar.

### [ ] 8. Query por keystroke con modal abierto
`filtros` → `cargar` con debounce 400ms dispara fetch por cada cambio dentro del modal. Fix: estado local → aplicar al cerrar.

### [ ] 9. Doble navegación en logout
`Perfil.tsx:41` `router.push('/login')` + guard ya redirige al quedar `user=null`. Fix: quitar el push.

### [ ] 10. Touch targets < 48dp
`filter-pill`/`fm-pill` ~36–38px. Fix: padding vertical 11–12px mínimo.

### [ ] 11. Sin `aria-pressed` en pills
Lectores de pantalla no anuncian estado activo de filtros/geo/CV picker.

### [ ] 12. Fallback "Remoto" incorrecto
`Dashboard.tsx:133,176` — `v.ciudad || 'Remoto'` muestra Remoto en presenciales sin ciudad. Fix: `|| 'Sin ubicacion'`.

### [ ] 13. Moneda hardcodeada `$`
`VacanteDetalle` muestra `$${min}` ignorando `v.moneda`. Fix: usar campo `moneda`.

### [ ] 14. Toast sin retry en Dashboard
`load()` silencia errores → dashboard vacío sin aviso. Fix: estado error + Reintentar.

### [ ] 15. Modalidad sin capitalizar en pills
`Buscar.tsx` muestra `presencial` crudo en vez de `Presencial`.

## DIFERIDOS / preferencias

- [ ] Mapa interactivo para filtro geo (decisión documentada: pill GPS + radio cubre el API).
- [ ] Skeleton loaders en vez de spinner.
- [ ] Splash fuerza 2s mínimo (`MIN_ANIM_MS`) — considerar 1.2s.
- [ ] Link "Ver todas" Dashboard → Buscar.
- [ ] Recuperar contraseña / registro en Login (MVP).
- [ ] `loading="lazy"` en imágenes de logos.

## Validación post-fix

```powershell
npm run build
.\gradlew.bat assembleDebug   # tras cap sync
# Lighthouse a11y en dev server
# Android: Settings > Display > Font size = Largest (font-scale)
```

---

## Ronda 2 — Auditoría Playwright dark/light (2026-11-21)

Capturas en `ux-shots/` (viewport 390x844, cuenta de prueba `uxtest@dev.local`).

### Resueltos en esta ronda

- [x] **Dark mode roto:** `auth.css`, `registro.css`, `buscar.css` no tenían reglas `prefers-color-scheme`. Card "Ya tengo una cuenta" ilegible, precios de Planes invisibles, links de Login/RegistroEmpresa navy-sobre-oscuro, chat con fondo claro. → Bloques dark añadidos + hexes de texto sustituidos por `var(--ion-text-color)` / `--cx-muted` en 10 archivos TSX.
- [x] **Inputs lado a lado (patrón web):** `.pf-row-2`, `.cx-chat-contact-fields` y grids inline `1fr 1fr` de RegistroEmpresa y MisVacantes → **1 columna siempre** (regla: input debajo de input).
- [x] **Toasts/alerts por defecto:** nuevo `src/utils/cxFeedback.ts` (`useCxToast` bento + `useCxAlert` estilo sweet-modal) reemplaza `useIonToast`/`useIonAlert` en 9 archivos (~94 call sites, misma firma).
- [x] **Enums crudos en Perfil:** `a_convenir`/`indiferente` → labels humanos (DISPONIBILIDAD_LABELS / MODALIDAD_LABELS).
- [x] **Candidato entraba a /app/mis-vacantes** → guard por rol + fetch bloqueado si `!isEmpresa`.
- [x] Nueva var `--cx-turq-text` (#0A8F8C claro / #0FC6C2 oscuro) para texto turquesa legible; `--cx-muted` dark subido a `#9aa5bd`.

### Nota técnica
- Ionic React renderiza `disabled="false"` como atributo → estilos de deshabilitado usar `.button-disabled`, nunca `[disabled]`.

## Ronda 3 — Selects → autocompletado en RegistroEmpresa (2026-11-21)

- [x] **Rubro/Sector y Departamento/Ciudad usaban `IonSelect` nativo** (popover por defecto, fuera de marca). → Nuevo `src/components/CxAutocomplete.tsx` + `.cx-ac-*` en `app.css`: input pill 50px con icono + dropdown bento, filtrado local sin acentos, texto libre permitido, `role=combobox/listbox` con flechas/Enter/Escape, opciones ≥44px. Verificado claro/oscuro (`ux-shots/v3-*`), payload `rubro`/`ciudad` sin cambios.
- [x] **Selects de MisVacantes (nueva vacante)** — Categoría/Rubro (id numérico), Modalidad y Ciudad → `CxAutocomplete` con opciones `{value,label}` y modo `strict` (solo valores de lista; match exacto al salir también selecciona). Ya no queda ningún `IonSelect` en la app.

## Ronda 4 — Flujo postulación/contacto + moneda en vacantes (2026-11-21)

### Funcional (no visual)

- [x] **Postular sin CV ya funcionaba** — `postularse` acepta `cv_id` null; modal muestra "Postularme con mi Perfil CONEX". Verificado end-to-end (postulación real creada con `cv_id=null`).
- [x] **`GET /empresa/vacantes/{id}/postulaciones` 500 en producción** — la query usaba columnas inexistentes en `cand_candidato` (`telefono`, `anios_experiencia`, `salario_deseado`, `titulo_academico`, `cv_url`). → Teléfono desde `auth_user`, `salario_esperado_usd AS salario_deseado`, años de `cat_nivel_experiencia`, título académico y CV vía subqueries a `cand_educacion`/`cand_cv`. Mismo bug en `solicitudes`, `solicitudDetalle` y listado de postulaciones de empresa — corregidos.
- [x] **Badge "Contactado"** — `EXISTS` mensaje `emisor_tipo='cliente'` en `srv_contacto_mensaje` (el contacto se crea al postular, así que contacto_id solo no bastaba). Chip turquesa en lista y ficha del postulante.
- [x] **Finalizar contacto** — nueva acción `finalizar` en `responderSolicitud` (solo cliente/empresa): estado `finalizado`, inserta mensaje `emisor_tipo='sistema'` en el hilo, y bloquea TODA escritura posterior (HTTP 423). `iniciarChatPostulante` también respeta el bloqueo.
- [x] **ENUMs de DB** — `srv_contacto.estado` era `enum(pendiente,aceptado,rechazado,en_revision)` → 'finalizado' se guardaba como `''` (strictOn off). `srv_contacto_mensaje.emisor_tipo` no tenía 'sistema'. Migración `2026-11-21-000001_SrvContactoFinalizado` creada y aplicada a la DB remota.
- [x] **Chat UI** — header de empresa con botón "Finalizar contacto" (icono mano, confirm `useCxAlert`); al finalizar el input bar se reemplaza por banner en ambos lados; mensajes `sistema` se renderizan como pill centrada.
- [x] **Moneda en vacantes** — pills `C$ Córdobas (NIO)` / `$ Dólares (USD)` + separador de miles automático (`6000`→`6,000`) en `MisVacantes`; `moneda` aceptada en crear/actualizar vacante (backend) y símbolo correcto en card + `VacanteDetalle`.

### Pendiente de despliegue
Los cambios de `app/Controllers/Api/*` requieren subir al IIS de producción (FTP). El ENUM de la DB ya quedó actualizado.

## Ronda 5 — Bordes adaptativos + items minimalistas (2026-11-21)

- [x] **Bordes adaptativos en card-items** — `.cx-inbox-item-card`, `.cx-cand-item`, `.cx-cand-card`, `.cx-ro-detail-item`, `.vd-meta-card`, `.vd-empresa-card`, `.tinder-actions-container` usaban `#E2E8F0`/`#fff` fijos → ahora `var(--cx-border)` (navy 12% claro / blanco 10% oscuro) + `var(--ion-card-background)`/`--cx-surface`. `ion-card` ya adaptaba.
- [x] **Textos fijos en items dark** — `.cx-inbox-name/snippet/time`, `.cx-cand-item-*`, `.cx-cand-card-*` → `--ion-text-color`/`--cx-muted` (nombre del contacto era ilegible en dark).
- [x] **Items minimalistas** — botón "Chat con la empresa" (tab Empleos) y chips "Llamar"/"WhatsApp" → iconos redondos (`.cx-inbox-open-chat-btn`, `.sol-chip-btn.icon`) con `aria-label`. Emojis en items (🔔💰🎯📍💼📅💵) → IonIcons o texto plano; preguntas rápidas y labels de estado sin emojis.
