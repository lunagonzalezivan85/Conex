<div class="container vacante-detalle">
    <a href="<?= base_url('buscar-empleo') ?>" class="vd-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a buscar
    </a>

    <div class="card vd-header-card">
        <div class="vd-header-top">
            <div class="vd-header-icon"><?= strtoupper(substr($vacante['titulo'], 0, 1)) ?></div>
            <div class="vd-header-info">
                <h1 class="vd-title"><?= esc($vacante['titulo']) ?></h1>
                <p class="vd-location">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <?= esc($vacante['ciudad'] ?? 'Ubicacion no especificada') ?><?= isset($vacante['region']) ? ' · ' . esc($vacante['region']) : '' ?>
                </p>
                <div class="vd-badges">
                    <?php if (isset($vacante['modalidad'])): ?>
                        <span class="vd-badge vd-badge-blue"><?= ucfirst($vacante['modalidad']) ?></span>
                    <?php endif; ?>
                    <?php if (isset($vacante['salario_min']) && $vacante['salario_min']): ?>
                        <span class="vd-badge vd-badge-green">$<?= number_format($vacante['salario_min'], 0) ?> - $<?= number_format($vacante['salario_max'] ?? $vacante['salario_min'], 0) ?> USD</span>
                    <?php endif; ?>
                    <?php if (isset($vacante['anios_experiencia']) && $vacante['anios_experiencia'] > 0): ?>
                        <span class="vd-badge vd-badge-yellow"><?= $vacante['anios_experiencia'] ?> ano(s) exp.</span>
                    <?php endif; ?>
                    <?php if (isset($vacante['vacantes_disponibles'])): ?>
                        <span class="vd-badge vd-badge-blue-light"><?= $vacante['vacantes_disponibles'] ?> vacante(s)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="vd-meta">
            <div class="vd-meta-item">
                <p class="vd-meta-label">Publicado</p>
                <p class="vd-meta-value"><?= isset($vacante['fecha_publicacion']) ? date('d M, Y', strtotime($vacante['fecha_publicacion'])) : 'N/A' ?></p>
            </div>
            <div class="vd-meta-item">
                <p class="vd-meta-label">Cierra</p>
                <p class="vd-meta-value"><?= isset($vacante['fecha_cierre']) ? date('d M, Y', strtotime($vacante['fecha_cierre'])) : 'Abierto' ?></p>
            </div>
            <div class="vd-meta-item">
                <p class="vd-meta-label">Max. postulantes</p>
                <p class="vd-meta-value"><?= $vacante['max_postulantes'] > 0 ? $vacante['max_postulantes'] : 'Ilimitado' ?></p>
            </div>
        </div>
    </div>

    <div class="card vd-section">
        <h2 class="vd-section-title">Descripcion</h2>
        <div class="vd-section-content"><?= nl2br(esc($vacante['descripcion'])) ?></div>
    </div>

    <?php if (!empty($vacante['funciones'])): ?>
    <div class="card vd-section">
        <h2 class="vd-section-title">Funciones</h2>
        <div class="vd-section-content"><?= nl2br(esc($vacante['funciones'])) ?></div>
    </div>
    <?php endif; ?>

    <div class="card vd-cta">
        <div class="vd-cta-content">
            <h2>Interesado en esta vacante?</h2>
            <p>Inicia sesion o registrate para postularte</p>
        </div>
        <div class="vd-cta-actions">
            <a href="<?= base_url('login') ?>" class="btn btn-primary">Iniciar sesion</a>
            <a href="<?= base_url('registro') ?>" class="btn btn-outline">Registrarse</a>
        </div>
    </div>
</div>
