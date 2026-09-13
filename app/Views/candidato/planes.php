<div class="page-header">
    <div>
        <h1>Planes disponibles</h1>
        <p>Mejora tu plan para destacar tu perfil y acceder a mas oportunidades.</p>
    </div>
</div>

<?php if (!empty($planActual)): ?>
<div class="dash-card" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:16px;font-weight:600;margin-bottom:4px;">Tu plan actual: <?= esc($planActual['plan_nombre']) ?></h3>
            <p style="font-size:13px;color:var(--text-secondary);">
                <?php if (($planActual['plan_slug'] ?? '') !== 'premium'): ?>
                    $<?= number_format($planActual['precio_mensual'] ?? 0, 0) ?>/mes
                <?php else: ?>
                    Plan maximo
                <?php endif; ?>
            </p>
        </div>
        <span class="verif-badge verif-badge-green">Activo</span>
    </div>
</div>
<?php endif; ?>

<div class="planes-panel-grid">
    <?php foreach ($planes as $plan): ?>
        <div class="plan-panel-card <?= ($plan['slug'] ?? '') === ($planActual['plan_slug'] ?? '') ? 'current' : '' ?>">
            <?php if (($plan['slug'] ?? '') === 'plus'): ?>
                <div class="plan-panel-badge">Mas popular</div>
            <?php endif; ?>
            <div class="plan-panel-header">
                <h3><?= esc($plan['nombre']) ?></h3>
                <p class="plan-panel-price">$<?= number_format($plan['precio_mensual'], 0) ?><span>/mes</span></p>
                <p class="plan-panel-desc"><?= esc($plan['descripcion'] ?? '') ?></p>
            </div>
            <ul class="plan-panel-features">
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Buscar y postularse a vacantes</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Crear perfil profesional</li>
                <li class="<?= $plan['destacar_vacantes'] ? '' : 'disabled' ?>">
                    <?php if ($plan['destacar_vacantes']): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <?php endif; ?>
                    Destacar perfil ante empresas
                </li>
                <li class="<?= $plan['ver_perfil_completo'] ? '' : 'disabled' ?>">
                    <?php if ($plan['ver_perfil_completo']): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <?php endif; ?>
                    Perfil verificado con insignia
                </li>
                <li class="<?= $plan['acceso_cvs'] ? '' : 'disabled' ?>">
                    <?php if ($plan['acceso_cvs']): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <?php endif; ?>
                    Asesoria de CV personalizada
                </li>
                <li class="<?= $plan['soporte_prioritario'] ? '' : 'disabled' ?>">
                    <?php if ($plan['soporte_prioritario']): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <?php endif; ?>
                    Soporte prioritario 24/7
                </li>
            </ul>
            <?php if (($plan['slug'] ?? '') === ($planActual['plan_slug'] ?? '')): ?>
                <button class="btn btn-ghost btn-block" disabled>Tu plan actual</button>
            <?php elseif ($plan['precio_mensual'] > ($planActual['precio_mensual'] ?? 0)): ?>
                <button class="btn btn-primary btn-block btn-upgrade" data-plan-id="<?= $plan['id'] ?>" data-plan-name="<?= esc($plan['nombre']) ?>">Mejorar a <?= esc($plan['nombre']) ?></button>
            <?php else: ?>
                <button class="btn btn-outline btn-block" disabled>No disponible</button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
