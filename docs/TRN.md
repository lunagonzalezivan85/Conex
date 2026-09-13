# TRN - Technical Requirements Notes

## CONEX - Sistema de Reclutamiento de Personal

**Version:** 1.0  
**Fecha:** 2026-09-12  
**Estado:** Borrador

---

## 1. Stack Tecnologico

| Componente    | Tecnologia                          |
|--------------|-------------------------------------|
| Framework     | CodeIgniter 4                       |
| Lenguaje      | PHP 8.2                             |
| Base de Datos | MySQL (via XAMPP)                   |
| Servidor Web  | Apache (XAMPP)                     |
| Frontend      | HTML5, CSS3, JavaScript (vanilla o libreria ligera) |
| Diseno        | ONE UI / Bento Grid, responsive     |
| Dependencias  | Composer                            |

---

## 2. Arquitectura del Proyecto

### 2.1 Patron de Arquitectura

El proyecto sigue una arquitectura en **3 capas** dentro de CodeIgniter 4:

```
Peticion HTTP
    |
    v
[Controller]  -- Valida entrada, delega logica
    |
    v
[Service]     -- Logica de negocio, orquesta modelos
    |
    v
[Model]       -- Acceso a datos, consultas, persistencia
    |
    v
[Base de Datos]
```

### 2.2 Estructura de Directorios

```
app/
├── Controllers/
│   ├── Public/           # Controladores del portal publico
│   ├── Admin/            # Controladores del panel de administracion
│   ├── Empresa/          # Controladores del panel de empresa
│   └── Candidato/        # Controladores del panel de candidato
├── Models/
│   ├── UserModel.php
│   ├── EmpresaModel.php
│   ├── CandidatoModel.php
│   ├── VacanteModel.php
│   ├── PostulacionModel.php
│   └── CategoriaModel.php
├── Services/
│   ├── AuthService.php         # Autenticacion y sesiones
│   ├── VacanteService.php      # Logica de vacantes
│   ├── PostulacionService.php  # Logica de postulaciones
│   ├── UsuarioService.php      # Gestion de usuarios
│   └── NotificacionService.php # Notificaciones internas
├── Views/
│   ├── layouts/
│   │   ├── publico/        # Layout del portal publico
│   │   ├── admin/          # Layout de administracion
│   │   ├── empresa/        # Layout de empresa
│   │   └── candidato/      # Layout de candidato
│   ├── partials/           # Componentes compartidos (header, footer, sidebar)
│   └── components/         # Componentes reutilizables (cards, tables, forms)
├── Filters/
│   ├── AuthFilter.php          # Verifica sesion activa
│   ├── RoleFilter.php          # Verifica rol del usuario
│   └── GuestFilter.php         # Solo para no autenticados
├── Config/
│   ├── Routes.php              # Definicion de rutas
│   ├── Filters.php             # Registro de filtros
│   └── Database.php            # Configuracion de BD
└── Libraries/
```

### 2.3 Convenciones de Nomenclatura

- **Controllers:** PascalCase, sufijo `Controller` opcional. Agrupados por layout en subcarpetas.
- **Models:** PascalCase + sufijo `Model`. Ej: `VacanteModel`.
- **Services:** PascalCase + sufijo `Service`. Ej: `VacanteService`.
- **Tablas de BD:** snake_case con prefijo por dominio. Ej: `auth_user`, `vac_vacante`, `crm_plan`.
- **Prefijos de tabla:** `auth_` (autenticacion/roles), `cat_` (catalogos), `emp_` (empresa), `cand_` (candidato), `vac_` (vacantes), `post_` (postulaciones), `crm_` (CRM/planes/contratos), `not_` (notificaciones), `ai_` (analisis IA/Gemini).
- **Rutas:** kebab-case, agrupadas por layout. Ej: `/empresa/vacantes/crear`.

---

## 3. Modelo de Datos

El esquema usa **prefijos por dominio** para organizar las tablas. Total: **32 tablas** en 9 modulos.

