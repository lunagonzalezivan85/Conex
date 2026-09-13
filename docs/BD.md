# Base de Datos - Documentacion

## CONEX - Sistema de Reclutamiento de Personal

**Version:** 1.0  
**Fecha:** 2026-09-12  
**Motor:** MySQL 8.0+ (XAMPP)  
**Charset:** utf8mb4  
**Collation:** utf8mb4_general_ci  
**Total de tablas:** 32

---

## 1. Convenciones

### 1.1 Prefijos por Dominio

| Prefijo  | Modulo                        | Cantidad |
|----------|-------------------------------|----------|
| `auth_`  | Autenticacion y roles          | 4        |
| `cat_`   | Catalogos                     | 7        |
| `emp_`   | Empresa                       | 1        |
| `cand_`  | Candidato                     | 7        |
| `vac_`   | Vacantes                      | 3        |
| `post_`  | Postulaciones                 | 3        |
| `crm_`   | CRM / Planes / Contratos      | 5        |
| `not_`   | Notificaciones                | 1        |
| `ai_`    | Analisis IA (Gemini)          | 1        |

### 1.2 Reglas Generales

- **Claves primarias:** `id` INT UNSIGNED AUTO_INCREMENT
- **Claves foraneas:** `{entidad}_id` INT UNSIGNED
- **Soft delete:** `deleted_at` DATETIME (nullable) en tablas principales
- **Timestamps:** `created_at`, `updated_at` DATETIME (nullable)
- **Slugs:** campo `slug` VARCHAR con UNIQUE para URLs amigables
- **Estados:** campos `estado` ENUM con valor por defecto
- **Codigos:** campos `codigo` VARCHAR con UNIQUE en contratos y pagos

---

## 2. Modulo: auth_ (Autenticacion y Roles)

### 2.1 auth_role

Almacena los roles del sistema.

| Campo         | Tipo          | Nulo | Default     | Extra                        |
|--------------|---------------|------|-------------|------------------------------|
| id           | INT UNSIGNED  | NO   | -           | PK, AUTO_INCREMENT           |
| nombre       | VARCHAR(50)   | NO   | -           | Nombre del rol               |
| slug         | VARCHAR(50)   | NO   | -           | UNIQUE                       |
| descripcion  | TEXT          | SI   | NULL        | -                            |
| es_asignable | BOOLEAN       | NO   | true        | Si puede elegirse en registro|
| created_at   | DATETIME      | SI   | NULL        | -                            |
| updated_at   | DATETIME      | SI   | NULL        | -                            |

**Slugs predefinidos:** `admin`, `asesor`, `empresa`, `candidato`

**Relaciones:**
- `auth_role` 1──N `auth_user`
- `auth_role` 1──N `auth_role_permission`

---

### 2.2 auth_user

Usuarios del sistema. El registro publico solo permite `candidato` o `empresa`.

| Campo           | Tipo          | Nulo | Default     | Extra                          |
|----------------|---------------|------|-------------|--------------------------------|
| id             | INT UNSIGNED  | NO   | -           | PK, AUTO_INCREMENT             |
| role_id        | INT UNSIGNED  | NO   | -           | FK → auth_role.id             |
| nombre         | VARCHAR(100)  | NO   | -           | -                              |
| apellido       | VARCHAR(100)  | NO   | -           | -                              |
| usuario        | VARCHAR(100)  | NO   | -           | UNIQUE, nombre de usuario      |
| email          | VARCHAR(150)  | NO   | -           | UNIQUE                         |
| password       | VARCHAR(255)  | NO   | -           | Hash bcrypt                    |
| telefono       | VARCHAR(20)   | NO   | -           | -                              |
| avatar         | VARCHAR(255)  | SI   | NULL        | Ruta del archivo               |
| estado         | ENUM          | NO   | activo      | activo, inactivo, suspendido   |
| perfil_completo| BOOLEAN       | NO   | false       | Si completo el perfil          |
| last_login     | DATETIME      | SI   | NULL        | Ultimo inicio de sesion         |
| created_at     | DATETIME      | SI   | NULL        | -                              |
| updated_at     | DATETIME      | SI   | NULL        | -                              |
| deleted_at     | DATETIME      | SI   | NULL        | Soft delete                    |

**Relaciones:**
- `auth_role` 1──N `auth_user` (role_id)
- `auth_user` 1──1 `emp_empresa` (user_id)
- `auth_user` 1──1 `cand_candidato` (user_id)
- `auth_user` 1──N `not_notificacion` (user_id)
- `auth_user` 1──N `crm_lead` (asesor_id)
- `auth_user` 1──N `crm_lead_seguimiento` (asesor_id)
- `auth_user` 1──N `post_historial` (user_id)

---

### 2.3 auth_permission

Permisos del sistema para control de acceso fino.

| Campo        | Tipo          | Nulo | Default | Extra                |
|-------------|---------------|------|---------|----------------------|
| id          | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT   |
| nombre      | VARCHAR(100)  | NO   | -       | -                    |
| slug        | VARCHAR(100)  | NO   | -       | UNIQUE               |
| modulo      | VARCHAR(50)   | NO   | -       | Modulo al que pertenece |
| descripcion | TEXT          | SI   | NULL    | -                    |
| created_at  | DATETIME      | SI   | NULL    | -                    |
| updated_at  | DATETIME      | SI   | NULL    | -                    |

**Relaciones:**
- `auth_permission` 1──N `auth_role_permission`

---

### 2.4 auth_role_permission

Tabla pivote entre roles y permisos (N──N).

| Campo         | Tipo          | Nulo | Default | Extra                          |
|--------------|---------------|------|---------|--------------------------------|
| id           | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT             |
| role_id      | INT UNSIGNED  | NO   | -       | FK → auth_role.id (CASCADE)   |
| permission_id| INT UNSIGNED  | NO   | -       | FK → auth_permission.id (CASCADE) |
| created_at   | DATETIME      | SI   | NULL    | -                              |

---

## 3. Modulo: cat_ (Catalogos)

### 3.1 cat_categoria

Categorias de empleo (ej: Tecnologia, Salud, Finanzas).

| Campo       | Tipo          | Nulo | Default | Extra                |
|------------|---------------|------|---------|----------------------|
| id         | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT   |
| nombre     | VARCHAR(100)  | NO   | -       | -                    |
| slug       | VARCHAR(120)  | NO   | -       | UNIQUE               |
| descripcion| TEXT          | SI   | NULL    | -                    |
| icono      | VARCHAR(100)  | SI   | NULL    | Nombre/icono UI      |
| estado     | ENUM          | NO   | activo  | activo, inactivo     |
| created_at | DATETIME      | SI   | NULL    | -                    |
| updated_at | DATETIME      | SI   | NULL    | -                    |

