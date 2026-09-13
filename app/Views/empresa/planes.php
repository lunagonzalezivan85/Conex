<div class="page-header">
    <div>
        <h1>Planes disponibles</h1>
        <p>Mejora tu plan para publicar mas vacantes y acceder a funciones avanzadas.</p>
    </div>
</div>

<?php if (!empty($planEmpresa)): ?>
<div class="dash-card" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:16px;font-weight:600;margin-bottom:4px;">Tu plan actual: <?= esc($planEmpresa['plan_nombre']) ?></h3>
            <p style="font-size:13px;color:var(--text-secondary);">
                Vacantes activas: <?= $vacantesActivas ?? 0 ?> / <?= $planEmpresa['max_vacantes'] ?? 0 ?>
                <?php if (($planEmpresa['plan_slug'] ?? '') !== 'premium'): ?>
                    &middot; $<?= number_format($planEmpresa['precio_mensual'] ?? 0, 0) ?>/mes
                <?php endif; ?>
            </p>
        </div>
        <span class="verif-badge verif-badge-green">Activo</span>
    </div>
</div>
<?php endif; ?>

<div class="planes-panel-grid">
    <?php foreach ($planes as $plan): ?>
        <div class="plan-panel-card <?= ($plan['slug'] ?? '') === ($planEmpresa['plan_slug'] ?? '') ? 'current' : '' ?>">
            <?php if (($plan['slug'] ?? '') === 'basico'): ?>
                <div class="plan-panel-badge">Mas popular</div>
            <?php endif; ?>
            <div class="plan-panel-header">
                <h3><?= esc($plan['nombre']) ?></h3>
                <p class="plan-panel-price">$<?= number_format($plan['precio_mensual'], 0) ?><span>/mes</span></p>
                <p class="plan-panel-desc"><?= esc($plan['descripcion'] ?? '') ?></p>
            </div>
            <ul class="plan-panel-features">
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> <?= $plan['max_vacantes'] >= 999 ? 'Vacantes ilimitadas' : $plan['max_vacantes'] . ' vacantes activas' ?></li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> <?= $plan['max_postulaciones'] >= 999 ? 'Postulaciones ilimitadas' : $plan['max_postulaciones'] . ' postulaciones' ?></li>
                <li class="<?= $plan['destacar_vacantes'] ? '' : 'disabled' ?>">
                    <?php if ($plan['destacar_vacantes']): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <?php endif; ?>
                    Destacar vacantes
                </li>
                <li class="<?= $plan['ver_perfil_completo'] ? '' : 'disabled' ?>">
                    <?php if ($plan['ver_perfil_completo']): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <?php endif; ?>
                    Ver perfil completo de candidatos
                </li>
                <li class="<?= $plan['acceso_cvs'] ? '' : 'disabled' ?>">
                    <?php if ($plan['acceso_cvs']): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <?php endif; ?>
                    Acceso a CVs
                </li>
                <li class="<?= $plan['soporte_prioritario'] ? '' : 'disabled' ?>">
                    <?php if ($plan['soporte_prioritario']): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <?php endif; ?>
                    Soporte prioritario
                </li>
            </ul>
            <?php if (($plan['slug'] ?? '') === ($planEmpresa['plan_slug'] ?? '')): ?>
                <button class="btn btn-ghost btn-block" disabled>Tu plan actual</button>
            <?php elseif ($plan['precio_mensual'] > ($planEmpresa['precio_mensual'] ?? 0)): ?>
                <button class="btn btn-primary btn-block btn-upgrade" data-plan-id="<?= $plan['id'] ?>" data-plan-name="<?= esc($plan['nombre']) ?>">Mejorar a <?= esc($plan['nombre']) ?></button>
            <?php else: ?>
                <button class="btn btn-outline btn-block" disabled>No disponible</button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