### 3.1 Modulo: auth_ (Autenticacion y Roles)

#### auth_role
| Campo         | Tipo         | Descripcion                          |
|--------------|-------------|--------------------------------------|
| id           | INT PK AI    | Identificador unico                  |
| nombre       | VARCHAR(50)  | Nombre del rol                       |
| slug         | VARCHAR(50)  | Slug unico (admin, asesor, empresa, candidato) |
| descripcion  | TEXT         | Descripcion del rol                  |
| es_asignable | BOOLEAN      | Si el rol puede ser elegido en registro |
| created_at   | DATETIME     |                                      |
| updated_at   | DATETIME     |                                      |

#### auth_user
| Campo           | Tipo         | Descripcion                          |
|----------------|-------------|--------------------------------------|
| id             | INT PK AI    | Identificador unico                  |
| role_id        | INT FK       | Referencia a auth_role.id            |
| nombre         | VARCHAR(100) | Nombre                              |
| apellido       | VARCHAR(100) | Apellido                            |
| email          | VARCHAR(150) | Email unico                         |
| password       | VARCHAR(255) | Hash bcrypt                         |
| telefono       | VARCHAR(20)  | Telefono                            |
| avatar         | VARCHAR(255) | Ruta del avatar                      |
| estado         | ENUM         | activo, inactivo, suspendido        |
| perfil_completo| BOOLEAN      | Si completo el perfil obligatorio    |
| last_login     | DATETIME     | Ultimo inicio de sesion              |
| created_at     | DATETIME     |                                      |
| updated_at     | DATETIME     |                                      |
| deleted_at     | DATETIME     | Soft delete                          |

#### auth_permission
| Campo       | Tipo         | Descripcion                          |
|------------|-------------|--------------------------------------|
| id         | INT PK AI    | Identificador unico                  |
| nombre     | VARCHAR(100) | Nombre del permiso                  |
| slug       | VARCHAR(100) | Slug unico                          |
| modulo     | VARCHAR(50)  | Modulo al que pertenece              |
| descripcion| TEXT         | Descripcion                          |
| created_at | DATETIME     |                                      |
| updated_at | DATETIME     |                                      |

#### auth_role_permission
| Campo         | Tipo     | Descripcion                          |
|--------------|---------|--------------------------------------|
| id           | INT PK AI| Identificador unico                  |
| role_id      | INT FK   | Referencia a auth_role.id            |
| permission_id| INT FK   | Referencia a auth_permission.id      |
| created_at   | DATETIME |                                      |

### 3.2 Modulo: cat_ (Catalogos)

#### cat_categoria
| Campo       | Tipo         | Descripcion                          |
|------------|-------------|--------------------------------------|
| id         | INT PK AI    | Identificador unico                  |
| nombre     | VARCHAR(100) | Nombre de la categoria              |
| slug       | VARCHAR(120) | Slug unico                          |
| descripcion| TEXT         | Descripcion                          |
| icono      | VARCHAR(100) | Icono representativo                 |
| estado     | ENUM         | activo, inactivo                    |
| created_at | DATETIME     |                                      |
| updated_at | DATETIME     |                                      |

#### cat_tipo_contrato
| Campo       | Tipo        | Descripcion                          |
|------------|-------------|--------------------------------------|
| id         | INT PK AI   | Identificador unico                  |
| nombre     | VARCHAR(80) | Nombre (tiempo completo, etc.)      |
| slug       | VARCHAR(80) | Slug unico                          |
| descripcion| TEXT        | Descripcion                          |
| estado     | ENUM        | activo, inactivo                    |
| created_at | DATETIME     |                                      |
| updated_at | DATETIME     |                                      |

#### cat_habilidad
| Campo        | Tipo         | Descripcion                          |
|-------------|-------------|--------------------------------------|
| id          | INT PK AI    | Identificador unico                  |
| nombre      | VARCHAR(100) | Nombre de la habilidad              |
| slug        | VARCHAR(120) | Slug unico                          |
| categoria_id| INT FK       | Referencia a cat_categoria.id (nullable) |
| estado      | ENUM         | activo, inactivo                    |
| created_at  | DATETIME     |                                      |
| updated_at  | DATETIME     |                                      |

