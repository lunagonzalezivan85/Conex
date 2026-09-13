# CONEX

**Plataforma de reclutamiento y seleccion de personal que conecta talentos verificados con empresas.**

Reclutamiento mas rapido, candidatos verificados y menos tiempo perdido.

---

## Que es CONEX

CONEX es una plataforma web que cambia la forma en que las empresas reclutan personal. En lugar de recibir cientos de curriculums sin verificar, la empresa accede a una base de candidatos con expedientes digitales previamente revisados.

**CONEX hace la busqueda y preseleccion. La empresa decide a quien entrevistar y contratar.**

### El problema que resuelve

Cada contratacion implica tiempo y recursos:

- Publicar la vacante
- Recibir y revisar curriculums
- Contactar candidatos
- Realizar entrevistas
- Verificar informacion
- Organizar expedientes
- Coordinar pruebas y procesos de seleccion
- Capacitar al nuevo personal

Y si la persona renuncia pocos dias despues, todo ese proceso tiene que comenzar nuevamente.

### La solucion CONEX

La empresa publica su vacante, accede a candidatos verificados, revisa perfiles y selecciona. CONEX mantiene disponible una base de talento verificado de forma permanente.

**Publicar -> Consultar candidatos -> Seleccionar -> Entrevistar -> Contratar**

---

## Plataforma

### Stack tecnologico

| Componente | Tecnologia |
|---|---|
| Backend | PHP 8.2 + CodeIgniter 4 |
| Base de datos | MySQL (via XAMPP) |
| Frontend | HTML5, CSS3, JavaScript vanilla |
| IA | Google Gemini API (analisis de CV) |
| Servidor | Apache (XAMPP) |

### Requisitos

- PHP 8.2 o superior
- Extensiones: `intl`, `mbstring`, `json`, `mysqlnd`
- XAMPP (Apache + MySQL)
- Composer

### Instalacion

1. Clonar el repositorio en `c:\xampp\htdocs\CONEX`
2. Copiar `env` a `.env` y configurar `baseURL` y conexion a base de datos
3. Ejecutar migraciones: `php spark migrate`
4. Levantar Apache y MySQL desde el panel de XAMPP
5. Acceder a `http://localhost/CONEX/`

### Estructura del proyecto

```
CONEX/
├── app/
│   ├── Controllers/       # Controladores (Auth, Candidato, Empresa, Admin, Public)
│   ├── Models/            # Modelos de datos (19 modelos)
│   ├── Views/              # Vistas organizadas por rol
│   │   ├── auth/          # Login y registro
│   │   ├── candidato/     # Panel del candidato (6 vistas)
│   │   ├── empresa/       # Panel de empresa (11 vistas)
│   │   ├── admin/         # Panel administrativo (8 vistas)
│   │   ├── public/        # Paginas publicas (7 vistas)
│   │   ├── layouts/       # Layouts: panel.php y publico.php
│   │   └── shared/        # Componentes compartidos
│   ├── Libraries/         # GeminiAI - analisis de CV con IA
│   ├── Config/            # Configuracion del framework y rutas
│   └── Filters/           # Filtros de peticion
├── public/
│   ├── css/core/          # Hojas de estilo (19 archivos)
│   ├── js/core/           # Scripts JavaScript (8 archivos)
│   └── images/            # Recursos visuales
├── css/core/              # CSS servido desde raiz (espejo)
├── js/core/               # JS servido desde raiz (espejo)
├── docs/                  # Documentacion del sistema
├── writable/              # Cache, logs, uploads
├── tests/                 # Pruebas
└── vendor/                # Dependencias (Composer)
```

### Roles de usuario

| Rol | Funcionalidad |
|---|---|
| **Candidato** | Perfil profesional, CV, postulaciones, busqueda de vacantes, planes |
| **Empresa** | Publicar vacantes, ver postulantes, expedientes, verificacion, planes |
| **Admin** | Verificacion de candidatos, gestion de usuarios, vacantes, noticias |

### Modulos principales

- **Autenticacion**: Registro y login por roles
- **Perfil del candidato**: Datos personales, experiencia, educacion, habilidades, idiomas, CV
- **Gestion de vacantes**: Publicacion, edicion, cierre de vacantes
- **Postulaciones**: Aplicacion a vacantes con calculo de puntaje de match
- **Verificacion**: Proceso de 6 pasos (asignacion, contacto, documentos, entrevista, evaluacion, presentacion)
- **Analisis de CV con IA**: Google Gemini para evaluar compatibilidad candidato-vacante
- **Planes y suscripciones**: 3 planes (Gratis, Basico, Premium) con diferentes limites
- **Encuestas**: Formularios de satisfaccion para empresas
- **Noticias**: Blog/publicacion de noticias
- **Atencion al cliente**: Sistema de incidencias

### Base de datos

38 tablas organizadas con prefijos por modulo:

| Prefijo | Modulo |
|---|---|
| `auth_` | Usuarios, roles, permisos |
| `cand_` | Candidatos, CV, experiencia, educacion, habilidades, idiomas |
| `emp_` | Empresas |
| `vac_` | Vacantes, requisitos, habilidades |
| `post_` | Postulaciones, documentos, verificacion, entrevistas |
| `crm_` | Planes, contratos, pagos, leads |
| `enc_` | Encuestas y respuestas |
| `cat_` | Catalogos (categorias, habilidades, idiomas, requisitos) |
| `not_` | Noticias y notificaciones |
| `inc_` | Incidencias |
| `ai_` | Analisis de CV con IA |

---

## Ventajas

### Para la empresa

