<div class="welcome-banner" style="background: var(--brand-navy); color: #fff;">
    <div>
        <h1>Hola, <?= esc(session()->get('nombre')) ?>!</h1>
        <p>Bienvenido a tu panel de candidato. Completa tu perfil para mejorar tus oportunidades.</p>
    </div>
    <div class="welcome-banner-actions">
        <a href="<?= base_url('candidato/planes') ?>" class="btn btn-outline-light">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.1 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.1-1.62 0-5 0-5"/></svg>
            Mejorar plan
        </a>
        <a href="<?= base_url('candidato/perfil') ?>" class="btn btn-light">
            Completar perfil
        </a>
    </div>
</div>

<?php if (($porcentajePerfil ?? 0) < 100): ?>
<div class="profile-progress">
    <div class="profile-progress-header">
        <h3>Progreso del perfil</h3>
        <span><?= $porcentajePerfil ?? 0 ?>%</span>
    </div>
    <div class="profile-progress-bar">
        <div class="profile-progress-fill" style="width: <?= $porcentajePerfil ?? 0 ?>%"></div>
    </div>
    <p class="profile-progress-text">
        Completa tu informacion personal, experiencia y educacion para aumentar tu visibilidad.
    </p>
</div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= $stats['postulaciones'] ?? 0 ?></div>
        <div class="stat-card-label">Postulaciones enviadas</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= $stats['en_revision'] ?? 0 ?></div>
        <div class="stat-card-label">Postulaciones en revision</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= $stats['favoritas'] ?? 0 ?></div>
        <div class="stat-card-label">Vacantes favoritas</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= $stats['visitas'] ?? 0 ?></div>
        <div class="stat-card-label">Visitas a tu perfil</div>
    </div>
</div>

<div class="dash-grid">
    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Postulaciones recientes</h2>
            <a href="<?= base_url('candidato/postulaciones') ?>">Ver todas</a>
        </div>
        <?php if (!empty($postulacionesRecientes)): ?>
            <div class="dash-list">
                <?php foreach ($postulacionesRecientes as $p): ?>
                    <a href="<?= base_url('vacante/' . $p['vacante_slug']) ?>" class="dash-list-item">
                        <div class="dash-list-info">
                            <div class="dash-list-title"><?= esc($p['vacante_titulo']) ?></div>
                            <div class="dash-list-subtitle"><?= esc($p['empresa_nombre']) ?></div>
                        </div>
                        <span class="dash-list-badge badge-<?= $p['estado'] ?>"><?= ucfirst(str_replace('_', ' ', $p['estado'])) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <h3>Aun no te has postulado</h3>
                <p>Explora las vacantes disponibles y postulate a las que mas te interesen.</p>
                <a href="<?= base_url('buscar-empleo') ?>" class="btn btn-primary">Buscar vacantes</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Recomendadas para ti</h2>
        </div>
        <?php if (!empty($vacantesRecomendadas)): ?>
            <div class="dash-list">
                <?php foreach ($vacantesRecomendadas as $v): ?>
                    <a href="<?= base_url('candidato/vacante/' . $v['slug']) ?>" class="dash-list-item">
                        <div class="dash-list-info">
                            <div class="dash-list-title"><?= esc($v['titulo']) ?></div>
                            <div class="dash-list-subtitle"><?= esc($v['empresa_nombre']) ?> · <?= esc($v['ciudad'] ?? '') ?></div>
                        </div>
                        <?php if (!empty($v['salario_min'])): ?>
                            <span class="dash-list-salary">$<?= number_format($v['salario_min'], 0) ?>+</span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <h3>No hay vacantes disponibles</h3>
                <p>Vuelve mas tarde para ver nuevas oportunidades.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
