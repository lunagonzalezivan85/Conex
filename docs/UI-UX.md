# UI/UX - Guia de Diseno

## CONEX - Sistema de Reclutamiento de Personal

**Version:** 1.0  
**Fecha:** 2026-09-12  
**Estado:** Borrador

---

## 1. Filosofia de Diseno

### 1.1 Estilo: ONE UI

El sistema adopta el lenguaje de diseno **ONE UI** (inspirado en Samsung One UI), caracterizado por:

- **Areas de contenido amplias** con mucho espacio en blanco.
- **Elementos grandes y tocables** en la parte superior de la pantalla.
- **Controles primarios en la zona inferior** o facilmente accesible.
- **Jerarquia visual clara** con secciones bien delimitadas.
- **Tipografia legible** con tamaños generosos.
- **Colores suaves** con fondos claros y acentos de color.
- **Bordes redondeados** en tarjetas, botones e inputs.

### 1.2 Layout: Bento Grid

El sistema de organizacion visual usa **Bento Grid**:

- Las paginas se dividen en **tarjetas (cards) modulares**.
- Cada tarjeta contiene una unidad de informacion o accion.
- Las tarjetas se organizan en una **rejilla flexible** que se reorganiza segun el tamano de pantalla.
- En desktop: grid de 3-4 columnas.
- En tablet: grid de 2 columnas.
- En movil: grid de 1 columna (apilado).

```
Ejemplo Bento Grid (Dashboard Empresa):

┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│  Vacantes    │ │ Postulaciones│ │  Metricas   │
│  Activas: 12 │ │  Nuevas: 8   │ │  Visitas    │
└──────────────┘ └──────────────┘ └──────────────┘
┌────────────────────────────┐ ┌──────────────┐
│  Tabla de Vacantes          │ │  Acciones   │
│  Recientes                  │ │  Rapidas    │
│  (con buscador y filtros)   │ │             │
└────────────────────────────┘ └──────────────┘
```

---

## 2. Sistema de Colores (Propuesta)

### 2.1 Paleta Base

| Token            | Color           | Uso                          |
|-----------------|-----------------|------------------------------|
| `--bg-primary`   | `#F8F9FA`       | Fondo principal              |
| `--bg-secondary` | `#FFFFFF`       | Tarjetas, inputs              |
| `--bg-dark`      | `#1A1A2E`       | Sidebar / header oscuro       |
| `--text-primary` | `#1A1A2E`       | Texto principal               |
| `--text-secondary`| `#6C757D`      | Texto secundario              |
| `--text-muted`   | `#ADB5BD`       | Texto tenue                   |
| `--accent`       | `#4361EE`       | Color primario / acentos      |
| `--accent-light` | `#E8EDFF`       | Fondo de acento suave          |
| `--success`     | `#2ECC71`       | Estados positivos              |
| `--warning`     | `#F39C12`       | Advertencias                   |
| `--danger`      | `#E74C3C`       | Errores / eliminar             |
| `--border`      | `#E9ECEF`       | Bordes y separadores           |

### 2.2 Colores por Layout

Cada layout tiene un color de acento distintivo para diferenciacion visual:

| Layout          | Acento           | Header/Sidebar    |
|-----------------|------------------|-------------------|
| Publico         | `#4361EE` (azul) | Claro / blanco    |
| Administracion  | `#6C5CE7` (violeta) | Oscuro          |
| Empresa         | `#00B894` (verde) | Oscuro            |
| Candidato       | `#0984E3` (azul claro) | Claro        |

---

## 3. Tipografia

| Elemento         | Tamano  | Peso   | Line Height |
|-----------------|---------|--------|-------------|
| H1 (Titulo pag) | 28px    | 700    | 1.3         |
| H2 (Seccion)    | 22px    | 600    | 1.4         |
| H3 (Card title) | 18px    | 600    | 1.4         |
| Body            | 15px    | 400    | 1.6         |
| Small / Caption | 13px    | 400    | 1.5         |
| Button text     | 15px    | 600    | 1.0         |

**Fuente:** Inter (Google Fonts) o similar sans-serif moderna.

---

## 4. Componentes UI

### 4.1 Tarjetas (Cards)

