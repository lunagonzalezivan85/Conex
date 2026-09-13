<div class="page-header">
    <div>
        <h1>Verificacion de Postulantes</h1>
        <p>Revisa cuantas vacantes aplica cada candidato y verifica su documentacion.</p>
    </div>
</div>

<div class="dash-card">
    <div class="section-header" style="margin-bottom:20px;">
        <h2>Postulantes</h2>
    </div>

    <?php if (!empty($postulantes)): ?>
        <div class="verif-table-wrap">
            <table class="verif-table">
                <thead>
                    <tr>
                        <th>Candidato</th>
                        <th>Email</th>
                        <th>Profesion</th>
                        <th style="text-align:center;">Vacantes</th>
                        <th style="text-align:center;">Verificados</th>
                        <th style="text-align:center;">En verificacion</th>
                        <th style="text-align:center;">Estado empresa</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($postulantes as $p): ?>
                        <tr>
                            <td>
                                <div class="verif-candidato-name">
                                    <?= esc($p['nombre'] . ' ' . $p['apellido']) ?>
                                </div>
                            </td>
                            <td><?= esc($p['email']) ?></td>
                            <td><?= esc($p['profesion'] ?? '-') ?></td>
                            <td style="text-align:center;">
                                <span class="verif-badge verif-badge-blue"><?= $p['total_vacantes'] ?></span>
                            </td>
                            <td style="text-align:center;">
                                <?php if ($p['total_verificados'] > 0): ?>
                                <span class="verif-badge verif-badge-green"><?= $p['total_verificados'] ?></span>
                                <?php else: ?>
                                <span class="verif-badge" style="background:#f3f4f6;color:#9ca3af;">0</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <?php if ($p['total_en_verificacion'] > 0): ?>
                                <span class="verif-badge verif-badge-orange"><?= $p['total_en_verificacion'] ?></span>
                                <?php else: ?>
                                <span class="verif-badge" style="background:#f3f4f6;color:#9ca3af;">0</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <?php
                                    $estadosEmp = [];
                                    if ($p['total_contratados'] > 0) $estadosEmp[] = '<span class="verif-badge verif-badge-green">Contratado</span>';
                                    if ($p['total_en_proceso_empresa'] > 0) $estadosEmp[] = '<span class="verif-badge verif-badge-blue">En proceso</span>';
                                    if ($p['total_rechazados'] > 0) $estadosEmp[] = '<span class="verif-badge" style="background:#fee2e2;color:#ef4444;">Rechazado</span>';
                                    if (empty($estadosEmp)) $estadosEmp[] = '<span class="verif-badge" style="background:#f3f4f6;color:#9ca3af;">Pendiente</span>';
                                    echo implode(' ', $estadosEmp);
                                ?>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                                    <?php if ($p['total_verificados'] > 0 && $p['total_en_verificacion'] == 0): ?>
                                        <span class="verif-badge verif-badge-green">Verificado</span>
                                    <?php elseif ($p['total_en_verificacion'] > 0): ?>
                                        <span class="verif-badge verif-badge-orange">En verificacion</span>
                                    <?php else: ?>
                                        <span class="verif-badge" style="background:#f3f4f6;color:#9ca3af;">Sin verificar</span>
                                    <?php endif; ?>
                                    <a href="<?= base_url('admin/postulantes/perfil/' . $p['candidato_id']) ?>" class="btn btn-ghost btn-sm">
                                        Ver perfil
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <h3>No hay postulantes</h3>
            <p>Cuando los candidatos comiencen a postularse, apareceran aqui para verificacion.</p>
        </div>
    <?php endif; ?>
</div>
