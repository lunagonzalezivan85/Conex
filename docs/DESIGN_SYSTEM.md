# Design System

## OpenToWork - Sistema de Diseño

**Versión:** 1.0  
**Fecha:** Agosto 2026  

---

## 1. Filosofía de Diseño

OpenToWork sigue un estilo inspirado en **Samsung One UI** combinado con **Bento Grids** para tarjetas minimalistas. El diseño prioriza:

- **Simplicidad:** Interfaces limpias sin elementos innecesarios.
- **Espaciado generoso:** Mucho espacio en blanco para evitar saturación visual.
- **Jerarquía visual clara:** Elementos importantes destacan por tamaño y color.
- **Bordes suaves:** Esquinas redondeadas amplias (One UI style).
- **Sombras sutiles:** Elevación ligera para dar profundidad sin sobrecargar.
- **Bento Grid:** Layout modular con tarjetas de diferentes tamaños que encajan como un puzzle.
- **Theming:** CSS y JavaScript no se queman en las vistas. Se organizan en temas independientes que el sistema puede intercambiar dinámicamente manteniendo la misma estructura.
- **i18n:** Los textos no se queman en las vistas. Se organizan en archivos JSON por idioma (`config/language/es/`, `config/language/en/`). El sistema puede cambiar de idioma dinámicamente. El idioma del usuario se guarda en `SY_UserPreferences.Language`.

---

## 2. Paleta de Colores

### 2.1 Colores Primarios

| Nombre | Hex | RGB | Uso |
|--------|-----|-----|-----|
| **Azul Marino Principal** | `#1B263B` | `27, 38, 59` | Fondo de headers, barra de navegación, texto primario |
| **Azul Marino Medio** | `#2C3E5C` | `44, 62, 92` | Botones primarios, elementos activos |
| **Azul Marino Claro** | `#415A77` | `65, 90, 119` | Hover states, elementos secundarios |
| **Azul Acento** | `#778DA9` | `119, 141, 169` | Links, iconos, bordes activos |

### 2.2 Colores Neutros

| Nombre | Hex | RGB | Uso |
|--------|-----|-----|-----|
| **Blanco** | `#FFFFFF` | `255, 255, 255` | Fondo principal, tarjetas |
| **Grisáceo Claro** | `#F5F5F7` | `245, 245, 247` | Fondo de secciones, inputs |
| **Grisáceo Medio** | `#E0E0E5` | `224, 224, 229` | Bordes, separadores |
| **Grisáceo Oscuro** | `#9BA3B0` | `155, 163, 176` | Texto secundario, placeholders |
| **Grisáceo Texto** | `#5C677D` | `92, 103, 125` | Texto terciario, captions |

### 2.3 Colores Funcionales

| Nombre | Hex | Uso |
|--------|-----|-----|
| **Verde Éxito** | `#2E7D5B` | Confirmaciones, estados activos |
| **Rojo Error** | `#C5365A` | Errores, eliminación, estados críticos |
| **Ámbar Advertencia** | `#D4922B` | Advertencias, estados pendientes |
| **Azul Información** | `#415A77` | Notificaciones informativas |

### 2.4 Gradientes

```css
/* Gradiente principal (headers, hero sections) */
--gradient-primary: linear-gradient(135deg, #1B263B 0%, #2C3E5C 100%);

/* Gradiente sutil (tarjetas destacadas) */
--gradient-card: linear-gradient(180deg, #FFFFFF 0%, #F5F5F7 100%);

/* Gradiente de acento (botones) */
--gradient-button: linear-gradient(135deg, #2C3E5C 0%, #415A77 100%);
```

---

## 3. Tipografía

### 3.1 Familia Tipográfica

```css
--font-primary: 'Inter', 'SF Pro Display', 'Segoe UI', system-ui, sans-serif;
--font-mono: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
```

> **Nota:** Inter es la fuente principal por su similitud con la tipografía de One UI y excelente legibilidad.

### 3.2 Escala Tipográfica

| Nivel | Tamaño | Peso | Line Height | Uso |
|-------|--------|------|-------------|-----|
| **Display** | 2.5rem (40px) | 700 | 1.2 | Títulos de página principales |
| **H1** | 2rem (32px) | 700 | 1.3 | Headers de sección |
| **H2** | 1.5rem (24px) | 600 | 1.4 | Sub-headers |
| **H3** | 1.25rem (20px) | 600 | 1.4 | Títulos de tarjeta |
| **Body Large** | 1.125rem (18px) | 400 | 1.6 | Texto destacado |
| **Body** | 1rem (16px) | 400 | 1.6 | Texto normal |
| **Body Small** | 0.875rem (14px) | 400 | 1.5 | Texto secundario |
| **Caption** | 0.75rem (12px) | 500 | 1.4 | Labels, captions, badges |