#### cat_nivel_educacion
| Campo       | Tipo        | Descripcion                          |
|------------|-------------|--------------------------------------|
| id         | INT PK AI   | Identificador unico                  |
| nombre     | VARCHAR(80) | Nombre (primaria, secundaria, etc.) |
| slug       | VARCHAR(80) | Slug unico                          |
| orden      | INT          | Orden de jerarquia                   |
| estado     | ENUM        | activo, inactivo                    |
| created_at | DATETIME     |                                      |
| updated_at | DATETIME     |                                      |

#### cat_nivel_experiencia
| Campo         | Tipo        | Descripcion                          |
|--------------|-------------|--------------------------------------|
| id           | INT PK AI   | Identificador unico                  |
| nombre       | VARCHAR(80) | Nombre (junior, senior, etc.)       |
| slug         | VARCHAR(80) | Slug unico                          |
| anios_minimos| INT          | Anios minimos de experiencia        |
| anios_maximos| INT          | Anios maximos (nullable)             |
| orden        | INT          | Orden                                |
| estado       | ENUM        | activo, inactivo                    |
| created_at   | DATETIME     |                                      |
| updated_at   | DATETIME     |                                      |

#### cat_requisito
| Campo       | Tipo         | Descripcion                          |
|------------|-------------|--------------------------------------|
| id         | INT PK AI    | Identificador unico                  |
| nombre     | VARCHAR(100) | Nombre del requisito                |
| slug       | VARCHAR(120) | Slug unico                          |
| tipo       | ENUM         | obligatorio, deseable               |
| estado     | ENUM         | activo, inactivo                    |
| created_at | DATETIME     |                                      |
| updated_at | DATETIME     |                                      |

#### cat_idioma
| Campo       | Tipo        | Descripcion                          |
|------------|-------------|--------------------------------------|
| id         | INT PK AI   | Identificador unico                  |
| nombre     | VARCHAR(80) | Nombre del idioma                   |
| slug       | VARCHAR(80) | Slug unico                          |
| estado     | ENUM        | activo, inactivo                    |
| created_at | DATETIME     |                                      |
| updated_at | DATETIME     |                                      |

### 3.3 Modulo: emp_ (Empresa)

#### emp_empresa
| Campo         | Tipo           | Descripcion                          |
|--------------|---------------|--------------------------------------|
| id           | INT PK AI      | Identificador unico                  |
| user_id      | INT FK         | Referencia a auth_user.id            |
| razon_social | VARCHAR(150)   | Nombre de la empresa                 |
| ruc          | VARCHAR(20)    | RUC / identificacion fiscal          |
| rubro        | VARCHAR(100)   | Sector empresarial                   |
| descripcion  | TEXT           | Descripcion de la empresa            |
| sitio_web    | VARCHAR(255)   | URL del sitio web                    |
| logo         | VARCHAR(255)   | Ruta del logo                        |
| telefono     | VARCHAR(20)    | Telefono                             |
| direccion    | VARCHAR(255)   | Direccion fisica                     |
| ciudad       | VARCHAR(100)   | Ciudad                               |
| region       | VARCHAR(100)   | Region / estado                      |
| pais         | VARCHAR(100)   | Pais                                 |
| latitud      | DECIMAL(10,8)  | Geolocalizacion                     |
| longitud     | DECIMAL(11,8)  | Geolocalizacion                     |
| verificada   | BOOLEAN        | Si la empresa esta verificada        |
| created_at   | DATETIME       |                                      |
| updated_at   | DATETIME       |                                      |
| deleted_at   | DATETIME       | Soft delete                          |

### 3.4 Modulo: cand_ (Candidato)