**Relaciones:**
- `cat_categoria` 1──N `vac_vacante` (categoria_id)
- `cat_categoria` 1──N `cat_habilidad` (categoria_id)

---

### 3.2 cat_tipo_contrato

Tipos de contrato laboral.

| Campo       | Tipo         | Nulo | Default | Extra                |
|------------|-------------|------|---------|----------------------|
| id         | INT UNSIGNED | NO   | -       | PK, AUTO_INCREMENT   |
| nombre     | VARCHAR(80)  | NO   | -       | -                    |
| slug       | VARCHAR(80)  | NO   | -       | UNIQUE               |
| descripcion| TEXT         | SI   | NULL    | -                    |
| estado     | ENUM         | NO   | activo  | activo, inactivo     |
| created_at | DATETIME     | SI   | NULL    | -                    |
| updated_at | DATETIME     | SI   | NULL    | -                    |

**Valores sugeridos:** tiempo_completo, medio_tiempo, proyecto, temporal, practicas

**Relaciones:**
- `cat_tipo_contrato` 1──N `vac_vacante` (tipo_contrato_id)

---

### 3.3 cat_habilidad

Habilidades/competencias tecnicas o blandas.

| Campo        | Tipo          | Nulo | Default | Extra                          |
|-------------|---------------|------|---------|--------------------------------|
| id          | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT             |
| nombre      | VARCHAR(100)  | NO   | -       | -                              |
| slug        | VARCHAR(120)  | NO   | -       | UNIQUE                         |
| categoria_id| INT UNSIGNED  | SI   | NULL    | FK → cat_categoria.id (SET NULL) |
| estado      | ENUM          | NO   | activo  | activo, inactivo               |
| created_at  | DATETIME      | SI   | NULL    | -                              |
| updated_at  | DATETIME      | SI   | NULL    | -                              |

**Relaciones:**
- `cat_categoria` 1──N `cat_habilidad`
- `cat_habilidad` 1──N `cand_habilidad`
- `cat_habilidad` 1──N `vac_habilidad`
- `cat_habilidad` 1──N `vac_requisito`

---

### 3.4 cat_nivel_educacion

Niveles educativos.

| Campo       | Tipo         | Nulo | Default | Extra                |
|------------|-------------|------|---------|----------------------|
| id         | INT UNSIGNED | NO   | -       | PK, AUTO_INCREMENT   |
| nombre     | VARCHAR(80)  | NO   | -       | -                    |
| slug       | VARCHAR(80)  | NO   | -       | UNIQUE               |
| orden      | INT          | NO   | 0       | Jerarquia            |
| estado     | ENUM         | NO   | activo  | activo, inactivo     |
| created_at | DATETIME     | SI   | NULL    | -                    |
| updated_at | DATETIME     | SI   | NULL    | -                    |

**Valores sugeridos:** primaria, secundaria, tecnico, universitario, posgrado, doctorado

**Relaciones:**
- `cat_nivel_educacion` 1──N `cand_educacion`
- `cat_nivel_educacion` 1──N `vac_requisito`

---

### 3.5 cat_nivel_experiencia

Niveles de experiencia laboral.

| Campo         | Tipo         | Nulo | Default | Extra                          |
|--------------|-------------|------|---------|--------------------------------|
| id           | INT UNSIGNED | NO   | -       | PK, AUTO_INCREMENT             |
| nombre       | VARCHAR(80)  | NO   | -       | -                              |
| slug         | VARCHAR(80)  | NO   | -       | UNIQUE                         |
| anios_minimos| INT          | NO   | 0       | -                              |
| anios_maximos| INT          | SI   | NULL    | NULL = sin limite              |
| orden        | INT          | NO   | 0       | -                              |
| estado       | ENUM         | NO   | activo  | activo, inactivo               |
| created_at   | DATETIME     | SI   | NULL    | -                              |
| updated_at   | DATETIME     | SI   | NULL    | -                              |

**Valores sugeridos:** sin_experiencia (0-0), junior (0-2), semi-senior (2-5), senior (5-10), lead (10+)

**Relaciones:**
- `cat_nivel_experiencia` 1──N `cand_candidato`
- `cat_nivel_experiencia` 1──N `vac_vacante`

---

### 3.6 cat_requisito

Requisitos predefinidos reutilizables.

| Campo       | Tipo          | Nulo | Default     | Extra                |
|------------|---------------|------|-------------|----------------------|
| id         | INT UNSIGNED  | NO   | -           | PK, AUTO_INCREMENT   |
| nombre     | VARCHAR(100)  | NO   | -           | -                    |
| slug       | VARCHAR(120)  | NO   | -           | UNIQUE               |
| tipo       | ENUM          | NO   | obligatorio | obligatorio, deseable |
| es_default | BOOLEAN       | NO   | false       | Aparece por defecto al crear vacante |
| estado     | ENUM          | NO   | activo      | activo, inactivo     |
| created_at | DATETIME      | SI   | NULL        | -                    |
| updated_at | DATETIME      | SI   | NULL        | -                    |

**Relaciones:**
- `cat_requisito` 1──N `vac_requisito`
- `cat_requisito` 1──N `post_verificacion`

---

### 3.7 cat_idioma

Idiomas disponibles.

| Campo       | Tipo         | Nulo | Default | Extra                |
|------------|-------------|------|---------|----------------------|
| id         | INT UNSIGNED | NO   | -       | PK, AUTO_INCREMENT   |
| nombre     | VARCHAR(80)  | NO   | -       | -                    |
| slug       | VARCHAR(80)  | NO   | -       | UNIQUE               |
| estado     | ENUM         | NO   | activo  | activo, inactivo     |
| created_at | DATETIME     | SI   | NULL    | -                    |
| updated_at | DATETIME     | SI   | NULL    | -                    |

**Relaciones:**
- `cat_idioma` 1──N `cand_idioma`

---

## 4. Modulo: emp_ (Empresa)

### 4.1 emp_empresa

Perfil empresarial. Una empresa por usuario.

