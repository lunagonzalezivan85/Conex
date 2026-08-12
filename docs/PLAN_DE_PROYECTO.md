# Plan de Proyecto

## OpenToWork - Plataforma de Candidatos

**Versión:** 1.0  
**Fecha:** Agosto 2026  

---

## 1. Visión General

OpenToWork es una plataforma de empleo construida con C# y Blazor que conecta candidatos con empresas. El proyecto se desarrolla en capas separadas con **dos APIs independientes** (Portal Principal y Portal Administrativo) y **dos frontends Blazor independientes**, siguiendo buenas prácticas de arquitectura de software.

---

## 2. Alcance

### Incluye
- **Dos APIs independientes:** `OpenToWork.API` (portal principal) y `OpenToWork.AdminAPI` (portal admin) con JWT aislado.
- Sistema de autenticación con JWT independiente por API.
- Gestión de candidatos (CRUD + búsqueda).
- Gestión de empresas (CRUD).
- Gestión de vacantes (CRUD + búsqueda).
- Sistema de solicitudes de vacantes.
- Portal administrativo con gestión de usuarios, vacantes, solicitudes y métricas.
- Dos interfaces Blazor responsive independientes (portal y admin).
- APIs REST documentadas con Swagger (una por API).

### No Incluye (Fase 1)
- Chat en tiempo real entre candidatos y empresas.
- Pasarela de pagos.
- Notificaciones push.
- Integración con LinkedIn.
- Multi-idioma.

---

## 3. Fases del Proyecto

### Fase 1: Fundación (Semana 1)
| Tarea | Descripción | Entregable |
|-------|-------------|------------|
| Documentación | PRD, TRN, APPFLOW, Plan | Documentos completos |
| Estructura de solución | Crear proyectos y referencias | `.sln` con 7 proyectos |
| Configuración base APIs | NuGets, DI, logging, CORS (dos APIs) | APIs compilables |
| Configuración base WEBs | NuGets, DI, HttpClient (dos Blazor) | WEBs compilables |
| Base de datos | EF Core, DbContext, migraciones init | BD creada |

### Fase 2: Modelos y Datos (Semana 2)
| Tarea | Descripción | Entregable |
|-------|-------------|------------|
| Entidades | User, Candidate, Company, Vacancy, Application, Skill | Models project |
| Configuración EF | Fluent API, relaciones, seed data | Migraciones |
| DTOs y Mapping | DTOs en Shared, perfiles de AutoMapper | Shared project |
| Enums y Constantes | Roles, estados, tipos de contrato | Shared project |

### Fase 3: Lógica de Negocio (Semana 3)
| Tarea | Descripción | Entregable |
|-------|-------------|------------|
| Interfaces de servicios | IService para cada entidad | Core project |
| Servicios | Implementación de lógica de negocio | Core project |
| Validación | FluentValidation para cada DTO | Core project |
| Autenticación | AuthService, JWT generation | Core project |

### Fase 4: APIs REST (Semana 4)
| Tarea | Descripción | Entregable |
|-------|-------------|------------|
| API Portal - Controllers | Auth, Candidates, Companies, Vacancies, Applications | OpenToWork.API |
| API Portal - Middleware | Manejo de errores, logging, CORS | OpenToWork.API |
| API Portal - Swagger | Configuración y documentación | Swagger UI (puerto 5000) |
| API Admin - Controllers | AdminAuth, Users, Candidates, Companies, Vacancies, Applications, Skills, Dashboard | OpenToWork.AdminAPI |
| API Admin - Middleware | Manejo de errores, logging, CORS (independiente) | OpenToWork.AdminAPI |
| API Admin - Swagger | Configuración y documentación | Swagger UI (puerto 5001) |
| Pruebas API Portal | Tests de integración con xUnit | OpenToWork.API.Tests |
| Pruebas API Admin | Tests de integración con xUnit | OpenToWork.AdminAPI.Tests |

