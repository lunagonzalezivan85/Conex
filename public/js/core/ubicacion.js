// Datos de departamentos y municipios de El Salvador con coordenadas aproximadas
const SALVADOR_DEPARTMENTS = {
    'San Salvador': {
        lat: 13.6929, lng: -89.2182,
        cities: ['San Salvador Centro', 'Aguilares', 'Ciudad Delgado', 'Cuscatancingo', 'El Paisnal', 'Guazapa', 'Ilopango', 'Mejicanos', 'Nueva San Salvador', 'Panchimalco', 'Rosario de Mora', 'San Marcos', 'San Martin', 'Santiago Texacuangos', 'Tonacatepeque', 'Zacatecoluca']
    },
    'La Libertad': {
        lat: 13.6633, lng: -89.3581,
        cities: ['Santa Tecla', 'Antiguo Cuscatlan', 'Chiltiupan', 'Ciudad Arce', 'Colonia', 'Comasagua', 'Huizucar', 'Jayaque', 'Jicalapa', 'La Libertad', 'Nahuizalco', 'Nuevo Cuscatlan', 'San Juan Opico', 'Quezaltepeque', 'Sacacoyo', 'San Jose Villanueva', 'San Matias', 'San Pablo Tacachico', 'Tepecoyo', 'Zaragoza']
    },
    'Santa Ana': {
        lat: 13.9942, lng: -89.5598,
        cities: ['Santa Ana', 'Candelaria de la Frontera', 'Chalchuapa', 'Coatepeque', 'El Congo', 'El Porvenir', 'Masahuat', 'Metapan', 'San Antonio Pajonal', 'San Sebastian Salitrillo', 'Santa Rosa Guajoyo', 'Santiago de la Frontera', 'Texistepeque']
    },
    'Ahuachapan': {
        lat: 13.9200, lng: -89.8400,
        cities: ['Ahuachapan', 'Apaneca', 'Atiquizaya', 'Concepcion de Ataco', 'El Refugio', 'Guaymango', 'Jujutla', 'San Francisco Menendez', 'San Lorenzo', 'San Pedro Puxtla', 'Tacuba', 'Turin']
    },
    'Sonsonate': {
        lat: 13.7189, lng: -89.7247,
        cities: ['Sonsonate', 'Acajutla', 'Armenia', 'Caluco', 'Cuisnahuat', 'El Izalco', 'Juayua', 'Nahulingo', 'Nahuizalco', 'Salcoatitan', 'San Antonio del Monte', 'San Julian', 'Santa Catarina Masahuat', 'Santa Isabel Ishuatlan', 'Santo Domingo', 'Sonzacate']
    },
    'Chalatenango': {
        lat: 14.0333, lng: -88.9333,
        cities: ['Chalatenango', 'Agua Caliente', 'Arcatao', 'Azacualpa', 'Cancasque', 'Citala', 'Comunidad Concepcion Quezaltepeque', 'Dulce Nombre de Maria', 'El Carrizal', 'El Paraiso', 'La Laguna', 'La Palma', 'La Reina', 'Las Vueltas', 'Nombre de Jesus', 'Nueva Concepcion', 'Nueva Trinidad', 'Ojos de Agua', 'Potonico', 'San Antonio de la Cruz', 'San Antonio Los Ranchos', 'San Fernando', 'San Francisco Lempa', 'San Ignacio', 'San Isidro Labrador', 'San Jose Cancasque', 'San Jose Las Flores', 'San Luis del Carmen', 'San Miguel de Mercedes', 'Nombre de Jesus', 'San Rafael', 'Santa Rita', 'Tejutla', 'Trapiche', 'Valladolid']
    },
    'Cabañas': {
        lat: 13.9333, lng: -88.7167,
        cities: ['Sensuntepeque', 'Cinquera', 'Dolores', 'Guacotecti', 'Ilobasco', 'Jutiapa', 'San Isidro', 'Tejutepeque', 'Victoria']
    },
    'La Paz': {
        lat: 13.4833, lng: -89.0667,
        cities: ['Zacatecoluca', 'Cuyultitan', 'El Rosario', 'Jerusalen', 'Mercedes La Ceiba', 'Olocuilta', 'Paraiso de Osorio', 'San Antonio Masahuat', 'San Emigdio', 'San Francisco Chinameca', 'San Juan Nonualco', 'San Juan Talpa', 'San Juan Tepezontes', 'San Luis La Herradura', 'San Luis Talpa', 'San Miguel Tepezontes', 'San Pedro Masahuat', 'San Pedro Nonualco', 'San Rafael Tasajera', 'Santa Maria Ostuma', 'Santiago Nonualco', 'Tapalhuaca']
    },
    'San Vicente': {
        lat: 13.6333, lng: -88.8000,
        cities: ['San Vicente', 'Apastepeque', 'Guadalupe', 'San Cayetano Istepeque', 'San Esteban Catarina', 'San Ildefonso', 'San Lorenzo', 'San Sebastian', 'Santa Clara', 'Santo Domingo', 'Tecoluca', 'Tepetitan', 'Verapaz']
    },
    'Usulutan': {
        lat: 13.3400, lng: -88.4500,
        cities: ['Usulutan', 'Alegría', 'Berlín', 'California', 'Concepción Batres', 'El Triunfo', 'Eregualaca', 'Estancia de la Caña', 'Jiquilisco', 'Jucuapa', 'Jutiapa', 'Mercedes Umaña', 'Nueva Granada', 'Ozatlán', 'Puerto El Triunfo', 'San Agustín', 'San Buenaventura', 'San Dionisio', 'San Francisco Javier', 'Santa Elena', 'Santa María', 'Santiago de María', 'Tecapán']
    },
    'San Miguel': {
        lat: 13.4833, lng: -88.1833,
        cities: ['San Miguel', 'Carolina', 'Chapeltique', 'Chinameca', 'Chirilagua', 'Ciudad Barrios', 'Comacarán', 'El Tránsito', 'Lolotique', 'Moncagua', 'Nueva Guadalupe', 'Nuevo Edén de San Juan', 'Quelepa', 'San Antonio del Mosco', 'San Gerardo', 'San Jorge', 'San Luis de la Reinsta', 'San Rafael Oriente', 'Sesori', 'Uluazapa']
    },
    'Morazan': {
        lat: 13.7667, lng: -88.1167,
        cities: ['San Francisco Gotera', 'Arambala', 'Cacaopera', 'Chilanga', 'Corinto', 'Delicias de Concepción', 'El Rosario', 'Gualococti', 'Guatajiagua', 'Joateca', 'Jocoaitique', 'Jocoro', 'Lolotiquillo', 'Meanguera del Golfo', 'Osicala', 'Perquín', 'San Carlos', 'San Isidro', 'San Simón', 'Sensembra', 'Sociedad', 'Torola', 'Yamabal', 'Yoloaiquín']
    },
    'La Union': {
        lat: 13.3333, lng: -87.8500,
        cities: ['La Unión', 'Anamorós', 'Bolívar', 'Concepción de Oriente', 'Conchagua', 'El Carmen', 'El Sauce', 'Intipucá', 'Lislique', 'Meanguera del Golfo', 'Nueva Esparta', 'Pasaquina', 'Polorós', 'San Alejo', 'San José', 'Santa Rosa de Lima', 'Yayantique', 'Yucuaiquín']
    },
    'Cuscatlan': {
        lat: 13.8500, lng: -88.9667,
        cities: ['Cojutepeque', 'Candelaria', 'El Carmen', 'El Rosario', 'Monte San Juan', 'Oratorio de Concepción', 'San Bartolomé Perulapía', 'San Cristóbal', 'San José Guayabal', 'San Pedro Perulapán', 'San Rafael Cedros', 'San Ramón', 'Santa Cruz Analquito', 'Santa Cruz Michapa', 'Suchitoto', 'Tenancingo']
    }
};