---

## 4. Espaciado

Sistema de espaciado basado en 4px (One UI usa espaciado generoso):

| Token | Valor | Uso |
|-------|-------|-----|
| `--space-xs` | 4px | Espaciado mínimo entre elementos |
| `--space-sm` | 8px | Espaciado entre elementos relacionados |
| `--space-md` | 16px | Espaciado entre grupos de elementos |
| `--space-lg` | 24px | Padding interno de tarjetas |
| `--space-xl` | 32px | Espaciado entre secciones |
| `--space-2xl` | 48px | Espaciado entre bloques grandes |
| `--space-3xl` | 64px | Padding de página, hero sections |

---

## 5. Bordes y Esquinas

### 5.1 Border Radius (One UI Style - Esquinas amplias)

| Token | Valor | Uso |
|-------|-------|-----|
| `--radius-sm` | 8px | Inputs, badges, chips |
| `--radius-md` | 12px | Botones, elementos pequeños |
| `--radius-lg` | 16px | Tarjetas pequeñas, modales |
| `--radius-xl` | 24px | Tarjetas principales (Bento) |
| `--radius-2xl` | 32px | Contenedores grandes, hero sections |
| `--radius-full` | 9999px | Avatar, botones circulares, pills |

### 5.2 Bordes

```css
--border-color: #E0E0E5;
--border-color-hover: #9BA3B0;
--border-color-active: #415A77;
--border-width: 1px;
```

---

## 6. Sombras (Sutiles)

Sombras suaves y sutiles que dan profundidad sin sobrecargar visualmente:

```css
/* Sombra mínima - elementos planos con ligera elevación */
--shadow-xs: 0 1px 2px rgba(27, 38, 59, 0.04);

/* Sombra sutil - tarjetas en reposo */
--shadow-sm: 0 2px 8px rgba(27, 38, 59, 0.06);

/* Sombra media - tarjetas en hover */
--shadow-md: 0 4px 16px rgba(27, 38, 59, 0.08);

/* Sombra grande - modales, dropdowns */
--shadow-lg: 0 8px 32px rgba(27, 38, 59, 0.10);

/* Sombra máxima - overlays */
--shadow-xl: 0 16px 48px rgba(27, 38, 59, 0.12);
```

> **Importante:** Las sombras usan el color azul marino (`rgba(27, 38, 59, ...)`) como base, no negro puro, para mantener coherencia con la paleta.

---

## 7. Bento Grid Layout

### 7.1 Concepto

El Bento Grid es un sistema de layout modular donde las tarjetas de diferentes tamaños se organizan en una cuadrícula flexible, similar a las cajas de un bento japonés. Cada tarjeta es independiente pero encaja armónicamente con las demás.

### 7.2 Estructura del Grid

```css
--bento-gap: 16px;
--bento-columns: 4; /* Desktop */
--bento-columns-tablet: 2; /* Tablet */
--bento-columns-mobile: 1; /* Mobile */

/* Tamaños de tarjetas Bento */
--bento-size-sm: span 1;   /* 1 columna */
--bento-size-md: span 2;   /* 2 columnas */
--bento-size-lg: span 3;   /* 3 columnas */
--bento-size-xl: span 4;   /* 4 columnas (full width) */
```

### 7.3 Ejemplo de Layout Bento - Dashboard

```
Desktop (4 columnas):
┌──────────┬──────────┬──────────┬──────────┐
│          │                     │          │
│  Avatar  │    Bienvenida       │  Stats   │
│  (1x1)   │    (2x1)            │  (1x1)   │
│          │                     │          │
├──────────┴──────────┬──────────┴──────────┤
│                     │                     │
│   Vacantes Activas  │   Solicitudes       │
│   (2x1)             │   Recientes (2x1)   │
│                     │                     │
├──────────┬──────────┴──────────┬──────────┤
│          │                     │          │
│ Perfil   │   Gráfico de        │ Acciones │
│ (1x1)    │   Actividad (2x1)   │ Rápidas  │
│          │                     │ (1x1)    │
└──────────┴─────────────────────┴──────────┘
```

