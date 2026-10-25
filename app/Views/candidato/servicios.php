<?php
$activo = !empty($candidato['ofrece_servicios']);
?>
<div class="srv-panel">

    <!-- Opt-in: aparecer en el directorio -->
    <div class="card srv-optin">
        <div class="srv-optin-info">
            <h3>Directorio publico de servicios</h3>
            <p>
                Personas particulares pueden encontrarte por oficio y zona
                (ubicacion aproximada — nunca tu direccion ni coordenadas).
                El solicitante deja sus datos; tu decides si aceptas y ves su contacto.
            </p>
            <?php if (!empty($pendientes)): ?>
                <p class="srv-pendientes-aviso">
                    Tienes <strong><?= (int)$pendientes ?></strong> solicitud(es) pendiente(s) —
                    <a href="<?= base_url('candidato/solicitudes') ?>">revisarlas</a>
                </p>
            <?php endif; ?>
        </div>
        <form action="<?= base_url('candidato/servicios/config') ?>" method="post" class="srv-optin-form">
            <?= csrf_field() ?>
            <label class="srv-switch">
                <input type="checkbox" name="ofrece_servicios" value="1" <?= $activo ? 'checked' : '' ?>
                       onchange="this.form.submit()">
                <span class="srv-switch-track"></span>
                <span class="srv-switch-label"><?= $activo ? 'Visible' : 'Oculto' ?></span>
            </label>
            <div class="form-group srv-zona-field">
                <label class="form-label">Zona aproximada publica</label>
                <input type="text" name="zona_servicio" class="form-control" maxlength="150"
                       placeholder="Ej: Ciudad Sandino, Zona 5" value="<?= esc($candidato['zona_servicio'] ?? '') ?>">
                <small>Barrio o zona, no tu direccion exacta.</small>
            </div>
            <button type="submit" class="btn btn-outline btn-sm">Guardar zona</button>
        </form>
    </div>

    <!-- Publicar nuevo servicio -->
    <div class="card">
        <h3 class="srv-card-h">Publicar un servicio</h3>
        <form action="<?= base_url('candidato/servicios/guardar') ?>" method="post">
            <?= csrf_field() ?>
            <div class="srv-form-grid">
                <div class="form-group">
                    <label class="form-label">Oficio *</label>
                    <input type="text" name="oficio" class="form-control" maxlength="100" required
                           placeholder="Ej: Electricista, Plomero, Albañil" value="<?= esc(old('oficio') ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" maxlength="100"
                           placeholder="<?= esc($candidato['ciudad'] ?? 'Managua') ?>" value="<?= esc(old('ciudad') ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Zona de cobertura</label>
                    <input type="text" name="zona" class="form-control" maxlength="150"
                           placeholder="Ej: Ciudad Sandino y alrededores" value="<?= esc(old('zona') ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Precio desde (USD)</label>
                    <input type="number" name="precio_desde" class="form-control" min="0" step="0.01"
                           placeholder="Opcional" value="<?= esc(old('precio_desde') ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Tipo de precio</label>
                    <select name="precio_tipo" class="form-control">
                        <option value="a_convenir">A convenir</option>
                        <option value="por_servicio">Por servicio</option>
                        <option value="por_hora">Por hora</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Descripcion</label>
                <textarea name="descripcion" class="form-control" rows="3" maxlength="2000"
                          placeholder="Que haces, experiencia, horarios de atencion..."><?= esc(old('descripcion') ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Publicar servicio</button>
        </form>
    </div>

    <!-- Mis servicios publicados -->
    <div class="card">
        <h3 class="srv-card-h">Mis servicios (<?= count($servicios) ?>)</h3>
        <?php if (!empty($servicios)): ?>
            <div class="srv-list">
                <?php foreach ($servicios as $s): ?>
                    <div class="srv-item <?= $s['estado'] === 'pausado' ? 'srv-item-pausado' : '' ?>">
                        <div class="srv-item-info">
                            <strong><?= esc($s['oficio']) ?></strong>
                            <span class="srv-item-meta">
                                <?= esc($s['zona'] ?: 'Zona por definir') ?> · <?= esc($s['ciudad'] ?: '') ?>
                                · <?= (int)$s['contactos'] ?> solicitud(es)
                            </span>
                            <?php if (!empty($s['descripcion'])): ?>
                                <span class="srv-item-desc"><?= esc(mb_substr($s['descripcion'], 0, 120)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="srv-item-actions">
                            <span class="badge <?= $s['estado'] === 'activo' ? 'badge-success' : 'badge-accent' ?>">
                                <?= $s['estado'] === 'activo' ? 'Activo' : 'Pausado' ?>
                            </span>
                            <form action="<?= base_url('candidato/servicios/estado/' . (int)$s['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline btn-sm">
                                    <?= $s['estado'] === 'activo' ? 'Pausar' : 'Activar' ?>
                                </button>
                            </form>
                            <form action="<?= base_url('candidato/servicios/eliminar/' . (int)$s['id']) ?>" method="post"
                                  onsubmit="return confirm('Eliminar este servicio?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-ghost btn-sm srv-del">Eliminar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="srv-empty">Aun no publicas servicios. Agrega tu primer oficio arriba y apareceras en el directorio.</p>
        <?php endif; ?>
    </div>
</div>
