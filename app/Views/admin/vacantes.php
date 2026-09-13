<div class="page-header">
    <div>
        <h1>Vacantes</h1>
        <p>Todas las vacantes publicadas en la plataforma.</p>
    </div>
</div>

<?php if (!empty($vacantes)): ?>
    <div class="vac-admin-grid">
        <?php foreach ($vacantes as $v): ?>
            <div class="vac-admin-card">
                <div class="vac-admin-card-top">
                    <div class="vac-admin-avatar">
                        <?= strtoupper(substr($v['titulo'], 0, 1)) ?>
                    </div>
                    <div class="vac-admin-card-info">
                        <h3><?= esc($v['titulo']) ?></h3>
                        <p class="vac-admin-empresa"><?= esc($v['empresa_nombre'] ?? 'Sin empresa') ?></p>
                    </div>
                    <span class="vac-admin-badge vac-admin-badge-<?= $v['estado'] === 'publicada' ? 'green' : ($v['estado'] === 'cerrada' ? 'red' : 'orange') ?>">
                        <?= ucfirst(esc($v['estado'])) ?>
                    </span>
                </div>
                <div class="vac-admin-card-meta">
                    <div class="vac-admin-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span><?= esc($v['ciudad'] ?? 'Sin ciudad') ?></span>
                    </div>
                    <div class="vac-admin-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <span><?= ucfirst(esc($v['modalidad'] ?? '-')) ?></span>
                    </div>
                    <?php if (!empty($v['salario_min'])): ?>
                    <div class="vac-admin-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        <span>$<?= number_format($v['salario_min'], 0) ?> - $<?= number_format($v['salario_max'] ?? $v['salario_min'], 0) ?> <?= esc($v['moneda'] ?? 'USD') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="vac-admin-card-footer">
                    <div class="vac-admin-postulantes">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <span><?= $v['total_postulantes'] ?> postulante(s)</span>
                    </div>
                    <span class="vac-admin-fecha">
                        <?= $v['fecha_publicacion'] ? date('d M Y', strtotime($v['fecha_publicacion'])) : 'Borrador' ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="dash-card">
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            <h3>No hay vacantes</h3>
            <p>Aun no se han publicado vacantes en la plataforma.</p>
        </div>
    </div>
<?php endif; ?>