### 7.4 Tarjeta Bento (Componente Base)

```
┌─────────────────────────────────┐
│  ┌──────┐                       │  ← Border radius: 24px
│  │ Icon │  Título de Tarjeta    │  ← Padding: 24px
│  └──────┘                       │  ← Shadow: sm (hover: md)
│                                 │
│  Contenido de la tarjeta...     │  ← Font: Body (16px)
│  Texto descriptivo aquí.        │
│                                 │
│  ┌─────────────────────────┐    │
│  │  Acción / Botón         │    │  ← Border radius: 12px
│  └─────────────────────────┘    │
└─────────────────────────────────┘
```

### 7.5 Reglas del Bento Grid

- Cada tarjeta tiene `border-radius: 24px` y `--shadow-sm`.
- En hover, la tarjeta eleva a `--shadow-md` con `transform: translateY(-2px)`.
- El gap entre tarjetas es de `16px` (`--bento-gap`).
- Las tarjetas tienen fondo blanco (`#FFFFFF`) sobre fondo grisáceo (`#F5F5F7`).
- En mobile, todas las tarjetas ocupan 1 columna (full width).
- No más de 6 tarjetas por vista para evitar saturación.

---

## 8. Componentes UI

### 8.1 Botones

| Variante | Fondo | Texto | Border Radius | Uso |
|----------|-------|-------|---------------|-----|
| **Primario** | `--gradient-button` | Blanco | 12px | Acción principal |
| **Secundario** | Transparente | `#1B263B` | 12px | Acción alternativa |
| **Ghost** | Transparente | `#415A77` | 12px | Acción terciaria |
| **Peligro** | `#C5365A` | Blanco | 12px | Eliminar |
| **Icon** | Transparente | `#415A77` | 9999px | Solo icono |

**Estados:**
- Hover: oscurecer fondo 10%, `--shadow-sm`
- Active: escalar 0.98, sin sombra
- Disabled: opacidad 0.4, cursor not-allowed
- Focus: outline 2px `#415A77` con offset 2px

### 8.2 Inputs

```
┌─────────────────────────────────────┐
│  Label                              │  ← Caption (12px, grisáceo)
│  ┌─────────────────────────────┐    │
│  │ Placeholder...              │    │  ← Border radius: 8px
│  └─────────────────────────────┘    │  ← Border: 1px #E0E0E5
│                                     │  ← Padding: 16px
└─────────────────────────────────────┘
```

- Focus: border `#415A77`, `--shadow-xs`
- Error: border `#C5365A`, texto de error en `#C5365A`
- Disabled: fondo `#F5F5F7`, texto `#9BA3B0`

### 8.3 Tarjetas (Bento Cards)

```
┌─────────────────────────────────┐
│  ┌──────┐                       │
│  │ Icon │  Título               │
│  └──────┘                       │
│                                 │
│  Contenido...                   │
│                                 │
│  ┌──────────────────────────┐   │
│  │  Botón                   │   │
│  └──────────────────────────┘   │
└─────────────────────────────────┘
```

- Fondo: `#FFFFFF`
- Border radius: 24px
- Padding: 24px
- Shadow: `--shadow-sm` (reposo), `--shadow-md` (hover)
- Border: 1px solid `#E0E0E5`

### 8.4 Navegación (One UI Style)

- Barra de navegación con fondo `#1B263B` (azul marino).
- Items con texto blanco, icono blanco.
- Item activo: fondo `#FFFFFF` con opacidad 0.1, border-radius 16px.
- En mobile: barra inferior fija (bottom navigation) estilo One UI.

### 8.5 Modales

```
┌─────────────────────────────────────┐
│                                     │  ← Border radius: 32px
│         Título del Modal            │  ← Padding: 32px
│                                     │  ← Shadow: --shadow-lg
│         Contenido del modal...      │  ← Max width: 500px
│                                     │
│    ┌──────────┐  ┌──────────┐      │
│    │ Cancelar │  │ Aceptar  │      │
│    └──────────┘  └──────────┘      │
└─────────────────────────────────────┘
```

### 8.6 Badges y Chips

- Border radius: 9999px (pill)
- Padding: 4px 12px
- Font: Caption (12px, 500)
- Variantes: sólido (fondo color) u outline (border color)

### 8.7 Tablas