| Campo         | Tipo            | Nulo | Default | Extra                          |
|--------------|-----------------|------|---------|--------------------------------|
| id           | INT UNSIGNED    | NO   | -       | PK, AUTO_INCREMENT             |
| user_id      | INT UNSIGNED    | NO   | -       | FK → auth_user.id (CASCADE)   |
| razon_social | VARCHAR(150)    | NO   | -       | Nombre legal                   |
| ruc          | VARCHAR(20)     | SI   | NULL    | Identificacion fiscal          |
| rubro        | VARCHAR(100)    | SI   | NULL    | Sector                         |
| descripcion  | TEXT            | SI   | NULL    | -                              |
| sitio_web    | VARCHAR(255)    | SI   | NULL    | URL                            |
| logo         | VARCHAR(255)    | SI   | NULL    | Ruta del logo                  |
| telefono     | VARCHAR(20)     | SI   | NULL    | -                              |
| direccion    | VARCHAR(255)    | SI   | NULL    | Direccion fisica               |
| ciudad       | VARCHAR(100)    | SI   | NULL    | -                              |
| region       | VARCHAR(100)    | SI   | NULL    | -                              |
| pais         | VARCHAR(100)    | SI   | NULL    | -                              |
| latitud      | DECIMAL(10,8)   | SI   | NULL    | Geolocalizacion               |
| longitud     | DECIMAL(11,8)   | SI   | NULL    | Geolocalizacion               |
| verificada   | BOOLEAN         | NO   | false   | Empresa verificada por admin   |
| created_at   | DATETIME        | SI   | NULL    | -                              |
| updated_at   | DATETIME        | SI   | NULL    | -                              |
| deleted_at   | DATETIME        | SI   | NULL    | Soft delete                    |

**Relaciones:**
- `auth_user` 1──1 `emp_empresa`
- `emp_empresa` 1──N `vac_vacante`
- `emp_empresa` 1──N `crm_contrato`
- `emp_empresa` 1──N `crm_pago`
- `emp_empresa` 1──N `crm_lead`

---

## 5. Modulo: cand_ (Candidato)

### 5.1 cand_candidato

Perfil del candidato. Un candidato por usuario.

| Campo                | Tipo            | Nulo | Default      | Extra                              |
|---------------------|-----------------|------|-------------|-------------------------------------|
| id                  | INT UNSIGNED    | NO   | -           | PK, AUTO_INCREMENT                 |
| user_id             | INT UNSIGNED    | NO   | -           | FK → auth_user.id (CASCADE)       |
| apellidos           | VARCHAR(100)    | NO   | -           | -                                   |
| fecha_nacimiento     | DATE            | SI   | NULL        | -                                   |
| profesion           | VARCHAR(100)    | SI   | NULL        | -                                   |
| nivel_experiencia_id| INT UNSIGNED    | SI   | NULL        | FK → cat_nivel_experiencia.id (SET NULL) |
| sobre_mi            | TEXT            | SI   | NULL        | Descripcion personal               |
| direccion           | VARCHAR(255)    | SI   | NULL        | -                                   |
| ciudad              | VARCHAR(100)    | SI   | NULL        | -                                   |
| region              | VARCHAR(100)    | SI   | NULL        | -                                   |
| pais                | VARCHAR(100)    | SI   | NULL        | -                                   |
| latitud             | DECIMAL(10,8)   | SI   | NULL        | Geolocalizacion                    |
| longitud            | DECIMAL(11,8)   | SI   | NULL        | Geolocalizacion                    |
| disponibilidad      | ENUM            | NO   | a_convenir  | inmediata, 15_dias, 30_dias, a_convenir |
| modalidad_preferida  | ENUM            | NO   | indiferente | presencial, remoto, hibrido, indiferente |
| salario_esperado_usd | DECIMAL(10,2)   | SI   | NULL        | En dolares (conversion a C$ interna) |
| portafolio_url       | VARCHAR(255)    | SI   | NULL        | URL del portafolio                 |
| linkedin_url         | VARCHAR(255)    | SI   | NULL        | URL del perfil de LinkedIn         |
| porcentaje_perfil    | INT             | NO   | 0           | % de completitud del perfil (0-100) |
| created_at          | DATETIME        | SI   | NULL        | -                                   |
| updated_at          | DATETIME        | SI   | NULL        | -                                   |
| deleted_at          | DATETIME        | SI   | NULL        | Soft delete                        |

**Relaciones:**
- `auth_user` 1──1 `cand_candidato`
- `cat_nivel_experiencia` 1──N `cand_candidato`
- `cand_candidato` 1──N `cand_cv`
- `cand_candidato` 1──N `cand_experiencia`
- `cand_candidato` 1──N `cand_educacion`
- `cand_candidato` 1──N `cand_habilidad`
- `cand_candidato` 1──N `cand_idioma`
- `cand_candidato` 1──N `cand_puesto_deseado`
- `cand_candidato` 1──N `post_postulacion`
- `cand_candidato` 1──N `ai_analisis_cv`

---

### 5.2 cand_cv

Archivos de CV del candidato (puede tener varios).

| Campo          | Tipo          | Nulo | Default | Extra                          |
|---------------|---------------|------|---------|--------------------------------|
| id            | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT             |
| candidato_id  | INT UNSIGNED  | NO   | -       | FK → cand_candidato.id (CASCADE) |
| archivo_path  | VARCHAR(255)  | NO   | -       | Ruta en servidor               |
| archivo_nombre| VARCHAR(255)  | NO   | -       | Nombre original                |
| es_principal  | BOOLEAN       | NO   | false   | CV principal                   |
| created_at    | DATETIME      | SI   | NULL    | -                              |
| updated_at    | DATETIME      | SI   | NULL    | -                              |

**Relaciones:**
- `cand_candidato` 1──N `cand_cv`
- `cand_cv` 1──N `post_postulacion` (cv_id)
- `cand_cv` 1──N `ai_analisis_cv` (cv_id)

---

### 5.3 cand_experiencia

Experiencia laboral del candidato.

| Campo        | Tipo          | Nulo | Default | Extra                          |
|-------------|---------------|------|---------|--------------------------------|
| id          | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT             |
| candidato_id| INT UNSIGNED  | NO   | -       | FK → cand_candidato.id (CASCADE) |
| empresa     | VARCHAR(150)  | NO   | -       | Nombre de la empresa           |
| cargo       | VARCHAR(100)  | NO   | -       | Cargo desempeñado              |
| descripcion | TEXT          | SI   | NULL    | -                              |
| fecha_inicio| DATE          | NO   | -       | -                              |
| fecha_fin   | DATE          | SI   | NULL    | NULL si es actual              |
| actual      | BOOLEAN       | NO   | false   | Si es empleo actual            |
| created_at  | DATETIME      | SI   | NULL    | -                              |
| updated_at  | DATETIME      | SI   | NULL    | -                              |

