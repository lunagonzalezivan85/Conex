<?php
$tab = $tab ?? 'empresa';
$cfg = $config ?? [];
$val = function($k) use ($cfg) { return esc($cfg[$k] ?? ''); };
?>

<?php if (session()->getFlashdata('info')): ?>
    <div class="alert alert-success" style="margin-bottom:16px;"><?= session()->getFlashdata('info') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error" style="margin-bottom:16px;"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- Tabs -->
<div class="cfg-tabs">
    <a href="<?= base_url('admin/config?tab=empresa') ?>" class="cfg-tab <?= $tab === 'empresa' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Info de la empresa
    </a>
    <a href="<?= base_url('admin/config?tab=ia') ?>" class="cfg-tab <?= $tab === 'ia' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a4 4 0 0 1 4 4c0 1.5-.8 2.8-2 3.5V11h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-1v.5a4 4 0 0 1-8 0V17H6a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2h2V9.5A4 4 0 0 1 8 6a4 4 0 0 1 4-4z"/></svg>
        Configuracion IA
    </a>
</div>

<?php if ($tab === 'empresa'): ?>
<!-- ===== TAB: Info de la empresa ===== -->
<div class="dash-card">
    <h3 style="font-size:16px;font-weight:600;margin-bottom:20px;">Informacion de la empresa</h3>
    <form action="<?= base_url('admin/config/empresa') ?>" method="post">
        <?= csrf_field() ?>
        <div class="cfg-grid">
            <div class="form-group">
                <label class="form-label">Nombre de la empresa</label>
                <input type="text" name="empresa_nombre" class="form-control" value="<?= $val('empresa_nombre') ?>" placeholder="CONEX">
            </div>
            <div class="form-group">
                <label class="form-label">RUC / Identificacion fiscal</label>
                <input type="text" name="empresa_ruc" class="form-control" value="<?= $val('empresa_ruc') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Telefono</label>
                <input type="text" name="empresa_telefono" class="form-control" value="<?= $val('empresa_telefono') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email de contacto</label>
                <input type="email" name="empresa_email" class="form-control" value="<?= $val('empresa_email') ?>">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Direccion</label>
                <input type="text" name="empresa_direccion" class="form-control" value="<?= $val('empresa_direccion') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Ciudad</label>
                <input type="text" name="empresa_ciudad" class="form-control" value="<?= $val('empresa_ciudad') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Region / Departamento</label>
                <input type="text" name="empresa_region" class="form-control" value="<?= $val('empresa_region') ?>">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Sitio web</label>
                <input type="url" name="empresa_sitio_web" class="form-control" value="<?= $val('empresa_sitio_web') ?>" placeholder="https://...">
            </div>
        </div>

        <h4 style="font-size:14px;font-weight:600;margin:20px 0 12px;color:var(--text-secondary);">Redes sociales</h4>
        <div class="cfg-grid cfg-grid-3">
            <div class="form-group">
                <label class="form-label">Facebook</label>
                <input type="url" name="empresa_facebook" class="form-control" value="<?= $val('empresa_facebook') ?>" placeholder="https://facebook.com/...">
            </div>
            <div class="form-group">
                <label class="form-label">Instagram</label>
                <input type="url" name="empresa_instagram" class="form-control" value="<?= $val('empresa_instagram') ?>" placeholder="https://instagram.com/...">
            </div>
            <div class="form-group">
                <label class="form-label">LinkedIn</label>
                <input type="url" name="empresa_linkedin" class="form-control" value="<?= $val('empresa_linkedin') ?>" placeholder="https://linkedin.com/...">
            </div>
        </div>

        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>