- Sin bordes verticales.
- Header con fondo `#F5F5F7`, texto `#1B263B` peso 600.
- Filas con border-bottom 1px `#E0E0E5`.
- Hover en fila: fondo `#F5F5F7`.
- Border radius de la tabla: 16px (overflow hidden).

---

## 9. Responsive Design

### 9.1 Breakpoints

| Breakpoint | Min Width | Columnas Bento | Layout |
|------------|-----------|----------------|--------|
| **Mobile** | 0px | 1 | Stack vertical, bottom nav |
| **Tablet** | 768px | 2 | Bento 2 columnas |
| **Desktop** | 1024px | 3 | Bento 3 columnas |
| **Wide** | 1440px | 4 | Bento 4 columnas |

### 9.2 Adaptaciones Mobile (One UI)

- **Bottom Navigation:** Barra inferior fija con iconos (estilo One UI).
- **Tarjetas full width:** En mobile, todas las tarjetas ocupan 100%.
- **FAB (Floating Action Button):** Botón flotante para acción principal.
- **Header simplificado:** Solo logo + avatar, sin menú completo.
- **Espaciado reducido:** `--space-lg` (24px) en lugar de `--space-xl` (32px).

---

## 10. Animaciones y Transiciones

```css
/* Transición base para todos los elementos interactivos */
--transition-fast: 150ms ease;
--transition-base: 250ms ease;
--transition-slow: 400ms ease;

/* Hover de tarjetas Bento */
.bento-card {
    transition: transform var(--transition-base),
                box-shadow var(--transition-base);
}
.bento-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Aparición de tarjetas (stagger) */
@keyframes bento-appear {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
```

- Las animaciones deben ser **sutiles** y rápidas (150-400ms).
- Usar `ease` para movimientos naturales.
- Evitar animaciones bruscas o excesivas.
- Stagger en aparición de tarjetas Bento (delay incremental de 50ms por tarjeta).

---

## 11. Sistema de Temas (Theming)

### 11.1 Principios

- **CSS y JavaScript no se queman en las vistas** (no inline styles, no inline scripts).
- Los estilos se organizan en **carpetas de tema** independientes.
- El sistema puede **cambiar de tema dinámicamente** sin modificar la estructura de los componentes.
- Todos los componentes Blazor usan **variables CSS** (custom properties), nunca valores hardcoded.
- Al cambiar de tema, solo se cambia el archivo CSS del tema activo; la estructura HTML y los componentes permanecen idénticos.
- JavaScript se organiza en archivos separados por funcionalidad, nunca embebido en `.razor`.

### 11.2 Estructura de Carpetas de Temas

```
wwwroot/
├── themes/
│   ├── navy/                         # Tema por defecto (Azul Marino)
│   │   ├── theme.css                # Variables CSS del tema navy
│   │   └── theme.js                 # JS específico del tema (opcional)
│   ├── dark/                        # Tema oscuro
│   │   ├── theme.css
│   │   └── theme.js
│   ├── light/                       # Tema claro alternativo
│   │   ├── theme.css
│   │   └── theme.js
│   └── corporate/                   # Tema corporativo (futuro)
│       ├── theme.css
│       └── theme.js
├── css/
│   ├── base.css                    # Reset, tipografía base, layout (independiente del tema)
│   ├── components.css              # Estilos de componentes (usa variables CSS, no colores hardcoded)
│   ├── bento-grid.css              # Layout del Bento Grid (estructura, no colores)
│   └── responsive.css              # Breakpoints y adaptaciones responsive
├── config/
│   └── language/
│       ├── es/                         # Español (por defecto)
│       │   ├── common.json             # Textos compartidos
│       │   ├── auth.json               # Autenticación
│       │   ├── wizard.json             # Wizard de registro
│       │   ├── dashboard.json          # Dashboard
│       │   ├── vacancies.json          # Vacantes
│       │   ├── profile.json            # Perfil
│       │   ├── validation.json         # Validación
│       │   └── errors.json             # Errores
│       └── en/                         # English
│           ├── common.json
│           ├── auth.json
│           ├── wizard.json
│           ├── dashboard.json
│           ├── vacancies.json
│           ├── profile.json
│           ├── validation.json
│           └── errors.json
├── js/
│   ├── theme-switcher.js           # Lógica para cambiar temas dinámicamente
│   ├── language-switcher.js        # Lógica para cambiar idioma dinámicamente
│   ├── bento-animations.js         # Animaciones de tarjetas Bento
│   ├── device-fingerprint.js       # Fingerprint del navegador
│   └── recaptcha.js                # Integración Google reCAPTCHA
└── index.html                      # Carga base.css + tema activo + idioma dinámicamente
```