#### cand_candidato
| Campo               | Tipo           | Descripcion                          |
|--------------------|---------------|--------------------------------------|
| id                 | INT PK AI      | Identificador unico                  |
| user_id            | INT FK         | Referencia a auth_user.id            |
| apellidos           | VARCHAR(100)   | Apellidos                            |
| fecha_nacimiento    | DATE           | Fecha de nacimiento                  |
| profesion           | VARCHAR(100)   | Profesion u oficio                  |
| nivel_experiencia_id| INT FK         | Referencia a cat_nivel_experiencia.id |
| sobre_mi           | TEXT           | Descripcion personal                 |
| direccion          | VARCHAR(255)   | Direccion                            |
| ciudad             | VARCHAR(100)   | Ciudad                               |
| region             | VARCHAR(100)   | Region                               |
| pais               | VARCHAR(100)   | Pais                                 |
| latitud            | DECIMAL(10,8)  | Geolocalizacion                     |
| longitud           | DECIMAL(11,8)  | Geolocalizacion                     |
| disponibilidad      | ENUM           | inmediata, 15_dias, 30_dias, a_convenir |
| modalidad_preferida | ENUM           | presencial, remoto, hibrido, indiferente |
| salario_esperado    | DECIMAL(10,2)  | Salario esperado (opcional)          |
| created_at         | DATETIME       |                                      |
| updated_at         | DATETIME       |                                      |
| deleted_at         | DATETIME       | Soft delete                          |

#### cand_cv
| Campo         | Tipo         | Descripcion                          |
|--------------|-------------|--------------------------------------|
| id           | INT PK AI    | Identificador unico                  |
| candidato_id | INT FK       | Referencia a cand_candidato.id       |
| archivo_path | VARCHAR(255) | Ruta del archivo PDF                 |
| archivo_nombre| VARCHAR(255) | Nombre original del archivo          |
| es_principal | BOOLEAN       | Si es el CV principal                |
| created_at   | DATETIME     |                                      |
| updated_at   | DATETIME     |                                      |

#### cand_experiencia
| Campo        | Tipo         | Descripcion                          |
|-------------|-------------|--------------------------------------|
| id          | INT PK AI    | Identificador unico                  |
| candidato_id| INT FK       | Referencia a cand_candidato.id       |
| empresa     | VARCHAR(150) | Nombre de la empresa                |
| cargo       | VARCHAR(100) | Cargo desempeñado                   |
| descripcion | TEXT         | Descripcion del puesto               |
| fecha_inicio| DATE         | Fecha de inicio                      |
| fecha_fin   | DATE         | Fecha fin (nullable si actual)       |
| actual      | BOOLEAN       | Si es el empleo actual               |
| created_at  | DATETIME     |                                      |
| updated_at  | DATETIME     |                                      |

#### cand_educacion
| Campo               | Tipo         | Descripcion                          |
|--------------------|-------------|--------------------------------------|
| id                 | INT PK AI    | Identificador unico                  |
| candidato_id        | INT FK       | Referencia a cand_candidato.id       |
| nivel_educacion_id  | INT FK       | Referencia a cat_nivel_educacion.id  |
| institucion         | VARCHAR(150) | Nombre de la institucion            |
| titulo              | VARCHAR(150) | Titulo obtenido                      |
| fecha_inicio        | DATE         | Fecha de inicio                      |
| fecha_fin           | DATE         | Fecha fin (nullable si en curso)     |
| en_curso            | BOOLEAN       | Si esta en curso                     |
| created_at          | DATETIME     |                                      |
| updated_at          | DATETIME     |                                      |

#### cand_habilidad
| Campo        | Tipo     | Descripcion                          |
|-------------|---------|--------------------------------------|
| id          | INT PK AI| Identificador unico                  |
| candidato_id| INT FK   | Referencia a cand_candidato.id       |
| habilidad_id| INT FK   | Referencia a cat_habilidad.id        |
| nivel       | ENUM      | basico, intermedio, avanzado, experto |
| created_at  | DATETIME |                                      |