```
┌─────────────────────────────────┐
│  [Icono]  Titulo de Card        │  <- Header con icono opcional
│  Subtitulo / descripcion        │
├─────────────────────────────────┤
│                                 │
│  Contenido de la tarjeta        │  <- Body flexible
│                                 │
├─────────────────────────────────┤
│              [Accion] [Accion]  │  <- Footer con botones (opcional)
└─────────────────────────────────┘
```

- Border-radius: 16px
- Padding: 24px
- Box-shadow: suave (0 2px 8px rgba(0,0,0,0.06))
- Background: `--bg-secondary` (blanco)

### 4.2 Tablas con Buscador y Filtros

**Todas las tablas y listas deben incluir:**

```
┌──────────────────────────────────────────────────────┐
│  [Q Buscar...]  [Filtro 1 v] [Filtro 2 v]  [Limpiar] │  <- Toolbar
├──────────────────────────────────────────────────────┤
│  Col 1        Col 2        Col 3        Acciones    │  <- Header
├──────────────────────────────────────────────────────┤
│  dato         dato         dato         [ver][edit] │  <- Rows
│  dato         dato         dato         [ver][edit] │
├──────────────────────────────────────────────────────┤
│  Mostrando 1-10 de 50        [< 1 2 3 ... >]        │  <- Pagination
└──────────────────────────────────────────────────────┘
```

- Buscador con icono de lupa, placeholder descriptivo.
- Filtros como dropdowns o chips seleccionables.
- Boton "Limpiar filtros" visible cuando hay filtros activos.
- Paginacion en la parte inferior.
- En movil: las tablas se transforman en tarjetas apiladas.

### 4.3 Formularios

- Inputs con border-radius 12px, padding 12px 16px.
- Labels arriba del input, texto secundario.
- Validacion visual: borde rojo para errores, verde para validos.
- Boton primario al final, alineado a la derecha.
- Agrupacion logica con separadores o secciones.

### 4.4 Botones

| Tipo      | Estilo                              | Uso                    |
|-----------|--------------------------------------|------------------------|
| Primario  | Fondo accent, texto blanco           | Accion principal       |
| Secundario| Borde accent, texto accent           | Accion alternativa     |
| Ghost     | Sin borde, texto accent              | Accion contextual      |
| Danger    | Fondo danger, texto blanco           | Eliminar / cancelar    |
| Icon      | Solo icono, fondo transparente       | Acciones en tablas     |

- Border-radius: 10px
- Padding: 10px 20px
- Transicion suave en hover

### 4.5 Badges / Estados

| Estado        | Color de fondo     | Texto          |
|---------------|---------------------|----------------|
| Activo        | `#D4EDDA`           | Verde oscuro   |
| Inactivo      | `#FFF3CD`           | Amarillo oscuro|
| Suspendido    | `#F8D7DA`           | Rojo oscuro    |
| Pendiente     | `#E8EDFF`           | Azul oscuro    |

### 4.6 Navegacion

#### Sidebar (Admin, Empresa, Candidato)

- Lateral izquierdo, ancho 260px (desktop).
- Colapsable a iconos en tablet.
- Oculto en movil, se abre con boton hamburguesa.
- Items con icono + texto, indicador visual de pagina activa.

#### Header (Publico)

- Barra superior fija con logo, buscador, botones de login/registro.
- En movil: logo + icono hamburguesa + icono buscar.

---

## 5. Layouts Detallados

### 5.1 Layout Publico

```
┌──────────────────────────────────────────────────────┐
│  [LOGO]  [Buscar vacantes...]    [Login] [Registro]  │  <- Header
├──────────────────────────────────────────────────────┤
│                                                      │
│  ┌────────────────────────────────────────────────┐  │
│  │  HERO: Banner principal con buscador grande    │  │
│  │  "Encuentra tu proximo empleo"                 │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐   │
│  │ Vacante │ │ Vacante │ │ Vacante │ │ Vacante │   │  <- Grid de vacantes
│  │ Card    │ │ Card    │ │ Card    │ │ Card    │   │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘   │
│                                                      │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐   │
│  │ Vacante │ │ Vacante │ │ Vacante │ │ Vacante │   │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘   │
│                                                      │
├──────────────────────────────────────────────────────┤
│  Footer: links, contacto, redes sociales             │
└──────────────────────────────────────────────────────┘
```

