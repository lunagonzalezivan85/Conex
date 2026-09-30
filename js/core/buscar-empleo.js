// Buscador de empleos - filtro de geolocalizacion con mapa y slider de radio
document.addEventListener('DOMContentLoaded', function() {
    var mapEl = document.getElementById('geoMapa');
    if (!mapEl) return;

    var latInput = document.getElementById('geoLat');
    var lngInput = document.getElementById('geoLng');
    var radioInput = document.getElementById('geoRadio');
    var radioHidden = document.getElementById('geoRadioHidden');
    var radioValue = document.getElementById('geoRadioValue');
    var btnMiUbicacion = document.getElementById('btnMiUbicacion');
    var btnLimpiar = document.getElementById('btnGeoLimpiar');
    var geoLabel = document.getElementById('geoLabel');

    var marker = null;
    var circle = null;

    function getRadio() {
        return parseInt(radioInput.value, 10) * 1000; // km -> metros
    }

    function updateRadioLabel() {
        radioValue.textContent = radioInput.value + ' km';
        radioHidden.value = radioInput.value;
        if (circle) circle.setRadius(getRadio());
    }

    function setLocation(lat, lng) {
        latInput.value = lat;
        lngInput.value = lng;
        if (geoLabel) geoLabel.textContent = 'Ubicacion seleccionada';

        if (marker) map.removeLayer(marker);
        if (circle) map.removeLayer(circle);

        marker = L.marker([lat, lng]).addTo(map);
        circle = L.circle([lat, lng], {
            radius: getRadio(),
            color: '#4361EE',
            fillColor: '#4361EE',
            fillOpacity: 0.12,
            weight: 2
        }).addTo(map);

        map.setView([lat, lng], 11);
    }

    function clearLocation() {
        latInput.value = '';
        lngInput.value = '';
        if (geoLabel) geoLabel.textContent = 'o haz clic en el mapa';
        if (marker) { map.removeLayer(marker); marker = null; }
        if (circle) { map.removeLayer(circle); circle = null; }
    }

    // Cargar Leaflet
    function init() {
        map = L.map('geoMapa', { zoomControl: true, scrollWheelZoom: false }).setView([12.8654, -85.2072], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 18
        }).addTo(map);
        map.on('click', function() { map.scrollWheelZoom.enable(); });
        map.on('mouseout', function() { map.scrollWheelZoom.disable(); });

        // Click en el mapa
        map.on('click', function(e) {
            setLocation(e.latlng.lat, e.latlng.lng);
        });

        // Restaurar ubicacion previa
        var prevLat = parseFloat(latInput.value);
        var prevLng = parseFloat(lngInput.value);
        if (!isNaN(prevLat) && !isNaN(prevLng)) {
            setLocation(prevLat, prevLng);
        }

        // Slider
        radioInput.addEventListener('input', updateRadioLabel);

        // Mi ubicacion
        if (btnMiUbicacion) {
            btnMiUbicacion.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    geoLabel.textContent = 'Geolocalizacion no soportada';
                    return;
                }
                geoLabel.textContent = 'Detectando...';
                btnMiUbicacion.disabled = true;
                navigator.geolocation.getCurrentPosition(function(pos) {
                    btnMiUbicacion.disabled = false;
                    setLocation(pos.coords.latitude, pos.coords.longitude);
                }, function() {
                    btnMiUbicacion.disabled = false;
                    geoLabel.textContent = 'No se pudo detectar. Haz clic en el mapa.';
                }, { timeout: 10000, enableHighAccuracy: true });
            });
        }

        // Limpiar
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', clearLocation);
        }
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
