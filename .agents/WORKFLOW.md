# OpenToWork - Flujo de Trabajo por Fase

## Regla Principal

**Ninguna fase puede iniciar hasta que la fase anterior este 100% completada y firmada por PM, QA y SEC.**

Cada fase tiene un flujo secuencial de 8 etapas (0 a 7). No se puede saltar etapas.

---

## Etapa 0: Declaracion de Identidad (Obligatoria)

**Antes de iniciar cualquier trabajo, la IA debe declarar quien es.**

### Identidades registradas

| IA | Carpeta de documentacion | Descripcion |
|---|---|---|
| **Iluna** | `docs/iluna/` | IA del proyecto OpenToWork |
| **Dsiezar** | `docs/dsiezar/` | IA del proyecto OpenToWork |

### Reglas de identidad

1. Al iniciar una sesion, la IA debe decir: **"Soy Iluna"** o **"Soy Dsiezar"**
2. Todo cambio realizado por esa IA debe documentarse en su carpeta correspondiente
3. Por cada fase, la IA crea un archivo `fase-N.md` usando `PLANTILLA.md` como base
4. Si ambas IAs trabajan en la misma fase, cada una documenta sus propios cambios por separado
5. La declaracion de identidad es obligatoria antes de Etapa 1
6. Si la IA no declara identidad, no puede continuar con el flujo

### Como documentar

```
docs/iluna/         # Bitacora de Iluna
  ├── README.md     # Indice de fases participadas
  ├── PLANTILLA.md  # Plantilla para copiar
  └── fase-N.md     # Registro de cambios de la fase N

docs/dsiezar/       # Bitacora de Dsiezar
  ├── README.md     # Indice de fases participadas
  ├── PLANTILLA.md  # Plantilla para copiar
  └── fase-N.md     # Registro de cambios de la fase N
```

**Gate:** IA declara identidad -> pasa a Etapa 1

---

## Estructura del Flujo

```
Fase N
 ├── [0] Declaracion de Identidad -> Iluna / Dsiezar
 ├── [1] Planificacion        -> PM
 ├── [2] Diseno Tecnico        -> PM + FS
 ├── [3] Implementacion        -> FS
 ├── [4] Pruebas Funcionales   -> QA
 ├── [5] Auditoria de Seguridad -> SEC
 ├── [6] Correcciones          -> FS (si QA o SEC encuentran issues)
 └── [7] Cierre y Aprobacion   -> PM
```

---

## Etapas Detalladas

### Etapa 1: Planificacion (PM)

**Entrada:** Roadmap actualizado en `README.md`
**Responsable:** PM
**Salida:** Documento de planificacion de fase

- [ ] PM define el alcance de la fase (que features entran, cuales no)
- [ ] PM desgloza features en tareas con prioridad (Alta, Media, Baja)
- [ ] PM asigna tareas a FS, QA y SEC
- [ ] PM define criterios de aceptacion de la fase
- [ ] PM identifica dependencias y riesgos
- [ ] PM actualiza `docs/NEURAL_MAP.md` seccion "Que falta implementar"

**Gate:** PM marca planificacion como aprobada -> pasa a Etapa 2

---

### Etapa 2: Diseno Tecnico (PM + FS)

**Entrada:** Documento de planificacion de fase
**Responsables:** PM (requisitos) + FS (arquitectura)
**Salida:** Diseno tecnico aprobado

- [ ] FS disena las entidades/DTOs nuevos (siguiendo `docs/DATABASE_DESIGN.md`)
- [ ] FS disena los endpoints de API nuevos
- [ ] FS disena los componentes/paginas Blazor nuevos
- [ ] FS define si se necesitan nuevas migraciones
- [ ] FS define si se necesitan nuevos paquetes NuGet
- [ ] PM valida que el diseno cumpla con `docs/PRD.md`
- [ ] SEC revisa el diseno por posibles riesgos de seguridad

**Gate:** PM + FS aprueban el diseno -> pasa a Etapa 3

---

### Etapa 3: Implementacion (FS)