---

### 5.4 cand_educacion

Formacion academica del candidato.

| Campo               | Tipo          | Nulo | Default | Extra                              |
|--------------------|---------------|------|---------|-------------------------------------|
| id                 | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT                 |
| candidato_id        | INT UNSIGNED  | NO   | -       | FK → cand_candidato.id (CASCADE)  |
| nivel_educacion_id | INT UNSIGNED  | NO   | -       | FK → cat_nivel_educacion.id (RESTRICT) |
| institucion         | VARCHAR(150)  | NO   | -       | -                                   |
| titulo              | VARCHAR(150)  | NO   | -       | -                                   |
| fecha_inicio        | DATE          | SI   | NULL    | -                                   |
| fecha_fin           | DATE          | SI   | NULL    | NULL si en curso                    |
| en_curso            | BOOLEAN       | NO   | false   | -                                   |
| created_at          | DATETIME      | SI   | NULL    | -                                   |
| updated_at          | DATETIME      | SI   | NULL    | -                                   |

---

### 5.5 cand_habilidad

Habilidades del candidato (N──N con cat_habilidad).

| Campo        | Tipo          | Nulo | Default     | Extra                          |
|-------------|---------------|------|-------------|--------------------------------|
| id          | INT UNSIGNED  | NO   | -           | PK, AUTO_INCREMENT             |
| candidato_id| INT UNSIGNED  | NO   | -           | FK → cand_candidato.id (CASCADE) |
| habilidad_id| INT UNSIGNED  | NO   | -           | FK → cat_habilidad.id (CASCADE) |
| nivel       | ENUM          | NO   | intermedio  | basico, intermedio, avanzado, experto |
| created_at  | DATETIME      | SI   | NULL        | -                              |

---

### 5.6 cand_idioma

Idiomas que domina el candidato (N──N con cat_idioma).

| Campo        | Tipo          | Nulo | Default     | Extra                          |
|-------------|---------------|------|-------------|--------------------------------|
| id          | INT UNSIGNED  | NO   | -           | PK, AUTO_INCREMENT             |
| candidato_id| INT UNSIGNED  | NO   | -           | FK → cand_candidato.id (CASCADE) |
| idioma_id   | INT UNSIGNED  | NO   | -           | FK → cat_idioma.id (CASCADE)   |
| nivel       | ENUM          | NO   | intermedio  | basico, intermedio, avanzado, nativo |
| created_at  | DATETIME      | SI   | NULL        | -                              |

---

### 5.7 cand_puesto_deseado

Puestos que el candidato busca activamente. Usado para recomendaciones de vacantes.

| Campo        | Tipo          | Nulo | Default | Extra                              |
|-------------|---------------|------|---------|-------------------------------------|
| id          | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT                 |
| candidato_id| INT UNSIGNED  | NO   | -       | FK → cand_candidato.id (CASCADE)  |
| categoria_id| INT UNSIGNED  | SI   | NULL    | FK → cat_categoria.id (SET NULL)   |
| puesto      | VARCHAR(150)  | NO   | -       | Nombre del puesto (ej: "Desarrollador PHP") |
| created_at  | DATETIME      | SI   | NULL    | -                                   |

**Relaciones:**
- `cand_candidato` 1──N `cand_puesto_deseado`
- `cat_categoria` 1──N `cand_puesto_deseado` (nullable)

---

## 6. Modulo: vac_ (Vacantes)

### 6.1 vac_vacante

Vacantes publicadas por empresas o desde el panel administrativo.

| Campo                | Tipo            | Nulo | Default   | Extra                              |
|---------------------|-----------------|------|-----------|-------------------------------------|
| id                  | INT UNSIGNED    | NO   | -         | PK, AUTO_INCREMENT                 |
| empresa_id          | INT UNSIGNED    | NO   | -         | FK → emp_empresa.id (CASCADE)     |
| categoria_id        | INT UNSIGNED    | NO   | -         | FK → cat_categoria.id (RESTRICT)  |
| tipo_contrato_id    | INT UNSIGNED    | SI   | NULL      | FK → cat_tipo_contrato.id (SET NULL) |
| nivel_experiencia_id| INT UNSIGNED    | SI   | NULL      | FK → cat_nivel_experiencia.id (SET NULL) |
| titulo              | VARCHAR(150)    | NO   | -         | -                                   |
| slug                | VARCHAR(180)    | NO   | -         | UNIQUE                              |
| descripcion         | TEXT            | NO   | -         | -                                   |
| funciones           | TEXT            | SI   | NULL      | -                                   |
| ubicacion           | VARCHAR(150)    | SI   | NULL      | -                                   |
| ciudad              | VARCHAR(100)    | SI   | NULL      | -                                   |
| region              | VARCHAR(100)    | SI   | NULL      | -                                   |
| modalidad           | ENUM            | NO   | presencial| presencial, remoto, hibrido         |
| salario_min         | DECIMAL(10,2)   | SI   | NULL      | -                                   |
| salario_max         | DECIMAL(10,2)   | SI   | NULL      | -                                   |
| moneda              | VARCHAR(3)      | NO   | USD       | -                                   |
| anios_experiencia   | INT             | NO   | 0         | Años de experiencia requeridos      |
| vacantes_disponibles| INT             | NO   | 1         | -                                   |
| max_postulantes     | INT             | NO   | 0         | Max de postulantes (0 = ilimitado)  |
| estado              | ENUM            | NO   | borrador  | borrador, publicada, cerrada, suspendida |
| destacada           | BOOLEAN         | NO   | false     | -                                   |
| fecha_publicacion   | DATETIME        | SI   | NULL      | -                                   |
| fecha_cierre        | DATETIME        | SI   | NULL      | -                                   |
| created_at          | DATETIME        | SI   | NULL      | -                                   |
| updated_at          | DATETIME        | SI   | NULL      | -                                   |
| deleted_at          | DATETIME        | SI   | NULL      | Soft delete                        |

**Maquina de estados:**
```
borrador ──> publicada ──> cerrada
                |
                +──> suspendida ──> publicada (reactivar)
```

**Relaciones:**
- `emp_empresa` 1──N `vac_vacante`
- `cat_categoria` 1──N `vac_vacante`
- `cat_tipo_contrato` 1──N `vac_vacante`
- `cat_nivel_experiencia` 1──N `vac_vacante`
- `vac_vacante` 1──N `vac_requisito`
- `vac_vacante` 1──N `vac_habilidad`
- `vac_vacante` 1──N `post_postulacion`
- `vac_vacante` 1──N `ai_analisis_cv`

