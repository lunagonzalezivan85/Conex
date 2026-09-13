<div class="page-header">
    <div>
        <h1><?= esc($lead['empresa_nombre'] ?? $lead['nombre_contacto']) ?></h1>
        <p>Lead #<?= $lead['id'] ?> - Estado: <strong><?= esc($estados[$lead['estado']] ?? $lead['estado']) ?></strong></p>
    </div>
    <a href="<?= base_url('admin/crm') ?>" class="btn btn-ghost">Volver al pipeline</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="crm-detail-grid">
    <div class="crm-detail-main">
        <div class="dash-card">
            <div class="section-header">
                <h2>Informacion del Lead</h2>
                <button class="btn btn-ghost btn-sm" onclick="document.getElementById('formEditar').style.display='block'">Editar</button>
            </div>

            <div id="formEditar" style="display:none;">
                <form action="<?= base_url('admin/crm/actualizar/' . $lead['id']) ?>" method="post" style="margin-top:16px;">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre de contacto</label>
                            <input type="text" name="nombre_contacto" value="<?= esc($lead['nombre_contacto']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email_contacto" value="<?= esc($lead['email_contacto']) ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Telefono</label>
                            <input type="text" name="telefono_contacto" value="<?= esc($lead['telefono_contacto']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Empresa</label>
                            <input type="text" name="empresa_nombre" value="<?= esc($lead['empresa_nombre']) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Rubro</label>
                            <input type="text" name="rubro" value="<?= esc($lead['rubro']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Tamano</label>
                            <select name="tamano_empresa">
                                <option value="">Seleccionar</option>
                                <?php foreach (['1-10','11-50','51-200','201-500','500+'] as $t): ?>
                                    <option value="<?= $t ?>" <?= $lead['tamano_empresa'] === $t ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Origen</label>
                            <select name="origen">
                                <?php foreach ($origenes as $key => $labelO): ?>
                                    <option value="<?= $key ?>" <?= $lead['origen'] === $key ? 'selected' : '' ?>><?= $labelO ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado">
                                <?php foreach ($estados as $key => $labelE): ?>
                                    <option value="<?= $key ?>" <?= $lead['estado'] === $key ? 'selected' : '' ?>><?= $labelE ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Plan de interes</label>
                            <select name="plan_interes_id">
                                <option value="">Sin plan</option>
                                <?php foreach ($planes as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $lead['plan_interes_id'] == $p['id'] ? 'selected' : '' ?>><?= esc($p['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Asesor</label>
                            <select name="asesor_id">
                                <?php foreach ($asesores as $a): ?>
                                    <option value="<?= $a['id'] ?>" <?= $lead['asesor_id'] == $a['id'] ? 'selected' : '' ?>><?= esc($a['nombre'] . ' ' . $a['apellido']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notas</label>
                        <textarea name="notas" rows="3"><?= esc($lead['notas']) ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-ghost" onclick="document.getElementById('formEditar').style.display='none'">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>

            <div id="infoLead" style="margin-top:16px;">
                <div class="crm-info-grid">
                    <div><strong>Contacto:</strong> <?= esc($lead['nombre_contacto']) ?></div>
                    <div><strong>Email:</strong> <?= esc($lead['email_contacto']) ?></div>
                    <div><strong>Telefono:</strong> <?= esc($lead['telefono_contacto'] ?? '-') ?></div>
                    <div><strong>Empresa:</strong> <?= esc($lead['empresa_nombre'] ?? '-') ?></div>
                    <div><strong>Rubro:</strong> <?= esc($lead['rubro'] ?? '-') ?></div>
                    <div><strong>Tamano:</strong> <?= esc($lead['tamano_empresa'] ?? '-') ?></div>
                    <div><strong>Origen:</strong> <?= esc($origenes[$lead['origen']] ?? $lead['origen']) ?></div>
                    <div><strong>Plan:</strong> <?= esc($lead['plan_nombre'] ?? 'Sin plan') ?></div>
                    <div><strong>Asesor:</strong> <?= esc(($lead['asesor_nombre'] ?? '') . ' ' . ($lead['asesor_apellido'] ?? '')) ?></div>
                    <div><strong>Tipo:</strong> <?= $lead['tipo'] === 'upsell' ? 'Upsell' : 'Nuevo' ?></div>
                </div>
                <?php if ($lead['notas']): ?>
                    <div style="margin-top:12px;"><strong>Notas:</strong><br><?= esc($lead['notas']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="dash-card" style="margin-top:20px;">
            <div class="section-header">
                <h2>Seguimientos</h2>
            </div>
            <form action="<?= base_url('admin/crm/seguimiento/' . $lead['id']) ?>" method="post" style="margin-bottom:20px;">
                <div class="form-row">
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="tipo_contacto">
                            <?php foreach ($tiposSeguimiento as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Proxima accion</label>
                        <input type="datetime-local" name="fecha_proxima_accion">
                    </div>
                </div>
                <div class="form-group">
                    <label>Comentario</label>
                    <textarea name="comentario" rows="2" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Agregar seguimiento</button>
            </form>

            <?php if (!empty($seguimientos)): ?>
                <div class="crm-timeline">
                    <?php foreach ($seguimientos as $s): ?>
                        <div class="crm-timeline-item">
                            <div class="crm-timeline-type"><?= esc($tiposSeguimiento[$s['tipo_contacto']] ?? $s['tipo_contacto']) ?></div>
                            <div class="crm-timeline-content">
                                <p><?= esc($s['comentario']) ?></p>
                                <span class="crm-timeline-meta">
                                    <?= esc($s['asesor_nombre'] ?? '') ?> - <?= date('d/m/Y H:i', strtotime($s['created_at'])) ?>
                                    <?php if ($s['fecha_proxima_accion']): ?>
                                        | Proxima: <?= date('d/m/Y H:i', strtotime($s['fecha_proxima_accion'])) ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color:var(--text-muted);">Sin seguimientos registrados.</p>
            <?php endif; ?>
        </div>

        <div class="dash-card" style="margin-top:20px;">
            <div class="section-header">
                <h2>Servicios Adicionales</h2>
            </div>
            <form action="<?= base_url('admin/crm/servicio/' . $lead['id']) ?>" method="post" style="margin-bottom:20px;">
                <div class="form-row">
                    <div class="form-group">
                        <label>Servicio</label>
                        <select name="servicio">
                            <?php foreach ($serviciosDisponibles as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cantidad</label>
                        <input type="number" name="cantidad" value="1" min="1">
                    </div>
                    <div class="form-group">
                        <label>Precio</label>
                        <input type="number" name="precio" value="0" step="0.01">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Agregar servicio</button>
            </form>

            <?php if (!empty($servicios)): ?>
                <table class="verif-table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Incluido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios as $s): ?>
                            <tr>
                                <td><?= esc($serviciosDisponibles[$s['servicio']] ?? $s['servicio']) ?></td>
                                <td><?= $s['cantidad'] ?></td>
                                <td>$<?= $s['precio'] ?></td>
                                <td><?= $s['incluido'] ? 'Si' : 'No' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color:var(--text-muted);">Sin servicios adicionales.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="crm-detail-side">
        <div class="dash-card">
            <h2>Convertir a Empresa</h2>
            <p style="color:var(--text-muted);font-size:14px;margin-bottom:16px;">Al cerrar el lead, se crea la empresa, el usuario y el contrato.</p>
            <form action="<?= base_url('admin/crm/convertir/' . $lead['id']) ?>" method="post">
                <div class="form-group">
                    <label>Plan</label>
                    <select name="plan_id" required>
                        <?php foreach ($planes as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['nombre']) ?> - $<?= $p['precio_mensual'] ?>/mes</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Facturacion</label>
                    <select name="tipo_facturacion">
                        <option value="mensual">Mensual</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;" <?= $lead['estado'] === 'cerrado' ? 'disabled' : '' ?>>
                    <?= $lead['estado'] === 'cerrado' ? 'Ya convertido' : 'Cerrar y crear contrato' ?>
                </button>
            </form>
        </div>
    </div>
</div>