#### cand_idioma
| Campo        | Tipo     | Descripcion                          |
|-------------|---------|--------------------------------------|
| id          | INT PK AI| Identificador unico                  |
| candidato_id| INT FK   | Referencia a cand_candidato.id       |
| idioma_id   | INT FK   | Referencia a cat_idioma.id           |
| nivel       | ENUM      | basico, intermedio, avanzado, nativo |
| created_at  | DATETIME |                                      |

### 3.5 Modulo: vac_ (Vacantes)

#### vac_vacante
| Campo                | Tipo           | Descripcion                          |
|---------------------|---------------|--------------------------------------|
| id                  | INT PK AI      | Identificador unico                  |
| empresa_id          | INT FK         | Referencia a emp_empresa.id          |
| categoria_id        | INT FK         | Referencia a cat_categoria.id        |
| tipo_contrato_id    | INT FK         | Referencia a cat_tipo_contrato.id    |
| nivel_experiencia_id| INT FK         | Referencia a cat_nivel_experiencia.id |
| titulo              | VARCHAR(150)   | Titulo de la vacante                 |
| slug                | VARCHAR(180)   | Slug unico                          |
| descripcion         | TEXT           | Descripcion detallada                |
| funciones           | TEXT           | Funciones del puesto                 |
| ubicacion           | VARCHAR(150)   | Ubicacion                            |
| ciudad              | VARCHAR(100)   | Ciudad                               |
| region              | VARCHAR(100)   | Region                               |
| modalidad           | ENUM           | presencial, remoto, hibrido          |
| salario_min         | DECIMAL(10,2)  | Salario minimo (opcional)            |
| salario_max         | DECIMAL(10,2)  | Salario maximo (opcional)            |
| moneda              | VARCHAR(3)     | Moneda (PEN, USD, etc.)              |
| vacantes_disponibles| INT            | Numero de vacantes                   |
| estado              | ENUM           | borrador, publicada, cerrada, suspendida |
| destacada           | BOOLEAN         | Si es una vacante destacada          |
| fecha_publicacion   | DATETIME       | Fecha de publicacion                 |
| fecha_cierre        | DATETIME       | Fecha de cierre                      |
| created_at          | DATETIME       |                                      |
| updated_at          | DATETIME       |                                      |
| deleted_at          | DATETIME       | Soft delete                          |

#### vac_requisito
| Campo              | Tipo     | Descripcion                          |
|-------------------|---------|--------------------------------------|
| id                | INT PK AI| Identificador unico                  |
| vacante_id        | INT FK   | Referencia a vac_vacante.id          |
| requisito_id      | INT FK   | Referencia a cat_requisito.id (nullable) |
| habilidad_id      | INT FK   | Referencia a cat_habilidad.id (nullable) |
| nivel_educacion_id| INT FK   | Referencia a cat_nivel_educacion.id (nullable) |
| descripcion       | TEXT     | Descripcion personalizada            |
| tipo              | ENUM      | obligatorio, deseable               |
| created_at        | DATETIME |                                      |
| updated_at        | DATETIME |                                      |

#### vac_habilidad
| Campo          | Tipo     | Descripcion                          |
|---------------|---------|--------------------------------------|
| id            | INT PK AI| Identificador unico                  |
| vacante_id    | INT FK   | Referencia a vac_vacante.id          |
| habilidad_id  | INT FK   | Referencia a cat_habilidad.id        |
| nivel_requerido| ENUM     | basico, intermedio, avanzado, experto |
| tipo          | ENUM      | obligatorio, deseable               |
| created_at    | DATETIME |                                      |

### 3.6 Modulo: post_ (Postulaciones)

#### post_postulacion
| Campo            | Tipo          | Descripcion                          |
|-----------------|---------------|--------------------------------------|
| id              | INT PK AI      | Identificador unico                  |
| vacante_id      | INT FK        | Referencia a vac_vacante.id          |
| candidato_id    | INT FK        | Referencia a cand_candidato.id       |
| cv_id           | INT FK        | Referencia a cand_cv.id (nullable)   |
| mensaje         | TEXT          | Mensaje del candidato                |
| estado          | ENUM          | enviada, en_revision, aceptada, rechazada, descartada |
| puntaje_match   | DECIMAL(5,2)  | Puntaje de match (IA)                |
| fecha_entrevista| DATETIME      | Fecha de entrevista (opcional)       |
| notas_empresa   | TEXT          | Notas internas de la empresa         |
| created_at      | DATETIME      |                                      |
| updated_at      | DATETIME      |                                      |
| deleted_at      | DATETIME      | Soft delete                          |

