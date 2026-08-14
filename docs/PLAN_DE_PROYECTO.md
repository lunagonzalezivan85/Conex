# Plan de Proyecto

## OpenToWork - Plataforma de Evaluacion y Confiabilidad de Talento

**Version:** 2.0  
**Fecha:** Agosto 2026  

---

> **NOTA (2026-08-12, Dsiezar):** Este documento es el **plan original** del proyecto, escrito antes de iniciar la implementación, con fases organizadas **por capa tecnica** (Fase 2 = Modelos, Fase 3 = Logica de Negocio, Fase 4 = APIs, Fase 5 = Frontend, etc.) y cronograma semanal.
>
> En la practica, la Fase 1 real (ver `docs/iluna/fase-1.md`) entrego de una sola vez entidades, DTOs, servicios, controllers y paginas Blazor, saltandose la separacion por capas de este plan. El proyecto migro a un esquema de fases **por feature/producto**, que es el que esta vigente y gobierna el trabajo actual: ver `README.md` (seccion "Fases del Proyecto"), `docs/GIT_BRANCHES.md` y `.agents/WORKFLOW.md`.
>
> Este archivo se conserva como referencia historica de la planificacion inicial, no como fuente de verdad del roadmap actual. Si necesitas saber "que fase estamos" o "que sigue", consulta `README.md`, no este documento.

---

## 1. Visión General

OpenToWork es una plataforma de evaluacion, validacion y scoring de candidatos construida con C# y Blazor. No compete con las bolsas de empleo tradicionales: funciona como la **capa de confianza** que permite a las empresas tomar mejores decisiones de contratacion.

El proyecto se compone de **3 portales independientes** con APIs y frontends separados:

| Portal | Descripcion | API | Frontend | Puerto API | Puerto WEB |
|--------|-------------|-----|----------|------------|------------|
| **Portal de Candidatos** | Registro, perfil, wizard, busqueda de vacantes, postulaciones | `OpenToWork.API` | `OpenToWork.WEB` | 5000 | 5100 |
| **Portal Administrativo** | Verificaciones manuales, moderacion, gestion de usuarios, auditoria | `OpenToWork.AdminAPI` | `OpenToWork.AdminWEB` | 5001 | 5101 |
| **Portal Corporativo** | Suscripcion mensual, perfiles evaluados, ranking, filtros avanzados | `OpenToWork.CorporateAPI` | `OpenToWork.CorporateWEB` | 5002 | 5102 |

Ver `docs/BUSINESS_PROPOSAL.md` para el detalle completo de la propuesta de negocio.

---

## 2. Arquitectura de los 3 Portales

```
                    ┌─────────────────────────────────────────────────┐
                    │              Base de Datos (MySQL)               │
                    │   OpenToWorkDb - Tablas con prefijos SC_, PT_,   │
                    │   SY_, AD_, CO_, VR_                              │
                    └──────────────┬──────────────────────────────────┘
                                   │
                    ┌──────────────┴──────────────────┐
                    │         OpenToWork.Core          │
                    │    Servicios de negocio          │
                    │    ScoringService                │
                    │    ValidationService             │
                    │    CompatibilityService          │
                    └──┬──────────┬──────────┬────────┘
                       │          │          │
              ┌────────┴───┐ ┌───┴──────┐ ┌──┴───────────┐
              │ Portal API │ │ AdminAPI │ │ CorporateAPI │
              │  (5000)    │ │  (5001)  │ │   (5002)     │
              └──────┬─────┘ └────┬─────┘ └──────┬───────┘
                     │            │              │
              ┌──────┴─────┐ ┌───┴──────┐ ┌──────┴───────┐
              │ Portal WEB │ │ AdminWEB │ │ CorporateWEB │
              │  (5100)    │ │  (5101)  │ │   (5102)     │
              └────────────┘ └──────────┘ └──────────────┘
```

### Portal de Candidatos (`OpenToWork.WEB` + `OpenToWork.API`)
- Registro con seleccion de rol (Candidato / Empresa)
- Wizard de 10 pasos (datos personales, ubicacion, perfil profesional, skills, preferencias, experiencia, educacion, certificaciones, CV)
- Perfil completo con CRUD de experiencia, educacion, certificaciones
- Busqueda y postulacion a vacantes
- Dashboard con stats y perfil completitud
- **Acceso gratuito**