**Entrada:** Diseno tecnico aprobado
**Responsable:** FS
**Salida:** Codigo implementado y compilando sin errores

- [ ] FS crea/actualiza entidades en `OpenToWork.Models/Entities/`
- [ ] FS crea/actualiza DTOs en `OpenToWork.Shared/DTOs/`
- [ ] FS crea/actualiza servicios en `OpenToWork.Core/Services/`
- [ ] FS crea/actualiza controllers en `OpenToWork.API/Controllers/`
- [ ] FS crea/actualiza componentes en `OpenToWork.SharedUI/Components/`
- [ ] FS crea/actualiza paginas en `OpenToWork.WEB/Components/Pages/`
- [ ] FS agrega claves i18n en `wwwroot/config/language/es/` y `wwwroot/config/language/en/`
- [ ] FS crea migracion: `dotnet ef migrations add <Nombre> --project src/OpenToWork.Models --startup-project src/OpenToWork.Models`
- [ ] FS aplica migracion: `dotnet ef database update --project src/OpenToWork.Models --startup-project src/OpenToWork.Models`
- [ ] FS ejecuta `dotnet build OpenToWork.slnx` -> 0 errores
- [ ] FS actualiza `docs/NEURAL_MAP.md` con nuevos archivos

**Gate:** Build sin errores -> pasa a Etapa 4

---

### Etapa 4: Pruebas Funcionales (QA)

**Entrada:** Codigo implementado y compilando
**Responsable:** QA
**Salida:** Reporte de pruebas (pass/fail por cada caso)

- [ ] QA ejecuta casos de prueba de la API (via Swagger o curl)
- [ ] QA ejecuta casos de prueba del frontend (navegacion, flujos)
- [ ] QA valida UI/UX contra `docs/DESIGN_SYSTEM.md`
- [ ] QA valida que no haya texto hardcoded en `.razor`
- [ ] QA valida que todas las claves i18n existan en ambos idiomas
- [ ] QA prueba responsive (mobile 768px, 480px)
- [ ] QA prueba cambio de tema e idioma
- [ ] QA verifica que la documentacion coincida con el codigo
- [ ] QA reporta bugs con: archivo, linea, descripcion, pasos para reproducir

**Gate:**
- Si hay bugs -> vuelve a Etapa 6 (Correcciones)
- Si no hay bugs -> pasa a Etapa 5

---

### Etapa 5: Auditoria de Seguridad (SEC)

**Entrada:** Codigo que paso pruebas funcionales de QA
**Responsable:** SEC
**Salida:** Reporte de auditoria (sin hallazgos / hallazgos por severidad)

- [ ] SEC revisa nuevos endpoints por validacion de inputs
- [ ] SEC verifica que endpoints protegidos tengan `[Authorize]`
- [ ] SEC audita JWT, refresh tokens, device fingerprinting
- [ ] SEC verifica que no haya secrets en codigo fuente
- [ ] SEC revisa dependencias nuevas por vulnerabilidades
- [ ] SEC valida CORS y headers de seguridad
- [ ] SEC verifica encriptacion de datos sensibles
- [ ] SEC reporta hallazgos con: severidad, archivo, linea, recomendacion

**Gate:**
- Si hay hallazgos Criticos o Altos -> vuelve a Etapa 6 (Correcciones)
- Si hay hallazgos Medios o Bajos -> SEC puede aprobar con plan de mitigacion
- Si no hay hallazgos -> pasa a Etapa 7

---

### Etapa 6: Correcciones (FS)

**Entrada:** Reporte de bugs de QA y/o hallazgos de SEC
**Responsable:** FS
**Salida:** Codigo corregido y compilando

- [ ] FS corrige cada bug reportado por QA
- [ ] FS corrige cada hallazgo reportado por SEC
- [ ] FS ejecuta `dotnet build OpenToWork.slnx` -> 0 errores
- [ ] FS marca cada bug/hallazgo como corregido

**Gate:** Todas las correcciones aplicadas -> vuelve a Etapa 4 (QA re-valida)

> **Nota:** Este ciclo Etapa 4 -> 6 -> 4 se repite hasta que QA y SEC no encuentren issues.

