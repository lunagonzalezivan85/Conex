# PRD - Product Requirements Document

## CONEX - Sistema de Reclutamiento de Personal

**Version:** 1.0  
**Fecha:** 2026-09-12  
**Estado:** Borrador

---

## 1. Resumen del Producto

CONEX es una plataforma web de reclutamiento de personal que conecta candidatos buscando empleo con empresas que publican vacantes. El sistema permite a los candidatos buscar y postularse a ofertas laborales, y a las empresas crear y gestionar vacantes, todo bajo un esquema de roles con layouts diferenciados.

---

## 2. Objetivos del Producto

- **Conectar candidatos y empresas** mediante un portal de empleo facil de usar.
- **Gestionar el ciclo completo de reclutamiento**: publicacion de vacantes, postulacion, revision y seleccion.
- **Proveer 4 experiencias diferenciadas** segun el rol del usuario (Administrador, Asesor, Empresa, Candidato).
- **Mantener un portal publico** donde cualquier visitante puede ver vacantes disponibles sin registrarse.

---

## 3. Roles de Usuario

| Rol          | Descripcion                                                                 |Acceso Layout      |
|-------------|-----------------------------------------------------------------------------|-------------------|
| Administrador | Control total del sistema: usuarios, empresas, candidatos, vacantes, configuracion general. | Administracion    |
| Asesor       | Apoyo operativo: gestion de vacantes, revision de postulaciones, atencion a empresas y candidatos. | Administracion    |
| Empresa      | Crea y gestiona sus propias vacantes, revisa postulaciones de candidatos, gestiona su perfil empresarial. | Empresa           |
| Candidato    | Busca vacantes, crea y gestiona su perfil/CV, se postula a ofertas laborales. | Candidato         |

### Registro de Usuarios

- Cualquier persona puede registrarse en la plataforma.
- Al registrarse, el usuario debe elegir unicamente uno de dos roles: **Candidato** o **Empresa**.
- Los roles **Administrador** y **Asesor** son asignados internamente desde el panel de administracion.

### Registro Basico (Candidato y Empresa)

El registro inicial es minimo y rapido:

| Campo     | Requerido | Descripcion                |
|-----------|-----------|---------------------------|
| nombre    | Si        | Nombre                     |
| apellido  | Si        | Apellido                   |
| usuario   | Si        | Nombre de usuario (unico)  |
| email     | Si        | Correo electronico (unico)  |
| telefono  | Si        | Numero telefonico          |
| password  | Si        | Contrasena                 |

### Wizard de Perfil (Candidato)

Despues del registro basico, si el perfil del candidato esta incompleto, el sistema muestra un **wizard opcional** para completar el perfil. El candidato puede saltarlo y hacerlo despues.

**Pasos del wizard:**

1. **Datos personales**: fecha de nacimiento, profesion, sobre mi, direccion con geolocalizacion, disponibilidad, modalidad preferida.
2. **Experiencia laboral**: agregar empleos anteriores (empresa, cargo, fechas, descripcion, actual).
3. **Educacion**: nivel educativo, institucion, titulo, fechas, en curso.
4. **Habilidades e idiomas**: seleccionar habilidades con nivel, idiomas con nivel.
5. **Puestos deseados**: puestos que busca (ej: "Desarrollador PHP", "Analista de Datos").
6. **Expectativas y enlaces**: salario esperado (en USD), portafolio, LinkedIn.
7. **Subir CV (PDF)**: el sistema usa Gemini para analizar el CV y auto-llenar los campos faltantes.

### Medidor de Porcentaje de Perfil

El dashboard del candidato muestra un **medidor circular** con el porcentaje de completitud del perfil. Si es menor a 100%, muestra un enlace al wizard.

**Criterios de calculo:**

| Criterio                              | Peso |
|--------------------------------------|------|
| Datos personales basicos              | 10%  |
| Direccion y geolocalizacion           | 10%  |
| Al menos 1 experiencia laboral        | 10%  |
| Al menos 1 registro de educacion     | 10%  |
| Al menos 3 habilidades                | 10%  |
| Al menos 1 idioma                    | 5%   |
| Al menos 1 puesto deseado             | 10%  |
| Expectativa salarial definida (USD)   | 10%  |
| Portafolio o LinkedIn                | 5%   |
| CV subido                            | 10%  |
| Disponibilidad y modalidad definidas  | 10%  |
| **Total**                            | 100% |

### Auto-llenado con Gemini IA

Cuando el candidato sube su CV en PDF:
1. El sistema envia el CV a **Google Gemini** para analisis.
2. Gemini extrae: experiencia laboral, educacion, habilidades, idiomas, profesion, enlaces (LinkedIn, portafolio), puestos sugeridos.
3. El sistema pre-llena los campos faltantes del perfil.
4. El candidato **revisa, edita o descarta** los datos extraidos.
5. Al confirmar, el porcentaje de perfil se recalcula automaticamente.

### Moneda y Conversion Salarial

- Toda la plataforma maneja salarios en **USD (dolares)** como moneda de visualizacion.
- Internamente el sistema hace la conversion a **C$ (cordobas nicaraguenses)** usando una tasa de cambio configurable.
- El candidato ingresa su expectativa salarial en USD.
- Las vacantes muestran salario en USD con opcion de ver equivalente en C$.

---

## 4. Layouts del Sistema

El sistema cuenta con **4 layouts** distintos, cada uno con un proposito especifico. Todos comparten un lenguaje de diseno similar pero con estructura de navegacion y componentes diferenciados.

### 4.1 Layout Publico
- Accesible sin autenticacion.
- Muestra vacantes disponibles con buscador y filtros.
- Permite registro e inicio de sesion.
- Landing page informativa sobre la plataforma.

