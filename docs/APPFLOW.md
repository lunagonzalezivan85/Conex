# APPFLOW - Flujo de la Aplicacion

## CONEX - Sistema de Reclutamiento de Personal

**Version:** 1.0  
**Fecha:** 2026-09-12  
**Estado:** Borrador

---

## 1. Flujo General de Navegacion

```
                    [Visitante]
                         |
            +------------+------------+
            |                         |
     [Explorar Vacantes]        [Iniciar Sesion]
            |                         |
            v                    +-----+-----+
     [Ver Detalle Vacante]      |           |
            |              [Registrarse]  [Login OK]
            |                    |           |
            v               +----+----+      |
     [Login / Registro]    |         |      |
                         [Candidato] [Empresa]
                              |         |
                              v         v
                         [Layout    [Layout
                          Candidato] Empresa]
                              |         |
                              |    [Admin/Asesor]
                              |         |
                              |    [Layout Admin]
                              |         |
                              v         v
                         [Dashboard segun rol]
```

---

## 2. Flujos por Rol

### 2.1 Flujo Publico (Visitante no autenticado)

```
[Home / Landing]
    |
    +---> [Buscar vacantes] ---> [Ver listado con filtros]
    |                              |
    |                              +---> [Ver detalle de vacante]
    |                                     |
    |                                     +---> [Postularse] ---> [Redirige a Login]
    |
    +---> [Iniciar Sesion] ---> [Formulario Login]
    |                              |
    |                              +---> [Login OK] ---> [Redirige a Dashboard segun rol]
    |                              +---> [Login Falla] ---> [Muestra error]
    |
    +---> [Registrarse] ---> [Elegir tipo de cuenta]
                                 |
                    +------------+------------+
                    |                         |
              [Candidato]                 [Empresa]
                    |                         |
                    v                         v
             [Form registro basico]    [Form registro empresa]
             [nombre, apellido,        [nombre, apellido,
              usuario, email,           usuario, email,
              telefono, password]       telefono, password]
                    |                         |
                    v                         v
             [Registro OK]            [Registro OK]
                    |                         |
                    v                         v
             [Login automatico] ---> [Dashboard segun rol]
                    |
                    v
             [Sistema verifica perfil_completo]
                    |
              +-----+-----+
              |           |
          [Completo]  [Incompleto]
              |           |
              v           v
          [Dashboard]  [Mostrar Wizard opcional]
                          |
                    +-----+-----+
                    |           |
              [Completar]  [Saltar]
                    |           |
                    v           v
              [Wizard pasos]  [Dashboard]
                    |       (con medidor %
                    v        de perfil)
              [Dashboard]
```

### 2.2 Flujo Candidato

#### 2.2.1 Registro y Wizard de Perfil

```
[Registro basico]
  Campos: nombre, apellido, usuario, email, telefono, password
    |
    v
[Cuenta creada: auth_user + cand_candidato (vacio)]
    |
    v
[Login automatico -> Dashboard Candidato]
    |
    v
[Sistema calcula porcentaje_perfil]
    |
    +---> Si porcentaje < 100%: mostrar banner "Completa tu perfil"
    |         |
    |         +---> [Abrir Wizard] (opcional, el candidato puede saltar)
    |         |
    |         +---> [Saltar] -> Dashboard con medidor de % visible
    |
    +---> Wizard pasos:
          |
          Paso 1: Datos personales
          - fecha_nacimiento, profesion, sobre_mi
          - direccion, ciudad, region, pais (geolocalizacion)
          - disponibilidad, modalidad_preferida
          |
          Paso 2: Experiencia laboral
          - Agregar experiencias (empresa, cargo, fechas, descripcion)
          - Marcar "actual" si es empleo vigente
          |
          Paso 3: Educacion
          - Agregar estudios (nivel, institucion, titulo, fechas)
          |
          Paso 4: Habilidades e Idiomas
          - Seleccionar habilidades con nivel
          - Seleccionar idiomas con nivel
          |
          Paso 5: Puestos deseados
          - Agregar puestos que busca (ej: "Desarrollador PHP", "Analista")
          - Asociar categoria opcional
          |
          Paso 6: Expectativas y enlaces
          - salario_esperado_usd (en dolares)
          - portafolio_url, linkedin_url
          |
          Paso 7: Subir CV (PDF)
          - Subir archivo
          - Gemini analiza el CV automaticamente
          - Sistema extrae datos y pre-llena campos faltantes
          - Candidato revisa y confirma
          |
          v
    [Porcentaje_perfil recalculado]
    [Dashboard con medidor actualizado]
```

