// Buscador de empleos - modal filtros, orden, geo por GPS y vista mapa fullscreen
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

    // --- Scope empleos/servicios en el buscador del hero ---
    var heroScope = document.getElementById('heroScope');
    var heroForm = document.querySelector('.be-hero-search');
    var heroInput = document.getElementById('heroSearchInput');
    if (heroScope && heroForm) {
        var actionBase = heroForm.getAttribute('action');
        var urlEmpleos = actionBase.replace('servicios', 'buscar-empleo');
        var urlServicios = actionBase.replace('buscar-empleo', 'servicios');
        heroScope.addEventListener('change', function() {
            heroForm.setAttribute('action', this.value === 'servicios' ? urlServicios : urlEmpleos);
            if (heroInput) {
                heroInput.placeholder = this.value === 'servicios'
                    ? 'Electricista, plomero, carpintero...'
                    : 'Titulo, palabra clave, empresa...';
            }
        });
    }

    // --- Modal de filtros (vista listado) ---
    if (modal && btnFiltros) {
        function openModal() {
            // Sincronizar chips del hero hacia el modal
            var pairs = { heroCat: 'categoria', heroModalidad: 'modalidad', heroCiudad: 'ciudad' };
            for (var id in pairs) {
                var src = document.getElementById(id);
                var dst = form ? form.querySelector('[name="' + pairs[id] + '"]') : null;
                if (src && dst) dst.value = src.value;
            }
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }
        btnFiltros.addEventListener('click', openModal);
        if (btnCerrarModal) btnCerrarModal.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
        });
    }

    // --- Geo por GPS (modal, sin mapa) ---
    var latInput = document.getElementById('geoLat');
    var lngInput = document.getElementById('geoLng');
    var radioInput = document.getElementById('geoRadio');
    var radioHidden = document.getElementById('geoRadioHidden');
    var radioValue = document.getElementById('geoRadioValue');
    var btnMiUbicacion = document.getElementById('btnMiUbicacion');
    var btnLimpiar = document.getElementById('btnGeoLimpiar');
    var geoLabel = document.getElementById('geoLabel');

    if (radioInput && radioValue) {
        radioInput.addEventListener('input', function() {
            radioValue.textContent = radioInput.value + ' km';
            if (radioHidden) radioHidden.value = radioInput.value;
        });
    }
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
                latInput.value = pos.coords.latitude;
                lngInput.value = pos.coords.longitude;
                geoLabel.textContent = 'Ubicacion seleccionada';
                if (btnLimpiar) btnLimpiar.style.display = '';
            }, function() {
                btnMiUbicacion.disabled = false;
                geoLabel.textContent = 'No se pudo detectar la ubicacion';
            }, { timeout: 10000, enableHighAccuracy: true });
        });
    }
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            latInput.value = '';
            lngInput.value = '';
            geoLabel.textContent = 'Usa tu ubicacion GPS para filtrar por distancia';
            btnLimpiar.style.display = 'none';
        });
    }

    // --- Vista mapa fullscreen ---
    var mapEl = document.getElementById('mapaCompleto');
    if (mapEl) {
        var mapForm = document.getElementById('mapaFilterForm');
        var mapRadio = document.getElementById('mapRadio');
        var mapRadioValue = document.getElementById('mapRadioValue');
        var btnMapaUbicacion = document.getElementById('btnMapaUbicacion');
        var mapLat = document.getElementById('mapLat');
        var mapLng = document.getElementById('mapLng');

        if (mapRadio && mapRadioValue) {
            mapRadio.addEventListener('input', function() {
                mapRadioValue.textContent = mapRadio.value + ' km';
            });
        }
        if (btnMapaUbicacion) {
            btnMapaUbicacion.addEventListener('click', function() {
                if (!navigator.geolocation) return;
                btnMapaUbicacion.disabled = true;
                navigator.geolocation.getCurrentPosition(function(pos) {
                    mapLat.value = pos.coords.latitude;
                    mapLng.value = pos.coords.longitude;
                    mapForm.submit();
                }, function() {
                    btnMapaUbicacion.disabled = false;
                }, { timeout: 10000, enableHighAccuracy: true });
            });
        }

        function initMapaCompleto() {
            var data = window.cxVacantesMapa || [];
            var map = new maplibregl.Map({
                container: 'mapaCompleto',
                style: 'https://tiles.openfreemap.org/styles/liberty',
                center: [-85.2072, 12.8654],
                zoom: 6.2,
                attributionControl: { compact: true }
            });
            map.addControl(new maplibregl.NavigationControl(), 'top-right');

            // Capa de departamentos de Nicaragua + highlight del buscado
            map.on('load', function() {
                fetch(window.cxGeoJsonUrl).then(function(r) { return r.json(); }).then(function(geo) {
                    map.addSource('departamentos', { type: 'geojson', data: geo });

                    // Borde tenue en todos los departamentos (siempre visible)
                    map.addLayer({
                        id: 'deptos-line',
                        type: 'line',
                        source: 'departamentos',
                        paint: {
                            'line-color': '#123D73',
                            'line-width': 1.2,
                            'line-opacity': 0.55
                        }
                    });

                    // Relleno + borde resaltado del departamento buscado
                    var buscado = (window.cxCiudadFiltro || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                    if (buscado) {
                        var match = geo.features.find(function(f) {
                            var nombre = (f.properties.shapeName || '')
                                .replace(/\s*\([^)]*\)\s*/g, '').toLowerCase()
                                .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                            return nombre === buscado || nombre.indexOf(buscado) !== -1 || buscado.indexOf(nombre) !== -1;
                        });
                        if (match) {
                            map.addLayer({
                                id: 'depto-hl-fill',
                                type: 'fill',
                                source: 'departamentos',
                                filter: ['==', ['get', 'shapeName'], match.properties.shapeName],
                                paint: { 'fill-color': '#FF8400', 'fill-opacity': 0.08 }
                            });
                            map.addLayer({
                                id: 'depto-hl-line',
                                type: 'line',
                                source: 'departamentos',
                                filter: ['==', ['get', 'shapeName'], match.properties.shapeName],
                                paint: { 'line-color': '#FF8400', 'line-width': 2.5 }
                            });
                            // Encuadrar el departamento resaltado
                            var db = new maplibregl.LngLatBounds();
                            var coords = match.geometry.coordinates;
                            function walk(c) {
                                if (typeof c[0] === 'number') { db.extend(c); return; }
                                c.forEach(walk);
                            }
                            walk(coords);
                            map.fitBounds(db, { padding: 70, maxZoom: 9 });
                        }
                    }
                }).catch(function() {});
            });

            var bounds = new maplibregl.LngLatBounds();
            data.forEach(function(v) {
                var el = document.createElement('span');
                el.className = 'cx-map-pin';
                el.title = v.titulo;

                var html = '<div class="cx-pop">' +
                    '<strong>' + v.titulo + '</strong>' +
                    (v.empresa ? '<span>' + v.empresa + '</span>' : '') +
                    '<span>' + (v.ciudad || '') + (v.modalidad ? ' - ' + v.modalidad : '') + '</span>' +
                    '<a href="' + v.url + '" class="cx-pop-link">Ver vacante</a>' +
                    '</div>';

                new maplibregl.Marker({ element: el })
                    .setLngLat([v.lng, v.lat])
                    .setPopup(new maplibregl.Popup({ offset: 16 }).setHTML(html))
                    .addTo(map);
                bounds.extend([v.lng, v.lat]);
            });
            if (!bounds.isEmpty()) {
                map.fitBounds(bounds, { padding: 60, maxZoom: 12 });
            }
        }

        if (typeof maplibregl !== 'undefined') {
            initMapaCompleto();
        } else {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css';
            document.head.appendChild(link);
            var script = document.createElement('script');
            script.src = 'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js';
            script.onload = initMapaCompleto;
            document.head.appendChild(script);
        }
    }
});