---

### 6.2 vac_requisito

Requisitos de cada vacante.

| Campo               | Tipo          | Nulo | Default     | Extra                              |
|--------------------|---------------|------|-------------|-------------------------------------|
| id                 | INT UNSIGNED  | NO   | -           | PK, AUTO_INCREMENT                 |
| vacante_id         | INT UNSIGNED  | NO   | -           | FK → vac_vacante.id (CASCADE)     |
| requisito_id       | INT UNSIGNED  | SI   | NULL        | FK → cat_requisito.id (SET NULL)  |
| habilidad_id       | INT UNSIGNED  | SI   | NULL        | FK → cat_habilidad.id (SET NULL)  |
| nivel_educacion_id | INT UNSIGNED  | SI   | NULL        | FK → cat_nivel_educacion.id (SET NULL) |
| descripcion        | TEXT          | SI   | NULL        | Descripcion personalizada          |
| tipo               | ENUM          | NO   | obligatorio | obligatorio, deseable              |
| created_at         | DATETIME      | SI   | NULL        | -                                   |
| updated_at         | DATETIME      | SI   | NULL        | -                                   |

---

### 6.3 vac_habilidad

Habilidades requeridas por vacante (N──N con cat_habilidad).

| Campo           | Tipo          | Nulo | Default     | Extra                          |
|----------------|---------------|------|-------------|--------------------------------|
| id             | INT UNSIGNED  | NO   | -           | PK, AUTO_INCREMENT             |
| vacante_id     | INT UNSIGNED  | NO   | -           | FK → vac_vacante.id (CASCADE) |
| habilidad_id   | INT UNSIGNED  | NO   | -           | FK → cat_habilidad.id (CASCADE) |
| nivel_requerido| ENUM          | NO   | intermedio  | basico, intermedio, avanzado, experto |
| tipo           | ENUM          | NO   | obligatorio | obligatorio, deseable          |
| created_at     | DATETIME      | SI   | NULL        | -                              |

---

## 7. Modulo: post_ (Postulaciones)

### 7.1 post_postulacion

Postulaciones de candidatos a vacantes.

| Campo            | Tipo           | Nulo | Default  | Extra                              |
|-----------------|----------------|------|----------|-------------------------------------|
| id              | INT UNSIGNED   | NO   | -        | PK, AUTO_INCREMENT                 |
| vacante_id      | INT UNSIGNED   | NO   | -        | FK → vac_vacante.id (CASCADE)     |
| candidato_id    | INT UNSIGNED   | NO   | -        | FK → cand_candidato.id (CASCADE)  |
| cv_id           | INT UNSIGNED   | SI   | NULL     | FK → cand_cv.id (SET NULL)       |
| mensaje         | TEXT           | SI   | NULL     | Mensaje del candidato              |
| estado          | ENUM           | NO   | enviada  | enviada, en_revision, aceptada, rechazada, descartada |
| puntaje_match   | DECIMAL(5,2)   | SI   | NULL     | Score calculado por IA             |
| fecha_entrevista| DATETIME       | SI   | NULL     | -                                   |
| notas_empresa   | TEXT           | SI   | NULL     | Notas internas                      |
| created_at      | DATETIME       | SI   | NULL     | -                                   |
| updated_at      | DATETIME       | SI   | NULL     | -                                   |
| deleted_at      | DATETIME       | SI   | NULL     | Soft delete                        |

**Maquina de estados:**
```
enviada ──> en_revision ──> aceptada
    |                          |
    |                          +──> rechazada
    +──> rechazada
    +──> descartada
```

**Relaciones:**
- `vac_vacante` 1──N `post_postulacion`
- `cand_candidato` 1──N `post_postulacion`
- `cand_cv` 1──N `post_postulacion`
- `post_postulacion` 1──N `post_historial`
- `post_postulacion` 1──N `post_verificacion`
- `post_postulacion` 1──N `ai_analisis_cv`

---

### 7.2 post_historial

Historial de cambios de estado de cada postulacion.

| Campo          | Tipo          | Nulo | Default | Extra                              |
|---------------|---------------|------|---------|-------------------------------------|
| id            | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT                 |
| postulacion_id| INT UNSIGNED  | NO   | -       | FK → post_postulacion.id (CASCADE) |
| estado_anterior| VARCHAR(50)   | SI   | NULL    | -                                   |
| estado_nuevo  | VARCHAR(50)   | NO   | -       | -                                   |
| comentario    | TEXT          | SI   | NULL    | -                                   |
| user_id       | INT UNSIGNED  | SI   | NULL    | FK → auth_user.id (SET NULL)      |
| created_at    | DATETIME      | SI   | NULL    | -                                   |

---

### 7.3 post_verificacion

Checklist de verificacion de candidatos. Cada postulacion genera items de verificacion basados en los requisitos de la vacante. El asesor los revisa en el portal administrativo.

| Campo            | Tipo          | Nulo | Default    | Extra                              |
|-----------------|---------------|------|------------|-------------------------------------|
| id              | INT UNSIGNED  | NO   | -          | PK, AUTO_INCREMENT                 |
| postulacion_id  | INT UNSIGNED  | NO   | -          | FK → post_postulacion.id (CASCADE) |
| requisito_id    | INT UNSIGNED  | SI   | NULL       | FK → cat_requisito.id (SET NULL)  |
| verificador_id   | INT UNSIGNED  | SI   | NULL       | FK → auth_user.id (SET NULL)      |
| item            | VARCHAR(150)  | NO   | -          | Nombre del requisito a verificar   |
| estado          | ENUM          | NO   | pendiente  | pendiente, aprobado, rechazado, no_aplica |
| comentario      | TEXT          | SI   | NULL       | Comentario del verificador         |
| fecha_verificacion| DATETIME    | SI   | NULL       | Fecha en que se completo el item   |
| created_at      | DATETIME      | SI   | NULL       | -                                   |
| updated_at      | DATETIME      | SI   | NULL       | -                                   |

**Flujo de verificacion:**
```
[Postulacion recibida]
    |
    v
[Sistema genera checklist con requisitos de la vacante]
    |
    v
[Asesor revisa cada item: pendiente → aprobado / rechazado / no_aplica]
    |
    +---> Todos aprobados → Candidato verificado (visible para empresas Basico+)
    +---> Algun rechazado → Candidato no verificado (notifica a empresa y candidato)
```

