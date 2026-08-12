# OpenToWork - Esquema de Diseno UI/UX

## Layouts por Portal y Pagina

> **Regla:** Todo layout debe respetar este esquema. FS no puede crear paginas que se desvien de estos diagramas.

---

## 1. Portal Principal (WEB) - Layout Autenticado

```
+============================================================================+
| LOGO  | NAV BAR (Dashboard, Vacantes, Perfil) | THEME | LANG | LOGOUT     |
+======+=======================================+=======+======+==============+
|                                                                            |
|                                                                            |
|  MAIN CONTENT AREA                                                         |
|  (Bento Grid / Formularios / Listas)                                       |
|                                                                            |
|  Max width: 1200px, centrado                                               |
|  Padding: 2rem 1rem                                                        |
|                                                                            |
|                                                                            |
+============================================================================+
```

### Dashboard (Bento Grid)

```
+============================================================================+
| LOGO  | NAV BAR                             | THEME | LANG | LOGOUT       |
+======+======================================+=======+======+==============+
|                                                                            |
|  H1: Dashboard                                                             |
|                                                                            |
|  +----------------------------------------------------------------------+  |
|  | [WIZARD PROMPT - si perfil incompleto]                              |  |
|  +----------------------------------------------------------------------+  |
|                                                                            |
|  BENTO GRID (auto-fill, minmax 300px)                                      |
|  +-------------------+-------------------+-------------------+            |
|  | STAT: % Perfil    | STAT: Vacantes    | STAT: Solicitudes |            |
|  +-------------------+-------------------+-------------------+            |
|                                                                            |
|  H2: Acciones Rapidas                                                      |
|  +-------------------+-------------------+-------------------+            |
|  | Completar Perfil  | Buscar Vacantes   | Crear Vacante    |            |
|  +-------------------+-------------------+-------------------+            |
|                                                                            |
|  H2: Vacantes Recomendadas                                                 |
|  +-------------------+-------------------+-------------------+            |
|  | Vacancy Card      | Vacancy Card      | Vacancy Card     |            |
|  +-------------------+-------------------+-------------------+            |
|                                                                            |
+============================================================================+
```

### Busqueda de Vacantes

```
+============================================================================+
| LOGO  | NAV BAR                             | THEME | LANG | LOGOUT       |
+======+======================================+=======+======+==============+
|                                                                            |
|  H1: Buscar Vacantes                                                       |
|                                                                            |
|  SEARCH BAR                                                                |
|  +---------------------+------------------+----------------+---------+    |
|  | Input: buscar...    | Input: ubicacion | Select: tipo   | Buscar  |    |
|  +---------------------+------------------+----------------+---------+    |
|                                                                            |
|  Resultados: X vacantes encontradas                                        |
|                                                                            |
|  BENTO GRID                                                                |
|  +-------------------+-------------------+-------------------+            |
|  | Vacancy Card      | Vacancy Card      | Vacancy Card     |            |
|  +-------------------+-------------------+-------------------+            |
|  +-------------------+-------------------+-------------------+            |
|  | Vacancy Card      | Vacancy Card      | Vacancy Card     |            |
|  +-------------------+-------------------+-------------------+            |
|                                                                            |
|  PAGINACION                                                                |
|  [ < ] [ 1 ] [ 2 ] [ 3 ] [ > ]                                            |
|                                                                            |
+============================================================================+
```

---

## 2. Portal Principal (WEB) - Layout Auth (Login/Register)

```
+============================================================================+
|  LOGO: OpenToWork                              | THEME | LANG |            |
+================================================+=======+======+============+
|                                                                            |
|                                                                            |
|                 +-----------------------+                                   |
|                 |                       |                                   |
|                 |    AUTH CARD          |                                   |
|                 |    (max-width: 440px) |                                   |
|                 |                       |                                   |
|                 |    H1: Login          |                                   |
|                 |    Subtitle           |                                   |
|                 |                       |                                   |
|                 |    [ Email input   ]  |                                   |
|                 |    [ Password input]  |                                   |
|                 |    Remember | Forgot   |                                   |
|                 |    [ LOGIN button  ]  |                                   |
|                 |    ------ o ------    |                                   |
|                 |    [ Google button ]  |                                   |
|                 |    No account? Register|                                   |
|                 +-----------------------+                                   |
|                                                                            |
|                 Centrado vertical y horizontal                              |
|                 Fondo: var(--bg-tertiary)                                   |
|                                                                            |
+============================================================================+
```

### Register (con seleccion de rol)

```
                 +-----------------------+
                 |                       |
                 |    AUTH CARD          |
                 |    (max-width: 440px) |
                 |                       |
                 |    H1: Crear Cuenta   |
                 |    Subtitle           |
                 |                       |
                 |   "Que deseas hacer?" |
                 |                       |
                 |  +-----------------+  |
                 |  |  BUSCAR EMPLEO  |  |
                 |  |  (Candidato)    |  |
                 |  +-----------------+  |
                 |  +-----------------+  |
                 |  |  CONTRATAR      |  |
                 |  |  (Empresa)      |  |
                 |  +-----------------+  |
                 |                       |
                 |    [ Email input   ]  |
                 |    [ Password input]  |
                 |    [ Identification]  |
                 |    [ Phone         ]  |
                 |    [ REGISTER btn  ]  |
                 |    Ya tienes cuenta?  |
                 |    Login aqui         |
                 +-----------------------+
```

