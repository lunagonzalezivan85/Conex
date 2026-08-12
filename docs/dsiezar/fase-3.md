# Fase 3: Portal Admin (AdminAPI + AdminWEB)

**IA:** Dsiezar
**Rol:** PM (Project Manager)
**Fecha inicio:** 2026-08-12
**Fecha fin:** Pendiente
**Estado:** En planificacion (Etapa 1)

---

## Resumen

Fase 3 construye el portal de administracion completo: `OpenToWork.AdminAPI` y `OpenToWork.AdminWEB`. Permite gestionar usuarios, moderar vacantes y solicitudes, ver metricas del sistema, administrar categorias/skills, exportar datos y auditar acciones administrativas.

**Nota de secuencia:** Segun `.agents/WORKFLOW.md`, la Fase 3 no puede entrar en Etapa 3 (Implementacion) ni cerrarse hasta que la Fase 2 (Iluna, en `iluna-fase-2`) este en Etapa 7 (Cierre, aprobada por PM+QA+SEC). Esta planificacion avanza en paralelo sin bloquear a Iluna.

---

## Etapa 1: Planificacion (PM)

**Alcance (segun `README.md` seccion "Fases del Proyecto" y "Como debe continuar el proyecto"):**

- [ ] `OpenToWork.AdminAPI` con JWT independiente del portal principal
- [ ] Gestion de usuarios: activar, desactivar, eliminar (soft delete)
- [ ] Moderacion de vacantes (temporales y permanentes)
- [ ] Dashboard con metricas y estadisticas (usuarios, vacantes, solicitudes)
- [ ] Gestion de categorias y skills (CRUD)
- [ ] Exportacion de datos (CSV/Excel)
- [ ] Log de auditoria admin (quien hizo que accion y cuando)
- [ ] `OpenToWork.AdminWEB` en Blazor con las paginas correspondientes

**Dependencias con Fase 2 (bloqueantes para ciertas tareas):**

| Tarea de Fase 3 | Depende de |
|---|---|
| Moderacion de vacantes permanentes | `PTVacancy` (Iluna, Fase 2) |
| Moderacion/vista de solicitudes | `PTApplication` (Iluna, Fase 2) |
| Metricas de perfiles completos | `PTCandidateExperience/Education/Certification` (Dsiezar, Fase 2 - aun no iniciada) |

**Tareas sin dependencia (se pueden empezar ya):**
- Estructura base de `OpenToWork.AdminAPI` (JWT independiente, Program.cs, appsettings)
- Gestion de usuarios (`SC_Users` ya existe desde Fase 1)
- Gestion de categorias y skills (`PT_Skills` ya existe desde Fase 1)
- Estructura base de `OpenToWork.AdminWEB` (layout, auth, theme/i18n reutilizando `SharedUI`)
- Log de auditoria admin (tabla nueva `AD_AuditLog`)

**Criterios de aceptacion (borrador, sujeto a validacion PM+QA+SEC):**
- [ ] Admin puede loguearse con JWT independiente del portal principal
- [ ] Admin puede activar/desactivar/eliminar usuarios
- [ ] Admin puede moderar (aprobar/rechazar/cerrar) vacantes
- [ ] Dashboard muestra metricas basicas (total usuarios, vacantes activas, solicitudes por estado)
- [ ] Admin puede crear/editar/eliminar categorias y skills
- [ ] Admin puede exportar listado de usuarios/vacantes a CSV
- [ ] Toda accion administrativa queda registrada en `AD_AuditLog`
- [ ] Build sin errores, i18n completo (es/en), QA y SEC aprueban

**Riesgos identificados:**
- Moderacion de vacantes/solicitudes no se puede implementar ni probar end-to-end hasta que Iluna cierre Fase 2.
- Se puede mitigar construyendo esas pantallas contra datos mock o adelantando solo la base del AdminAPI/AdminWEB mientras tanto.

- [ ] Aprobacion para pasar a Etapa 2 (Diseno Tecnico)

---

## Resumen de Cambios

Planificacion inicial de Fase 3 (Portal Admin). Sin implementacion de codigo aun.