---

### Etapa 7: Cierre y Aprobacion (PM)

**Entrada:** QA sin bugs + SEC sin hallazgos criticos/altos
**Responsable:** PM
**Salida:** Fase marcada como completada

- [ ] PM verifica que todos los criterios de aceptacion se cumplan
- [ ] PM actualiza `README.md` marcando la fase como completada
- [ ] PM actualiza `docs/NEURAL_MAP.md` con el estado final
- [ ] PM genera script SQL si hubo cambios en BD: `dotnet ef migrations script --project src/OpenToWork.Models --startup-project src/OpenToWork.Models --output docs/<Nombre>.sql`
- [ ] PM actualiza `docs/NEURAL_MAP.md` seccion "Mapa de Archivos Clave"
- [ ] PM comunica el cierre de la fase al equipo

**Gate:** Fase marcada como COMPLETADA -> puede iniciar la siguiente fase

---

## Diagrama del Flujo

```
INICIO FASE N
      |
      v
[0] Declaracion de Identidad (Soy Iluna / Soy Dsiezar)
      |
      v
[1] Planificacion (PM)
      |
      v
[2] Diseno Tecnico (PM + FS + SEC review)
      |
      v
[3] Implementacion (FS) ----build OK-----> [4] Pruebas (QA)
      |                                        |
      |                                   +----+----+
      |                                   |         |
      |                               bugs v         v no bugs
      |                                   |         |
      |                              [6] Correcciones  [5] Auditoria (SEC)
      |                                   |              |
      |                                   |         +----+----+
      |                                   |         |         |
      |                              +----+         v         v
      |                              |        hallazgos      sin hallazgos
      |                              |        criticos         |
      |                              v            |            |
      |                         [4] re-valida <---+            |
      |                                             |
      |                                             v
      |                                        [7] Cierre (PM)
      |                                             |
      |                                             v
      +----------------------------------------> FASE N COMPLETADA
                                                    |
                                                    v
                                              INICIO FASE N+1
```

---

## Estado Actual

| Fase | Estado | Etapa Actual | Bloqueado por |
|---|---|---|---|
| Fase 1 | COMPLETADA | Cierre aprobado | - |
| Fase 2 | PENDIENTE | No iniciada | Esperando inicio de Etapa 1 |
| Fase 3 | PENDIENTE | No iniciada | Esperando Fase 2 |

---

## Reglas Estrictas

1. **Declaracion de identidad obligatoria:** La IA debe decir "Soy Iluna" o "Soy Dsiezar" antes de iniciar cualquier trabajo. Sin identidad, no hay flujo.
2. **Documentacion por IA:** Todo cambio debe registrarse en `docs/iluna/fase-N.md` o `docs/dsiezar/fase-N.md` segun corresponda.
3. **No saltar fases:** Fase N+1 no puede iniciar hasta que Fase N este en Etapa 7 (Cierre).
4. **No saltar etapas:** Dentro de una fase, no se puede saltar de Etapa 3 a Etapa 5 sin pasar por Etapa 4.
5. **Build obligatorio:** FS debe ejecutar `dotnet build OpenToWork.slnx` con 0 errores antes de pasar a QA.
6. **Cero bugs criticos:** QA no puede aprobar si hay bugs de severidad critica.
7. **Cero hallazgos criticos:** SEC no puede aprobar si hay hallazgos de severidad critica o alta.
8. **Ciclo de correccion:** Si QA o SEC encuentran issues, FS corrige y el ciclo se repite hasta cero issues.
9. **Documentacion obligatoria:** Toda feature implementada debe estar reflejada en `docs/NEURAL_MAP.md` y en la bitacora de la IA (`docs/iluna/` o `docs/dsiezar/`).
10. **i18n obligatorio:** Toda feature con UI debe tener claves en ambos idiomas (es/en).
11. **Migracion obligatoria:** Todo cambio en entidades debe tener su migracion creada y aplicada.
12. **Aprobacion triple:** El cierre de fase requiere firma de PM + QA + SEC.