---

## 3. Portal Principal (WEB) - Wizard

```
+============================================================================+
|  LOGO: OpenToWork                              | THEME | LANG |            |
+================================================+=======+======+============+
|                                                                            |
|  WIZARD CONTAINER (max-width: 640px)                                       |
|  Centrado, padding 2rem                                                    |
|                                                                            |
|  H2: Completa tu perfil                                                    |
|                                                                            |
|  PROGRESS BAR                                                              |
|  [##############..............] 50%                                        |
|                                                                            |
|  STEP INDICATOR                                                            |
|  (1)----(2)----(3)----(4)----(5)----(6)                                   |
|  OK      OK    [3]     .      .      .                                    |
|                                                                            |
|  +----------------------------------------------------------------------+  |
|  | H3: Titulo del paso actual                                          |  |
|  | Subtitle: descripcion                                               |  |
|  |                                                                      |  |
|  | [ Input: campo 1 ]                                                  |  |
|  | [ Input: campo 2 ]                                                  |  |
|  | [ Input: campo 3 ]                                                  |  |
|  | [ Select: opcion ]                                                  |  |
|  +----------------------------------------------------------------------+  |
|                                                                            |
|  [ ANTERIOR ]                                    [ SIGUIENTE ]            |
|                                                                            |
+============================================================================+
```

---

## 4. Portal Principal (WEB) - Home (Seleccion de Rol)

```
+============================================================================+
|  LOGO: OpenToWork                              | THEME | LANG |            |
+================================================+=======+======+============+
|                                                                            |
|                                                                            |
|  H1: OpenToWork                                                            |
|  Tu portal de empleo                                                       |
|                                                                            |
|  ROLE SELECTOR (grid 2 columnas)                                           |
|  +-----------------------+-----------------------+                         |
|  |                       |                       |                         |
|  |    BUSCAR EMPLEO      |    CONTRATAR          |                         |
|  |    (icono: buscar)    |    (icono: empresa)   |                         |
|  |                       |                       |                         |
|  |    Encuentra tu       |    Contrata talento   |                         |
|  |    proximo job        |    para tu empresa    |                         |
|  |                       |                       |                         |
|  +-----------------------+-----------------------+                         |
|                                                                            |
|  Ya tienes cuenta? Iniciar Sesion                                          |
|                                                                            |
+============================================================================+
```

---

## 5. Portal Admin (AdminWEB) - Layout Principal [FASE 3]

```
+============================================================================+
| LOGO  | NAV BAR (Dashboard, Usuarios, Vacantes, Solicitudes) | PROFILE    |
+======+======================================================+============+
|       |                                                                    |
|       |                                                                    |
| SIDE  |   MAIN CONTENT AREA                                                |
| BAR   |                                                                    |
|       |   H1: Dashboard Admin                                              |
| menu  |                                                                    |
| lat-  |   STATS ROW                                                        |
| eral  |   +--------+---------+----------+-------------+----------+         |
|       |   | TOTAL  | ACTIVOS | VACANTES | SOLICITUDES | REPORTES |         |
|       |   +--------+---------+----------+-------------+----------+         |
|       |                                                                    |
| - Dash|   DATA TABLE                                                       |
| - User|   +----+--------+----------+--------+--------+--------+            |
| - Vacs|   | ID | NOMBRE | EMAIL    | ROL    | ESTADO | ACCION |            |
| - Apps|   +----+--------+----------+--------+--------+--------+            |
| - Cats|   | 01 | Juan   | j@x.com  | Candi. | Activo | [...]   |            |
| - Logs|   | 02 | Empresa| e@x.com  | Company| Activo | [...]   |            |
| - Conf|   +----+--------+----------+--------+--------+--------+            |
|       |                                                                    |
|       |   PAGINACION                                                       |
|       |   [ < ] [ 1 ] [ 2 ] [ 3 ] [ > ]                                   |
|       |                                                                    |
+=======+====================================================================+
```

### Sidebar Admin (expandido)

```
+-------------------+
|                   |
|   ADMIN PANEL     |
|                   |
|   [ Dashboard ]   |
|                   |
|   [ Usuarios ]    |
|     - Candidatos  |
|     - Empresas    |
|                   |
|   [ Vacantes ]    |
|     - Activas     |
|     - Pendientes  |
|     - Eliminadas  |
|                   |
|   [ Solicitudes ] |
|                   |
|   [ Categorias ]  |
|     - Skills      |
|     - Areas       |
|                   |
|   [ Auditoria ]   |
|                   |
|   [ Config ]      |
|                   |
+-------------------+
```

---

## 6. Componentes UI - Esquema

### Bento Card

```
+----------------------------------------+
|  [icono]  TITULO DE LA CARD            |
+----------------------------------------+
|                                        |
|  Descripcion o contenido               |
|  de la tarjeta.                        |
|                                        |
|  [ ChildContent ]                      |
|                                        |
+----------------------------------------+
|  Footer text (opcional)                |
+----------------------------------------+
```

