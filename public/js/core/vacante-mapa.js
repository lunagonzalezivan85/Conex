// Mapa de solo lectura para detalle de vacante — MapLibre + OpenFreeMap Liberty
document.addEventListener('DOMContentLoaded', function() {
    var mapEl = document.getElementById('vacanteMapa');
    if (!mapEl) return;

    var lat = parseFloat(mapEl.dataset.lat);
    var lng = parseFloat(mapEl.dataset.lng);
    if (isNaN(lat) || isNaN(lng)) {
        mapEl.parentElement.style.display = 'none';
        return;
    }

    var titulo = mapEl.dataset.titulo || 'Ubicacion';

    function init() {
        var map = new maplibregl.Map({
            container: 'vacanteMapa',
            style: 'https://tiles.openfreemap.org/styles/liberty',
            center: [lng, lat],
            zoom: 14,
            attributionControl: { compact: true },
            cooperativeGestures: true // no secuestrar el scroll de la pagina
        });
        map.addControl(new maplibregl.NavigationControl(), 'top-right');

        // Pin navy/naranja (mismo estilo que el mapa de busqueda)
        var el = document.createElement('span');
        el.className = 'cx-map-pin';
        el.title = titulo;

        new maplibregl.Marker({ element: el })
            .setLngLat([lng, lat])
            .setPopup(
                new maplibregl.Popup({ offset: 18 })
                    .setHTML('<div class="cx-pop"><strong>' + titulo + '</strong></div>')
            )
            .addTo(map)
            .togglePopup();
    }

    if (typeof maplibregl !== 'undefined') {
        init();
    } else {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css';
        document.head.appendChild(link);
        var script = document.createElement('script');
        script.src = 'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js';
        script.onload = init;
        document.head.appendChild(script);
    }
});