**Relaciones:**
- `post_postulacion` 1──N `post_verificacion`
- `cat_requisito` 1──N `post_verificacion` (nullable)
- `auth_user` 1──N `post_verificacion` (verificador_id, nullable)

---

## 8. Modulo: crm_ (CRM / Planes / Contratos)

### 8.1 crm_plan

Planes de suscripcion para empresas.

| Campo               | Tipo           | Nulo | Default | Extra                          |
|--------------------|----------------|------|---------|--------------------------------|
| id                 | INT UNSIGNED   | NO   | -       | PK, AUTO_INCREMENT             |
| nombre             | VARCHAR(100)   | NO   | -       | -                              |
| slug               | VARCHAR(100)   | NO   | -       | UNIQUE                         |
| descripcion        | TEXT           | SI   | NULL    | -                              |
| precio_mensual     | DECIMAL(10,2)  | NO   | -       | -                              |
| precio_anual       | DECIMAL(10,2)  | SI   | NULL    | -                              |
| moneda             | VARCHAR(3)     | NO   | USD     | -                              |
| max_vacantes       | INT            | NO   | 0       | 0 = ilimitado                  |
| max_postulaciones  | INT            | NO   | 0       | 0 = ilimitado                  |
| destacar_vacantes  | BOOLEAN        | NO   | false   | -                              |
| ver_perfil_completo| BOOLEAN        | NO   | false   | Ver perfil completo del candidato |
| ver_datos_basicos  | BOOLEAN        | NO   | false   | Ver datos basicos (profesion, ciudad) |
| acceso_candidatos_verificados| BOOLEAN | NO | false | Ver solo candidatos verificados |
| acceso_cvs        | BOOLEAN        | NO   | false   | Ver y descargar CVs             |
| soporte_prioritario| BOOLEAN        | NO   | false   | -                              |
| estado            | ENUM           | NO   | activo  | activo, inactivo               |
| created_at         | DATETIME       | SI   | NULL    | -                              |
| updated_at         | DATETIME       | SI   | NULL    | -                              |

**Permisos por plan:**

| Plan | ver_perfil | ver_datos | verificados | acceso_cvs | destacar |
|------|-----------|-----------|------------|------------|----------|
| Freemium | false | false | false | false | false |
| Basico | false | true | true | false | false |
| Pro | true | true | true | true | true |
| Enterprise | true | true | true | true | true |

**Relaciones:**
- `crm_plan` 1──N `crm_contrato`
- `crm_plan` 1──N `crm_lead` (plan_interes_id)

---

### 8.2 crm_contrato

Contratos activos entre empresas y planes.

| Campo            | Tipo         | Nulo | Default   | Extra                          |
|-----------------|--------------|------|-----------|--------------------------------|
| id              | INT UNSIGNED | NO   | -         | PK, AUTO_INCREMENT             |
| empresa_id      | INT UNSIGNED | NO   | -         | FK → emp_empresa.id (CASCADE) |
| plan_id         | INT UNSIGNED | NO   | -         | FK → crm_plan.id (RESTRICT)   |
| codigo          | VARCHAR(50)  | NO   | -         | UNIQUE                         |
| tipo_facturacion| ENUM         | NO   | mensual   | mensual, anual                 |
| fecha_inicio    | DATE         | NO   | -         | -                              |
| fecha_fin       | DATE         | SI   | NULL      | NULL = vigente indefinidamente |
| estado          | ENUM         | NO   | pendiente | activo, suspendido, cancelado, vencido, pendiente |
| auto_renovar    | BOOLEAN      | NO   | false     | -                              |
| created_at      | DATETIME     | SI   | NULL      | -                              |
| updated_at      | DATETIME     | SI   | NULL      | -                              |

**Relaciones:**
- `emp_empresa` 1──N `crm_contrato`
- `crm_plan` 1──N `crm_contrato`
- `crm_contrato` 1──N `crm_pago`

---

### 8.3 crm_pago

Pagos asociados a contratos.

| Campo            | Tipo           | Nulo | Default    | Extra                          |
|-----------------|----------------|------|------------|--------------------------------|
| id              | INT UNSIGNED   | NO   | -          | PK, AUTO_INCREMENT             |
| contrato_id     | INT UNSIGNED   | NO   | -          | FK → crm_contrato.id (CASCADE) |
| empresa_id      | INT UNSIGNED   | NO   | -          | FK → emp_empresa.id (CASCADE) |
| codigo          | VARCHAR(50)    | NO   | -          | UNIQUE                         |
| monto           | DECIMAL(10,2)  | NO   | -          | -                              |
| moneda          | VARCHAR(3)     | NO   | PEN        | -                              |
| metodo_pago     | VARCHAR(50)    | SI   | NULL       | -                              |
| estado          | ENUM           | NO   | pendiente  | pendiente, pagado, fallido, reembolsado |
| fecha_pago      | DATETIME       | SI   | NULL       | -                              |
| comprobante_path| VARCHAR(255)   | SI   | NULL       | Ruta del comprobante           |
| notas           | TEXT           | SI   | NULL       | -                              |
| created_at      | DATETIME       | SI   | NULL       | -                              |
| updated_at      | DATETIME       | SI   | NULL       | -                              |

---

### 8.4 crm_lead

Leads de captacion de empresas (CRM comercial).

| Campo            | Tipo          | Nulo | Default | Extra                              |
|-----------------|---------------|------|---------|-------------------------------------|
| id              | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT                 |
| empresa_id      | INT UNSIGNED  | SI   | NULL    | FK → emp_empresa.id (SET NULL)    |
| nombre_contacto | VARCHAR(150)  | NO   | -       | -                                   |
| email_contacto  | VARCHAR(150)  | NO   | -       | -                                   |
| telefono_contacto| VARCHAR(20)   | SI   | NULL    | -                                   |
| empresa_nombre  | VARCHAR(150)  | SI   | NULL    | Si la empresa no esta registrada    |
| origen          | ENUM          | NO   | web     | web, referido, campana, directo, otro |
| plan_interes_id | INT UNSIGNED  | SI   | NULL    | FK → crm_plan.id (SET NULL)       |
| estado          | ENUM          | NO   | nuevo   | nuevo, contactado, calificado, propuesta, negociacion, ganado, perdido |
| asesor_id       | INT UNSIGNED  | SI   | NULL    | FK → auth_user.id (SET NULL)       |
| notas           | TEXT          | SI   | NULL    | -                                   |
| created_at      | DATETIME      | SI   | NULL    | -                                   |
| updated_at      | DATETIME      | SI   | NULL    | -                                   |