### 5.2 Layout Administracion

```
┌────────┬─────────────────────────────────────────────┐
│        │  [Buscar...] [Notif] [Avatar]                │  <- Topbar
│ SIDEBAR├─────────────────────────────────────────────┤
│        │                                             │
│ Dashbd │  ┌──────────┐ ┌──────────┐ ┌──────────┐     │
│ Usuari │  │ Card 1   │ │ Card 2   │ │ Card 3   │     │  <- Bento Grid
│ Vacant │  └──────────┘ └──────────┘ └──────────┘     │
│ Catego │  ┌────────────────┐ ┌────────────────┐     │
│ Config │  │ Tabla con       │ │ Grafico /      │     │
│        │  │ buscador+filtros│ │ Estadisticas   │     │
│        │  └────────────────┘ └────────────────┘     │
│        │                                             │
└────────┴─────────────────────────────────────────────┘
```

### 5.3 Layout Empresa

```
┌────────┬─────────────────────────────────────────────┐
│        │  [Buscar...] [Notif] [Avatar]                │  <- Topbar
│ SIDEBAR├─────────────────────────────────────────────┤
│        │                                             │
│ Dashbd │  ┌──────────┐ ┌──────────┐ ┌──────────┐     │
│ Vacant │  │ Vacantes │ │ Postul.  │ │ Vistas   │     │  <- Metricas
│ Postul │  │ Activas  │ │ Nuevas   │ │ Perfil   │     │
│ Perfil │  └──────────┘ └──────────┘ └──────────┘     │
│        │  ┌────────────────────────────────────┐     │
│        │  │ Tabla de vacantes                  │     │
│        │  │ [Buscar] [Filtro estado] [Limpiar] │     │
│        │  └────────────────────────────────────┘     │
│        │                                             │
└────────┴─────────────────────────────────────────────┘
```

### 5.4 Layout Candidato

```
┌────────┬─────────────────────────────────────────────┐
│        │  [Buscar vacantes...] [Notif] [Avatar]       │  <- Topbar
│ SIDEBAR├─────────────────────────────────────────────┤
│        │                                             │
│ Dashbd │  ┌──────────┐ ┌──────────┐ ┌──────────┐     │
│ Buscar │  │ Postul.  │ │ CV %     │ │ Recomen- │     │  <- Metricas
│ Postul │  │ Activas  │ │ Completo │ │ daciones │     │
│ Perfil │  └──────────┘ └──────────┘ └──────────┘     │
│        │  ┌────────────────────────────────────┐     │
│        │  │ Vacantes recomendadas               │     │
│        │  │ [Buscar] [Filtro categoria] [Limpiar]│     │
│        │  └────────────────────────────────────┘     │
│        │                                             │
└────────┴─────────────────────────────────────────────┘
```

---

## 6. Responsive Breakpoints

| Breakpoint | Ancho      | Comportamiento                          |
|------------|-----------|-----------------------------------------|
| Mobile     | < 640px   | 1 columna, sidebar oculto, tablas->cards|
| Tablet     | 640-1024px| 2 columnas, sidebar colapsado           |
| Desktop    | > 1024px  | 3-4 columnas, sidebar expandido         |

---

## 7. Principios de UX

1. **Buscador siempre visible** en toda pantalla con listas o tablas.
2. **Filtros accesibles** junto al buscador, no ocultos.
3. **Estados vacios** con ilustracion y mensaje orientador.
4. **Feedback inmediato** en acciones (toast, badge, cambio de color).
5. **Confirmacion antes de eliminar** (modal o alerta).
6. **Breadcrumbs** en pantallas con multiple nivel de navegacion.
7. **Loading states** con skeleton o spinner.
8. **Agrupacion por contexto** en formularios largos.

---

## 8. Pendientes de Diseno

- [ ] Definir paleta de colores final.
- [ ] Disenar wireframes de cada pantalla principal.
- [ ] Definir sistema de iconos (Lucide, Heroicons, etc.).
- [ ] Crear prototipo en Figma o similar.
- [ ] Definir guia de componentes reutilizables.
- [ ] Establecer estandar de microinteracciones.
