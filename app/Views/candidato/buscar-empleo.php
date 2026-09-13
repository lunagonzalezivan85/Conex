<?php
$filtros = $filtros ?? ['q' => '', 'categoria' => '', 'modalidad' => '', 'tipo_contrato' => '', 'ciudad' => ''];
?>
<div class="dash-card" style="margin-bottom: 24px;">
    <div class="section-header" style="margin-bottom: 20px;">
        <h2>Buscar Vacantes</h2>
        <p style="font-size: 14px; color: var(--text-secondary); margin-top: 4px;">Encuentra las mejores oportunidades laborales para ti.</p>
    </div>

    <form action="<?= base_url('candidato/buscar-empleo') ?>" method="get" id="filterForm" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
            <label class="form-label">Buscar</label>
            <input type="text" name="q" class="form-control" placeholder="Titulo, palabra clave..." value="<?= esc($filtros['q']) ?>">
        </div>
        <div class="form-group" style="min-width:160px;margin-bottom:0;">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-control">
                <option value="">Todas</option>
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $filtros['categoria'] == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="form-group" style="min-width:140px;margin-bottom:0;">
            <label class="form-label">Modalidad</label>
            <select name="modalidad" class="form-control">
                <option value="">Todas</option>
                <option value="presencial" <?= $filtros['modalidad'] === 'presencial' ? 'selected' : '' ?>>Presencial</option>
                <option value="remoto" <?= $filtros['modalidad'] === 'remoto' ? 'selected' : '' ?>>Remoto</option>
                <option value="hibrido" <?= $filtros['modalidad'] === 'hibrido' ? 'selected' : '' ?>>Hibrido</option>
            </select>
        </div>
        <div class="form-group" style="min-width:140px;margin-bottom:0;">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad" class="form-control" placeholder="Ciudad..." value="<?= esc($filtros['ciudad']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            Buscar
        </button>
        <a href="<?= base_url('candidato/buscar-empleo') ?>" class="btn btn-ghost">Limpiar</a>
    </form>
</div>

<p style="font-size:14px;color:var(--text-secondary);margin-bottom:16px;"><?= count($vacantes) ?> vacante(s) encontrada(s)</p>

<div class="resultados-list" style="display:flex;flex-direction:column;gap:16px;">
    <?php if (!empty($vacantes)): ?>
        <?php foreach ($vacantes as $v): ?>
            <a href="<?= base_url('candidato/vacante/' . $v['slug']) ?>" class="dash-card vacante-item" style="display:flex;align-items:center;gap:20px;color:inherit;text-decoration:none;transition:box-shadow 0.2s;padding:20px;">
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
                        <?php if (isset($v['modalidad']) && $v['modalidad']): ?>
                            <span style="font-size:11px;padding:3px 10px;border-radius:12px;background:#e0e7ff;color:#4361EE;font-weight:500;"><?= ucfirst($v['modalidad']) ?></span>
                        <?php endif; ?>
                        <?php if (isset($v['salario_min']) && $v['salario_min']): ?>
                            <span style="font-size:11px;padding:3px 10px;border-radius:12px;background:#dcfce7;color:#16a34a;font-weight:500;">$<?= number_format($v['salario_min'], 0) ?> - $<?= number_format($v['salario_max'] ?? $v['salario_min'], 0) ?> USD</span>
                        <?php endif; ?>
                        <?php if (isset($v['anios_experiencia']) && $v['anios_experiencia'] > 0): ?>
                            <span style="font-size:11px;padding:3px 10px;border-radius:12px;background:#fef3c7;color:#d97706;font-weight:500;"><?= $v['anios_experiencia'] ?> año(s) exp.</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <p style="font-size:12px;color:var(--text-muted,#9ca3af);">
                        <?= isset($v['fecha_publicacion']) ? date('d M', strtotime($v['fecha_publicacion'])) : '' ?>
                    </p>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="dash-card empty-state" style="text-align:center;padding:64px 20px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin-bottom:16px;color:var(--text-muted,#9ca3af);"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <h3 style="font-size:18px;font-weight:600;margin-bottom:8px;">No se encontraron vacantes</h3>
            <p style="color:var(--text-secondary);margin-bottom:20px;">Intenta ajustar los filtros de busqueda.</p>
            <a href="<?= base_url('candidato/buscar-empleo') ?>" class="btn btn-outline">Limpiar filtros</a>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($pager) && $pager): ?>
    <div style="display:flex;justify-content:center;margin-top:32px;">
        <?= $pager->links() ?>
    </div>
<?php endif; ?>