**Maquina de estados del lead:**
```
nuevo ──> contactado ──> calificado ──> propuesta ──> negociacion ──> ganado
                                                                    +──> perdido
```

**Relaciones:**
- `emp_empresa` 1──N `crm_lead` (nullable)
- `crm_plan` 1──N `crm_lead` (plan_interes_id)
- `auth_user` 1──N `crm_lead` (asesor_id)
- `crm_lead` 1──N `crm_lead_seguimiento`

---

### 8.5 crm_lead_seguimiento

Seguimiento de interacciones con cada lead.

| Campo               | Tipo          | Nulo | Default  | Extra                          |
|--------------------|---------------|------|----------|--------------------------------|
| id                 | INT UNSIGNED  | NO   | -        | PK, AUTO_INCREMENT             |
| lead_id            | INT UNSIGNED  | NO   | -        | FK → crm_lead.id (CASCADE)    |
| asesor_id          | INT UNSIGNED  | SI   | NULL     | FK → auth_user.id (SET NULL)  |
| tipo_contacto      | ENUM          | NO   | llamada  | llamada, email, reunion, whatsapp, otro |
| comentario         | TEXT          | NO   | -        | -                              |
| fecha_proxima_accion| DATETIME      | SI   | NULL     | -                              |
| created_at         | DATETIME      | SI   | NULL     | -                              |

---

## 9. Modulo: not_ (Notificaciones)

### 9.1 not_notificacion

Notificaciones internas del sistema para usuarios.

| Campo      | Tipo          | Nulo | Default | Extra                          |
|-----------|---------------|------|---------|--------------------------------|
| id        | INT UNSIGNED  | NO   | -       | PK, AUTO_INCREMENT             |
| user_id   | INT UNSIGNED  | NO   | -       | FK → auth_user.id (CASCADE)   |
| tipo      | VARCHAR(50)   | NO   | -       | Tipo de notificacion           |
| titulo    | VARCHAR(150)  | NO   | -       | -                              |
| mensaje   | TEXT          | NO   | -       | -                              |
| url       | VARCHAR(255)  | SI   | NULL    | URL de accion                  |
| leida     | BOOLEAN       | NO   | false   | -                              |
| created_at| DATETIME      | SI   | NULL    | -                              |

**Tipos de notificacion:**

| Tipo          | Destinatario | Evento                              |
|--------------|-------------|-------------------------------------|
| postulacion  | Empresa     | Nueva postulacion a vacante         |
| postulacion  | Candidato   | Postulacion aceptada                 |
| postulacion  | Candidato   | Postulacion rechazada                |
| postulacion  | Candidato   | Postulacion en revision              |
| vacante      | Empresa     | Vacante aprobada por admin          |
| vacante      | Empresa     | Vacante suspendida por admin        |
| cuenta       | Usuario     | Cuenta suspendida                    |
| contrato     | Empresa     | Contrato por vencer                  |
| pago         | Empresa     | Pago confirmado                      |

---

## 10. Modulo: ai_ (Analisis IA / Gemini)

### 10.1 ai_analisis_cv

Analisis de CVs usando Google Gemini. Puede ser general o contra una vacante especifica.

| Campo               | Tipo           | Nulo | Default    | Extra                              |
|--------------------|----------------|------|-----------|-------------------------------------|
| id                 | INT UNSIGNED   | NO   | -         | PK, AUTO_INCREMENT                 |
| candidato_id       | INT UNSIGNED   | NO   | -         | FK → cand_candidato.id (CASCADE)  |
| cv_id              | INT UNSIGNED   | NO   | -         | FK → cand_cv.id (CASCADE)         |
| postulacion_id     | INT UNSIGNED   | SI   | NULL      | FK → post_postulacion.id (SET NULL) |
| vacante_id         | INT UNSIGNED   | SI   | NULL      | FK → vac_vacante.id (SET NULL)    |
| prompt             | TEXT           | SI   | NULL      | Prompt enviado a Gemini            |
| respuesta          | LONGTEXT       | NO   | -         | Respuesta de Gemini               |
| score_match        | DECIMAL(5,2)   | SI   | NULL      | Puntaje de compatibilidad 0-100    |
| fortalezas         | TEXT           | SI   | NULL      | Fortalezas detectadas             |
| debilidades        | TEXT           | SI   | NULL      | Debilidades detectadas            |
| recomendacion      | TEXT           | SI   | NULL      | Recomendacion de la IA             |
| modelo_ia          | VARCHAR(50)    | NO   | gemini    | Modelo usado                      |
| tokens_usados      | INT            | SI   | NULL      | Tokens consumidos                 |
| tiempo_respuesta_ms| INT            | SI   | NULL      | Tiempo de respuesta en ms         |
| estado             | ENUM           | NO   | procesando| procesando, completado, error     |
| error              | TEXT           | SI   | NULL      | Mensaje de error si aplica         |
| created_at         | DATETIME       | SI   | NULL      | -                                   |
| updated_at         | DATETIME       | SI   | NULL      | -                                   |

**Tipos de analisis:**
1. **Analisis general del CV** (sin vacante_id): Gemini analiza el CV y extrae fortalezas, debilidades, score general.
2. **Match contra vacante** (con vacante_id): Gemini compara el CV con los requisitos de la vacante y genera score_match.

**Flujo:**
```
[Candidato sube CV] ──> [ai_analisis_cv: estado=procesando]
    |
    +──> [Gemini analiza CV] ──> [estado=completado, score_match, fortalezas, debilidades]
    |
    +──> [Error] ──> [estado=error, error=mensaje]
```

---

## 11. Diagrama ER Completo