### 11.3 Arquitectura del Theming

```
┌──────────────────────────────────────────────────────────┐
│                    COMPONENTES BLAZOR (.razor)            │
│                                                          │
│  Usan SOLO clases CSS y variables CSS.                   │
│  NUNCA inline styles. NUNCA colores hardcoded.           │
│  NUNCA inline scripts.                                   │
│                                                          │
│  Ej: <div class="bento-card">...</div>                  │
│      <button class="btn btn-primary">...</button>       │
└────────────────────────┬─────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────┐
│                    CSS ESTRUCTURAL (css/)                 │
│                                                          │
│  base.css        → Reset, tipografía, layout base         │
│  components.css  → .bento-card, .btn, .input (usa vars)   │
│  bento-grid.css  → Grid layout (columnas, gaps)           │
│  responsive.css  → Media queries                          │
│                                                          │
│  Estos archivos NUNCA cambian. Usan variables CSS.        │
└────────────────────────┬─────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────┐
│                    TEMAS (themes/)                        │
│                                                          │
│  theme.css define SOLO los valores de las variables CSS:  │
│                                                          │
│  --color-navy-primary: #1B263B;  (tema navy)             │
│  --color-navy-primary: #0F172A;  (tema dark)             │
│  --color-navy-primary: #2563EB;  (tema corporate)        │
│                                                          │
│  Al cambiar tema, solo se reemplaza este archivo.        │
│  La estructura y componentes permanecen idénticos.       │
└──────────────────────────────────────────────────────────┘
```

### 11.4 Tema por Defecto: Navy (Azul Marino)

Archivo: `wwwroot/themes/navy/theme.css`

```css
/* Tema: Navy (Azul Marino) - Por defecto */
:root {
    /* Colores primarios */
    --color-navy-primary: #1B263B;
    --color-navy-medium: #2C3E5C;
    --color-navy-light: #415A77;
    --color-navy-accent: #778DA9;

    /* Colores neutros */
    --color-white: #FFFFFF;
    --color-gray-light: #F5F5F7;
    --color-gray-medium: #E0E0E5;
    --color-gray-dark: #9BA3B0;
    --color-gray-text: #5C677D;

    /* Colores funcionales */
    --color-success: #2E7D5B;
    --color-error: #C5365A;
    --color-warning: #D4922B;
    --color-info: #415A77;

    /* Tipografía */
    --font-primary: 'Inter', 'SF Pro Display', 'Segoe UI', system-ui, sans-serif;
    --font-mono: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;

    /* Espaciado */
    --space-xs: 4px;
    --space-sm: 8px;
    --space-md: 16px;
    --space-lg: 24px;
    --space-xl: 32px;
    --space-2xl: 48px;
    --space-3xl: 64px;

    /* Border radius */
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-2xl: 32px;
    --radius-full: 9999px;

    /* Sombras */
    --shadow-xs: 0 1px 2px rgba(27, 38, 59, 0.04);
    --shadow-sm: 0 2px 8px rgba(27, 38, 59, 0.06);
    --shadow-md: 0 4px 16px rgba(27, 38, 59, 0.08);
    --shadow-lg: 0 8px 32px rgba(27, 38, 59, 0.10);
    --shadow-xl: 0 16px 48px rgba(27, 38, 59, 0.12);

    /* Bordes */
    --border-color: #E0E0E5;
    --border-color-hover: #9BA3B0;
    --border-color-active: #415A77;
    --border-width: 1px;

    /* Transiciones */
    --transition-fast: 150ms ease;
    --transition-base: 250ms ease;
    --transition-slow: 400ms ease;

    /* Gradientes */
    --gradient-primary: linear-gradient(135deg, #1B263B 0%, #2C3E5C 100%);
    --gradient-card: linear-gradient(180deg, #FFFFFF 0%, #F5F5F7 100%);
    --gradient-button: linear-gradient(135deg, #2C3E5C 0%, #415A77 100%);

    /* Bento Grid */
    --bento-gap: 16px;

    /* Metadata del tema */
    --theme-name: 'Navy';
    --theme-mode: 'light';
}
```

### 11.5 Tema Alternativo: Dark

Archivo: `wwwroot/themes/dark/theme.css`