<?php else: ?>
<!-- ===== TAB: Configuracion IA ===== -->
<div class="dash-card" style="margin-bottom:24px;">
    <h3 style="font-size:16px;font-weight:600;margin-bottom:6px;">
        <?= !empty($modeloEditar) ? 'Editar modelo IA' : 'Agregar modelo IA' ?>
    </h3>
    <p style="font-size:13px;color:var(--text-secondary);margin-bottom:18px;">Configura los proveedores de IA para analisis de CV, matching y asistente.</p>

    <form action="<?= base_url('admin/config/ia') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= esc($modeloEditar['id'] ?? '') ?>">

        <div class="cfg-grid cfg-grid-3">
            <div class="form-group">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required value="<?= esc($modeloEditar['nombre'] ?? '') ?>" placeholder="Ej: GPT-4o Analisis">
            </div>
            <div class="form-group">
                <label class="form-label">Proveedor</label>
                <select name="proveedor" class="form-control" required>
                    <?php $prov = $modeloEditar['proveedor'] ?? ''; ?>
                    <option value="">Seleccionar...</option>
                    <?php foreach (['openai' => 'OpenAI', 'anthropic' => 'Anthropic', 'google' => 'Google Gemini', 'azure' => 'Azure OpenAI', 'ollama' => 'Ollama (local)', 'otro' => 'Otro'] as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $prov === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Proposito</label>
                <select name="proposito" class="form-control">
                    <?php $prop = $modeloEditar['proposito'] ?? 'general'; ?>
                    <?php foreach ($propositos as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $prop === $k ? 'selected' : '' ?>><?= esc($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="cfg-grid">
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">URL base de la API</label>
                <input type="url" name="url" class="form-control" required value="<?= esc($modeloEditar['url'] ?? '') ?>" placeholder="https://api.openai.com/v1">
            </div>
            <div class="form-group">
                <label class="form-label">Modelo</label>
                <input type="text" name="modelo" class="form-control" required value="<?= esc($modeloEditar['modelo'] ?? '') ?>" placeholder="gpt-4o, claude-3-5-sonnet, gemini-1.5-pro">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">API Key <?= !empty($modeloEditar) ? '(dejar vacio para conservar)' : '' ?></label>
                <input type="password" name="api_key" class="form-control" value="" placeholder="sk-..." autocomplete="new-password">
            </div>
        </div>

        <div class="form-group" style="display:flex;align-items:center;gap:8px;margin-top:8px;">
            <input type="checkbox" name="activo" id="iaActivo" value="1" <?= (!isset($modeloEditar['activo']) || $modeloEditar['activo']) ? 'checked' : '' ?>>
            <label for="iaActivo" style="margin:0;font-size:14px;">Modelo activo</label>
        </div>

        <div style="margin-top:16px;display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary"><?= !empty($modeloEditar) ? 'Actualizar' : 'Agregar modelo' ?></button>
            <?php if (!empty($modeloEditar)): ?>
                <a href="<?= base_url('admin/config?tab=ia') ?>" class="btn btn-ghost">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Lista de modelos -->
<div class="dash-card">
    <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">Modelos configurados (<?= count($modelosIa) ?>)</h3>
    <?php if (empty($modelosIa)): ?>
        <p style="color:var(--text-secondary);font-size:14px;">No hay modelos de IA configurados todavia.</p>
    <?php else: ?>
        <div class="cfg-ia-list">
            <?php foreach ($modelosIa as $m): ?>
                <div class="cfg-ia-item <?= $m['activo'] ? '' : 'inactivo' ?>">
                    <div class="cfg-ia-info">
                        <div class="cfg-ia-nombre">
                            <?= esc($m['nombre']) ?>
                            <?php if (!$m['activo']): ?><span class="cfg-ia-tag-off">Inactivo</span><?php endif; ?>
                        </div>
                        <div class="cfg-ia-meta">
                            <span class="cfg-ia-tag"><?= esc(ucfirst($m['proveedor'])) ?></span>
                            <span class="cfg-ia-tag cfg-ia-tag-prop"><?= esc($propositos[$m['proposito']] ?? $m['proposito']) ?></span>
                            <span class="cfg-ia-modelo"><?= esc($m['modelo']) ?></span>
                        </div>
                        <div class="cfg-ia-url"><?= esc($m['url']) ?></div>
                    </div>
                    <div class="cfg-ia-actions">
                        <a href="<?= base_url('admin/config?tab=ia&editar=' . $m['id']) ?>" class="btn btn-ghost btn-sm">Editar</a>
                        <a href="<?= base_url('admin/config/ia/eliminar/' . $m['id']) ?>" class="btn btn-ghost btn-sm cfg-btn-danger" onclick="return confirm('Eliminar este modelo?');">Eliminar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
