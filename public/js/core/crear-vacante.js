// Crear vacante - logica de tag selectors (requisitos, habilidades) y ubicacion

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar selector de ubicacion con mapa
    if (typeof initUbicacionSelector !== 'undefined') {
        initUbicacionSelector('ubicacionContainer', 'ext_latitud', 'ext_longitud', 'ext_region', 'ext_ciudad');
    }

    var data = window.VAC_DATA || {};
    var requisitos = data.requisitos || [];
    var habilidades = data.habilidades || [];

    // Estado de seleccionados
    var reqSelected = {}; // {id: {id, nombre, tipo}}
    var habSelected = {}; // {id: {id, nombre, nivel, tipo}}

    // --- Requisitos ---
    var reqSearch = document.getElementById('reqSearch');
    var reqDropdown = document.getElementById('reqDropdown');
    var reqSelectedEl = document.getElementById('reqSelected');
    var reqHighlight = -1;

    if (reqSearch) {
        reqSearch.addEventListener('input', function() {
            renderReqDropdown(this.value);
        });

        reqSearch.addEventListener('keydown', function(e) {
            var items = reqDropdown.querySelectorAll('.tag-dropdown-item:not(.already-selected)');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                reqHighlight = Math.min(reqHighlight + 1, items.length - 1);
                updateHighlight(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                reqHighlight = Math.max(reqHighlight - 1, 0);
                updateHighlight(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (reqHighlight >= 0 && items[reqHighlight]) {
                    items[reqHighlight].click();
                }
            } else if (e.key === 'Escape') {
                reqDropdown.classList.remove('open');
            }
        });

        reqSearch.addEventListener('focus', function() {
            if (this.value.trim()) renderReqDropdown(this.value);
        });
    }

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#reqTagSelector')) {
            reqDropdown.classList.remove('open');
        }
        if (!e.target.closest('#habTagSelector')) {
            habDropdown.classList.remove('open');
        }
    });

    function renderReqDropdown(query) {
        query = (query || '').trim().toLowerCase();
        if (!query) {
            reqDropdown.classList.remove('open');
            return;
        }
        var matches = requisitos.filter(function(r) {
            return r.nombre.toLowerCase().includes(query);
        });
        reqHighlight = -1;
        if (matches.length === 0) {
            reqDropdown.innerHTML = '<div class="tag-dropdown-empty">No se encontraron requisitos</div>';
            reqDropdown.classList.add('open');
            return;
        }
        reqDropdown.innerHTML = matches.map(function(r) {
            var sel = reqSelected[r.id] ? ' already-selected' : '';
            return '<div class="tag-dropdown-item' + sel + '" data-id="' + r.id + '" data-nombre="' + escapeAttr(r.nombre) + '">' + escapeHtml(r.nombre) + '</div>';
        }).join('');
        reqDropdown.classList.add('open');

        reqDropdown.querySelectorAll('.tag-dropdown-item:not(.already-selected)').forEach(function(item) {
            item.addEventListener('click', function() {
                addReq(parseInt(this.dataset.id), this.dataset.nombre);
            });
        });
    }

    function addReq(id, nombre) {
        if (reqSelected[id]) return;
        reqSelected[id] = { id: id, nombre: nombre, tipo: 'obligatorio' };
        renderReqChips();
        reqSearch.value = '';
        reqDropdown.classList.remove('open');
    }

    function removeReq(id) {
        delete reqSelected[id];
        renderReqChips();
    }

    function changeReqTipo(id, tipo) {
        if (!reqSelected[id]) return;
        reqSelected[id].tipo = tipo;
        renderReqChips();
    }

    function renderReqChips() {
        var html = '';
        var keys = Object.keys(reqSelected);
        keys.forEach(function(key) {
            var r = reqSelected[key];
            var deseableClass = r.tipo === 'deseable' ? ' deseable' : '';
            html += '<div class="tag-chip req' + deseableClass + '">'
                + '<span class="tag-chip-name">' + escapeHtml(r.nombre) + '</span>'
                + '<span class="tag-chip-meta"><select onchange="changeReqTipo(' + r.id + ', this.value)">'
                + '<option value="obligatorio"' + (r.tipo === 'obligatorio' ? ' selected' : '') + '>Obligatorio</option>'
                + '<option value="deseable"' + (r.tipo === 'deseable' ? ' selected' : '') + '>Deseable</option>'
                + '</select></span>'
                + '<span class="tag-chip-remove" onclick="removeReq(' + r.id + ')">&times;</span>'
                + '</div>';
        });
        reqSelectedEl.innerHTML = html;

        // Actualizar hidden inputs
        var form = document.querySelector('form');
        form.querySelectorAll('input[name^="req_hidden_"]').forEach(function(el) { el.remove(); });
        keys.forEach(function(key) {
            var r = reqSelected[key];
            var i1 = document.createElement('input');
            i1.type = 'hidden'; i1.name = 'requisitos[]'; i1.value = r.id;
            i1.className = 'req-hidden-input';
            form.appendChild(i1);
            var i2 = document.createElement('input');
            i2.type = 'hidden'; i2.name = 'req_tipo_' + r.id; i2.value = r.tipo;
            i2.className = 'req-hidden-input';
            form.appendChild(i2);
        });
    }

    // --- Habilidades ---
    var habSearch = document.getElementById('habSearch');
    var habDropdown = document.getElementById('habDropdown');
    var habSelectedEl = document.getElementById('habSelected');
    var habHighlight = -1;

    if (habSearch) {
        habSearch.addEventListener('input', function() {
            renderHabDropdown(this.value);
        });

        habSearch.addEventListener('keydown', function(e) {
            var items = habDropdown.querySelectorAll('.tag-dropdown-item:not(.already-selected)');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                habHighlight = Math.min(habHighlight + 1, items.length - 1);
                updateHighlight(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                habHighlight = Math.max(habHighlight - 1, 0);
                updateHighlight(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (habHighlight >= 0 && items[habHighlight]) {
                    items[habHighlight].click();
                }
            } else if (e.key === 'Escape') {
                habDropdown.classList.remove('open');
            }
        });

        habSearch.addEventListener('focus', function() {
            if (this.value.trim()) renderHabDropdown(this.value);
        });
    }

    function renderHabDropdown(query) {
        query = (query || '').trim().toLowerCase();
        if (!query) {
            habDropdown.classList.remove('open');
            return;
        }
        var matches = habilidades.filter(function(h) {
            return h.nombre.toLowerCase().includes(query);
        });
        habHighlight = -1;
        if (matches.length === 0) {
            habDropdown.innerHTML = '<div class="tag-dropdown-empty">No se encontraron habilidades</div>';
            habDropdown.classList.add('open');
            return;
        }
        habDropdown.innerHTML = matches.map(function(h) {
            var sel = habSelected[h.id] ? ' already-selected' : '';
            return '<div class="tag-dropdown-item' + sel + '" data-id="' + h.id + '" data-nombre="' + escapeAttr(h.nombre) + '">' + escapeHtml(h.nombre) + '</div>';
        }).join('');
        habDropdown.classList.add('open');

        habDropdown.querySelectorAll('.tag-dropdown-item:not(.already-selected)').forEach(function(item) {
            item.addEventListener('click', function() {
                addHab(parseInt(this.dataset.id), this.dataset.nombre);
            });
        });
    }

    function addHab(id, nombre) {
        if (habSelected[id]) return;
        habSelected[id] = { id: id, nombre: nombre, nivel: 'intermedio', tipo: 'obligatorio' };
        renderHabChips();
        habSearch.value = '';
        habDropdown.classList.remove('open');
    }

    function removeHab(id) {
        delete habSelected[id];
        renderHabChips();
    }

    function changeHabField(id, field, value) {
        if (!habSelected[id]) return;
        habSelected[id][field] = value;
        renderHabChips();
    }

    function renderHabChips() {
        var html = '';
        var keys = Object.keys(habSelected);
        keys.forEach(function(key) {
            var h = habSelected[key];
            var deseableClass = h.tipo === 'deseable' ? ' deseable' : '';
            html += '<div class="tag-chip hab' + deseableClass + '">'
                + '<span class="tag-chip-name">' + escapeHtml(h.nombre) + '</span>'
                + '<span class="tag-chip-meta"><select onchange="changeHabField(' + h.id + ', \'nivel\', this.value)">'
                + '<option value="basico"' + (h.nivel === 'basico' ? ' selected' : '') + '>Basico</option>'
                + '<option value="intermedio"' + (h.nivel === 'intermedio' ? ' selected' : '') + '>Intermedio</option>'
                + '<option value="avanzado"' + (h.nivel === 'avanzado' ? ' selected' : '') + '>Avanzado</option>'
                + '<option value="experto"' + (h.nivel === 'experto' ? ' selected' : '') + '>Experto</option>'
                + '</select></span>'
                + '<span class="tag-chip-meta"><select onchange="changeHabField(' + h.id + ', \'tipo\', this.value)">'
                + '<option value="obligatorio"' + (h.tipo === 'obligatorio' ? ' selected' : '') + '>Obligatorio</option>'
                + '<option value="deseable"' + (h.tipo === 'deseable' ? ' selected' : '') + '>Deseable</option>'
                + '</select></span>'
                + '<span class="tag-chip-remove" onclick="removeHab(' + h.id + ')">&times;</span>'
                + '</div>';
        });
        habSelectedEl.innerHTML = html;

        // Actualizar hidden inputs
        var form = document.querySelector('form');
        form.querySelectorAll('input.hab-hidden-input').forEach(function(el) { el.remove(); });
        keys.forEach(function(key) {
            var h = habSelected[key];
            var i1 = document.createElement('input');
            i1.type = 'hidden'; i1.name = 'habilidades[]'; i1.value = h.id;
            i1.className = 'hab-hidden-input';
            form.appendChild(i1);
            var i2 = document.createElement('input');
            i2.type = 'hidden'; i2.name = 'hab_nivel_' + h.id; i2.value = h.nivel;
            i2.className = 'hab-hidden-input';
            form.appendChild(i2);
            var i3 = document.createElement('input');
            i3.type = 'hidden'; i3.name = 'hab_tipo_' + h.id; i3.value = h.tipo;
            i3.className = 'hab-hidden-input';
            form.appendChild(i3);
        });
    }

    // --- Helpers ---
    function updateHighlight(items) {
        items.forEach(function(item, i) {
            item.classList.toggle('highlighted', i === reqHighlight || i === habHighlight);
        });
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function escapeAttr(str) {
        return str.replace(/"/g, '"').replace(/'/g, '&#39;');
    }

    // Exponer funciones globales para los onclick inline
    window.removeReq = removeReq;
    window.changeReqTipo = changeReqTipo;
    window.removeHab = removeHab;
    window.changeHabField = changeHabField;

    // --- Sugerir con IA ---
    var btnSugerirDesc = document.getElementById('btnSugerirDesc');
    var btnSugerirFunc = document.getElementById('btnSugerirFunc');
    var textareaDesc = document.getElementById('textareaDescripcion');
    var textareaFunc = document.getElementById('textareaFunciones');
    var tituloInput = document.querySelector('input[name="titulo"]');

    function sugerir(btn, textarea, tipo) {
        var titulo = tituloInput ? tituloInput.value.trim() : '';
        if (!titulo) {
            alert('Primero escribe el titulo de la vacante');
            tituloInput && tituloInput.focus();
            return;
        }

        btn.classList.add('loading');
        btn.disabled = true;
        var originalHTML = btn.innerHTML;
        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.3h6c0-1 .4-1.8 1-2.3A7 7 0 0 0 12 2z"/></svg> Generando...';

        var formData = new FormData();
        formData.append('titulo', titulo);
        formData.append('tipo', tipo);
        // Incluir CSRF token del formulario si existe
        var csrfInput = document.querySelector('form input[name^="csrf"]');
        if (csrfInput) {
            formData.append(csrfInput.name, csrfInput.value);
        }

        fetch('/CONEX/empresa/vacante/sugerir', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.error) {
                alert('Error: ' + data.error);
            } else if (data.texto) {
                textarea.value = data.texto;
            }
        })
        .catch(function(err) {
            alert('Error al conectar con el servidor');
        })
        .finally(function() {
            btn.classList.remove('loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        });
    }

    if (btnSugerirDesc) {
        btnSugerirDesc.addEventListener('click', function() {
            sugerir(btnSugerirDesc, textareaDesc, 'descripcion');
        });
    }
    if (btnSugerirFunc) {
        btnSugerirFunc.addEventListener('click', function() {
            sugerir(btnSugerirFunc, textareaFunc, 'funciones');
        });
    }
});
