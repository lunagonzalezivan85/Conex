<div class="page-header">
    <div>
        <h1>Validación de Prestadores de Servicios</h1>
        <p>Valida las referencias de los prestadores de oficios independientes (albañiles, electricistas, fontaneros, etc.) y otórgales el sello de verificación oficial.</p>
    </div>
    <a href="<?= base_url('servicios') ?>" target="_blank" rel="noopener" class="btn btn-outline">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;vertical-align:middle;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Ver Directorio Público de Servicios &#8599;
    </a>
</div>

<?php if (session()->getFlashdata('info')): ?>
    <div class="dash-card" style="border-left:4px solid #22c55e;margin-bottom:16px;">
        <p style="color:#22c55e;margin:0;font-weight:600;"><?= esc(session()->getFlashdata('info')) ?></p>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="dash-card" style="border-left:4px solid #ef4444;margin-bottom:16px;">
        <p style="color:#ef4444;margin:0;font-weight:600;"><?= esc(session()->getFlashdata('error')) ?></p>
    </div>
<?php endif; ?>

<?php
    $total = count($prestadores ?? []);
    $verificados = 0;
    foreach ($prestadores as $p) {
        if (!empty($p['prestador_verificado'])) $verificados++;
    }
    $pendientes = $total - $verificados;
?>

<div class="agente-stats-grid" style="margin-bottom:20px;">
    <div class="agente-kpi-card">
        <div class="agente-kpi-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        </div>
        <div class="agente-kpi-info">
            <span class="agente-kpi-num"><?= $total ?></span>
            <span class="agente-kpi-label">Prestadores Registrados</span>
        </div>
    </div>
    <div class="agente-kpi-card">
        <div class="agente-kpi-icon orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="agente-kpi-info">
            <span class="agente-kpi-num"><?= $pendientes ?></span>
            <span class="agente-kpi-label">Pendientes por Validar</span>
        </div>
    </div>
    <div class="agente-kpi-card">
        <div class="agente-kpi-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="agente-kpi-info">
            <span class="agente-kpi-num"><?= $verificados ?></span>
            <span class="agente-kpi-label">Con Sello Verificado</span>
        </div>
    </div>
</div>

<div class="prestadores-grid">
    <?php if (!empty($prestadores)): ?>
        <?php foreach ($prestadores as $prestador): ?>
            <?php
                $fullName = trim($prestador['nombre'] . ' ' . $prestador['apellido']);
                $initial = strtoupper(substr($prestador['nombre'] ?: 'P', 0, 1));
                $isVerif = !empty($prestador['prestador_verificado']);
                $oficios = $prestador['oficios_lista'] ?: ($prestador['profesion'] ?: 'Oficio no especificado');
            ?>
            <div class="prestador-card" style="<?= $isVerif ? 'border-color:#86EFAC;' : '' ?>">
                <div>
                    <div class="prestador-top">
                        <div class="prestador-avatar" style="<?= $isVerif ? 'background:#DCFCE7;color:#15803D;' : '' ?>">
                            <?= $initial ?>
                        </div>
                        <div class="prestador-meta" style="flex:1;">
                            <h4><?= esc($fullName) ?></h4>
                            <p>
                                <strong>Oficios:</strong> <?= esc($oficios) ?>
                            </p>
                            <div style="font-size:12.5px;color:var(--text-secondary);display:flex;gap:8px;flex-wrap:wrap;">
                                <span>📍 <?= esc($prestador['ciudad'] ?: 'Nicaragua') ?></span>
                                <?php if (!empty($prestador['telefono'])): ?>
                                    &middot; <span>📞 <a href="tel:<?= esc($prestador['telefono']) ?>"><?= esc($prestador['telefono']) ?></a></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:12px;">
                        <?php if ($isVerif): ?>
                            <span class="badge badge-success" style="display:inline-flex;align-items:center;gap:4px;">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                Prestador Verificado por CONEX
                            </span>
                        <?php else: ?>
                            <span class="badge badge-warning">
                                Sin verificación (Llamar para validar referencias)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="prestador-actions">
                    <a href="<?= base_url('admin/postulantes/perfil/' . $prestador['candidato_id']) ?>" class="btn btn-ghost btn-sm">
                        Ver perfil
                    </a>

                    <form action="<?= base_url('agente/prestadores/verificar/' . $prestador['candidato_id']) ?>" method="POST" style="margin:0;">
                        <?= csrf_field() ?>
                        <?php if ($isVerif): ?>
                            <button type="submit" class="btn btn-outline btn-sm" style="color:#DC2626;border-color:#FCA5A5;">
                                Quitar verificación
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary btn-sm" style="background:#16A34A;border-color:#16A34A;">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
                                Otorgar Verificación
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="dash-card" style="grid-column:1/-1;text-align:center;padding:40px;">
            <h3>No hay prestadores de servicios registrados</h3>
            <p style="color:var(--text-secondary);font-size:14px;">Los candidatos que marquen que ofrecen servicios técnicos u oficios aparecerán aquí.</p>
        </div>
    <?php endif; ?>
</div>
