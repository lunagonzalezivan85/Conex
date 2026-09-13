<a href="<?= base_url('empresa/vacantes') ?>" class="vd-back">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Volver a mis vacantes
</a>

<div class="dash-card vd-header-card">
    <div class="vd-header-top">
        <div class="vd-header-icon">
            <?= strtoupper(substr($vacante['titulo'], 0, 1)) ?>
        </div>
        <div class="vd-header-info">
            <h2 class="vd-title"><?= esc($vacante['titulo']) ?></h2>
            <p class="vd-location">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?= esc($vacante['ciudad'] ?? '') ?>
                <?php if (!empty($vacante['region'])): ?> · <?= esc($vacante['region']) ?><?php endif; ?>
            </p>
            <div class="vd-badges">
                <?php
                    $estadoColors = [
                        'publicada' => ['bg' => '#dcfce7', 'color' => '#16a34a'],
                        'borrador' => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
                        'cerrada' => ['bg' => '#fee2e2', 'color' => '#ef4444'],
                        'suspendida' => ['bg' => '#fef3c7', 'color' => '#d97706'],
                    ];
                    $ec = $estadoColors[$vacante['estado'] ?? 'borrador'] ?? $estadoColors['borrador'];
                ?>
                <span class="vd-badge" style="background:<?= $ec['bg'] ?>;color:<?= $ec['color'] ?>;"><?= esc($vacante['estado']) ?></span>
                <?php if (!empty($vacante['modalidad'])): ?>
                <span class="vd-badge vd-badge-blue"><?= ucfirst($vacante['modalidad']) ?></span>
                <?php endif; ?>
                <?php if (!empty($vacante['salario_min'])): ?>
                <span class="vd-badge vd-badge-green">$<?= number_format($vacante['salario_min'], 0) ?> - $<?= number_format($vacante['salario_max'] ?? $vacante['salario_min'], 0) ?> USD</span>
                <?php endif; ?>
                <?php if (!empty($vacante['anios_experiencia']) && $vacante['anios_experiencia'] > 0): ?>
                <span class="vd-badge vd-badge-yellow"><?= $vacante['anios_experiencia'] ?> año(s) exp.</span>
                <?php endif; ?>
                <?php if (isset($vacante['vacantes_disponibles'])): ?>
                <span class="vd-badge vd-badge-blue-light"><?= $vacante['vacantes_disponibles'] ?> vacante(s)</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="vd-meta">
        <div class="vd-meta-item">
            <span class="vd-meta-label">Publicado</span>
            <span class="vd-meta-value"><?= isset($vacante['fecha_publicacion']) ? date('d M, Y', strtotime($vacante['fecha_publicacion'])) : 'N/A' ?></span>
        </div>
        <div class="vd-meta-item">
            <span class="vd-meta-label">Cierra</span>
            <span class="vd-meta-value"><?= isset($vacante['fecha_cierre']) ? date('d M, Y', strtotime($vacante['fecha_cierre'])) : 'Abierto' ?></span>
        </div>
        <div class="vd-meta-item">
            <span class="vd-meta-label">Max. postulantes</span>
            <span class="vd-meta-value"><?= ($vacante['max_postulantes'] ?? 0) > 0 ? $vacante['max_postulantes'] : 'Ilimitado' ?></span>
        </div>
    </div>
</div>

<!-- Stats postulantes - bento grid -->
<div class="vd-bento">
    <div class="vd-bento-card vd-bento-total">
        <span class="vd-bento-label">Total postulantes</span>
        <span class="vd-bento-num"><?= $totalPostulantes ?? 0 ?></span>
        <svg class="vd-bento-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <div class="vd-bento-card vd-bento-verified">
        <span class="vd-bento-label">Verificados</span>
        <span class="vd-bento-num"><?= $postulantesVerificados ?? 0 ?></span>
        <svg class="vd-bento-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c2.39 0 4.68.94 6.36 2.64"/><path d="M21 3v6h-6"/></svg>
    </div>
    <div class="vd-bento-card vd-bento-process">
        <span class="vd-bento-label">En proceso</span>
        <span class="vd-bento-num"><?= $postulantesEnProceso ?? 0 ?></span>
        <svg class="vd-bento-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
</div>

<div class="dash-card vd-section">
    <h3 class="vd-section-title">Descripcion</h3>
    <div class="vd-section-content"><?= nl2br(esc($vacante['descripcion'])) ?></div>
</div>

<?php if (!empty($vacante['funciones'])): ?>
<div class="dash-card vd-section">
    <h3 class="vd-section-title">Funciones</h3>
    <div class="vd-section-content"><?= nl2br(esc($vacante['funciones'])) ?></div>
</div>
<?php endif; ?>

<div class="vd-actions">
    <a href="<?= base_url('empresa/vacantes') ?>" class="btn btn-ghost">Volver</a>
</div>