### Stat Card

```
+------------------------+
|                        |
|        42              |
|       (grande)         |
|                        |
|     Label stat         |
|                        |
+------------------------+
```

### Vacancy Card

```
+----------------------------------------+
|  Titulo de la Vacante                  |
|  [icono] Ubicacion                     |
+----------------------------------------+
|  Descripcion de la vacante (2 lineas   |
|  maximo con line-clamp)...             |
+----------------------------------------+
|  $1,000 - $2,000     | [ BADGE: FT ]   |
|  Expira en 5 dias                       |
+----------------------------------------+
```

### OTButton (variantes)

```
PRIMARY            SECONDARY          OUTLINE            GHOST
+-------------+    +-------------+    +-------------+    +-------------+
| [ Guardar ] |    |  Guardar    |    |  Guardar    |    |  Guardar    |
+-------------+    +-------------+    +-------------+    +-------------+
bg: accent         bg: tertiary       border: accent     bg: transparent
color: white       color: text        color: accent      color: secondary
```

### OTInput

```
+--------------------------------+
| Label                          |
+--------------------------------+
| [ input field_______________ ] |
+--------------------------------+
| Error message (red)            |
+--------------------------------+
```

### Wizard Step Indicator

```
  (OK)----(OK)----([3])----( . )----( . )----( . )
  done     done    active   pending   pending   pending
  green    green   blue     gray      gray      gray
```

---

## 7. Responsive Breakpoints

### Desktop (> 768px)

```
+============================================================================+
| NAV BAR completa: LOGO | links | THEME | LANG | LOGOUT                    |
+============================================================================+
| MAIN CONTENT: max-width 1200px, centrado                                  |
| BENTO GRID: auto-fill, minmax(300px, 1fr)                                 |
| ROLE SELECTOR: 2 columnas                                                 |
+============================================================================+
```

### Tablet / Mobile (<= 768px)

```
+========================================+
| LOGO              | THEME | LANG |     |
+===================+=======+======+=====+
|                                        |
|  MAIN CONTENT                          |
|  padding: 1rem 0.75rem                 |
|                                        |
|  BENTO GRID: 1 columna                 |
|  bento-card--large: span 1             |
|  bento-card--wide: span 1              |
|                                        |
|  ROLE SELECTOR: 1 columna              |
|                                        |
|  NAV: oculta (menu hamburguesa)        |
|                                        |
+========================================+
```

### Mobile pequeno (<= 480px)

```
+================================+
| LOGO       | THEME | LANG |    |
+============+=======+======+====+
|                                |
|  MAIN CONTENT                  |
|  padding: 1rem                 |
|                                |
|  BENTO GRID: gap 0.75rem       |
|  CARD padding: 1rem            |
|                                |
|  AUTH CARD: padding 1.5rem     |
|  H1: 1.4rem                    |
|                                |
+================================+
```

---

## 8. Sistema de Temas - Esquema de Variables

```
TEMA NAVY (default)        TEMA DARK                  TEMA LIGHT
------------------------   ------------------------   ------------------------
--bg-primary:    #F5F7FA   --bg-primary:    #0F172A   --bg-primary:    #FFFFFF
--bg-secondary:  #FFFFFF   --bg-secondary:  #1E293B   --bg-secondary:  #F8FAFC
--bg-tertiary:   #EDF1F7   --bg-tertiary:   #334155   --bg-tertiary:   #F1F5F9
--text-primary:  #1B263B   --text-primary:  #F1F5F9   --text-primary:  #1E293B
--text-secondary:#5A6B85   --text-secondary:#CBD5E1   --text-secondary:#475569
--accent-primary:#3B82F6   --accent-primary:#3B82F6   --accent-primary:#0284C7
--accent-hover:  #2563EB   --accent-hover:  #60A5FA   --accent-hover:  #0369A1
--card-bg:       #FFFFFF   --card-bg:       #1E293B   --card-bg:       #FFFFFF
--nav-bg:        #1B263B   --nav-bg:        #0F172A   --nav-bg:        #1E293B
--border-color:  #E2E8F0   --border-color:  #334155   --border-color:  #E2E8F0
--success:       #10B981   --success:       #10B981   --success:       #059669
--danger:        #EF4444   --danger:        #EF4444   --danger:        #DC2626
--warning:       #F59E0B   --warning:       #F59E0B   --warning:       #D97706
------------------------   ------------------------   ------------------------
```

---

## 9. Reglas del Esquema

1. **Todo FS debe seguir estos layouts** al crear paginas nuevas.
2. **No se puede cambiar la estructura** sin aprobacion de PM.
3. **QA valida** que las paginas coincidan con estos esquemas.
4. **Nuevas paginas** deben agregar su esquema aqui antes de implementarlas.
5. **Cambios responsive** deben reflejarse en la seccion 7.
6. **Nuevos temas** deben agregar sus variables en la seccion 8.
7. **Nuevos componentes** deben agregar su esquema en la seccion 6.