// Cargar Leaflet dinamicamente si no esta presente
function loadLeaflet(callback) {
    if (typeof L !== 'undefined') {
        callback();
        return;
    }
    // CSS
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(link);
    // JS
    var script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    script.onload = callback;
    document.head.appendChild(script);
}

// Reverse geocoding con Nominatim
function nominatimReverse(lat, lng, callback) {
    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=10&addressdetails=1')
        .then(function(r) { return r.json(); })
        .then(function(data) { callback(data); })
        .catch(function() { callback(null); });
}

// Forward geocoding (buscar) con Nominatim
function nominatimSearch(query, callback) {
    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&limit=8&addressdetails=1')
        .then(function(r) { return r.json(); })
        .then(function(data) { callback(data); })
        .catch(function() { callback([]); });
}

// Inicializar selector de ubicacion con mapa
function initUbicacionSelector(containerId, latInputId, lngInputId, regionInputId, ciudadInputId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Crear estructura HTML
    container.innerHTML = `
        <div class="ubicacion-selector">
            <div class="ubicacion-search-wrap">
                <input type="text" class="form-control ubicacion-search" id="${containerId}-search" placeholder="Buscar ubicacion..." autocomplete="off">
                <div class="ubicacion-suggestions" id="${containerId}-suggestions" style="display:none;"></div>
            </div>
            <div class="ubicacion-map-wrap">
                <div class="ubicacion-map" id="${containerId}-map"></div>
            </div>
            <div class="ubicacion-fields">
                <div class="form-group" style="flex:1;min-width:180px;">
                    <label class="form-label">Region / Departamento</label>
                    <input type="text" class="form-control ubicacion-region" id="${containerId}-region" placeholder="Selecciona en el mapa o busca...">
                </div>
                <div class="form-group" style="flex:1;min-width:180px;">
                    <label class="form-label">Ciudad / Municipio</label>
                    <input type="text" class="form-control ubicacion-ciudad" id="${containerId}-ciudad" placeholder="Selecciona en el mapa o busca...">
                </div>
            </div>
        </div>
    `;

    const mapDiv = document.getElementById(`${containerId}-map`);
    const searchInput = document.getElementById(`${containerId}-search`);
    const suggestionsDiv = document.getElementById(`${containerId}-suggestions`);
    const regionInput = document.getElementById(`${containerId}-region`);
    const ciudadInput = document.getElementById(`${containerId}-ciudad`);

    // Los departamentos de El Salvador siguen disponibles como busqueda rapida en el dropdown

    // Inicializar mapa Leaflet
    loadLeaflet(function() {
        if (typeof L === 'undefined') return;

        const map = L.map(mapDiv, { zoomControl: true }).setView([13.7, -88.9], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 18
        }).addTo(map);

        let marker = null;

        function setMarker(lat, lng, label) {
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map);
            if (label) marker.bindPopup(label).openPopup();
            map.setView([lat, lng], 12);
        }

    function selectRegion(regionName) {
        const dept = SALVADOR_DEPARTMENTS[regionName];
        if (!dept) return;

        regionInput.value = regionName;
        setMarker(dept.lat, dept.lng, regionName);

        // Actualizar inputs hidden
        const latInput = document.getElementById(latInputId);
        const lngInput = document.getElementById(lngInputId);
        const regionHidden = document.getElementById(regionInputId);
        if (latInput) latInput.value = dept.lat;
        if (lngInput) lngInput.value = dept.lng;
        if (regionHidden) regionHidden.value = regionName;

        // Sincronizar campos externos si existen
        const extRegion = document.getElementById('ext_region');
        if (extRegion) extRegion.value = regionName;
    }

    function selectCity(regionName, cityName) {
        const dept = SALVADOR_DEPARTMENTS[regionName];
        if (!dept) return;
        setMarker(dept.lat, dept.lng, `${cityName}, ${regionName}`);

        const ciudadHidden = document.getElementById(ciudadInputId);
        if (ciudadHidden) ciudadHidden.value = cityName;

        const extCiudad = document.getElementById('ext_ciudad');
        if (extCiudad) extCiudad.value = cityName;
    }

    // Seleccionar ubicacion desde resultado de Nominatim
    function selectFromNominatim(result) {
        var lat = parseFloat(result.lat);
        var lng = parseFloat(result.lon);
        var addr = result.address || {};
        var regionName = addr.state || addr.region || addr.county || addr.country || 'Desconocido';
        var cityName = addr.city || addr.town || addr.village || addr.municipality || addr.county || '';

        setMarker(lat, lng, result.display_name);
        regionInput.value = regionName;
        ciudadInput.value = cityName;

        // Actualizar inputs hidden
        var latHidden = document.getElementById(latInputId);
        var lngHidden = document.getElementById(lngInputId);
        var regionHidden = document.getElementById(regionInputId);
        var ciudadHidden = document.getElementById(ciudadInputId);
        if (latHidden) latHidden.value = lat;
        if (lngHidden) lngHidden.value = lng;
        if (regionHidden) regionHidden.value = regionName;
        if (ciudadHidden) ciudadHidden.value = cityName;

        // Sincronizar campos externos
        var extRegion = document.getElementById('ext_region');
        var extCiudad = document.getElementById('ext_ciudad');
        if (extRegion) extRegion.value = regionName;
        if (extCiudad) extCiudad.value = cityName;

        searchInput.value = result.display_name.split(',')[0];
        suggestionsDiv.style.display = 'none';
    }

    // Evento: buscar ubicacion con Nominatim
    var searchTimer = null;
    searchInput.addEventListener('input', function() {
        var query = this.value.trim();
        clearTimeout(searchTimer);
        if (query.length < 2) {
            suggestionsDiv.style.display = 'none';
            return;
        }
        suggestionsDiv.innerHTML = '<div class="ubicacion-suggestion-item" style="color:#999;">Buscando...</div>';
        suggestionsDiv.style.display = 'block';

        searchTimer = setTimeout(function() {
            // Primero buscar en departamentos de El Salvador
            var localMatches = Object.keys(SALVADOR_DEPARTMENTS).filter(function(d) {
                return d.toLowerCase().includes(query.toLowerCase());
            });

            // Luego buscar con Nominatim
            nominatimSearch(query, function(results) {
                var html = '';
                if (localMatches.length > 0) {
                    html += localMatches.map(function(d) {
                        return '<div class="ubicacion-suggestion-item" data-region="' + d + '"><strong>' + d + '</strong> (El Salvador)</div>';
                    }).join('');
                }
                if (results && results.length > 0) {
                    html += results.map(function(r) {
                        return '<div class="ubicacion-suggestion-item" data-nominatim="' + encodeURIComponent(JSON.stringify(r)) + '">' + r.display_name + '</div>';
                    }).join('');
                }
                if (!html) {
                    html = '<div class="ubicacion-suggestion-item" style="color:#999;">Sin resultados</div>';
                }
                suggestionsDiv.innerHTML = html;
                suggestionsDiv.style.display = 'block';
            });
        }, 350);
    });

    // Evento: clic en sugerencia
    suggestionsDiv.addEventListener('click', function(e) {
        var item = e.target.closest('.ubicacion-suggestion-item');
        if (!item) return;

        if (item.dataset.region) {
            // Seleccion de El Salvador
            searchInput.value = item.dataset.region;
            suggestionsDiv.style.display = 'none';
            selectRegion(item.dataset.region);
        } else if (item.dataset.nominatim) {
            // Seleccion de Nominatim
            var result = JSON.parse(decodeURIComponent(item.dataset.nominatim));
            selectFromNominatim(result);
        }
    });

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            suggestionsDiv.style.display = 'none';
        }
    });

    // Evento: clic en el mapa (reverse geocoding)
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;
        setMarker(lat, lng, 'Ubicacion seleccionada');
        regionInput.value = 'Cargando...';
        ciudadInput.value = 'Cargando...';

        nominatimReverse(lat, lng, function(data) {
            if (!data || !data.address) {
                regionInput.value = '';
                ciudadInput.value = '';
                return;
            }
            var addr = data.address;
            var regionName = addr.state || addr.region || addr.county || addr.country || 'Desconocido';
            var cityName = addr.city || addr.town || addr.village || addr.municipality || addr.county || '';

            regionInput.value = regionName;
            ciudadInput.value = cityName;

            var latHidden = document.getElementById(latInputId);
            var lngHidden = document.getElementById(lngInputId);
            var regionHidden = document.getElementById(regionInputId);
            var ciudadHidden = document.getElementById(ciudadInputId);
            if (latHidden) latHidden.value = lat;
            if (lngHidden) lngHidden.value = lng;
            if (regionHidden) regionHidden.value = regionName;
            if (ciudadHidden) ciudadHidden.value = cityName;

            var extRegion = document.getElementById('ext_region');
            var extCiudad = document.getElementById('ext_ciudad');
            if (extRegion) extRegion.value = regionName;
            if (extCiudad) extCiudad.value = cityName;

            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(data.display_name).openPopup();
        });
    });

    // Valores preexistentes
    var existingRegion = document.getElementById(regionInputId);
    var existingCiudad = document.getElementById(ciudadInputId);
    if (existingRegion && existingRegion.value) {
        var regionVal = existingRegion.value;
        if (SALVADOR_DEPARTMENTS[regionVal]) {
            searchInput.value = regionVal;
            selectRegion(regionVal);
        } else {
            regionInput.value = regionVal;
            if (existingCiudad) ciudadInput.value = existingCiudad.value;
        }
    }

    // Detectar ubicacion del dispositivo
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            setMarker(lat, lng, 'Mi ubicacion');
            map.setView([lat, lng], 10);
            regionInput.value = 'Detectando...';
            ciudadInput.value = 'Detectando...';

            nominatimReverse(lat, lng, function(data) {
                if (!data || !data.address) {
                    regionInput.value = '';
                    ciudadInput.value = '';
                    return;
                }
                var addr = data.address;
                var regionName = addr.state || addr.region || addr.county || addr.country || 'Desconocido';
                var cityName = addr.city || addr.town || addr.village || addr.municipality || addr.county || '';

                regionInput.value = regionName;
                ciudadInput.value = cityName;

                var latHidden = document.getElementById(latInputId);
                var lngHidden = document.getElementById(lngInputId);
                var regionHidden = document.getElementById(regionInputId);
                var ciudadHidden = document.getElementById(ciudadInputId);
                if (latHidden) latHidden.value = lat;
                if (lngHidden) lngHidden.value = lng;
                if (regionHidden) regionHidden.value = regionName;
                if (ciudadHidden) ciudadHidden.value = cityName;

                var extRegion = document.getElementById('ext_region');
                var extCiudad = document.getElementById('ext_ciudad');
                if (extRegion) extRegion.value = regionName;
                if (extCiudad) extCiudad.value = cityName;

                if (marker) map.removeLayer(marker);
                marker = L.marker([lat, lng]).addTo(map);
                marker.bindPopup(data.display_name).openPopup();
            });
        }, function() {
            // Error o permiso denegado: mantener vista por defecto
        }, { timeout: 8000, enableHighAccuracy: true });
    }
    });
}