### Portal Administrativo (`OpenToWork.AdminWEB` + `OpenToWork.AdminAPI`)
- Login admin con JWT independiente
- Gestion de usuarios (activar, desactivar, eliminar)
- Moderacion de vacantes
- **Verificaciones manuales** de referencias laborales
- **Revision de validaciones automaticas** (aprobar/rechazar)
- Dashboard con metricas y estadisticas
- Gestion de categorias y skills
- Log de auditoria admin
- Exportacion de datos

### Portal Corporativo (`OpenToWork.CorporateWEB` + `OpenToWork.CorporateAPI`)
- **Suscripcion mensual** (planes: Basic, Pro, Enterprise)
- Busqueda avanzada de talento con filtros por score
- **Perfiles evaluados** con indices de confiabilidad, estabilidad, evidencia
- **Ranking automatico** de candidatos por compatibilidad
- Vista de verificaciones (checkmarks: identidad, LinkedIn, experiencia, portafolio, referencias)
- Filtros por confiabilidad y estabilidad
- Reportes avanzados
- Integraciones con sistemas de RRHH
- **Servicios premium**: verificacion manual de referencias, evaluaciones por industria

---

## 3. Alcance

### Incluye
- **3 APIs independientes** con JWT aislado por portal.
- **3 frontends Blazor** responsive independientes.
- Sistema de autenticacion con JWT independiente por API.
- Registro y perfil de candidato con wizard multi-paso.
- **Sistema de validacion automatica** (LinkedIn, portafolio, coherencia cronologica).
- **Motor de evaluacion con 4 indices** (Estabilidad, Confiabilidad, Evidencia, Compatibilidad).
- **Sistema de verificaciones** (checkmarks: identidad, LinkedIn, experiencia, portafolio, referencias).
- **Referencias laborales** (entidad y CRUD).
- **Pruebas de habilidades** (evaluaciones basicas).
- Gestion de vacantes y postulaciones.
- Portal admin con verificaciones manuales y moderacion.
- Portal corporativo con suscripciones y perfiles evaluados.
- Sistema de temas (navy, dark, light) e i18n (es/en).
- APIs REST documentadas con Swagger (una por API).

### No Incluye (fases iniciales)
- Chat en tiempo real entre candidatos y empresas.
- Notificaciones push.
- Pasarela de pagos integrada (fase 8).
- Integracion directa con LinkedIn API (fase 7).

---

## 4. Entidades Nuevas Requeridas

### Scoring y Validacion

| Entidad | Prefijo | Descripcion |
|---------|---------|-------------|
| `PTCandidateScore` | PT_ | Scores: ReliabilityIndex, StabilityIndex, EvidenceIndex, CompatibilityIndex, OverallScore |
| `PTVerification` | PT_ | Verificaciones: Type (Identity, LinkedIn, Portfolio, Experience, References), Status (Pending, Verified, Rejected), VerifiedAt, VerifiedBy |
| `PTCandidateReference` | PT_ | Referencias laborales: RefereeName, Company, Position, Phone, Email, Relationship, VerifiedStatus |
| `PTSkillTest` | PT_ | Pruebas de habilidades: SkillId, Questions, PassingScore |
| `PTCandidateTestResult` | PT_ | Resultados de pruebas: CandidateId, TestId, Score, TakenAt |

### Portal Corporativo

| Entidad | Prefijo | Descripcion |
|---------|---------|-------------|
| `COCompany` | CO_ | Empresa: Name, Industry, Size, Website, LogoUrl |
| `COSubscription` | CO_ | Suscripcion: CompanyId, Plan (Basic/Pro/Enterprise), Status, StartDate, EndDate, MonthlyFee |
| `COSearchHistory` | CO_ | Historial de busquedas: CompanyId, Filters, ResultCount, SearchedAt |
| `COCandidateView` | CO_ | Vista de candidato evaluado: CompanyId, CandidateId, ScoreSnapshot, ViewedAt |

### Portal Admin

| Entidad | Prefijo | Descripcion |
|---------|---------|-------------|
| `ADAdminUser` | AD_ | Usuario admin: Username, Email, Role (Admin, Verifier, Moderator), Permissions |
| `ADAuditLog` | AD_ | Log de auditoria: AdminUserId, Action, EntityType, EntityId, Timestamp, Details |

---

## 5. Fases del Proyecto

### Fase 1: Fundacion - COMPLETADA
| Tarea | Descripcion | Estado |
|-------|-------------|--------|
| Documentacion | PRD, TRN, APPFLOW, Plan | Done |
| Estructura de solucion | 8 proyectos creados | Done |
| Configuracion base APIs | NuGets, DI, logging, CORS | Done |
| Configuracion base WEBs | NuGets, DI, HttpClient | Done |
| Base de datos | EF Core, DbContext, migracion init | Done |