#### 2.2.2 Calculo de Porcentaje de Perfil

```
[Porcentaje = 0%]

Criterios (cada uno suma puntos):
  +10% - Datos personales basicos (fecha_nac, profesion, sobre_mi)
  +10% - Direccion y geolocalizacion (direccion, ciudad, lat, lng)
  +10% - Al menos 1 experiencia laboral
  +10% - Al menos 1 registro de educacion
  +10% - Al menos 3 habilidades
  +5%  - Al menos 1 idioma
  +10% - Al menos 1 puesto deseado
  +10% - Expectativa salarial definida (salario_esperado_usd)
  +5%  - Portafolio o LinkedIn
  +10% - CV subido (al menos 1 en cand_cv)
  +10% - Disponibilidad y modalidad definidas
  ----
  100% total

El porcentaje se muestra como barra circular en el dashboard.
Si < 100%, muestra enlace "Completa tu perfil" al wizard.
```

#### 2.2.3 Subida de CV con Auto-llenado por Gemini

```
[Candidato sube CV en PDF]
    |
    v
[Guardar archivo en servidor -> cand_cv]
    |
    v
[Marcar como es_principal = true (si es el primero)]
    |
    v
[Enviar CV a Gemini para analisis]
    |
    v
[ai_analisis_cv: estado = procesando]
    |
    v
[Gemini extrae:
  - Experiencia laboral (empresa, cargo, fechas, descripcion)
  - Educacion (institucion, titulo, nivel, fechas)
  - Habilidades detectadas
  - Idiomas detectados
  - Posibles puestos/profesion
  - Datos de contacto (linkedin, portafolio)]
    |
    v
[Sistema pre-llena:
  - cand_experiencia (si no tiene)
  - cand_educacion (si no tiene)
  - cand_habilidad (sugeridas, no confirmadas)
  - cand_idioma (sugeridos)
  - profesion, linkedin_url, portafolio_url
  - puestos deseados sugeridos]
    |
    v
[Candidato revisa datos extraidos]
    |
    +---> [Aceptar] -> Guardar todo, recalcular porcentaje
    +---> [Editar] -> Modificar antes de guardar
    +---> [Descartar] -> No guardar datos extraidos
    |
    v
[ai_analisis_cv: estado = completado]
[porcentaje_perfil recalculado]
```

#### 2.2.4 Dashboard del Candidato

```
[Dashboard Candidato]
    |
    +---> [Medidor de % de perfil (barra circular)]
    |        |
    |        +---> Si < 100%: "Completa tu perfil" -> Wizard
    |
    +---> [Buscar Vacantes]
    |        |
    |        +---> [Filtros: categoria, ubicacion, modalidad, salario, tipo]
    |        |
    |        +---> [Ver detalle de vacante]
    |               |
    |               +---> [Postularse]
    |                      |
    |                      +---> [Seleccionar CV a enviar]
    |                      +---> [Confirmar postulacion]
    |                      +---> [Postulacion enviada]
    |                      +---> [Notificacion a Empresa]
    |                      +---> [Gemini analiza match CV vs Vacante]
    |                             +---> [Score de match visible para empresa]
    |
    +---> [Mis Postulaciones]
    |        |
    |        +---> [Ver estado: enviada / en_revision / aceptada / rechazada]
    |        +---> [Filtrar por estado]
    |        +---> [Ver score de match IA]
    |
    +---> [Mi Perfil / CV]
             |
             +---> [Editar datos personales]
             +---> [Gestionar experiencias laborales]
             +---> [Gestionar educacion]
             +---> [Gestionar habilidades e idiomas]
             +---> [Gestionar puestos deseados]
             +---> [Subir / reemplazar CV (PDF)]
             +---> [Ver analisis IA del CV]
             +---> [Editar expectativas (salario USD, disponibilidad, modalidad)]
             +---> [Editar enlaces (portafolio, linkedin)]
             +---> [Guardar cambios] -> Recalcular porcentaje
```

### 2.3 Flujo Empresa

