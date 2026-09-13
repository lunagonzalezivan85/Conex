<div class="welcome-banner" style="background: var(--brand-navy); color: #fff;">
    <div>
        <h1>Hola, <?= esc(session()->get('nombre')) ?>!</h1>
        <p>Bienvenido al panel de empresa. Gestiona tus vacantes y candidatos.</p>
        <?php if (!empty($planEmpresa)): ?>
            <span class="plan-badge">Plan <?= esc($planEmpresa['plan_nombre']) ?> · <?= $vacantesActivas ?? 0 ?>/<?= $planEmpresa['max_vacantes'] ?? 0 ?> vacantes</span>
        <?php endif; ?>
    </div>
    <div class="welcome-banner-actions">
        <a href="<?= base_url('empresa/planes') ?>" class="btn btn-outline-light">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.1 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.1-1.62 0-5 0-5"/></svg>
            Mejorar plan
        </a>
        <a href="<?= base_url('empresa/vacante/crear') ?>" class="btn btn-light">Publicar vacante</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= $vacantesActivas ?? 0 ?></div>
        <div class="stat-card-label">Vacantes activas</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= $totalPostulantes ?? 0 ?></div>
        <div class="stat-card-label">Total postulantes</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= $pendientesRevision ?? 0 ?></div>
        <div class="stat-card-label">Pendientes de revision</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= $encuestasPendientes ?? 0 ?></div>
        <div class="stat-card-label">Encuestas pendientes</div>
    </div>
</div>

<div class="dash-grid">
    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Vacantes recientes</h2>
            <a href="<?= base_url('empresa/vacantes') ?>">Ver todas</a>
        </div>
        <?php if (!empty($vacantesRecientes)): ?>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <?php foreach ($vacantesRecientes as $v): ?>
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;border:1px solid var(--border);border-radius:10px;">
                        <div style="width:40px;height:40px;background:#f0f4ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#4361EE;flex-shrink:0;">
                            <?= strtoupper(substr($v['titulo'], 0, 1)) ?>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:14px;font-weight:600;color:var(--text-primary);"><?= esc($v['titulo']) ?></div>
                            <div style="font-size:12px;color:var(--text-secondary);"><?= date('d M Y', strtotime($v['created_at'])) ?></div>
                        </div>
                        <span style="font-size:11px;padding:3px 10px;border-radius:12px;background:<?= $v['estado'] === 'publicada' ? '#dcfce7' : ($v['estado'] === 'cerrada' ? '#fee2e2' : '#f3f4f6') ?>;color:<?= $v['estado'] === 'publicada' ? '#16a34a' : ($v['estado'] === 'cerrada' ? '#ef4444' : '#6b7280') ?>;font-weight:500;text-transform:capitalize;"><?= esc($v['estado']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <h3>No tienes vacantes publicadas</h3>
                <p>Publica tu primera vacante para empezar a recibir candidatos.</p>
                <a href="<?= base_url('empresa/vacante/crear') ?>" class="btn btn-primary">Publicar vacante</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Postulantes recientes</h2>
            <a href="<?= base_url('empresa/postulantes') ?>">Ver todos</a>
        </div>
        <?php if (!empty($postulantesRecientes)): ?>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <?php foreach ($postulantesRecientes as $p): ?>
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;border:1px solid var(--border);border-radius:10px;">
                        <div style="width:40px;height:40px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#16a34a;flex-shrink:0;">
                            <?= strtoupper(substr($p['nombre'], 0, 1)) ?>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:14px;font-weight:600;color:var(--text-primary);"><?= esc($p['nombre'] . ' ' . $p['apellido']) ?></div>
                            <div style="font-size:12px;color:var(--text-secondary);"><?= esc($p['vacante_titulo']) ?></div>
                        </div>
                        <span style="font-size:11px;padding:3px 10px;border-radius:12px;background:<?= $p['estado'] === 'aceptada' ? '#dcfce7' : ($p['estado'] === 'rechazada' ? '#fee2e2' : '#fef3c7') ?>;color:<?= $p['estado'] === 'aceptada' ? '#16a34a' : ($p['estado'] === 'rechazada' ? '#ef4444' : '#b45309') ?>;font-weight:500;text-transform:capitalize;"><?= esc($p['estado']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <h3>Sin postulantes aun</h3>
                <p>Cuando recibas postulaciones, apareceran aqui.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
