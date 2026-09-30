# AGENT — Reglas de trabajo para CONEX

> Lee este archivo antes de trabajar en el proyecto. Aplica en toda tarea de UI/UX.

## Contexto del proyecto

- **App:** `app-conex/` — Ionic 8 + React + Capacitor 7 (Android), APK debug en `app-conex/android/app/build/outputs/apk/debug/app-debug.apk`
- **API:** CodeIgniter 4 en `app/` — producción `https://conex.softlutionic.com/api` (IIS), DB remota `db_aa03a4_conex`
- **Roles:** candidato, empresa, asesor, admin
- **Build:** `npm run build` → `npx cap sync android` → `gradlew.bat assembleDebug` con `JAVA_HOME=C:\Users\MT\.jdks\jdk-21.0.12.1+1`
- **Git:** master local → `git push -f origin master:main`

## Sistema de marca (fuente única: `src/theme/variables.css`)

| Rol | Color | Uso |
|---|---|---|
| Primario | `#1B2A4A` navy | pills activos, iconos, bordes, títulos |
| CTA | `#F97316` naranja | botones de acción (login, postularse, aplicar filtros, badge) |
| Acento | `#0FC6C2` turquesa | iconos, focus rings, links*, skills pills, X del wordmark |
| Surface | `--cx-surface` | fondos de inputs/cards bento |
| Dark bg | `#0d1526` | fondo tema oscuro |

*`#0FC6C2` NO usar como texto pequeño sobre blanco (falla AA) → `#0A8F8C` para texto.

Vars disponibles: `--cx-navy`, `--cx-orange`, `--cx-turq`, `--cx-grad`, `--cx-grad-cta`, `--cx-surface`, `--cx-border`, `--cx-muted`.

**Wordmark:** `CON` navy · `E` naranja · `X` turquesa (todo blanco en dark). Logo tile navy con `C` blanca + `x` turquesa.

## Patrón de diseño — One UI + bento

- Headers **flat** — sin sombra ni borde (`ion-header box-shadow: none`, `ion-toolbar --border-width: 0`)
- Cards **bento** — sin sombra, borde `1px var(--cx-border)`, radius 20px
- Pills para selección en vez de selects nativos
- Inputs pill de 48–52px con fondo surface + focus ring
- Footer CTAs naranja con sombra `rgba(249,115,22,.4)`
- Radius: inputs/pills 14–20px, cards 16–24px, modales bottom-sheet 24px top

## Regla obligatoria — evaluar UX + diseño en TODO cambio

Antes de proponer o implementar cualquier cambio visual o de flujo:

### Preguntas de experiencia que SIEMPRE hacer (internamente o al usuario)

1. **Goal:** ¿Qué quiere lograr el usuario en esta pantalla? ¿Lo logra en 1–2 taps?
2. **Affordance:** ¿Todo lo que parece tocable lo es? ¿Hay chevrons/sombras que mienten?
3. **Estados:** ¿Están cubiertos loading, vacío, error, offline, sin permisos? ¿Error ≠ vacío?
4. **Feedback:** ¿Toda acción confirma (toast, estado, navegación)? ¿Errores claros con retry?
5. **Rol:** ¿Funciona igual de bien para candidato y empresa? ¿CTAs visibles solo donde aplican?
6. **Jerarquía:** ¿El elemento más importante es el más visible? ¿Un solo CTA primario por pantalla?

### Checklist de diseño a evaluar SIEMPRE

- [ ] Colores solo del sistema de marca (`--cx-*`), nunca hexes nuevos sin preguntar
- [ ] Contraste: texto ≥4.5:1, iconos/UI ≥3:1
- [ ] Touch targets ≥44–48dp
- [ ] `aria-*` en componentes custom (pills, toggles, pickers)
- [ ] Tema oscuro funciona (vars, no colores fijos para texto/superficies)
- [ ] Dark mode: wordmark blanco, primary aclarado
- [ ] Nada fijo (`position: fixed`) que tape contenido
- [ ] Iconos coherentes: navegación navy, acciones naranja, acentos turquesa
- [ ] Sin queries por keystroke (debounce o aplicar-al-cerrar)
- [ ] Consistencia con el skill `screen-ux-audit` (GDvega) — si dudas, leerlo

## Cuando reportar hallazgos

Formato del skill `screen-ux-audit`: severidad → evidencia (archivo:línea) → impacto al usuario → fix mínimo → validación. Nunca listar preferencias sin impacto real.

## Skills de referencia (guardados en memoria)

- `gdlbo/material-skill` — M3 + Expressive, restricción visual
- `skydashnet/material-design-3-ui-skill` — 13 refs: color, type, motion, a11y, anti-patterns
- `tawhidmonowar/android-design-skills` — layouts, spacing, edge-to-edge, estados
- `piyushverma0/android-agent-skills` — design-system M3E, tokens únicos
- `GDvega/super-android-kotlin-firebase-skill` — `screen-ux-audit`, `design-test-gate`, `ui-state-design`

## Pendientes

Ver `UX-AUDIT.md` — findings de la auditoría 2026-09-29 priorizados.
