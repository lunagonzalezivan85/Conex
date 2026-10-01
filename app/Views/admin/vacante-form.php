<?php
$v = $vacante ?? null;
$esEdicion = !empty($v);
$accion = $esEdicion
    ? base_url('admin/vacantes/actualizar/' . $v['id'])
    : base_url('admin/vacantes/guardar');
$val = fn(string $campo, $default = '') => esc(old($campo, $v[$campo] ?? $default));
?>

<div class="page-header">
    <div>
        <h1><?= $esEdicion ? 'Editar Vacante' : 'Captar Vacante' ?></h1>
        <p><?= $esEdicion ? 'Actualiza los datos de la vacante.' : 'Registra una vacante vista en Facebook u otra fuente, en modo borrador.' ?></p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="dash-card" style="border-left:4px solid #ef4444;margin-bottom:16px;">
        <p style="color:#ef4444;margin:0;"><?= esc(session()->getFlashdata('error')) ?></p>
    </div>
<?php endif; ?>

<form action="<?= $accion ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Datos de captacion</h2>
            <p style="font-size:13px;color:var(--text-secondary);">De donde viene la vacante y como contactar a la empresa.</p>
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
            <div class="form-group" style="flex:1;min-width:220px;">
                <label class="form-label">Empresa registrada</label>
                <select name="empresa_id" class="form-control">
                    <option value="">— No registrada (captada externa) —</option>
                    <?php foreach ($empresas as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= old('empresa_id', $v['empresa_id'] ?? '') == $e['id'] ? 'selected' : '' ?>>
                            <?= esc($e['razon_social']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex:1;min-width:220px;">
                <label class="form-label">Nombre externo (si no esta registrada)</label>
                <input type="text" name="empresa_externa" class="form-control" value="<?= $val('empresa_externa') ?>" placeholder="Ej: Pupuseria El Buen Sabor">
            </div>
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
            <div class="form-group" style="flex:1;min-width:200px;">
                <label class="form-label">Contacto externo (tel/email)</label>
                <input type="text" name="contacto_externo" class="form-control" value="<?= $val('contacto_externo') ?>" placeholder="Ej: 8888-1234">
            </div>
            <div class="form-group" style="min-width:160px;">
                <label class="form-label">Origen</label>
                <select name="origen" class="form-control">
                    <?php foreach (['facebook' => 'Facebook', 'manual' => 'Manual', 'whatsapp' => 'WhatsApp', 'otro' => 'Otro'] as $k => $lbl): ?>
                        <option value="<?= $k ?>" <?= old('origen', $v['origen'] ?? 'manual') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex:1;min-width:220px;">
                <label class="form-label">URL de origen (post de FB)</label>
                <input type="url" name="origen_url" class="form-control" value="<?= $val('origen_url') ?>" placeholder="https://facebook.com/...">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Poster / imagen del anuncio</label>
            <?php if (!empty($v['poster'])): ?>
                <div style="margin-bottom:8px;">
                    <img src="<?= base_url('uploads/' . $v['poster']) ?>" alt="Poster actual" style="max-width:220px;border-radius:12px;border:1px solid var(--border-color,#e5e7eb);">
                </div>
            <?php endif; ?>
            <input type="file" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp">
            <small style="color:var(--text-secondary);">JPG, PNG o WebP · max 5MB</small>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Informacion de la vacante</h2>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label">Titulo de la vacante *</label>
            <input type="text" name="titulo" class="form-control" required value="<?= $val('titulo') ?>" placeholder="Ej: Cajero(a) medio tiempo">
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
            <div class="form-group" style="flex:1;min-width:200px;">
                <label class="form-label">Categoria *</label>
                <select name="categoria_id" class="form-control" required>
                    <option value="">Selecciona...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= old('categoria_id', $v['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= esc($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="min-width:170px;">
                <label class="form-label">Modalidad</label>
                <select name="modalidad" class="form-control">
                    <?php foreach (['presencial' => 'Presencial', 'remoto' => 'Remoto', 'hibrido' => 'Hibrido'] as $k => $lbl): ?>
                        <option value="<?= $k ?>" <?= old('modalidad', $v['modalidad'] ?? 'presencial') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="min-width:170px;">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <?php foreach (['borrador' => 'Borrador', 'publicada' => 'Publicada', 'suspendida' => 'Suspendida', 'cerrada' => 'Cerrada'] as $k => $lbl): ?>
                        <option value="<?= $k ?>" <?= old('estado', $v['estado'] ?? 'borrador') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
            <div class="form-group" style="flex:1;min-width:150px;">
                <label class="form-label">Ciudad</label>
                <input type="text" name="ciudad" class="form-control" value="<?= $val('ciudad') ?>" placeholder="Ej: Managua">
            </div>
            <div class="form-group" style="flex:1;min-width:150px;">
                <label class="form-label">Region / Departamento</label>
                <input type="text" name="region" class="form-control" value="<?= $val('region') ?>" placeholder="Ej: Managua">
            </div>
            <div class="form-group" style="min-width:110px;">
                <label class="form-label">Moneda</label>
                <select name="moneda" class="form-control">
                    <option value="USD" <?= old('moneda', $v['moneda'] ?? 'USD') === 'USD' ? 'selected' : '' ?>>USD</option>
                    <option value="NIO" <?= old('moneda', $v['moneda'] ?? '') === 'NIO' ? 'selected' : '' ?>>NIO</option>
                </select>
            </div>
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;">
            <div class="form-group" style="flex:1;min-width:140px;">
                <label class="form-label">Salario minimo</label>
                <input type="number" name="salario_min" class="form-control" min="0" value="<?= $val('salario_min') ?>" placeholder="0">
            </div>
            <div class="form-group" style="flex:1;min-width:140px;">
                <label class="form-label">Salario maximo</label>
                <input type="number" name="salario_max" class="form-control" min="0" value="<?= $val('salario_max') ?>" placeholder="0">
            </div>
            <div class="form-group" style="min-width:120px;">
                <label class="form-label">Años exp.</label>
                <input type="number" name="anios_experiencia" class="form-control" min="0" value="<?= $val('anios_experiencia', 0) ?>">
            </div>
            <div class="form-group" style="min-width:120px;">
                <label class="form-label">Vacantes</label>
                <input type="number" name="vacantes_disponibles" class="form-control" min="1" value="<?= $val('vacantes_disponibles', 1) ?>">
            </div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Descripcion</h2>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label">Descripcion de la vacante *</label>
            <textarea name="descripcion" class="form-control" rows="5" required placeholder="Copia el texto del anuncio o redacta la descripcion..."><?= $val('descripcion') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Funciones principales</label>
            <textarea name="funciones" class="form-control" rows="4" placeholder="Funciones del puesto (opcional)..."><?= $val('funciones') ?></textarea>
        </div>
    </div>

    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
            <?= $esEdicion ? 'Guardar cambios' : 'Guardar vacante' ?>
        </button>
        <a href="<?= base_url('admin/vacantes') ?>" class="btn btn-ghost">Cancelar</a>
    </div>
</form>
