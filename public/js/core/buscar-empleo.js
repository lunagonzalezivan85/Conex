// Buscador de empleos - modal de filtros, dropdown orden, mapa geo
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('filterForm');
    var modal = document.getElementById('filterModal');
    var btnFiltros = document.getElementById('btnFiltros');
    var btnCerrarModal = document.getElementById('btnCerrarModal');

    // --- Dropdown de orden ---
    var btnSort = document.getElementById('btnSort');
    var sortMenu = document.getElementById('sortMenu');
    var ordenInput = document.getElementById('ordenInput');

    if (btnSort && sortMenu) {
        btnSort.addEventListener('click', function(e) {
            e.stopPropagation();
            sortMenu.classList.toggle('open');
        });
        sortMenu.querySelectorAll('.be-sort-item').forEach(function(item) {
            item.addEventListener('click', function() {
                ordenInput.value = this.dataset.orden;
                form.submit();
            });
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.be-sort')) sortMenu.classList.remove('open');
        });
    }

    // --- Modal de filtros ---
    function openModal() {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        // Inicializar mapa en primer open (lazy) y recalcular tamano
        if (!mapInited) {
            initMap();
            mapInited = true;
        } else if (map) {
            setTimeout(function() { map.invalidateSize(); }, 120);
        }
    }
    function closeModal() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (btnFiltros) btnFiltros.addEventListener('click', openModal);
    if (btnCerrarModal) btnCerrarModal.addEventListener('click', closeModal);
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('open')) closeModal();
    });

    // --- Mapa de geolocalizacion dentro del modal ---
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

    var map = null, marker = null, circle = null, mapInited = false;

    function getRadio() {
        return parseInt(radioInput.value, 10) * 1000;
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
        if (btnLimpiar) btnLimpiar.style.display = '';

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
        if (btnLimpiar) btnLimpiar.style.display = 'none';
        if (marker) { map.removeLayer(marker); marker = null; }
        if (circle) { map.removeLayer(circle); circle = null; }
    }

    function initMap() {
        function onReady() {
            map = L.map('geoMapa', { zoomControl: true, scrollWheelZoom: false }).setView([12.8654, -85.2072], 7);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 18
            }).addTo(map);
            map.on('click', function() { map.scrollWheelZoom.enable(); });
            map.on('mouseout', function() { map.scrollWheelZoom.disable(); });

            map.on('click', function(e) {
                setLocation(e.latlng.lat, e.latlng.lng);
            });

            // Restaurar ubicacion previa
            var prevLat = parseFloat(latInput.value);
            var prevLng = parseFloat(lngInput.value);
            if (!isNaN(prevLat) && !isNaN(prevLng)) {
                setLocation(prevLat, prevLng);
            }

            setTimeout(function() { map.invalidateSize(); }, 150);
        }

        if (typeof L !== 'undefined') {
            onReady();
        } else {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);
            var script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = onReady;
            document.head.appendChild(script);
        }
    }

    radioInput.addEventListener('input', updateRadioLabel);

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

    if (btnLimpiar) btnLimpiar.addEventListener('click', clearLocation);
});
