<div class="page-header">
    <div>
        <a href="<?= base_url('candidato/buscar-empleo') ?>" class="btn btn-ghost btn-sm" style="margin-bottom:12px;">
            &larr; Volver a buscar
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error" style="margin-bottom:16px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="dash-card" style="margin-bottom:24px;">
    <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:20px;">
        <div style="width:56px;height:56px;background:#f0f4ff;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:#4361EE;flex-shrink:0;">
            <?= strtoupper(substr($vacante['titulo'], 0, 1)) ?>
        </div>
        <div style="flex:1;">
            <h1 style="font-size:22px;font-weight:700;margin-bottom:4px;"><?= esc($vacante['titulo']) ?></h1>
            <p style="font-size:14px;color:var(--text-secondary);margin-bottom:8px;">
                <?= esc($empresa['razon_social'] ?? 'Empresa') ?>
            </p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <?php if (isset($vacante['modalidad'])): ?>
                    <span class="verif-badge verif-badge-blue"><?= ucfirst($vacante['modalidad']) ?></span>
                <?php endif; ?>
                <?php if (isset($vacante['salario_min']) && $vacante['salario_min']): ?>
                    <span class="verif-badge verif-badge-green">$<?= number_format($vacante['salario_min'], 0) ?> - $<?= number_format($vacante['salario_max'] ?? $vacante['salario_min'], 0) ?> <?= esc($vacante['moneda'] ?? 'USD') ?></span>
                <?php endif; ?>
                <?php if (isset($vacante['anios_experiencia']) && $vacante['anios_experiencia'] > 0): ?>
                    <span class="verif-badge verif-badge-orange"><?= $vacante['anios_experiencia'] ?> año(s) exp.</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:24px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border);">
        <div>
            <p style="font-size:12px;color:var(--text-secondary);text-transform:uppercase;font-weight:600;margin-bottom:4px;">Ciudad</p>
            <p style="font-size:14px;"><?= esc($vacante['ciudad'] ?? '-') ?></p>
        </div>
        <div>
            <p style="font-size:12px;color:var(--text-secondary);text-transform:uppercase;font-weight:600;margin-bottom:4px;">Publicado</p>
            <p style="font-size:14px;"><?= isset($vacante['fecha_publicacion']) ? date('d M, Y', strtotime($vacante['fecha_publicacion'])) : 'N/A' ?></p>
        </div>
        <div>
            <p style="font-size:12px;color:var(--text-secondary);text-transform:uppercase;font-weight:600;margin-bottom:4px;">Cierra</p>
            <p style="font-size:14px;"><?= isset($vacante['fecha_cierre']) ? date('d M, Y', strtotime($vacante['fecha_cierre'])) : 'Abierto' ?></p>
        </div>
        <div>
            <p style="font-size:12px;color:var(--text-secondary);text-transform:uppercase;font-weight:600;margin-bottom:4px;">Vacantes</p>
            <p style="font-size:14px;"><?= $vacante['vacantes_disponibles'] > 0 ? $vacante['vacantes_disponibles'] : 'Ilimitado' ?></p>
        </div>
    </div>
</div>

<div class="dash-card" style="margin-bottom:24px;">
    <h2 style="font-size:16px;font-weight:600;margin-bottom:12px;">Descripcion</h2>
    <div style="font-size:14px;line-height:1.6;color:var(--text-primary);">
        <?= nl2br(esc($vacante['descripcion'])) ?>
    </div>
</div>

<?php if (!empty($vacante['funciones'])): ?>
<div class="dash-card" style="margin-bottom:24px;">
    <h2 style="font-size:16px;font-weight:600;margin-bottom:12px;">Funciones</h2>
    <div style="font-size:14px;line-height:1.6;color:var(--text-primary);">
        <?= nl2br(esc($vacante['funciones'])) ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($requisitos)): ?>
<div class="dash-card" style="margin-bottom:24px;">
    <h2 style="font-size:16px;font-weight:600;margin-bottom:12px;">Requisitos</h2>
    <div style="display:flex;flex-direction:column;gap:8px;">
        <?php foreach ($requisitos as $req): ?>
            <div style="display:flex;align-items:center;gap:8px;font-size:14px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:#16a34a;flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                <span><?= esc($req['requisito_nombre']) ?></span>
                <span class="verif-requisito-tipo"><?= esc($req['tipo']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="dash-card">
    <?php if ($yaPostulado): ?>
        <div style="text-align:center;padding:20px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;color:#16a34a;margin-bottom:12px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h2 style="font-size:18px;font-weight:600;margin-bottom:8px;">Ya te has postulado</h2>
            <p style="color:var(--text-secondary);margin-bottom:16px;">Tu postulacion a esta vacante ya fue enviada.</p>
            <a href="<?= base_url('candidato/postulaciones') ?>" class="btn btn-primary">Ver mis postulaciones</a>
        </div>
    <?php elseif (!$candidato): ?>
        <div style="text-align:center;padding:20px;">
            <h2 style="font-size:18px;font-weight:600;margin-bottom:8px;">Completa tu perfil</h2>
            <p style="color:var(--text-secondary);margin-bottom:16px;">Debes completar tu perfil antes de postularte.</p>
            <a href="<?= base_url('candidato/perfil') ?>" class="btn btn-primary">Ir a mi perfil</a>
        </div>
    <?php else: ?>
        <h2 style="font-size:18px;font-weight:600;margin-bottom:8px;">Postularme a esta vacante</h2>
        <p style="color:var(--text-secondary);margin-bottom:16px;font-size:14px;">Al postularte, tu informacion sera enviada a la empresa.</p>
        <form action="<?= base_url('candidato/postularse/' . $vacante['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Mensaje (opcional)</label>
                <textarea name="mensaje" class="form-control" rows="3" placeholder="Cuentale a la empresa por que eres el candidato ideal..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Postularme ahora</button>
        </form>
    <?php endif; ?>
</div>
