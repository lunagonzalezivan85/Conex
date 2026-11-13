<div class="page-header">
    <div>
        <h1>Consola de Verificación de Candidatos</h1>
        <p>Valida los requisitos, documentos y antecedentes de los candidatos antes de presentarlos a las empresas contratantes.</p>
    </div>
</div>

<div class="dash-card" style="margin-bottom:20px;padding:16px 20px;">
    <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;justify-content:space-between;">
        <div style="flex:1;min-width:260px;">
            <input type="text" id="verifSearchInput" class="agente-input" placeholder="Buscar por candidato, vacante, empresa o profesión...">
        </div>
        <div class="role-tabs" id="verifTabs">
            <button type="button" class="role-tab-btn active" data-filter="all">Todos</button>
            <button type="button" class="role-tab-btn" data-filter="enviada">Pendientes</button>
            <button type="button" class="role-tab-btn" data-filter="en_revision">En Verificación</button>
            <button type="button" class="role-tab-btn" data-filter="aceptada">Aceptados</button>
        </div>
    </div>
</div>

<div class="dash-card">
    <?php if (!empty($postulantes)): ?>
        <div style="overflow-x:auto;">
            <table class="dash-table" id="verifTable">
                <thead>
                    <tr>
                        <th>Candidato</th>
                        <th>Vacante Aplicada</th>
                        <th>Empresa</th>
                        <th>Fecha Postulación</th>
                        <th>Estado</th>
                        <th style="text-align:right;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($postulantes as $p): ?>
                        <?php
                            $fullName = trim($p['nombre'] . ' ' . $p['apellido']);
                            $initial = strtoupper(substr($p['nombre'] ?: 'C', 0, 1));
                            $estado = $p['estado'] ?? 'enviada';
                        ?>
                        <tr class="verif-row" data-estado="<?= esc($estado) ?>" data-search="<?= esc(strtolower($fullName . ' ' . $p['vacante_titulo'] . ' ' . $p['empresa_nombre'] . ' ' . ($p['profesion'] ?? ''))) ?>">
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div style="width:38px;height:38px;border-radius:50%;background:#EFF6FF;color:#2563EB;display:flex;align-items:center;justify-content:center;font-weight:700;">
                                        <?= $initial ?>
                                    </div>
                                    <div>
                                        <strong style="display:block;font-size:14px;color:var(--text-primary);"><?= esc($fullName) ?></strong>
                                        <span style="font-size:12.5px;color:var(--text-secondary);"><?= esc($p['profesion'] ?: 'Candidato') ?> &middot; <?= esc($p['ciudad'] ?: 'Nicaragua') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong style="color:var(--text-primary);"><?= esc($p['vacante_titulo']) ?></strong>
                            </td>
                            <td>
                                <?= esc($p['empresa_nombre'] ?? 'Empresa') ?>
                            </td>
                            <td>
                                <?= esc(date('d/m/Y', strtotime($p['fecha_postulacion']))) ?>
                            </td>
                            <td>
                                <?php if ($estado === 'aceptada' || $estado === 'contratada'): ?>
                                    <span class="badge badge-success" style="text-transform:capitalize;">Aceptado</span>
                                <?php elseif ($estado === 'en_revision'): ?>
                                    <span class="badge badge-warning" style="text-transform:capitalize;">En Verificación</span>
                                <?php elseif ($estado === 'rechazada' || $estado === 'descartada'): ?>
                                    <span class="badge badge-danger" style="text-transform:capitalize;">Rechazado</span>
                                <?php else: ?>
                                    <span class="badge badge-accent" style="text-transform:capitalize;">Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex;gap:6px;">
                                    <a href="<?= base_url('admin/postulantes/perfil/' . $p['candidato_id']) ?>" class="btn btn-ghost btn-sm" title="Ver perfil completo">
                                        Perfil
                                    </a>
                                    <a href="<?= base_url('admin/postulantes/ver/' . $p['candidato_id']) ?>" class="btn btn-primary btn-sm">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                        Verificar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state" style="padding:40px;text-align:center;">
            <h3>No hay postulaciones registradas</h3>
            <p style="color:var(--text-secondary);font-size:14px;">Cuando los candidatos se postulen a las vacantes, aparecerán aquí para su validación.</p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('verifSearchInput');
    const tabs = document.getElementById('verifTabs');
    const rows = Array.from(document.querySelectorAll('.verif-row'));
    let currentFilter = 'all';

    function filterRows() {
        const query = (searchInput.value || '').trim().toLowerCase();

        rows.forEach(row => {
            const rowSearch = row.dataset.search || '';
            const rowEstado = row.dataset.estado || '';

            const matchTab = (currentFilter === 'all') || (rowEstado === currentFilter);
            const matchQuery = !query || rowSearch.includes(query);

            if (matchTab && matchQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput?.addEventListener('input', filterRows);

    tabs?.addEventListener('click', function(e) {
        const btn = e.target.closest('.role-tab-btn');
        if (!btn) return;
        tabs.querySelectorAll('.role-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = btn.dataset.filter || 'all';
        filterRows();
    });
});
</script>