```
                                    ┌─────────────┐
                                    │ auth_role   │
                                    └──────┬──────┘
                                           │ 1
                              ┌────────────┼────────────┐
                              │ N          │ N          │
                     ┌────────┴──────┐ ┌────┴──────────────┐
                     │  auth_user   │ │ auth_role_permission│
                     └──────┬──────┘ └─────────┬───────────┘
                            │ 1                 │ N
              ┌─────────────┼──────────┐  ┌─────┴──────────┐
              │ 1           │ 1        │  │ auth_permission │
     ┌────────┴────┐ ┌─────┴──────┐  │  └────────────────┘
     │ emp_empresa │ │cand_candidato│ │
     └──────┬──────┘ └──────┬─────┘  │
            │ 1              │ 1      │ N
     ┌──────┼──────┐  ┌──────┼──────┐ │
     │ N    │ N    │  │ N    │ N    │ │ ┌──────────────┐
┌────┴──┐┌─┴────┐│┌─┴────┐┌┴────┐│  │ │not_notificacion│
│crm_   ││crm_  │││cand_ ││cand_││  │ └──────────────┘
│contrato││pago  │││cv    ││exp. ││
└──┬───┘└──────┘│└──┬───┘└─────┘│
   │ N          │   │ N         │ N
┌──┴────┐       │┌──┴──────┐┌───┴──────┐┌──────────┐
│crm_plan│      ││ai_anal. ││cand_hab. ││cand_idioma│
└───────┘      ││  cv     │└────┬─────┘└────┬─────┘
               │└─────────┘     │ N          │ N
               │                │            │
               │           ┌────┴─────┐ ┌────┴────┐
               │           │cat_habil.│ │cat_idioma│
               │           └──────────┘ └─────────┘
               │
     ┌─────────┴──────┐
     │  vac_vacante   │
     └──────┬────────┘
            │ 1
     ┌──────┼──────────────┐
     │ N    │ N            │ N
┌────┴──┐┌─┴────────┐┌────┴──────┐
│vac_req││vac_habil. ││post_post. │
└───────┘└──────────┘└─────┬─────┘
                           │ 1
                     ┌─────┼─────┐
                     │ N   │ N   │ N
              ┌──────┴──┐┌─┴──────┐┌───┴──────────┐
              │post_hist.││post_ver.││ai_analisis_cv│
              └─────────┘└────────┘└──────────────┘
```

---

## 12. Seeds Iniciales (Sugeridos)

### 12.1 Roles
| nombre      | slug      | es_asignable |
|------------|-----------|-------------|
| Administrador | admin   | false       |
| Asesor       | asesor   | false       |
| Empresa      | empresa  | true        |
| Candidato    | candidato| true        |

### 12.2 Categorias
| nombre        | slug          |
|--------------|---------------|
| Tecnologia    | tecnologia    |
| Salud         | salud         |
| Finanzas      | finanzas      |
| Educacion     | educacion     |
| Construcción  | construccion  |
| Administracion| administracion|
| Ventas        | ventas        |
| Logistica     | logistica     |
| Marketing     | marketing     |
| Diseno        | diseno        |

### 12.3 Tipos de Contrato
| nombre          | slug            |
|----------------|-----------------|
| Tiempo Completo | tiempo_completo |
| Medio Tiempo    | medio_tiempo    |
| Por Proyecto    | proyecto        |
| Temporal        | temporal        |
| Practicas       | practicas       |

### 12.4 Niveles de Educacion
| nombre       | slug         | orden |
|-------------|-------------|-------|
| Primaria    | primaria    | 1     |
| Secundaria  | secundaria  | 2     |
| Tecnico     | tecnico     | 3     |
| Universitario| universitario| 4    |
| Posgrado    | posgrado    | 5     |
| Doctorado   | doctorado   | 6     |

### 12.5 Niveles de Experiencia
| nombre        | slug          | anios_min | anios_max |
|--------------|--------------|----------|----------|
| Sin Experiencia| sin_experiencia| 0      | 0        |
| Junior        | junior        | 0        | 2        |
| Semi-Senior   | semi_senior   | 2        | 5        |
| Senior        | senior        | 5        | 10       |
| Lead/Expert   | lead          | 10       | NULL     |

### 12.6 Planes
| nombre    | slug       | precio_mensual | max_vacantes | max_postulaciones | ver_perfil | ver_datos | verificados | acceso_cvs | destacar |
|-----------|-----------|---------------|-------------|-------------------|------------|-----------|------------|------------|----------|
| Freemium  | freemium  | 0.00          | 3           | 50                | false      | false     | false      | false      | false    |
| Basico    | basico    | 99.00         | 10          | 200               | false      | true      | true       | false      | false    |
| Pro       | pro       | 299.00        | 0           | 0                 | true       | true      | true       | true       | true     |
| Enterprise| enterprise| 799.00        | 0           | 0                 | true       | true      | true       | true       | true     |

### 12.7 Requisitos Predefinidos (es_default = true)
| nombre                                | slug                    | tipo        | es_default |
|--------------------------------------|------------------------|------------|-----------|
| Cedula de identidad                   | cedula-identidad       | obligatorio | true      |
| Antecedentes penales (Record Policia) | antecedentes-penales   | obligatorio | true      |
| Cotizaciones INSS                     | cotizaciones-inss      | obligatorio | true      |
| Curriculum actualizado                | cv-actualizado         | obligatorio | true      |
| Referencias laborales                 | referencias-laborales  | obligatorio | true      |
| Experiencia comprobable               | experiencia-comprobable | obligatorio | true     |
| Titulo academico                      | titulo-academico       | obligatorio | true      |
| Disponibilidad inmediata              | disponibilidad-inmediata | deseable  | true      |
| Vehiculo propio                       | vehiculo-propio        | deseable    | true      |
| Disponibilidad para viajar            | disponibilidad-viajar   | deseable   | true      |
| Licencia de conducir                  | licencia-conducir      | deseable    | true      |

### 12.8 Requisitos No-Default (buscador)
| nombre                    | slug                    | tipo     | es_default |
|--------------------------|------------------------|----------|-----------|
| Conocimiento en C#        | conocimiento-csharp     | deseable | false      |
| Conocimiento en PHP       | conocimiento-php        | deseable | false      |
| Conocimiento en Python    | conocimiento-python     | deseable | false      |
| Conocimiento en JavaScript| conocimiento-javascript | deseable | false      |
| Conocimiento en Java      | conocimiento-java       | deseable | false      |
| Manejo de Excel avanzado  | excel-avanzado         | deseable | false      |
| Manejo de paquetes contables | paquetes-contables   | deseable | false      |
| Ingles conversacional     | ingles-conversacional   | deseable | false      |
| Trabajo bajo presion      | trabajo-bajo-presion   | deseable | false      |
| Liderazgo de equipos      | liderazgo-equipos      | deseable | false      |
| Certificacion tecnica     | certificacion-tecnica   | deseable | false      |

**Logica de requisitos al crear vacante:**
1. Los requisitos con `es_default = true` aparecen como checkboxes marcados por defecto.
2. La empresa puede desmarcar los que no necesita.
3. Un buscador permite buscar requisitos con `es_default = false` (o todos) y agregarlos.
4. La empresa tambien puede crear nuevos requisitos personalizados desde el buscador.