### Fase 2: Portal de Candidatos - COMPLETADA
| Tarea | Descripcion | Estado |
|-------|-------------|--------|
| Autenticacion JWT | Register, login, refresh, device fingerprint | Done |
| Wizard 10 pasos | Datos personales, ubicacion, perfil, skills, experiencia, educacion, certificaciones, CV | Done |
| Perfil candidato | CRUD experiencia, educacion, certificaciones | Done |
| Vacantes | Publicacion, busqueda, filtros | Done |
| Postulaciones | Aplicar, estados, mis solicitudes | Done |
| Dashboard | Stats, bento grid, acciones rapidas | Done |
| Seguridad | Google OAuth, reCAPTCHA, AES-256, recuperacion | Done |
| i18n | Espanol e ingles, 10 archivos JSON | Done |
| UI/UX | One UI, Bento Grid, temas (navy/dark/light) | Done |

### Fase 3: Motor de Evaluacion y Scoring - Pendiente
| Tarea | Descripcion | Entregable |
|-------|-------------|------------|
| Entidades de scoring | `PTCandidateScore`, `PTVerification`, `PTCandidateReference` | Models + migracion |
| ValidationService | Verificacion automatica: LinkedIn, portafolio, coherencia cronologica | Core project |
| ScoringService | Calculo de indices: Estabilidad, Confiabilidad, Evidencia | Core project |
| CompatibilityService | Match candidato-vacante: skills, experiencia, formacion | Core project |
| API endpoints | `GET /api/candidates/{id}/score`, `GET /api/candidates/{id}/verifications` | OpenToWork.API |
| Dashboard candidato | Mostrar scores y verificaciones en el perfil | OpenToWork.WEB |
| Referencias laborales | CRUD de referencias en wizard y perfil | OpenToWork.WEB |
| Pruebas de habilidades | `PTSkillTest`, `PTCandidateTestResult`, UI basica | OpenToWork.WEB |

### Fase 4: Portal Administrativo - 85% COMPLETADA (por Dsiezar)
| Tarea | Descripcion | Entregable |
|-------|-------------|------------|
| Estructura AdminAPI | JWT independiente, controllers base | OpenToWork.AdminAPI |
| Estructura AdminWEB | Layout, nav, routing, login admin | OpenToWork.AdminWEB |
| Gestion de usuarios | Activar, desactivar, eliminar, roles | AdminWEB pages |
| Verificaciones manuales | Revisar y aprobar/rechazar validaciones | AdminWEB pages |
| Moderacion de vacantes | Aprobar, rechazar, editar vacantes | AdminWEB pages |
| Dashboard admin | Metricas, estadisticas, graficos | AdminWEB pages |
| Gestion categorias/skills | CRUD de categorias y skills | AdminWEB pages |
| Log de auditoria | `ADAuditLog`, vista de auditoria | AdminWEB pages |
| Exportacion de datos | CSV/Excel de candidatos, vacantes | AdminWEB pages |

### Fase 5: Portal Corporativo - Pendiente
| Tarea | Descripcion | Entregable |
|-------|-------------|------------|
| Estructura CorporateAPI | JWT independiente, controllers base | OpenToWork.CorporateAPI |
| Estructura CorporateWEB | Layout, nav, routing, login empresa | OpenToWork.CorporateWEB |
| Registro de empresas | `COCompany`, wizard de empresa | CorporateWEB pages |
| Sistema de suscripciones | `COSubscription`, planes (Basic/Pro/Enterprise) | CorporateAPI + WEB |
| Busqueda avanzada | Filtros por score, confiabilidad, estabilidad | CorporateWEB pages |
| Perfiles evaluados | Vista de candidato con scores y verificaciones | CorporateWEB pages |
| Ranking automatico | Ordenamiento por compatibilidad | CorporateAPI |
| Reportes avanzados | Exportacion, metricas de reclutamiento | CorporateWEB pages |

### Fase 6: Integracion y Servicios Premium - Pendiente
| Tarea | Descripcion | Entregable |
|-------|-------------|------------|
| Verificacion manual de referencias | Servicio premium para empresas | CorporateAPI |
| Evaluaciones por industria | Pruebas especificas por sector | CorporateAPI |
| Integraciones RRHH | API endpoints para sistemas externos | CorporateAPI |
| Reportes avanzados | Analytics de reclutamiento | CorporateWEB |

