<div class="welcome-banner">
    <div>
        <h1>Panel Administrativo</h1>
        <p>Gestion general de la plataforma CONEX.</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= $totalUsuarios ?? 0 ?></div>
        <div class="stat-card-label">Usuarios registrados</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
        </div>
        <div class="stat-card-value">0</div>
        <div class="stat-card-label">Vacantes publicadas</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
        </div>
        <div class="stat-card-value">0</div>
        <div class="stat-card-label">Postulaciones</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
        </div>
        <div class="stat-card-value">0</div>
        <div class="stat-card-label">Empresas registradas</div>
    </div>
</div>

<div class="dash-grid">
    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Usuarios recientes</h2>
            <a href="<?= base_url('admin/usuarios') ?>">Ver todos</a>
        </div>
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-secondary);">Cargando datos...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Actividad reciente</h2>
        </div>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <h3>Sin actividad</h3>
            <p>Las acciones recientes apareceran aqui.</p>
        </div>
    </div>
</div>