- **Ahorro de tiempo**: Convierte horas de busqueda en minutos de seleccion
- **Candidatos verificados**: Acceso a expedientes digitales previamente revisados
- **Proceso estructurado**: Flujo de verificacion de 6 pasos estandarizado
- **Base de talento permanente**: No empieza desde cero en cada vacante
- **Filtrado inteligente**: Puntaje de match calculado automaticamente
- **Reduccion de rotacion**: Mejor seleccion = menos renuncias tempranas

### Para el candidato

- **Perfil profesional completo**: Un solo lugar para toda su informacion
- **Postulaciones simplificadas**: Un clic para aplicar
- **Puntaje de compatibilidad**: Visualiza su match con cada vacante
- **Seguimiento del proceso**: Ve el estado de su postulacion en tiempo real
- **Analisis de CV con IA**: Recibe feedback automatico de su CV

---

## Material para vendedores

### Presentacion comercial

**CONEX - Reclutamiento mas rapido, candidatos verificados y menos tiempo perdido.**

#### Que esta comprando realmente la empresa

No esta pagando simplemente por recibir curriculums. Esta pagando por **ahorrar tiempo** en la busqueda y **reducir el trabajo operativo** del reclutamiento.

Por una tarifa mensual, la empresa obtiene acceso a la plataforma CONEX para:

- Publicar sus vacantes
- Consultar candidatos disponibles
- Acceder a expedientes digitales
- Encontrar personal previamente verificado
- Filtrar y seleccionar candidatos
- Reducir el tiempo dedicado a la busqueda inicial
- Mantener un flujo constante de candidatos para futuras vacantes

#### Como funciona CONEX

1. **Publicar vacantes**: La empresa indica que puesto necesita y cuales son las caracteristicas del perfil que busca.
2. **Acceder a candidatos verificados**: Los candidatos cuentan con informacion y documentacion previamente revisada dentro de su expediente digital.
3. **Revisar y seleccionar**: La empresa consulta los perfiles disponibles y selecciona los candidatos que mejor se ajustan a sus necesidades.
4. **Contactar y contratar**: CONEX facilita el proceso para que la empresa pueda concentrarse en lo mas importante: evaluar al candidato y tomar la decision de contratacion.
5. **Mantener su propio proceso interno**: El candidato todavia debera pasar por las entrevistas, pruebas, practicas o cualquier otro proceso que la empresa requiera antes de contratarlo.

#### El verdadero ahorro

Imagine que Recursos Humanos necesita cubrir una vacante.

**Sin CONEX**: Alguien debe invertir horas buscando candidatos, revisando curriculums, contactando personas y verificando informacion.

**Con CONEX**: La empresa entra a la plataforma, publica la vacante y puede comenzar a revisar candidatos previamente verificados.

**Eso convierte horas de busqueda en minutos de seleccion.**

La pregunta deja de ser: *"Cuanto cuesta CONEX?"*

Y pasa a ser: *"Cuanto vale para mi empresa recuperar todas las horas que actualmente invertimos buscando candidatos?"*

#### Una suscripcion que trabaja para tu empresa

Por una tarifa mensual, CONEX se convierte en una herramienta permanente de reclutamiento. No tienes que comenzar desde cero cada vez que aparece una vacante.

**Publicar -> Consultar candidatos -> Seleccionar -> Entrevistar -> Contratar**

Mientras CONEX se encarga de mantener disponible una base de talento verificado.

### Planes disponibles

| Plan | Precio | Vacantes | Caracteristicas |
|---|---|---|---|
| **Gratis** | $0/mes | 3 activas | Perfil basico, datos basicos de candidatos |
| **Basico** | $29/mes | 10 activas | Perfil completo, acceso a CVs, destacar vacantes |
| **Premium** | $79/mes | Ilimitadas | Todas las funciones, soporte prioritario, candidatos verificados |

### Argumentos de venta clave

1. **Costo de contratacion fallida**: Cuanto le cuesta a tu empresa contratar a una persona que renuncia a los pocos dias?
2. **Tiempo = Dinero**: Cuanto vale para tu empresa recuperar todas las horas que invierten buscando candidatos?
3. **Base permanente**: No empiezas desde cero cada vez que aparece una vacante
4. **Verificacion incluida**: Candidatos con documentacion revisada antes de contactarlos
5. **IA integrada**: Analisis automatico de compatibilidad candidato-vacante

### Tagline

> **CONEX**
> Menos tiempo buscando.
> Mas tiempo contratando.

---

## Comandos utiles

```bash
# Iniciar servidor de desarrollo
php spark serve

# Migraciones
php spark migrate

# Crear una migracion
php spark make:migration NombreTabla

# Crear un modelo
php spark make:model NombreModelo

# Crear un controlador
php spark make:controller NombreController
```

## Arquitectura

El proyecto sigue el patron MVC de CodeIgniter 4:

- **Controllers**: Reciben la peticion HTTP, validan entrada y delegan a servicios.
- **Models**: Encapsulan el acceso a base de datos y reglas de persistencia.
- **Views**: Plantillas HTML organizadas por rol, con CSS y JS en archivos externos.

### Convenciones

- CSS y JS en archivos independientes (`public/css/core/` y `public/js/core/`)
- Servidos desde raiz (`css/core/` y `js/core/`) via configuracion de Apache
- Sin estilos inline ni scripts embebidos en vistas PHP
- Prefijos en tablas de base de datos por modulo
- Variables CSS para colores de marca (`--brand-orange`, `--brand-teal`, `--brand-navy`)

## Documentacion

La documentacion del sistema se encuentra en la carpeta [`docs/`](docs/).

## Licencia

Proyecto privado. Todos los derechos reservados.