### Fase 7: Integraciones Externas - Pendiente
| Tarea | Descripcion | Entregable |
|-------|-------------|------------|
| LinkedIn API | Validacion real de perfiles | OpenToWork.API |
| Pasarela de pagos | Stripe/PayPal para suscripciones | CorporateAPI |
| Notificaciones email | SMTP para alertas y confirmaciones | Core project |
| Notificaciones push | Push notifications para candidatos | OpenToWork.WEB |

### Fase 8: Pruebas y Despliegue - Pendiente
| Tarea | Descripcion | Entregable |
|-------|-------------|------------|
| Pruebas unitarias | Cobertura > 70% en Core | Tests project |
| Pruebas integracion | Endpoints de las 3 APIs | Tests project |
| Documentacion final | README, guia de despliegue | docs/ |
| Despliegue | Configuracion produccion | App publicada |

---

## 6. Cronograma Resumido

```
Fase 1  ████████████  Fundacion (COMPLETADA)
Fase 2  ████████████  Portal de Candidatos (COMPLETADA)
Fase 3  ░░░░░░░░░░░░  Motor de Evaluacion y Scoring
Fase 4  ░░░░░░░░░░░░  Portal Administrativo
Fase 5  ░░░░░░░░░░░░  Portal Corporativo
Fase 6  ░░░░░░░░░░░░  Servicios Premium
Fase 7  ░░░░░░░░░░░░  Integraciones Externas
Fase 8  ░░░░░░░░░░░░  Pruebas y Despliegue
```

---

## 7. Matriz de Responsabilidades

| Rol | Responsabilidad |
|-----|-----------------|
| **Tech Lead** | Arquitectura, revision de codigo, decisiones tecnicas |
| **Backend Dev** | API, Core, Models, base de datos, scoring engine |
| **Frontend Dev** | Blazor, componentes UI, UX, 3 portales |
| **QA** | Pruebas unitarias, integracion, manuales |
| **DevOps** | CI/CD, despliegue, infraestructura |
| **SEC** | Auditoria de seguridad, JWT, encriptacion, validacion de inputs |

---

## 8. Gestion de Riesgos

| Riesgo | Probabilidad | Impacto | Mitigacion |
|--------|-------------|---------|------------|
| Complejidad del motor de scoring | Alta | Alto | Disenar algoritmos simples primero, iterar |
| Integracion con LinkedIn API | Media | Medio | Iniciar con validacion heuristica, API despues |
| Retraso en portal corporativo | Media | Alto | Reutilizar componentes del portal principal |
| Cambios de requisitos | Media | Medio | Diseno flexible en capas |
| Rendimiento de scoring en volumen | Baja | Alto | Pruebas de carga tempranas, cache de scores |

---

## 9. Criterios de Salida (Definition of Done)

### Portal de Candidatos
- [x] Registro y login con JWT funcional
- [x] Wizard de 10 pasos completo
- [x] Perfil con CRUD de experiencia, educacion, certificaciones
- [x] Busqueda y postulacion a vacantes
- [x] Dashboard con stats
- [x] i18n (es/en) y temas (navy/dark/light)

### Motor de Evaluacion
- [ ] Entidades de scoring y verificacion creadas y migradas
- [ ] ValidationService verifica LinkedIn, portafolio y coherencia cronologica
- [ ] ScoringService calcula los 4 indices (Estabilidad, Confiabilidad, Evidencia, Compatibilidad)
- [ ] Candidato puede ver sus scores y verificaciones en su perfil
- [ ] Referencias laborales funcionales (CRUD)
- [ ] Pruebas de habilidades basicas implementadas

### Portal Administrativo
- [ ] AdminAPI con JWT independiente funcional
- [ ] AdminWEB con login y layout completos
- [ ] Gestion de usuarios operativa
- [ ] Verificaciones manuales (aprobar/rechazar)
- [ ] Moderacion de vacantes
- [ ] Dashboard con metricas
- [ ] Log de auditoria funcional

### Portal Corporativo
- [ ] CorporateAPI con JWT independiente funcional
- [ ] CorporateWEB con login y layout completos
- [ ] Registro de empresas y suscripciones
- [ ] Busqueda avanzada con filtros por score
- [ ] Perfiles evaluados visibles con checkmarks
- [ ] Ranking automatico de candidatos
- [ ] Reportes avanzados

### General
- [ ] Las 3 APIs son independientes: puertos, JWT y procesos separados
- [ ] Cobertura de pruebas > 70% en Core
- [ ] Documentacion completa y actualizada
- [ ] Aplicaciones desplegables en produccion