```css
/* Tema: Dark - Modo oscuro */
:root {
    /* Colores primarios (invertidos para dark) */
    --color-navy-primary: #0F172A;
    --color-navy-medium: #1E293B;
    --color-navy-light: #334155;
    --color-navy-accent: #94A3B8;

    /* Colores neutros (invertidos) */
    --color-white: #1E293B;          /* Fondo de tarjetas en dark */
    --color-gray-light: #0F172A;     /* Fondo de secciones en dark */
    --color-gray-medium: #334155;
    --color-gray-dark: #64748B;
    --color-gray-text: #94A3B8;

    /* Colores funcionales (ajustados para dark) */
    --color-success: #34D399;
    --color-error: #F87171;
    --color-warning: #FBBF24;
    --color-info: #60A5FA;

    /* Tipografía (misma) */
    --font-primary: 'Inter', 'SF Pro Display', 'Segoe UI', system-ui, sans-serif;
    --font-mono: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;

    /* Espaciado (mismo) */
    --space-xs: 4px;
    --space-sm: 8px;
    --space-md: 16px;
    --space-lg: 24px;
    --space-xl: 32px;
    --space-2xl: 48px;
    --space-3xl: 64px;

    /* Border radius (mismo) */
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-2xl: 32px;
    --radius-full: 9999px;

    /* Sombras (ajustadas para dark) */
    --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.20);
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.25);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.30);
    --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.35);
    --shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.40);

    /* Bordes */
    --border-color: #334155;
    --border-color-hover: #64748B;
    --border-color-active: #94A3B8;
    --border-width: 1px;

    /* Transiciones (mismas) */
    --transition-fast: 150ms ease;
    --transition-base: 250ms ease;
    --transition-slow: 400ms ease;

    /* Gradientes (ajustados) */
    --gradient-primary: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
    --gradient-card: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
    --gradient-button: linear-gradient(135deg, #334155 0%, #475569 100%);

    /* Bento Grid (mismo) */
    --bento-gap: 16px;

    /* Metadata del tema */
    --theme-name: 'Dark';
    --theme-mode: 'dark';
}
```

### 11.6 CSS Estructural (independiente del tema)

Archivo: `wwwroot/css/components.css`

```css
/* Este archivo NUNCA cambia. Usa variables CSS del tema activo. */

.bento-card {
    background-color: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--space-lg);
    box-shadow: var(--shadow-sm);
    border: var(--border-width) solid var(--border-color);
    transition: transform var(--transition-base),
                box-shadow var(--transition-base);
}

.bento-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-primary {
    background: var(--gradient-button);
    color: var(--color-white);
    border-radius: var(--radius-md);
    padding: var(--space-md) var(--space-lg);
    border: none;
    transition: all var(--transition-fast);
}

.btn-primary:hover {
    box-shadow: var(--shadow-sm);
    filter: brightness(1.1);
}

.input-field {
    background-color: var(--color-gray-light);
    border: var(--border-width) solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: var(--space-md);
    color: var(--color-navy-primary);
    transition: border-color var(--transition-fast);
}

.input-field:focus {
    border-color: var(--border-color-active);
    box-shadow: var(--shadow-xs);
    outline: none;
}

/* NUNCA usar valores hardcoded como: */
/* background: #1B263B; ← INCORRECTO */
/* background: var(--color-navy-primary); ← CORRECTO */
```

### 11.7 Cambio de Tema Dinámico

Archivo: `wwwroot/js/theme-switcher.js`

```javascript
// Lista de temas disponibles
const AVAILABLE_THEMES = ['navy', 'dark', 'light', 'corporate'];
const DEFAULT_THEME = 'navy';
const THEME_STORAGE_KEY = 'opentowork-theme';

// Cambiar tema dinámicamente
function switchTheme(themeName) {
    if (!AVAILABLE_THEMES.includes(themeName)) {
        console.error(`Tema '${themeName}' no disponible`);
        return;
    }

    // Remover tema anterior
    const oldLink = document.getElementById('theme-stylesheet');
    if (oldLink) {
        oldLink.remove();
    }

    // Cargar nuevo tema
    const link = document.createElement('link');
    link.id = 'theme-stylesheet';
    link.rel = 'stylesheet';
    link.href = `/themes/${themeName}/theme.css`;
    document.head.appendChild(link);

    // Guardar preferencia
    localStorage.setItem(THEME_STORAGE_KEY, themeName);

    // Disparar evento para que Blazor reaccione si es necesario
    window.dispatchEvent(new CustomEvent('theme-changed', {
        detail: { theme: themeName }
    }));
}

// Cargar tema guardado al iniciar
function loadSavedTheme() {
    const savedTheme = localStorage.getItem(THEME_STORAGE_KEY) || DEFAULT_THEME;
    switchTheme(savedTheme);
}

// Exponer para Blazor via JSInterop
window.themeSwitcher = {
    switch: switchTheme,
    getCurrent: () => localStorage.getItem(THEME_STORAGE_KEY) || DEFAULT_THEME,
    getAvailable: () => AVAILABLE_THEMES
};

// Auto-cargar al iniciar
document.addEventListener('DOMContentLoaded', loadSavedTheme);
```