```
[Dashboard Empresa]
    |
    +---> [Mis Vacantes]
    |        |
    |        +---> [Buscar y filtrar vacantes por estado]
    |        |
    |        +---> [Crear Vacante]
    |        |      |
    |        |      +---> [Formulario: titulo, descripcion, categoria,]
    |        |      |    [ubicacion, tipo contrato, salario (USD), modalidad,]
    |        |      |    [años de experiencia, vacantes disponibles,]
    |        |      |    [max_postulantes]
    |        |      |
    |        |      +---> [Seleccionar requisitos predefinidos]
    |        |      |      (lista de cat_requisito con es_default, marcar los necesarios)
    |        |      |
    |        |      +---> [Guardar como borrador] o [Publicar]
    |        |
    |        +---> [Editar Vacante]
    |        |      |
    |        |      +---> [Modificar campos]
    |        |      +---> [Guardar cambios]
    |        |
    |        +---> [Cerrar Vacante]
    |        |      |
    |        |      +---> [Confirmar cierre] ---> [Vacante marcada como cerrada]
    |        |
    |        +---> [Ver Postulaciones de una Vacante]
    |               |
    |               +---> [Lista de candidatos postulados]
    |               |      [con buscador y filtros]
    |               |
    |               +---> [Segun plan de la empresa:]
    |               |      |
    |               |      +---> [Freemium]:
    |               |      |      Solo ve: nombre, apellidos, fecha de postulacion
    |               |      |      No puede ver perfil ni CV
    |               |      |
    |               |      +---> [Basico]:
    |               |      |      Ve candidatos verificados + datos basicos
    |               |      |      (profesion, ciudad, experiencia, score match)
    |               |      |      No puede ver CV
    |               |      |
    |               |      +---> [Pro/Enterprise]:
    |               |      |      Ve perfil completo, CV, score IA, descarga CV
    |               |
    |               +---> [Cambiar estado de postulacion]
    |                      |
    |                      +---> [Aceptar] ---> [Notificacion al candidato]
    |                      +---> [Rechazar] ---> [Notificacion al candidato]
    |                      +---> [Marcar en revision]
    |
    +---> [Mi Plan / Suscripcion]
    |        |
    |        +---> [Ver plan actual]
    |        +---> [Ver limites del plan]
    |        +---> [Upgrade de plan] ---> [CRM: contacto con asesor]
    |
    +---> [Perfil de Empresa]
             |
             +---> [Editar datos: razon social, rubro, descripcion, logo]
             +---> [Guardar cambios]
```

### 2.4 Flujo Administrador / Asesor

```
[Dashboard Admin]
    |
    +---> [Gestion de Usuarios]
    |        |
    |        +---> [Listar todos los usuarios]
    |        |      [buscador + filtros: rol, estado]
    |        |
    |        +---> [Ver detalle de usuario]
    |        +---> [Suspender / Activar usuario]
    |        +---> [Crear usuario Asesor / Admin]
    |        +---> [Editar usuario]
    |
    +---> [Moderacion de Vacantes]
    |        |
    |        +---> [Listar todas las vacantes]
    |        |      [buscador + filtros: estado, empresa, categoria]
    |        |
    |        +---> [Aprobar vacante]
    |        +---> [Suspender vacante]
    |        +---> [Eliminar vacante]
    |
    +---> [Verificacion de Candidatos]
    |        |
    |        +---> [Listar postulaciones pendientes de verificacion]
    |        |      [filtrar por vacante, empresa, fecha]
    |        |
    |        +---> [Abrir checklist de verificacion]
    |        |      |
    |        |      +---> [Sistema genera checklist con requisitos de la vacante]
    |        |      |      (items de cat_requisito marcados en vac_requisito)
    |        |      |
    |        |      +---> [Asesor revisa cada item:]
    |        |      |      +---> [Aprobado] (cumple el requisito)
    |        |      |      +---> [Rechazado] (no cumple)
    |        |      |      +---> [No aplica]
    |        |      |      +---> [Agregar comentario por item]
    |        |      |
    |        |      +---> [Completar verificacion]
    |        |             |
    |        |             +---> [Todos aprobados] ---> [Candidato verificado]
    |        |             |                        [Visible para empresas Basico+]
    |        |             |
    |        |             +---> [Algun rechazado] ---> [Candidato no verificado]
    |        |             |                           [Notifica a empresa y candidato]
    |        |
    |        +---> [Ver historial de verificaciones]
    |
    +---> [Gestion de Categorias]
    |        |
    |        +---> [Listar categorias]
    |        +---> [Crear categoria]
    |        +---> [Editar categoria]
    |        +---> [Activar / Desactivar categoria]
    |
    +---> [Gestion de Requisitos Predefinidos]
    |        |
    |        +---> [Listar requisitos (cat_requisito)]
    |        +---> [Crear requisito]
    |        +---> [Marcar como es_default (aparece por defecto al crear vacante)]
    |        +---> [Editar / Activar / Desactivar requisito]
    |
    +---> [Gestion de Planes (CRM)]
    |        |
    |        +---> [Listar planes]
    |        +---> [Crear / Editar plan]
    |        +---> [Configurar permisos del plan]
    |        |      (ver_perfil_completo, ver_datos_basicos, acceso_candidatos_verificados, acceso_cvs)
    |        +---> [Activar / Desactivar plan]
    |
    +---> [Reportes / Estadisticas]
    |        |
    |        +---> [Total usuarios por rol]
    |        +---> [Vacantes activas vs cerradas]
    |        +---> [Postulaciones por periodo]
    |        +---> [Empresas mas activas]
    |        +---> [Tasa de verificacion de candidatos]
    |
    +---> [Configuracion del Sistema] (solo Administrador)
             |
             +---> [Parametros generales]
             +---> [Tasa de cambio USD → C$]
             +---> [Gestion de roles y permisos]
```