### Fase 5: Frontend Blazor (Semana 5-6)
| Tarea | Descripción | Entregable |
|-------|-------------|------------|
| Portal Principal - Layout | Layout, NavMenu, routing | OpenToWork.WEB |
| Portal Principal - Inicio | Selección "buscar empleo" / "contratar" | Home page |
| Portal Principal - Auth | Login, Register (candidato y empresa) | Auth pages |
| Portal Principal - Candidato | Perfil, buscar vacantes, mis solicitudes | Candidate pages |
| Portal Principal - Empresa | Perfil, vacantes, buscar candidatos, solicitudes | Company pages |
| Portal Admin - Layout | Layout admin, NavMenu, routing | OpenToWork.AdminWEB |
| Portal Admin - Auth | Login admin (AdminAPI independiente) | Admin auth |
| Portal Admin - Gestión | Usuarios, candidatos, empresas, vacantes, solicitudes | Admin pages |
| Portal Admin - Dashboard | Métricas, estadísticas, gráficos | Admin dashboard |
| Componentes reutilizables | Cards, tablas, formularios, modales | Shared components |

### Fase 6: Pruebas y Despliegue (Semana 7)
| Tarea | Descripción | Entregable |
|-------|-------------|------------|
| Pruebas unitarias | Cobertura > 70% en Core | Tests project |
| Pruebas integración | Endpoints API | Tests project |
| Documentación final | README, guía de despliegue | docs/ |
| Despliegue | Configuración para producción | App publicada |

---

## 4. Cronograma Resumido

```
Semana 1  ████████████  Fase 1: Fundación (2 APIs + 2 WEBs + Core/Models/Shared)
Semana 2  ████████████  Fase 2: Modelos y Datos
Semana 3  ████████████  Fase 3: Lógica de Negocio
Semana 4  ████████████  Fase 4: APIs REST (Portal + Admin)
Semana 5  ████████████  Fase 5: Frontend Blazor (Portal Principal)
Semana 6  ████████████  Fase 5: Frontend Blazor (Portal Admin)
Semana 7  ████████████  Fase 6: Pruebas y Despliegue
```

---

## 5. Matriz de Responsabilidades

| Rol | Responsabilidad |
|-----|-----------------|
| **Tech Lead** | Arquitectura, revisión de código, decisiones técnicas |
| **Backend Dev** | API, Core, Models, base de datos |
| **Frontend Dev** | Blazor, componentes UI, UX |
| **QA** | Pruebas unitarias, integración, manuales |
| **DevOps** | CI/CD, despliegue, infraestructura |

---

## 6. Gestión de Riesgos

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|-------------|---------|------------|
| Retraso en frontend | Media | Alto | Componentes reutilizables desde el inicio |
| Complejidad de EF Core | Baja | Medio | Migraciones incrementales |
| Cambios de requisitos | Media | Medio | Diseño flexible en capas |
| Problemas de rendimiento | Baja | Alto | Pruebas de carga tempranas |

---

## 7. Criterios de Salida (Definition of Done)

- [ ] Todos los endpoints de la API del portal principal funcionan y están documentados en Swagger.
- [ ] Todos los endpoints de la API del portal admin funcionan y están documentados en Swagger.
- [ ] Las dos APIs son independientes: puertos, JWT y procesos separados.
- [ ] Los tokens de una API no son válidos en la otra.
- [ ] La interfaz Blazor del portal principal cubre todos los flujos de candidato y empresa.
- [ ] La interfaz Blazor del portal admin cubre la gestión del sistema.
- [ ] Autenticación JWT funcional e independiente para portal y admin.
- [ ] CRUD completo de candidatos, vacantes y solicitudes.
- [ ] Búsqueda de candidatos y vacantes operativa.
- [ ] Portal admin con gestión de usuarios, métricas y configuración.
- [ ] Cobertura de pruebas > 70% en ambas APIs.
- [ ] Documentación completa y actualizada.
- [ ] Aplicaciones desplegables en entorno de producción.
