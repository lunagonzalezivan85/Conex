# CONEX · mokup-app

Mockups HTML estáticos del diseño de la app — **sin servicios ni datos reales**.
Se diseña aquí primero y luego se implementa en `app-conex` (Ionic React).

## Ver

Abrir en el navegador (XAMPP ya sirve el repo):

```
http://localhost/CONEX/mokup-app/
```

- **Marco de teléfono**: Samsung A54 (412×915 CSS px), se autoescala a la ventana.
- **Selector de pantallas**: barra superior del visor.

## Estructura

```
mokup-app/
├── index.html          Visor: marco A54 + selector de pantallas
├── css/mockup.css      Tokens CONEX + estilos compartidos (replica variables.css/app.css)
└── screens/
    ├── inicio.html     Home: hero slider, búsqueda flotante, stats, categorías, toggle, cards
    ├── categorias.html Grid completo de categorías (Vista "Ver todas")
    ├── servicios.html  Directorio de prestadores
    └── buscar.html     Búsqueda con filtros/chips
```

## Agregar una pantalla

1. Crear `screens/nombre.html` (copiar estructura de otra: `.status-space` + `.appbar` + `.content` + `.tabbar`).
2. Registrarla en el array `SCREENS` de `index.html`.

## Notas

- Iconos: SVG inline (Feather-like), sin dependencias externas.
- Colores: `--cx-navy #1B2A4A`, `--cx-teal #0FC6C2`, `--cx-orange #F97316`.
- El scroll interno simula el contenido de la app; el marco queda fijo.
