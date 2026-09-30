<?php
$filtros = $filtros ?? ['q' => '', 'categoria' => '', 'modalidad' => '', 'tipo_contrato' => '', 'ciudad' => '', 'orden' => 'reciente', 'lat' => '', 'lng' => '', 'radio' => 50];
$ordenLabels = [
    'reciente'  => 'Mas reciente',
    'antigua'   => 'Mas antigua',
    'salario'   => 'Mayor salario',
    'distancia' => 'Mas cercana',
];
$ordenActual = $filtros['orden'] ?? 'reciente';
$activos = (int)!empty($filtros['categoria']) + (int)!empty($filtros['modalidad']) + (int)!empty($filtros['ciudad']) + (int)!empty($filtros['lat']);
?>
<div class="buscar-empleo" style="padding:0;">
    <form action="<?= base_url('candidato/buscar-empleo') ?>" method="get" id="filterForm">

        <!-- Topbar: busqueda + orden + filtros + paginacion -->
        <div class="be-topbar">
            <div class="be-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="be-search-icon"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="q" placeholder="Buscar por titulo o palabra clave..." value="<?= esc($filtros['q']) ?>">
            </div>

            <div class="be-actions">
                <div class="be-sort">
                    <button type="button" class="btn btn-ghost be-sort-btn" id="btnSort">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 4v16"/></svg>
                        <span id="sortLabel"><?= esc($ordenLabels[$ordenActual] ?? 'Mas reciente') ?></span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="be-sort-menu" id="sortMenu">
                        <?php foreach ($ordenLabels as $k => $label): ?>
                            <button type="button" class="be-sort-item <?= $ordenActual === $k ? 'active' : '' ?>" data-orden="<?= $k ?>"><?= esc($label) ?></button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="orden" id="ordenInput" value="<?= esc($ordenActual) ?>">
                </div>

                <button type="button" class="btn btn-outline be-filter-btn" id="btnFiltros">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filtros
                    <?php if ($activos > 0): ?><span class="be-filter-count"><?= $activos ?></span><?php endif; ?>
                </button>
            </div>

            <?php if (isset($pager) && $pager): ?>
            <div class="be-pagination-top">
                <?= $pager->links() ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Modal de filtros -->
        <div class="be-modal-overlay" id="filterModal">
            <div class="be-modal">
                <div class="be-modal-header">
                    <h2>Filtros</h2>
                    <button type="button" class="be-modal-close" id="btnCerrarModal" aria-label="Cerrar">&times;</button>
                </div>
                <div class="be-modal-body">
                    <div class="be-modal-grid">
                        <div class="form-group">
                            <label class="form-label">Categoria</label>
                            <select name="categoria" class="form-control">
                                <option value="">Todas</option>
                                <?php if (!empty($categorias)): ?>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $filtros['categoria'] == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Modalidad</label>
                            <select name="modalidad" class="form-control">
                                <option value="">Todas</option>
                                <option value="presencial" <?= $filtros['modalidad'] === 'presencial' ? 'selected' : '' ?>>Presencial</option>
                                <option value="remoto" <?= $filtros['modalidad'] === 'remoto' ? 'selected' : '' ?>>Remoto</option>
                                <option value="hibrido" <?= $filtros['modalidad'] === 'hibrido' ? 'selected' : '' ?>>Hibrido</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control" placeholder="Ciudad..." value="<?= esc($filtros['ciudad']) ?>">
                        </div>
                    </div>

                    <div class="geo-filter">
                        <label class="form-label">Buscar cerca de</label>
                        <button type="button" class="btn btn-outline btn-sm geo-my-location" id="btnMiUbicacion">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/><circle cx="12" cy="12" r="9"/></svg>
                            Mi ubicacion
                        </button>
                        <p class="geo-hint" id="geoLabel"><?= !empty($filtros['lat']) ? 'Ubicacion seleccionada' : 'o haz clic en el mapa' ?></p>
                        <div id="geoMapa" class="geo-mapa"></div>
                        <div class="geo-radio-row">
                            <input type="range" id="geoRadio" min="5" max="200" step="5" value="<?= esc($filtros['radio'] ?? 50) ?>">
                            <span class="geo-radio-value" id="geoRadioValue"><?= (int)($filtros['radio'] ?? 50) ?> km</span>
                        </div>
                        <input type="hidden" name="lat" id="geoLat" value="<?= esc($filtros['lat'] ?? '') ?>">
                        <input type="hidden" name="lng" id="geoLng" value="<?= esc($filtros['lng'] ?? '') ?>">
                        <input type="hidden" name="radio" id="geoRadioHidden" value="<?= esc($filtros['radio'] ?? 50) ?>">
                        <button type="button" class="btn btn-ghost btn-sm" id="btnGeoLimpiar" <?= empty($filtros['lat']) ? 'style="display:none;"' : '' ?>>Quitar ubicacion</button>
                    </div>
                </div>
                <div class="be-modal-footer">
                    <a href="<?= base_url('candidato/buscar-empleo') ?>" class="btn btn-ghost">Limpiar todo</a>
                    <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                </div>
            </div>
        </div>
    </form>

    <p class="resultados-count"><?= count($vacantes) ?> vacante(s) encontrada(s)</p>

    <div class="be-grid">
        <?php if (!empty($vacantes)): ?>
            <?php foreach ($vacantes as $v): ?>
                <a href="<?= base_url('candidato/vacante/' . $v['slug']) ?>" class="dash-card be-card">
                    <div class="be-card-top">
                        <?php if (!empty($v['empresa_logo'])): ?>
                            <img src="<?= base_url('uploads/' . $v['empresa_logo']) ?>" alt="" class="be-card-logo">
                        <?php else: ?>
                            <div class="be-card-icon"><?= strtoupper(substr($v['empresa_nombre'] ?? $v['titulo'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <span class="be-card-date"><?= isset($v['fecha_publicacion']) ? date('d M', strtotime($v['fecha_publicacion'])) : '' ?></span>
                    </div>
                    <h3 class="be-card-title"><?= esc($v['titulo']) ?></h3>
                    <?php if (!empty($v['empresa_nombre'])): ?>
                        <p class="be-card-empresa"><?= esc($v['empresa_nombre']) ?></p>
                    <?php endif; ?>
                    <p class="be-card-location">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?= esc($v['ciudad'] ?? 'Sin ubicacion') ?><?= !empty($v['region']) ? ' · ' . esc($v['region']) : '' ?>
                        <?php if (isset($v['distancia_km'])): ?>
                            <span class="badge-distance"><?= number_format($v['distancia_km'], 1) ?> km</span>
                        <?php endif; ?>
                    </p>
                    <div class="be-card-badges">
                        <?php if (isset($v['modalidad']) && $v['modalidad']): ?>
                            <span class="badge badge-accent"><?= ucfirst($v['modalidad']) ?></span>
                        <?php endif; ?>
                        <?php if (isset($v['salario_min']) && $v['salario_min']): ?>
                            <span class="badge badge-success">$<?= number_format($v['salario_min'], 0) ?> - $<?= number_format($v['salario_max'] ?? $v['salario_min'], 0) ?> USD</span>
                        <?php endif; ?>
                        <?php if (isset($v['anios_experiencia']) && $v['anios_experiencia'] > 0): ?>
                            <span class="badge badge-warning"><?= $v['anios_experiencia'] ?> año(s) exp.</span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="dash-card resultados-empty be-empty">
                <p class="empty-state-icon">&#128269;</p>
                <h3>No se encontraron vacantes</h3>
                <p>Intenta ajustar los filtros de busqueda.</p>
                <a href="<?= base_url('candidato/buscar-empleo') ?>" class="btn btn-outline">Limpiar filtros</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($pager) && $pager): ?>
        <div class="pagination-wrapper"><?= $pager->links() ?></div>
    <?php endif; ?>
</div>
