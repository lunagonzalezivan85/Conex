// Mapa de solo lectura para detalle de vacante
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

    // Cargar Leaflet si no esta presente
    function init() {
        var map = L.map('vacanteMapa', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView([lat, lng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 18
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup(titulo)
            .openPopup();

        // Activar scroll zoom solo al hacer clic (mejor UX en scroll de pagina)
        map.on('click', function() { map.scrollWheelZoom.enable(); });
        map.on('mouseout', function() { map.scrollWheelZoom.disable(); });
    }

    if (typeof L !== 'undefined') {
        init();
    } else {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
        var script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.onload = init;
        document.head.appendChild(script);
    }
});