---

## 3. Flujos Transversales

### 3.1 Flujo de Autenticacion

```
[Usuario ingresa credenciales]
         |
         v
[AuthService valida email + password]
         |
    +----+----+
    |         |
 [OK]      [Fallo]
    |         |
    v         v
[Crear    [Mostrar error:
 sesion]   credenciales
    |      invalidas]
    v
[Redirigir segun rol]
    |
    +---> admin/asesor -> /admin
    +---> empresa      -> /empresa
    +---> candidato    -> /candidato
```

### 3.2 Flujo de Registro

```
[Visitante elige "Registrarse"]
         |
         v
[Selecciona tipo de cuenta]
    |              |
    v              v
[Candidato]    [Empresa]
    |              |
    v              v
[Formulario    [Formulario
 comun:         comun:
 nombre,       nombre,
 email,        email,
 password]     password]
    |              |
    v              v
[Datos extra   [Datos extra
 candidato:    empresa:
 apellidos,    razon_social,
 telefono,     rubro,
 profesion]    telefono,
    |          direccion]
    |              |
    +------+-------+
           |
           v
    [UsuarioService crea usuario]
           |
           v
    [Crea perfil segun rol]
    (CandidatoModel o EmpresaModel)
           |
           v
    [Sesion iniciada automaticamente]
           |
           v
    [Redirige a dashboard correspondiente]
```

### 3.3 Flujo de Postulacion

```
[Candidato ve detalle de vacante]
         |
         v
[Click "Postularse"]
         |
         v
[Verifica que el CV este completo?]
    |              |
   [Si]           [No]
    |              |
    v              v
[Confirmar     [Redirige a
 postulacion]  completar perfil]
    |
    v
[PostulacionService crea registro]
    |
    v
[NotificacionService envia notificacion a Empresa]
    |
    v
[Postulacion visible en "Mis Postulaciones" del candidato]
```

### 3.4 Flujo de Notificaciones

```
[Evento del sistema]
         |
         v
[NotificacionService.crear(usuario_id, tipo, titulo, mensaje)]
         |
         v
[Registro en tabla notificaciones]
         |
         v
[Visible en campana de notificaciones del usuario]
         |
         v
[Usuario marca como leida]
```

Eventos que generan notificaciones:

| Evento                              | Destinatario  | Tipo            |
|-------------------------------------|---------------|-----------------|
| Nueva postulacion a vacante         | Empresa       | `postulacion`   |
| Postulacion aceptada                | Candidato     | `postulacion`   |
| Postulacion rechazada               | Candidato     | `postulacion`   |
| Postulacion en revision             | Candidato     | `postulacion`   |
| Vacante aprobada por admin          | Empresa       | `vacante`       |
| Vacante suspendida por admin        | Empresa       | `vacante`       |
| Usuario suspendido                  | Usuario       | `cuenta`        |

---