### 11.8 Integración con Blazor

#### index.html
```html
<!DOCTYPE html>
<html>
<head>
    <!-- CSS Estructural (nunca cambia) -->
    <link rel="stylesheet" href="css/base.css" />
    <link rel="stylesheet" href="css/components.css" />
    <link rel="stylesheet" href="css/bento-grid.css" />
    <link rel="stylesheet" href="css/responsive.css" />

    <!-- Tema activo (cargado dinámicamente por theme-switcher.js) -->
    <!-- Se inserta aquí dinámicamente -->
</head>
<body>
    <!-- App Blazor -->
    <div id="app">...</div>

    <!-- JavaScript (separado, no inline) -->
    <script src="js/theme-switcher.js"></script>
    <script src="js/bento-animations.js"></script>
    <script src="js/device-fingerprint.js"></script>
    <script src="js/recaptcha.js"></script>
</body>
</html>
```

#### Servicio Blazor para temas

`ThemeService.cs` en OpenToWork.WEB/Services:

```csharp
public class ThemeService
{
    private readonly IJSRuntime _jsRuntime;
    private string _currentTheme = "navy";

    public event Action? OnThemeChanged;

    public ThemeService(IJSRuntime jsRuntime)
    {
        _jsRuntime = jsRuntime;
    }

    public string CurrentTheme => _currentTheme;

    public async Task SwitchThemeAsync(string themeName)
    {
        _currentTheme = themeName;
        await _jsRuntime.InvokeVoidAsync("themeSwitcher.switch", themeName);
        OnThemeChanged?.Invoke();
    }

    public async Task<string> GetCurrentThemeAsync()
    {
        return await _jsRuntime.InvokeAsync<string>("themeSwitcher.getCurrent");
    }

    public async Task<List<string>> GetAvailableThemesAsync()
    {
        return (await _jsRuntime.InvokeAsync<string[]>("themeSwitcher.getAvailable")).ToList();
    }
}
```

#### Componente Blazor para selector de tema

`ThemeSwitcher.razor`:

```razor
@inject ThemeService ThemeService

<div class="theme-switcher">
    @foreach (var theme in _availableThemes)
    {
        <button class="theme-btn @(theme == ThemeService.CurrentTheme ? "active" : "")"
                @onclick="() => SwitchTheme(theme)">
            @theme
        </button>
    }
</div>

@code {
    private List<string> _availableThemes = new();

    protected override async Task OnInitializedAsync()
    {
        _availableThemes = await ThemeService.GetAvailableThemesAsync();
    }

    private async Task SwitchTheme(string theme)
    {
        await ThemeService.SwitchThemeAsync(theme);
    }
}
```

### 11.9 Reglas Obligatorias

| Regla | Descripción |
|-------|-------------|
| **No inline styles** | Prohibido `style="..."` en archivos `.razor` |
| **No inline scripts** | Prohibido `<script>...</script>` dentro de `.razor` |
| **No colores hardcoded** | Prohibido `#1B263B` en CSS estructural. Solo en `theme.css` |
| **Usar variables CSS** | Todo componente debe usar `var(--color-...)` |
| **Clases semánticas** | Usar clases como `.bento-card`, `.btn-primary`, no estilos inline |
| **JS separado** | Todo JavaScript en `wwwroot/js/`, cargado via `<script src="...">` |
| **Temas en carpeta** | Cada tema en `wwwroot/themes/{nombre}/theme.css` |
| **Misma estructura** | Al cambiar tema, el HTML no cambia, solo el archivo CSS |
| **Preferencia persistente** | El tema seleccionado se guarda en localStorage |

### 11.10 Endpoints API para Temas (Opcional)