#### post_historial
| Campo          | Tipo         | Descripcion                          |
|---------------|-------------|--------------------------------------|
| id            | INT PK AI    | Identificador unico                  |
| postulacion_id| INT FK       | Referencia a post_postulacion.id     |
| estado_anterior| VARCHAR(50) | Estado anterior                      |
| estado_nuevo  | VARCHAR(50)  | Estado nuevo                         |
| comentario    | TEXT         | Comentario del cambio                |
| user_id       | INT FK       | Referencia a auth_user.id (nullable) |
| created_at    | DATETIME     |                                      |

### 3.7 Modulo: crm_ (CRM / Planes / Contratos)

#### crm_plan
| Campo              | Tipo          | Descripcion                          |
|-------------------|---------------|--------------------------------------|
| id                | INT PK AI      | Identificador unico                  |
| nombre            | VARCHAR(100)   | Nombre del plan                      |
| slug              | VARCHAR(100)   | Slug unico                          |
| descripcion       | TEXT           | Descripcion del plan                 |
| precio_mensual    | DECIMAL(10,2)  | Precio mensual                       |
| precio_anual      | DECIMAL(10,2)  | Precio anual (opcional)              |
| moneda            | VARCHAR(3)     | Moneda                               |
| max_vacantes      | INT            | Limite de vacantes (0 = ilimitado)   |
| max_postulaciones | INT            | Limite de postulaciones              |
| destacar_vacantes  | BOOLEAN         | Permite destacar vacantes            |
| acceso_cvs        | BOOLEAN         | Acceso a base de CVs                 |
| soporte_prioritario| BOOLEAN        | Soporte prioritario                  |
| estado            | ENUM           | activo, inactivo                    |
| created_at        | DATETIME       |                                      |
| updated_at        | DATETIME       |                                      |

#### crm_contrato
| Campo           | Tipo        | Descripcion                          |
|----------------|-------------|--------------------------------------|
| id             | INT PK AI   | Identificador unico                  |
| empresa_id     | INT FK      | Referencia a emp_empresa.id          |
| plan_id        | INT FK      | Referencia a crm_plan.id             |
| codigo         | VARCHAR(50) | Codigo unico del contrato            |
| tipo_facturacion| ENUM        | mensual, anual                      |
| fecha_inicio   | DATE        | Fecha de inicio                      |
| fecha_fin      | DATE        | Fecha fin (nullable si vigente)      |
| estado         | ENUM        | activo, suspendido, cancelado, vencido, pendiente |
| auto_renovar   | BOOLEAN      | Renovacion automatica                |
| created_at     | DATETIME    |                                      |
| updated_at     | DATETIME    |                                      |

#### crm_pago
| Campo           | Tipo          | Descripcion                          |
|----------------|---------------|--------------------------------------|
| id             | INT PK AI      | Identificador unico                  |
| contrato_id    | INT FK        | Referencia a crm_contrato.id         |
| empresa_id     | INT FK        | Referencia a emp_empresa.id          |
| codigo         | VARCHAR(50)   | Codigo unico del pago               |
| monto          | DECIMAL(10,2)  | Monto del pago                       |
| moneda         | VARCHAR(3)     | Moneda                               |
| metodo_pago    | VARCHAR(50)    | Metodo de pago                       |
| estado         | ENUM          | pendiente, pagado, fallido, reembolsado |
| fecha_pago     | DATETIME      | Fecha del pago                       |
| comprobante_path| VARCHAR(255)  | Ruta del comprobante                 |
| notas          | TEXT          | Notas                                |
| created_at     | DATETIME      |                                      |
| updated_at     | DATETIME      |                                      |

