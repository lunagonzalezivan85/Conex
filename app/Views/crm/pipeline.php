<div class="page-header">
    <div>
        <h1>CRM Pipeline</h1>
        <p>Gestiona tus prospectos desde el primer contacto hasta el cierre.</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('modalNuevoLead').style.display='flex'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nuevo Lead
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="crm-pipeline">
    <?php foreach ($estados as $key => $label): ?>
        <div class="crm-column" data-estado="<?= $key ?>">
            <div class="crm-column-header">
                <h3><?= $label ?></h3>
                <span class="crm-count"><?= count($leadsPorEstado[$key]) ?></span>
            </div>
            <div class="crm-cards" data-estado="<?= $key ?>">
                <?php foreach ($leadsPorEstado[$key] as $lead): ?>
                    <div class="crm-card" data-lead-id="<?= $lead['id'] ?>" draggable="true">
                        <a href="<?= base_url('admin/crm/lead/' . $lead['id']) ?>" class="crm-card-link">
                            <div class="crm-card-title"><?= esc($lead['empresa_nombre'] ?? $lead['nombre_contacto']) ?></div>
                            <div class="crm-card-contact"><?= esc($lead['nombre_contacto']) ?></div>
                            <?php if ($lead['plan_nombre']): ?>
                                <span class="crm-tag"><?= esc($lead['plan_nombre']) ?></span>
                            <?php endif; ?>
                            <?php if ($lead['tipo'] === 'upsell'): ?>
                                <span class="crm-tag crm-tag-blue">Upsell</span>
                            <?php endif; ?>
                            <div class="crm-card-meta">
                                <span class="crm-origen"><?= esc($origenes[$lead['origen']] ?? $lead['origen']) ?></span>
                                <?php if ($lead['asesor_nombre']): ?>
                                    <span class="crm-asesor"><?= esc($lead['asesor_nombre'] . ' ' . substr($lead['asesor_apellido'] ?? '', 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($leadsPorEstado[$key])): ?>
                    <div class="crm-empty-column">Sin leads</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div id="modalNuevoLead" class="crm-modal" style="display:none;">
    <div class="crm-modal-content">
        <div class="crm-modal-header">
            <h2>Nuevo Lead</h2>
            <button class="crm-modal-close" onclick="document.getElementById('modalNuevoLead').style.display='none'">&times;</button>
        </div>
        <form action="<?= base_url('admin/crm/crear') ?>" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de Lead</label>
                    <select name="tipo" id="tipoLead">
                        <option value="nuevo">Nuevo prospecto</option>
                        <option value="upsell">Upsell (empresa existente)</option>
                    </select>
                </div>
                <div class="form-group" id="grupoEmpresaExistente" style="display:none;">
                    <label>Empresa existente *</label>
                    <select name="empresa_id" id="empresaSelect">
                        <option value="">Seleccionar empresa</option>
                        <?php foreach ($empresas as $e): ?>
                            <option value="<?= $e['id'] ?>"
                                data-nombre="<?= esc($e['contacto_nombre'] . ' ' . $e['contacto_apellido']) ?>"
                                data-email="<?= esc($e['email']) ?>"
                                data-telefono="<?= esc($e['telefono']) ?>"
                                data-empresa="<?= esc($e['razon_social']) ?>"
                                data-rubro="<?= esc($e['rubro']) ?>"
                                data-plan="<?= esc($e['plan_nombre'] ?? 'Gratis') ?>">
                                <?= esc($e['razon_social']) ?> (<?= esc($e['plan_nombre'] ?? 'Sin plan') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre de contacto *</label>
                    <input type="text" name="nombre_contacto" required>
                </div>
                <div class="form-group">
                    <label>Email de contacto *</label>
                    <input type="email" name="email_contacto" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Telefono</label>
                    <input type="text" name="telefono_contacto">
                </div>
                <div class="form-group">
                    <label>Nombre de empresa</label>
                    <input type="text" name="empresa_nombre">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Rubro</label>
                    <input type="text" name="rubro">
                </div>
                <div class="form-group">
                    <label>Tamano de empresa</label>
                    <select name="tamano_empresa">
                        <option value="">Seleccionar</option>
                        <option value="1-10">1-10 empleados</option>
                        <option value="11-50">11-50 empleados</option>
                        <option value="51-200">51-200 empleados</option>
                        <option value="201-500">201-500 empleados</option>
                        <option value="500+">500+ empleados</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Origen del contacto</label>
                    <select name="origen">
                        <?php foreach ($origenes as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Plan de interes</label>
                    <select name="plan_interes_id">
                        <option value="">Sin plan especifico</option>
                        <?php foreach ($planes as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['nombre']) ?> - $<?= $p['precio_mensual'] ?>/mes</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Asesor asignado</label>
                <select name="asesor_id">
                    <?php foreach ($asesores as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $a['id'] == $userId ? 'selected' : '' ?>><?= esc($a['nombre'] . ' ' . $a['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Notas</label>
                <textarea name="notas" rows="3"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('modalNuevoLead').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear Lead</button>
            </div>
        </form>
    </div>
</div>
