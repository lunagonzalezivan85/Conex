<div class="page-header">
    <div>
        <h1>Mis Vacantes</h1>
        <p>Gestiona las vacantes publicadas por tu empresa.</p>
    </div>
    <a href="<?= base_url('empresa/vacante/crear') ?>" class="btn btn-primary">Publicar vacante</a>
</div>

<?php if (!empty($vacantes)): ?>
<div style="display:flex;flex-direction:column;gap:16px;">
    <?php foreach ($vacantes as $v): ?>
    <div class="dash-card" style="display:flex;align-items:center;gap:20px;padding:20px;">
        <div style="width:52px;height:52px;background:var(--accent-light,#e0e7ff);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--accent,#4361EE);flex-shrink:0;">
            <?= strtoupper(substr($v['titulo'], 0, 1)) ?>
        </div>
        <div style="flex:1;min-width:0;">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:4px;"><?= esc($v['titulo']) ?></h3>
            <p style="font-size:13px;color:var(--text-secondary);margin-bottom:8px;">
                <?= esc($v['ciudad'] ?? '') ?>
                <?php if (!empty($v['region'])): ?> · <?= esc($v['region']) ?><?php endif; ?>
            </p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <?php
                    $estadoColors = [
                        'publicada' => '#dcfce7|#16a34a',
                        'borrador' => '#f3f4f6|#6b7280',
                        'cerrada' => '#fee2e2|#ef4444',
                        'suspendida' => '#fef3c7|#d97706',
                    ];
                    $colors = $estadoColors[$v['estado'] ?? 'borrador'] ?? '#f3f4f6|#6b7280';
                    list($bg, $color) = explode('|', $colors);
                ?>
                <span style="font-size:11px;padding:3px 10px;border-radius:12px;background:<?= $bg ?>;color:<?= $color ?>;font-weight:500;text-transform:capitalize;"><?= esc($v['estado']) ?></span>
                <?php if (isset($v['modalidad']) && $v['modalidad']): ?>
                    <span style="font-size:11px;padding:3px 10px;border-radius:12px;background:#e0e7ff;color:#4361EE;font-weight:500;"><?= ucfirst($v['modalidad']) ?></span>
                <?php endif; ?>
                <?php if (isset($v['salario_min']) && $v['salario_min']): ?>
                    <span style="font-size:11px;padding:3px 10px;border-radius:12px;background:#dcfce7;color:#16a34a;font-weight:500;">$<?= number_format($v['salario_min'], 0) ?> - $<?= number_format($v['salario_max'] ?? $v['salario_min'], 0) ?> USD</span>
                <?php endif; ?>
                <?php if (!empty($v['vacantes_disponibles'])): ?>
                    <span style="font-size:11px;padding:3px 10px;border-radius:12px;background:#f0f4ff;color:#4361EE;font-weight:500;"><?= $v['vacantes_disponibles'] ?> vacante(s)</span>
                <?php endif; ?>
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0;display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
            <p style="font-size:12px;color:var(--text-muted,#9ca3af);">
                <?= isset($v['fecha_publicacion']) ? date('d M Y', strtotime($v['fecha_publicacion'])) : '' ?>
            </p>
            <div style="display:flex;gap:6px;">
                <a href="<?= base_url('empresa/vacante/ver/' . $v['id']) ?>" class="btn btn-ghost btn-sm" style="font-size:12px;">Ver</a>
                <?php if (($v['estado'] ?? '') === 'publicada'): ?>
                <a href="<?= base_url('empresa/vacante/cerrar/' . $v['id']) ?>" class="btn btn-ghost btn-sm" style="font-size:12px;color:#ef4444;" onclick="return confirm('Cerrar esta vacante? Se generara una encuesta para calificar a los postulantes seleccionados.')">Cerrar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="dash-card">
    <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        <h3>No tienes vacantes</h3>
        <p>Publica tu primera vacante para empezar a recibir candidatos.</p>
        <a href="<?= base_url('empresa/vacante/crear') ?>" class="btn btn-primary">Publicar vacante</a>
    </div>
</div>
<?php endif; ?>