#### crm_lead
| Campo           | Tipo         | Descripcion                          |
|----------------|-------------|--------------------------------------|
| id             | INT PK AI    | Identificador unico                  |
| empresa_id     | INT FK       | Referencia a emp_empresa.id (nullable) |
| nombre_contacto| VARCHAR(150) | Nombre del contacto                 |
| email_contacto | VARCHAR(150) | Email del contacto                  |
| telefono_contacto| VARCHAR(20) | Telefono                           |
| empresa_nombre | VARCHAR(150) | Nombre de empresa (si no registrada) |
| origen         | ENUM         | web, referido, campana, directo, otro |
| plan_interes_id| INT FK       | Referencia a crm_plan.id (nullable) |
| estado         | ENUM         | nuevo, contactado, calificado, propuesta, negociacion, ganado, perdido |
| asesor_id      | INT FK       | Referencia a auth_user.id (nullable) |
| notas          | TEXT         | Notas del lead                       |
| created_at     | DATETIME     |                                      |
| updated_at     | DATETIME     |                                      |

#### crm_lead_seguimiento
| Campo              | Tipo     | Descripcion                          |
|-------------------|---------|--------------------------------------|
| id                | INT PK AI| Identificador unico                  |
| lead_id           | INT FK   | Referencia a crm_lead.id             |
| asesor_id         | INT FK   | Referencia a auth_user.id (nullable) |
| tipo_contacto     | ENUM      | llamada, email, reunion, whatsapp, otro |
| comentario        | TEXT     | Comentario del seguimiento           |
| fecha_proxima_accion| DATETIME | Fecha proxima accion (nullable)    |
| created_at        | DATETIME |                                      |

### 3.8 Modulo: not_ (Notificaciones)

#### not_notificacion
| Campo      | Tipo         | Descripcion                          |
|-----------|-------------|--------------------------------------|
| id        | INT PK AI    | Identificador unico                  |
| user_id   | INT FK       | Referencia a auth_user.id            |
| tipo      | VARCHAR(50)  | Tipo de notificacion                 |
| titulo    | VARCHAR(150) | Titulo                               |
| mensaje   | TEXT         | Contenido                            |
| url       | VARCHAR(255) | URL de accion (opcional)            |
| leida     | BOOLEAN       | Si fue leida                         |
| created_at| DATETIME     |                                      |

### 3.9 Modulo: ai_ (Analisis IA / Gemini)

#### ai_analisis_cv
| Campo              | Tipo          | Descripcion                          |
|-------------------|---------------|--------------------------------------|
| id                | INT PK AI      | Identificador unico                  |
| candidato_id      | INT FK         | Referencia a cand_candidato.id       |
| cv_id             | INT FK         | Referencia a cand_cv.id              |
| postulacion_id    | INT FK         | Referencia a post_postulacion.id (nullable) |
| vacante_id        | INT FK         | Referencia a vac_vacante.id (nullable) |
| prompt            | TEXT           | Prompt enviado a Gemini              |
| respuesta         | LONGTEXT       | Respuesta de Gemini                  |
| score_match       | DECIMAL(5,2)   | Puntaje de compatibilidad            |
| fortalezas        | TEXT           | Fortalezas detectadas                |
| debilidades       | TEXT           | Debilidades detectadas               |
| recomendacion     | TEXT           | Recomendacion de la IA               |
| modelo_ia         | VARCHAR(50)    | Modelo usado (gemini)                |
| tokens_usados     | INT            | Tokens consumidos                    |
| tiempo_respuesta_ms| INT           | Tiempo de respuesta en ms            |
| estado            | ENUM           | procesando, completado, error        |
| error             | TEXT           | Mensaje de error (si aplica)         |
| created_at        | DATETIME       |                                      |
| updated_at        | DATETIME       |                                      |

### 3.10 Diagrama de Relaciones