## 4. Mapa de Pantallas

### 4.1 Publico

| #  | Pantalla                | Ruta                  | Descripcion                          |
|----|------------------------|-----------------------|--------------------------------------|
| P1 | Home / Landing         | `/`                   | Banner + vacantes destacadas         |
| P2 | Listado de Vacantes    | `/vacantes`           | Grid con buscador y filtros          |
| P3 | Detalle de Vacante     | `/vacantes/{id}`      | Info completa + boton postularse     |
| P4 | Login                  | `/login`              | Formulario de inicio de sesion       |
| P5 | Registro               | `/registro`           | Seleccion de tipo + formulario       |

### 4.2 Administracion

| #  | Pantalla                | Ruta                        | Descripcion                          |
|----|------------------------|-----------------------------|--------------------------------------|
| A1 | Dashboard              | `/admin`                    | Metricas + graficos                  |
| A2 | Usuarios               | `/admin/usuarios`           | Tabla CRUD con filtros               |
| A3 | Detalle Usuario        | `/admin/usuarios/{id}`       | Perfil + acciones                    |
| A4 | Vacantes (Moderacion)  | `/admin/vacantes`            | Tabla con filtros + aprobar/suspender|
| A5 | Categorias             | `/admin/categorias`         | CRUD de categorias                   |
| A6 | Reportes               | `/admin/reportes`           | Estadisticas                         |
| A7 | Configuracion          | `/admin/configuracion`      | Parametros del sistema (solo admin)  |

### 4.3 Empresa

| #  | Pantalla                | Ruta                              | Descripcion                          |
|----|------------------------|-----------------------------------|--------------------------------------|
| E1 | Dashboard              | `/empresa`                        | Resumen de vacantes y postulaciones  |
| E2 | Mis Vacantes           | `/empresa/vacantes`               | Tabla con buscador y filtros        |
| E3 | Crear Vacante          | `/empresa/vacantes/crear`         | Formulario de nueva vacante          |
| E4 | Editar Vacante         | `/empresa/vacantes/{id}/editar`   | Formulario de edicion                |
| E5 | Postulaciones          | `/empresa/postulaciones`          | Lista general de postulaciones       |
| E6 | Postulaciones por Vac. | `/empresa/vacantes/{id}/postulaciones` | Candidatos postulados a una vacante |
| E7 | Perfil Empresa         | `/empresa/perfil`                | Editar datos de la empresa           |

### 4.4 Candidato

| #  | Pantalla                | Ruta                          | Descripcion                          |
|----|------------------------|-------------------------------|--------------------------------------|
| C1 | Dashboard              | `/candidato`                  | Resumen + vacantes recomendadas      |
| C2 | Buscar Vacantes        | `/candidato/vacantes`         | Buscador con filtros avanzados       |
| C3 | Mis Postulaciones      | `/candidato/postulaciones`    | Lista con estado de cada postulacion |
| C4 | Mi Perfil / CV         | `/candidato/perfil`           | Editar perfil + subir CV             |

---

## 5. Flujo de Errores y Casos Especiales

### 5.1 Errores de Autenticacion

| Escenario                        | Comportamiento                          |
|----------------------------------|-----------------------------------------|
| Credenciales invalidas           | Mensaje de error, mantener en login     |
| Cuenta suspendida                | Mensaje "cuenta suspendida", no login   |
| Acceso a ruta sin permisos       | Redirigir a dashboard del rol           |
| Acceso a ruta sin sesion          | Redirigir a login con mensaje           |

### 5.2 Estados de Vacante

```
[borrador] ---> [publicada] ---> [cerrada]
                    |
                    +---> [suspendida] ---> [publicada] (reactivar)
```

### 5.3 Estados de Postulacion

```
[enviada] ---> [en_revision] ---> [aceptada]
    |                              |
    |                              +---> [rechazada]
    +---> [rechazada]
```

---

## 6. Pendientes de Flujo

- [ ] Definir flujo de recuperacion de contrasena.
- [ ] Definir si las vacantes requieren aprobacion del admin antes de publicarse.
- [ ] Definir si un candidato puede retirar una postulacion.
- [ ] Definir si una empresa puede contactar directamente al candidato.
- [ ] Definir flujo de mensajeria interna entre empresa y candidato.
- [ ] Definir si el asesor tiene permisos restringidos vs admin.
