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