Si se requiere que el tema se guarde en el backend por usuario:

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/users/theme` | Obtener tema preferido del usuario |
| PUT | `/api/users/theme` | Guardar tema preferido del usuario |

Tabla en BD: `UserPreferences`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| Id | GUID | PK |
| UserId | GUID | FK → Users |
| Theme | NVARCHAR(50) | Nombre del tema (navy, dark, etc.) |
| UpdatedAt | DATETIME2 | Fecha de actualización |

---

## 12. Aplicación por Pantalla

> **Nota:** Los mockups siguientes usan las variables CSS del tema activo. Al cambiar de tema, los colores cambian pero la estructura permanece idéntica.

### 12.1 Pantalla de Selección Inicial

```
┌─────────────────────────────────────────────────┐
│                                                 │  ← Fondo: --gradient-primary
│                                                 │
│              OpenToWork                         │  ← Display (40px, blanco)
│              Encuentra tu próximo paso          │  ← Body Large (18px, #778DA9)
│                                                 │
│  ┌──────────────────┐  ┌──────────────────┐   │  ← Bento cards (2x1 cada una)
│  │                  │  │                  │   │  ← Border radius: 24px
│  │   🔍             │  │   💼              │   │  ← Fondo: blanco
│  │                  │  │                  │   │  ← Shadow: --shadow-md
│  │  Buscar Empleo   │  │   Contratar      │   │
│  │                  │  │                  │   │
│  │  Encuentra       │  │  Publica         │   │
│  │  oportunidades   │  │  vacantes y      │   │
│  │  para ti         │  │  encuentra       │   │
│  │                  │  │  talento         │   │
│  └──────────────────┘  └──────────────────┘   │
│                                                 │
└─────────────────────────────────────────────────┘
```

### 12.2 Login

```
┌─────────────────────────────────────────┐
│                                         │  ← Fondo: --color-gray-light
│   ┌─────────────────────────────────┐   │
│   │                                 │   │  ← Card blanca, radius 32px
│   │        Iniciar Sesión           │   │  ← H1 (32px, navy primary)
│   │                                 │   │
│   │   ┌─────────────────────────┐   │   │
│   │   │  Email                   │   │   │  ← Input, radius 8px
│   │   └─────────────────────────┘   │   │
│   │                                 │   │
│   │   ┌─────────────────────────┐   │   │
│   │   │  Contraseña              │   │   │
│   │   └─────────────────────────┘   │   │
│   │                                 │   │
│   │   ☐ Mantener sesión activa      │   │  ← Checkbox One UI style
│   │                                 │   │
│   │   ┌─────────────────────────┐   │   │
│   │   │      Ingresar            │   │   │  ← Button primary, radius 12px
│   │   └─────────────────────────┘   │   │
│   │                                 │   │
│   │   ─── ─── ─── ─── ─── ─── ───  │   │  ← Separador
│   │                                 │   │
│   │   ┌─────────────────────────┐   │   │
│   │   │  G  Continuar con Google│   │   │  ← Button secondary
│   │   └─────────────────────────┘   │   │
│   │                                 │   │
│   └─────────────────────────────────┘   │
│                                         │
└─────────────────────────────────────────┘
```

### 12.3 Dashboard (Bento Grid)

```
┌─────────────────────────────────────────────────────┐
│  ┌──────┐  Hola, Juan                    ┌──────┐  │  ← Header navy
│  │ Avatar│  Candidato                     │ 🔔 ⚙ │  │
│  └──────┘                                └──────┘  │
├─────────────────────────────────────────────────────┤
│  ┌──────────┬──────────────────────┬──────────┐    │  ← Fondo grisáceo
│  │          │                      │          │    │
│  │  Perfil  │   Vacantes           │  Stats   │    │  ← Bento cards
│  │  80%     │   Recomendadas (5)   │  12      │    │  ← Radio 24px
│  │  (1x1)   │   (2x1)              │  (1x1)   │    │  ← Shadow sm
│  │          │                      │          │    │
│  ├──────────┼──────────────────────┼──────────┤    │
│  │          │                      │          │    │
│  │ Mis      │   Gráfico de         │  Acción  │    │
│  │ Solic.   │   Actividad          │  Rápida  │    │
│  │ (3)      │   (2x1)              │  (1x1)   │    │
│  │ (1x1)    │                      │          │    │
│  └──────────┴──────────────────────┴──────────┘    │
└─────────────────────────────────────────────────────┘
```