### 4.2 Layout Administracion
- Para roles: Administrador y Asesor.
- Dashboard con metricas globales del sistema.
- Gestion de usuarios, empresas, candidatos y vacantes.
- Configuracion general del sistema.
- Reportes y estadisticas.

### 4.3 Layout Empresa
- Para rol: Empresa.
- Dashboard con resumen de vacantes y postulaciones.
- Gestion de vacantes (crear, editar, cerrar, eliminar).
- Revision de candidatos postulados.
- Perfil empresarial.

### 4.4 Layout Candidato
- Para rol: Candidato.
- Dashboard con vacantes recomendadas y estado de postulaciones.
- Buscador de vacantes con filtros avanzados.
- Gestion de perfil/CV.
- Seguimiento de postulaciones.

---

## 5. Funcionalidades Principales

### 5.1 Portal Publico
- Listado de vacantes con buscador y filtros (categoria, ubicacion, tipo de contrato, salario).
- Vista detalle de vacante.
- Registro de nuevo usuario (Candidato o Empresa).
- Inicio de sesion.

### 5.2 Gestion de Vacantes (Empresa)
- Crear vacante con campos: titulo, descripcion, categoria, ubicacion, tipo de contrato, salario (USD), modalidad, años de experiencia requeridos, vacantes disponibles, maximo de postulantes.
- Seleccionar requisitos de una lista predefinida (marcar los necesarios).
- Editar y cerrar vacantes.
- Ver listado de postulaciones recibidas por vacante.
- Filtrar y buscar postulaciones.
- **Segun el plan de la empresa**, el acceso a datos de candidatos varia:
  - **Freemium**: ve solo nombre, apellidos y fecha de postulacion.
  - **Basico**: ve candidatos verificados + datos basicos del perfil.
  - **Pro/Enterprise**: ve perfil completo, CV, score de match IA, descarga CV.

### 5.3 Busqueda y Postulacion (Candidato)
- Buscar vacantes con filtros avanzados.
- Ver detalle de vacante.
- Postularse a vacante (enviar CV / perfil).
- Ver estado de postulaciones (enviada, en revision, aceptada, rechazada).
- Gestionar perfil profesional y CV.

### 5.4 Administracion del Sistema (Administrador / Asesor)
- CRUD de usuarios (empresas y candidatos).
- Moderacion de vacantes (aprobar, suspender, eliminar).
- Gestion de categorias de empleo.
- Gestion de requisitos predefinidos (cat_requisito con es_default).
- Dashboard con metricas: total usuarios, vacantes activas, postulaciones, etc.
- Configuracion general del sistema.
- **Verificacion de candidatos**: pipeline/checklist donde el asesor verifica que el candidato cumple los requisitos de la vacante.
  - Cada postulacion genera un checklist con los requisitos marcados en la vacante.
  - El asesor marca cada item como: aprobado, rechazado, no aplica.
  - Al completar el checklist, el candidato se marca como verificado.
  - Los candidatos verificados son visibles para empresas con plan Basico o superior.
- Configuracion de tasa de cambio USD → C$.

### 5.6 Planes de Suscripcion (CRM)

El sistema controla el acceso de las empresas segun su plan:

| Plan | Ver Postulantes | Ver Perfil | Ver CV | Candidatos Verificados | Destacar Vacantes |
|------|---------------|------------|--------|------------------------|-------------------|
| Freemium | Solo nombre, apellido y fecha | No | No | No | No |
| Basico | Datos basicos del perfil | Perfil basico | No | Si | No |
| Pro | Perfil completo | Perfil completo | Si | Si | Si |
| Enterprise | Perfil completo + score IA | Perfil completo | Si + descarga | Si | Si |

**Control de acceso por plan (crm_plan):**
- `ver_perfil_completo`: permite ver el perfil completo del candidato.
- `ver_datos_basicos`: permite ver datos basicos (profesion, ciudad, experiencia).
- `acceso_candidatos_verificados`: permite ver solo candidatos que pasaron el pipeline de verificacion.
- `acceso_cvs`: permite ver y descargar el CV del candidato.
- `max_vacantes`: limite de vacantes activas (0 = ilimitado).
- `max_postulaciones`: limite de postulaciones por vacante (0 = ilimitado).

### 5.7 Funcionalidades Transversales
- **Buscador y filtros** en toda interfaz que contenga listas o tablas.
- **Notificaciones** internas (postulaciones recibidas, cambios de estado).
- **Responsive design** en todos los layouts.

---

## 6. Requisitos No Funcionales

- **Responsive:** Todos los layouts deben funcionar en desktop, tablet y movil.
- **Rendimiento:** Carga inicial bajo 3 segundos.
- **Seguridad:** Autenticacion con sesiones, validacion de inputs, proteccion CSRF.
- **Accesibilidad:** Cumplir estandares basicos WCAG 2.1 AA.
- **SEO:** El portal publico debe ser indexable por motores de busqueda.

---

## 7. Restricciones y Supuestos

- El sistema se ejecuta sobre XAMPP (Apache + MySQL + PHP 8.2).
- Framework: CodeIgniter 4.
- El diseno visual seguira el estilo **ONE UI** con **Bento Grid**.
- La logica de negocio detallada se documentara posteriormente.

---

## 8. Pendientes de Definicion

- [ ] Logica de negocio detallada (flujo de aprobacion de vacantes, flujo de seleccion de candidatos, notificaciones).
- [x] Campos exactos del perfil de candidato y CV.
- [ ] Campos exactos del perfil de empresa.
- [ ] Categorias de empleo predefinidas.
- [ ] Requisitos predefinidos por defecto (cat_requisito con es_default).
- [ ] Politica de privacidad y tratamiento de datos.
- [ ] Integraciones de terceros (si aplica).
