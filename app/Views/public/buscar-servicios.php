<?php
$filtros = $filtros ?? ['q' => '', 'ciudad' => '', 'zona' => '', 'verificado' => false, 'lat' => '', 'lng' => '', 'radio' => 50];
$vista = $vista ?? 'listado';
$qsBase = array_filter($_GET ?? [], function ($v) { return $v !== '' && $v !== null; });
$urlLista = base_url('servicios') . '?' . http_build_query(array_merge($qsBase, ['vista' => 'listado']));
$urlMapa  = base_url('servicios') . '?' . http_build_query(array_merge($qsBase, ['vista' => 'mapa']));
$activos = 0;
foreach (['ciudad', 'zona', 'verificado', 'lat'] as $k) { if (!empty($filtros[$k])) $activos++; }
?>

<?php if ($vista === 'mapa'): ?>
<!-- ============ VISTA MAPA ============ -->
<section class="be-mapa-view">
    <aside class="be-mapa-panel">
        <div class="be-mapa-panel-top">
            <a href="<?= $urlLista ?>" class="be-view-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6l9-4 9 4v12l-9 4-9-4z"/><path d="M3 6l9 4 9-4M12 10v12"/></svg>
                Volver al listado
            </a>
            <span class="be-mapa-count"><?= count($serviciosMapa ?? []) ?> prestador(es) en el mapa</span>
        </div>
        <form action="<?= base_url('servicios') ?>" method="get" class="be-mapa-form" id="mapaFilterForm">
            <input type="hidden" name="vista" value="mapa">
            <div class="be-mapa-group">
                <label>Buscar</label>
                <input type="text" name="q" placeholder="Electricista, plomero..." value="<?= esc($filtros['q']) ?>">
            </div>
            <div class="be-mapa-group">
                <label>Ciudad</label>
                <select name="ciudad">
                    <option value="">Toda Nicaragua</option>
                    <?php foreach ($ciudades ?? [] as $c): ?>
                        <option value="<?= esc($c) ?>" <?= $filtros['ciudad'] === $c ? 'selected' : '' ?>><?= esc($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="be-mapa-group">
                <label>Zona / barrio</label>
                <input type="text" name="zona" placeholder="Zona / barrio" value="<?= esc($filtros['zona']) ?>">
            </div>
            <label class="be-mapa-check">
                <input type="checkbox" name="verificado" value="1" <?= !empty($filtros['verificado']) ? 'checked' : '' ?>>
                Solo verificados
            </label>
            <div class="be-mapa-group">
                <button type="button" class="btn btn-ghost" id="btnMapaUbicacion">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/><circle cx="12" cy="12" r="9"/></svg>
                    Cerca de mi
                </button>
                <div class="geo-radio-row">
                    <input type="range" id="mapRadio" min="5" max="200" step="5" value="<?= esc($filtros['radio'] ?? 50) ?>">
                    <span class="geo-radio-value" id="mapRadioValue"><?= (int)($filtros['radio'] ?? 50) ?> km</span>
                </div>
            </div>
            <input type="hidden" name="lat" id="mapLat" value="<?= esc($filtros['lat'] ?? '') ?>">
            <input type="hidden" name="lng" id="mapLng" value="<?= esc($filtros['lng'] ?? '') ?>">
            <input type="hidden" name="radio" id="mapRadioHidden" value="<?= esc($filtros['radio'] ?? 50) ?>">
            <button type="submit" class="btn btn-primary btn-block">Aplicar filtros</button>
        </form>
    </aside>
    <div id="mapaCompleto" class="be-mapa-full"></div>
    <script>
    window.cxVacantesMapa = <?= json_encode($serviciosMapa ?? [], JSON_UNESCAPED_UNICODE) ?>;
    window.cxCiudadFiltro = <?= json_encode($filtros['ciudad'] ?? '') ?>;
    window.cxGeoJsonUrl = <?= json_encode(base_url('js/data/nic-departamentos.geojson')) ?>;
    </script>
</section>

<?php else: ?>
<!-- ============ VISTA LISTADO ============ -->
<section class="page-hero">
    <div class="page-hero-inner">
        <div class="page-hero-top">
            <a href="<?= $urlMapa ?>" class="be-view-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Ver en mapa
            </a>
        </div>
        <h1>Encuentra <span class="hero-accent">profesionales</span> cerca de ti</h1>
        <p><?= count($servicios ?? []) ?> prestador(es) <?= !empty($filtros['q']) || !empty($filtros['ciudad']) || !empty($filtros['zona']) ? 'con los filtros aplicados' : 'activos ahora' ?></p>

        <form action="<?= base_url('servicios') ?>" method="get" class="be-hero-search">
            <div class="be-search-pill">
                <select id="heroScope" class="be-scope-select" aria-label="Buscar empleos o servicios">
                    <option value="empleos">Empleos</option>
                    <option value="servicios" selected>Servicios</option>
                </select>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="q" id="heroSearchInput" placeholder="Electricista, plomero, carpintero..." value="<?= esc($filtros['q']) ?>">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
            <div class="be-hero-chips">
                <select name="ciudad" class="be-chip">
                    <option value="">Toda Nicaragua</option>
                    <?php foreach ($ciudades ?? [] as $c): ?>
                        <option value="<?= esc($c) ?>" <?= $filtros['ciudad'] === $c ? 'selected' : '' ?>><?= esc($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="zona" class="be-chip be-chip-input" placeholder="Zona / barrio" value="<?= esc($filtros['zona']) ?>">
                <button type="button" class="be-chip be-chip-btn" id="btnFiltros">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filtros<?php if ($activos > 0): ?> (<?= $activos ?>)<?php endif; ?>
                </button>
            </div>
        </form>
    </div>
</section>

<div class="container buscar-empleo srv-directorio">
    <div class="be-topbar be-topbar-slim">
        <?php if (isset($pager) && $pager): ?>
        <div class="be-pagination-top"><?= $pager->links('default', 'conex_full') ?></div>
        <?php endif; ?>
    </div>

    <p class="resultados-count"><?= count($servicios) ?> prestador(es) encontrado(s)</p>

    <div class="be-grid reveal">
        <?php if (!empty($servicios)): ?>
            <?php foreach ($servicios as $s): ?>
                <div class="card be-card srv-card">
                    <div class="srv-card-head">
                        <?php if (!empty($s['avatar_url'])): ?>
                            <img src="<?= esc($s['avatar_url']) ?>" alt="" class="srv-avatar">
                        <?php else: ?>
                            <div class="srv-avatar srv-avatar-ini"><?= esc($s['iniciales']) ?></div>
                        <?php endif; ?>
                        <div class="srv-card-id">
                            <span class="srv-nombre">
                                <?= esc($s['nombre_publico']) ?>
                                <?php if (!empty($s['prestador_verificado'])): ?>
                                    <span class="srv-verified" title="Identidad verificada">
                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M12 2l2.4 2.4 3.4-.4 1 3.3 3 1.7-1.3 3.1 1.3 3.1-3 1.7-1 3.3-3.4-.4L12 22l-2.4-2.4-3.4.4-1-3.3-3-1.7L3.5 12 2.2 8.9l3-1.7 1-3.3 3.4.4z"/><path d="M9.5 12.2l1.8 1.8 3.5-3.8" stroke="#fff" stroke-width="2" fill="none"/></svg>
                                    </span>
                                <?php endif; ?>
                            </span>
                            <h3 class="be-card-title"><?= esc($s['oficio']) ?></h3>
                        </div>
                    </div>
                    <p class="be-card-location">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?= esc($s['zona'] ?: 'Zona por confirmar') ?><?= !empty($s['ciudad']) ? ' · ' . esc($s['ciudad']) : '' ?>
                    </p>
                    <?php if (!empty($s['descripcion'])): ?>
                        <p class="srv-desc"><?= esc(mb_substr($s['descripcion'], 0, 110)) ?><?= mb_strlen($s['descripcion']) > 110 ? '…' : '' ?></p>
                    <?php endif; ?>
                    <div class="be-card-badges">
                        <?php if (!empty($s['precio_desde'])): ?>
                            <span class="badge badge-success">Desde $<?= number_format($s['precio_desde'], 0) ?><?= $s['precio_tipo'] === 'por_hora' ? '/h' : '' ?></span>
                        <?php else: ?>
                            <span class="badge badge-accent">Precio a convenir</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= base_url('servicios/p/' . (int)$s['id']) ?>" class="btn btn-primary srv-cta">Ver perfil y solicitar contacto</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card resultados-empty be-empty">
                <p class="empty-state-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></p>
                <h3>Aun no hay prestadores en esta zona</h3>
                <p>Intenta con otra ciudad u oficio.</p>
                <a href="<?= base_url('servicios') ?>" class="btn btn-outline">Ver todos</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($pager) && $pager): ?>
        <div class="pagination-wrapper"><?= $pager->links('default', 'conex_full') ?></div>
    <?php endif; ?>
</div>

<!-- Modal de filtros -->
<div class="be-modal-overlay" id="filterModal">
    <div class="be-modal">
        <div class="be-modal-header">
            <h2>Filtros</h2>
            <button type="button" class="be-modal-close" id="btnCerrarModal">&times;</button>
        </div>
        <form action="<?= base_url('servicios') ?>" method="get">
            <input type="hidden" name="q" value="<?= esc($filtros['q']) ?>">
            <div class="be-modal-body">
                <div class="be-modal-grid">
                    <div class="form-group">
                        <label class="form-label">Ciudad</label>
                        <select name="ciudad" class="form-control">
                            <option value="">Toda Nicaragua</option>
                            <?php foreach ($ciudades ?? [] as $c): ?>
                                <option value="<?= esc($c) ?>" <?= $filtros['ciudad'] === $c ? 'selected' : '' ?>><?= esc($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Zona / barrio</label>
                        <input type="text" name="zona" class="form-control" placeholder="Zona / barrio" value="<?= esc($filtros['zona']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="be-mapa-check">
                            <input type="checkbox" name="verificado" value="1" <?= !empty($filtros['verificado']) ? 'checked' : '' ?>>
                            Solo prestadores verificados
                        </label>
                    </div>
                </div>
                <div class="geo-filter">
                    <label class="form-label">Cerca de mi</label>
                    <button type="button" class="btn btn-ghost" id="btnMiUbicacion">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/><circle cx="12" cy="12" r="9"/></svg>
                        Mi ubicacion
                    </button>
                    <p class="geo-hint" id="geoLabel"><?= !empty($filtros['lat']) ? 'Ubicacion seleccionada' : 'Filtra prestadores por distancia' ?></p>
                    <div class="geo-radio-row">
                        <input type="range" id="geoRadio" min="5" max="200" step="5" value="<?= esc($filtros['radio'] ?? 50) ?>">
                        <span class="geo-radio-value" id="geoRadioValue"><?= (int)($filtros['radio'] ?? 50) ?> km</span>
                    </div>
                    <input type="hidden" name="radio" id="geoRadioHidden" value="<?= esc($filtros['radio'] ?? 50) ?>">
                    <input type="hidden" name="lat" id="geoLat" value="<?= esc($filtros['lat'] ?? '') ?>">
                    <input type="hidden" name="lng" id="geoLng" value="<?= esc($filtros['lng'] ?? '') ?>">
                    <?php if (!empty($filtros['lat'])): ?>
                        <button type="button" class="btn btn-ghost btn-sm" id="btnGeoLimpiar">Quitar ubicacion</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="be-modal-footer">
                <a href="<?= base_url('servicios') ?>" class="be-clear-link">Limpiar todo</a>
                <button type="submit" class="btn btn-primary">Aplicar filtros</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