```
auth_role ──< auth_user >── auth_role_permission >── auth_permission

auth_user ──< emp_empresa >── crm_contrato >── crm_plan
                              crm_contrato >── crm_pago
                              crm_lead >── crm_lead_seguimiento

auth_user ──< cand_candidato >── cand_cv
                               >── cand_experiencia
                               >── cand_educacion >── cat_nivel_educacion
                               >── cand_habilidad >── cat_habilidad
                               >── cand_idioma >── cat_idioma

emp_empresa ──< vac_vacante >── cat_categoria
                            >── cat_tipo_contrato
                            >── cat_nivel_experiencia
                            >── vac_requisito >── cat_requisito, cat_habilidad
                            >── vac_habilidad >── cat_habilidad

vac_vacante ──< post_postulacion >── cand_candidato
                                >── cand_cv
                                >── post_historial

cand_cv + post_postulacion ──< ai_analisis_cv

auth_user ──< not_notificacion
```

---

## 4. Autenticacion y Autorizacion

### 4.1 Autenticacion

- Sistema de sesiones nativo de CodeIgniter 4.
- Password hasheado con `password_hash()` (bcrypt).
- Filtro `AuthFilter` protege rutas que requieren sesion.
- Filtro `GuestFilter` para rutas exclusivas de no autenticados (login, registro).

### 4.2 Autorizacion por Roles

- Filtro `RoleFilter` valida que el usuario tenga el rol requerido para la ruta.
- Mapeo de roles a layouts:
  - `admin`, `asesor` -> Layout Administracion
  - `empresa` -> Layout Empresa
  - `candidato` -> Layout Candidato
  - No autenticado -> Layout Publico

### 4.3 Estructura de Rutas (Propuesta)

```
# Publico
GET  /                        -> Portal publico (listado de vacantes)
GET  /vacantes                -> Listado de vacantes
GET  /vacantes/{id}           -> Detalle de vacante
GET  /login                   -> Formulario de inicio de sesion
POST /login                  -> Procesar login
GET  /registro                -> Formulario de registro
POST /registro               -> Procesar registro

# Administracion
GET  /admin                   -> Dashboard admin
GET  /admin/usuarios          -> Gestion de usuarios
GET  /admin/vacantes          -> Moderacion de vacantes
GET  /admin/categorias        -> Gestion de categorias

# Empresa
GET  /empresa                 -> Dashboard empresa
GET  /empresa/vacantes        -> Listado de vacantes propias
GET  /empresa/vacantes/crear  -> Formulario crear vacante
POST /empresa/vacantes/crear -> Procesar creacion
GET  /empresa/vacantes/{id}/editar
GET  /empresa/postulaciones   -> Postulaciones recibidas

# Candidato
GET  /candidato               -> Dashboard candidato
GET  /candidato/postulaciones -> Mis postulaciones
GET  /candidato/perfil         -> Editar perfil / CV
```

---

## 5. Configuracion del Entorno

### 5.1 Archivo .env

```
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/CONEX/public/'
database.default.hostname = localhost
database.default.database = conex
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

### 5.2 Virtual Host (Recomendado)

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/CONEX/public"
    ServerName conex.local
    <Directory "C:/xampp/htdocs/CONEX/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 6. Pendientes Tecnicos

- [x] Definir campos exactos de CV del candidato (educacion, experiencia, habilidades).
- [x] Definir estructura de tablas con prefijos por dominio.
- [x] Definir tablas de CRM (planes, contratos, pagos, leads).
- [x] Definir tabla de analisis IA (Gemini).
- [x] Definir geolocalizacion en empresa y candidato.
- [ ] Definir libreria frontend (vanilla JS, Alpine.js, etc.).
- [ ] Definir sistema de subida de archivos (CV en PDF).
- [ ] Definir politicas de cache.
- [ ] Definir estrategia de seeds (roles, categorias, planes iniciales).
- [ ] Configurar API Key de Gemini.
- [ ] Definir flujo de integracion con Gemini (endpoint, prompts, parseo de respuesta).
