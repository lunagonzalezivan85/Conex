<div class="usuarios-page-header">
    <div>
        <h1>Gestión de Usuarios</h1>
        <p>Explora la lista completa de usuarios registrados. Haz clic en cualquier usuario para ver su perfil y detalles.</p>
    </div>
</div>

<!-- KPI Stats Grid -->
<div class="usuarios-stats-grid">
    <div class="stat-card-mini">
        <div class="stat-mini-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-mini-info">
            <span class="stat-mini-num"><?= (int)($stats['total'] ?? 0) ?></span>
            <span class="stat-mini-label">Total Usuarios</span>
        </div>
    </div>
    <div class="stat-card-mini">
        <div class="stat-mini-icon indigo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div class="stat-mini-info">
            <span class="stat-mini-num"><?= (int)($stats['candidatos'] ?? 0) ?></span>
            <span class="stat-mini-label">Candidatos</span>
        </div>
    </div>
    <div class="stat-card-mini">
        <div class="stat-mini-icon teal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div class="stat-mini-info">
            <span class="stat-mini-num"><?= (int)($stats['empresas'] ?? 0) ?></span>
            <span class="stat-mini-label">Empresas</span>
        </div>
    </div>
    <div class="stat-card-mini">
        <div class="stat-mini-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="stat-mini-info">
            <span class="stat-mini-num"><?= (int)($stats['staff'] ?? 0) ?></span>
            <span class="stat-mini-label">Staff (Admins / Asesores)</span>
        </div>
    </div>
    <div class="stat-card-mini">
        <div class="stat-mini-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-mini-info">
            <span class="stat-mini-num"><?= (int)($stats['activos'] ?? 0) ?></span>
            <span class="stat-mini-label">Usuarios Activos</span>
        </div>
    </div>
</div>

<!-- Toolbar: Search & Filters -->
<div class="usuarios-toolbar">
    <div class="usuarios-search-wrap">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="usuarioSearchInput" class="usuarios-search-input" placeholder="Buscar usuario por nombre, usuario, correo, teléfono, profesión o empresa..." autocomplete="off">
        <button type="button" id="searchClearBtn" class="usuarios-search-clear" title="Limpiar búsqueda">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <div class="usuarios-filters-row">
        <div class="role-tabs" id="roleTabs">
            <button type="button" class="role-tab-btn active" data-role="all">
                Todos <span class="role-count"><?= (int)($stats['total'] ?? 0) ?></span>
            </button>
            <button type="button" class="role-tab-btn" data-role="candidato">
                Candidatos <span class="role-count"><?= (int)($stats['candidatos'] ?? 0) ?></span>
            </button>
            <button type="button" class="role-tab-btn" data-role="empresa">
                Empresas <span class="role-count"><?= (int)($stats['empresas'] ?? 0) ?></span>
            </button>
            <button type="button" class="role-tab-btn" data-role="admin">
                Administradores
            </button>
            <button type="button" class="role-tab-btn" data-role="asesor">
                Asesores
            </button>
        </div>

        <div class="filters-secondary">
            <select id="statusFilter" class="filter-select" aria-label="Filtrar por estado">
                <option value="all">Todos los estados</option>
                <option value="activo">Solo Activos</option>
                <option value="inactivo">Solo Inactivos</option>
            </select>

            <select id="sortFilter" class="filter-select" aria-label="Ordenar usuarios">
                <option value="recent">Más recientes</option>
                <option value="oldest">Más antiguos</option>
                <option value="alpha_asc">Nombre (A - Z)</option>
                <option value="alpha_desc">Nombre (Z - A)</option>
            </select>

            <span id="resultsCounter" class="results-counter">
                Mostrando <?= count($usuarios ?? []) ?> usuarios
            </span>
        </div>
    </div>
</div>

<!-- List View -->
<div class="user-list" id="userList">
    <?php if (!empty($usuarios)): ?>
        <?php foreach ($usuarios as $u): ?>
            <?php
                $fullName = trim(($u['nombre'] ?? '') . ' ' . ($u['apellido'] ?? ''));
                if (empty($fullName)) $fullName = $u['usuario'] ?? 'Usuario sin nombre';
                $roleSlug = $u['role_slug'] ?? 'candidato';
                $roleName = $u['role_nombre'] ?? ucfirst($roleSlug);
                $initial = strtoupper(substr($u['nombre'] ?: ($u['usuario'] ?: 'U'), 0, 1));
                $hasAvatar = !empty($u['avatar']);
                $avatarUrl = $hasAvatar ? base_url('uploads/avatars/' . $u['avatar']) : '';

                // Extra context details
                $contextSnippet = '';
                if ($roleSlug === 'candidato') {
                    $contextSnippet = esc($u['profesion'] ?: 'Candidato');
                    if (!empty($u['cand_ciudad'])) {
                        $contextSnippet .= ' &middot; ' . esc($u['cand_ciudad']);
                    }
                } elseif ($roleSlug === 'empresa') {
                    $contextSnippet = esc($u['razon_social'] ?: ($u['rubro'] ?: 'Empresa'));
                    if (!empty($u['emp_ciudad'])) {
                        $contextSnippet .= ' &middot; ' . esc($u['emp_ciudad']);
                    }
                } else {
                    $contextSnippet = 'Equipo CONEX &middot; ' . esc($roleName);
                }

                $regDate = !empty($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : '-';
                $regTimestamp = !empty($u['created_at']) ? strtotime($u['created_at']) : 0;
                $lastLogin = !empty($u['last_login']) ? date('d/m/Y H:i', strtotime($u['last_login'])) : 'Nunca';
            ?>
            <div class="user-item"
                 tabindex="0"
                 role="button"
                 aria-label="Ver perfil de <?= esc($fullName) ?>"
                 data-id="<?= (int)$u['id'] ?>"
                 data-nombre="<?= esc($fullName) ?>"
                 data-usuario="<?= esc($u['usuario'] ?? '') ?>"
                 data-email="<?= esc($u['email'] ?? '') ?>"
                 data-telefono="<?= esc($u['telefono'] ?? '') ?>"
                 data-role="<?= esc($roleSlug) ?>"
                 data-role-nombre="<?= esc($roleName) ?>"
                 data-estado="<?= esc($u['estado'] ?? 'activo') ?>"
                 data-avatar="<?= esc($avatarUrl) ?>"
                 data-initial="<?= esc($initial) ?>"
                 data-created="<?= esc($regDate) ?>"
                 data-timestamp="<?= $regTimestamp ?>"
                 data-login="<?= esc($lastLogin) ?>"
                 data-perfil-completo="<?= (int)($u['perfil_completo'] ?? 0) ?>"
                 data-candidato-id="<?= esc($u['candidato_id'] ?? '') ?>"
                 data-profesion="<?= esc($u['profesion'] ?? '') ?>"
                 data-cand-ciudad="<?= esc($u['cand_ciudad'] ?? '') ?>"
                 data-cand-pais="<?= esc($u['cand_pais'] ?? '') ?>"
                 data-disponibilidad="<?= esc($u['disponibilidad'] ?? '') ?>"
                 data-porcentaje="<?= esc($u['porcentaje_perfil'] ?? '') ?>"
                 data-sobre-mi="<?= esc($u['sobre_mi'] ?? '') ?>"
                 data-servicios="<?= (int)($u['ofrece_servicios'] ?? 0) ?>"
                 data-empresa-id="<?= esc($u['empresa_id'] ?? '') ?>"
                 data-razon-social="<?= esc($u['razon_social'] ?? '') ?>"
                 data-rubro="<?= esc($u['rubro'] ?? '') ?>"
                 data-ruc="<?= esc($u['ruc'] ?? '') ?>"
                 data-sitio-web="<?= esc($u['sitio_web'] ?? '') ?>"
                 data-emp-ciudad="<?= esc($u['emp_ciudad'] ?? '') ?>"
                 data-verificada="<?= (int)($u['emp_verificada'] ?? 0) ?>">

                <!-- Avatar -->
                <div class="user-avatar-wrap">
                    <?php if ($hasAvatar): ?>
                        <img src="<?= $avatarUrl ?>" alt="<?= esc($fullName) ?>" class="user-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="user-avatar-initials role-<?= esc($roleSlug) ?>" style="display:none;"><?= $initial ?></div>
                    <?php else: ?>
                        <div class="user-avatar-initials role-<?= esc($roleSlug) ?>"><?= $initial ?></div>
                    <?php endif; ?>
                </div>

                <!-- Primary Info -->
                <div class="user-info-primary">
                    <div class="user-name-row">
                        <span class="user-name"><?= esc($fullName) ?></span>
                        <span class="user-username">@<?= esc($u['usuario'] ?? '') ?></span>
                    </div>
                    <div class="user-tags-row">
                        <span class="badge-role <?= esc($roleSlug) ?>">
                            <?php if ($roleSlug === 'admin'): ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <?php elseif ($roleSlug === 'asesor'): ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <?php elseif ($roleSlug === 'empresa'): ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <?php endif; ?>
                            <?= esc($roleName) ?>
                        </span>

                        <span class="badge-status <?= $u['estado'] === 'activo' ? 'activo' : 'inactivo' ?>">
                            <span class="badge-status-dot"></span>
                            <?= $u['estado'] === 'activo' ? 'Activo' : 'Inactivo' ?>
                        </span>

                        <?php if (!empty($u['ofrece_servicios'])): ?>
                            <span class="badge-sub" title="Ofrece servicios independientes">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                Servicios
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($u['emp_verificada'])): ?>
                            <span class="badge-sub verified" title="Empresa verificada">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Verificada
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Secondary Context Info -->
                <div class="user-info-secondary">
                    <div class="user-meta-line user-role-snippet" title="<?= esc(strip_tags($contextSnippet)) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <span><?= $contextSnippet ?></span>
                    </div>
                    <div class="user-meta-line" title="<?= esc($u['email']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <span><?= esc($u['email']) ?></span>
                    </div>
                    <?php if (!empty($u['telefono'])): ?>
                        <div class="user-meta-line">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span><?= esc($u['telefono']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Dates -->
                <div class="user-dates">
                    <div>
                        <span class="user-dates-label">Registro:</span>
                        <strong><?= $regDate ?></strong>
                    </div>
                    <div>
                        <span class="user-dates-label">Acceso:</span>
                        <span><?= $lastLogin ?></span>
                    </div>
                </div>

                <!-- Action -->
                <div class="user-action">
                    <span class="btn-ver-perfil">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Ver perfil
                    </span>
                    <svg class="user-chevron" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Empty State -->
<div class="user-empty-state" id="userEmptyState" style="<?= empty($usuarios) ? 'display:block;' : '' ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
    <h3>No se encontraron usuarios</h3>
    <p>Prueba ajustando los filtros de rol, estado o el término de búsqueda.</p>
    <button type="button" class="btn btn-outline btn-sm" id="resetFiltersBtn">Restablecer filtros</button>
</div>

<!-- ==========================================================================
     User Profile Interactive Modal (Drawer/Dialog)
     ========================================================================== -->
<div class="user-modal-overlay" id="userProfileModal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="user-modal-dialog">
        <!-- Modal Header -->
        <div class="user-modal-header">
            <button type="button" class="user-modal-close" id="modalCloseBtn" aria-label="Cerrar modal">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="modal-avatar-wrap" id="modalAvatarWrap">
                <!-- Injected via JS -->
            </div>
            <div class="modal-user-intro">
                <h3 class="modal-user-name" id="modalUserName">Nombre del Usuario</h3>
                <div class="modal-user-meta">
                    <span class="modal-user-username" id="modalUserHandle">@usuario</span>
                    <span id="modalRoleBadge" class="badge-role">Rol</span>
                    <span id="modalStatusBadge" class="badge-status">
                        <span class="badge-status-dot"></span>
                        <span id="modalStatusText">Activo</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="user-modal-body">
            <!-- Quick Contact Bar -->
            <div class="modal-contact-bar" id="modalContactBar">
                <a href="#" id="modalEmailLink" class="modal-contact-pill" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span id="modalEmailText">email@dominio.com</span>
                </a>
                <a href="#" id="modalPhoneLink" class="modal-contact-pill" style="display:none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span id="modalPhoneText">+000 000 000</span>
                </a>
            </div>

            <!-- Account Details Grid -->
            <div>
                <div class="modal-section-title">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    Datos de la Cuenta
                </div>
                <div class="modal-info-grid">
                    <div class="modal-info-box">
                        <span class="label">ID de Usuario</span>
                        <span class="value" id="modalUserId">#0</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Fecha de Registro</span>
                        <span class="value" id="modalCreatedDate">-</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Último Inicio de Sesión</span>
                        <span class="value" id="modalLastLogin">-</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Perfil Completo</span>
                        <span class="value" id="modalProfileComplete">No</span>
                    </div>
                </div>
            </div>

            <!-- Dynamic Role Details Section -->
            <div id="modalRoleSpecificSection">
                <div class="modal-section-title">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Información del Perfil
                </div>
                <div class="modal-role-card" id="modalRoleContent">
                    <!-- Injected dynamically -->
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="user-modal-footer">
            <button type="button" class="btn btn-ghost btn-sm" id="modalCancelBtn">Cerrar</button>
            <div class="actions-right">
                <a href="#" id="modalExtraActionBtn" class="btn btn-outline btn-sm" style="display:none;">Acción Extra</a>
                <a href="#" id="modalFullProfileBtn" class="btn btn-primary btn-sm">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Ver perfil completo
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('usuarioSearchInput');
    const searchClear = document.getElementById('searchClearBtn');
    const roleTabs = document.getElementById('roleTabs');
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter = document.getElementById('sortFilter');
    const userList = document.getElementById('userList');
    const userItems = Array.from(document.querySelectorAll('.user-item'));
    const emptyState = document.getElementById('userEmptyState');
    const counter = document.getElementById('resultsCounter');
    const resetBtn = document.getElementById('resetFiltersBtn');

    // Modal elements
    const modal = document.getElementById('userProfileModal');
    const modalClose = document.getElementById('modalCloseBtn');
    const modalCancel = document.getElementById('modalCancelBtn');
    const modalAvatarWrap = document.getElementById('modalAvatarWrap');
    const modalUserName = document.getElementById('modalUserName');
    const modalUserHandle = document.getElementById('modalUserHandle');
    const modalRoleBadge = document.getElementById('modalRoleBadge');
    const modalStatusBadge = document.getElementById('modalStatusBadge');
    const modalStatusText = document.getElementById('modalStatusText');
    const modalEmailLink = document.getElementById('modalEmailLink');
    const modalEmailText = document.getElementById('modalEmailText');
    const modalPhoneLink = document.getElementById('modalPhoneLink');
    const modalPhoneText = document.getElementById('modalPhoneText');
    const modalUserId = document.getElementById('modalUserId');
    const modalCreatedDate = document.getElementById('modalCreatedDate');
    const modalLastLogin = document.getElementById('modalLastLogin');
    const modalProfileComplete = document.getElementById('modalProfileComplete');
    const modalRoleContent = document.getElementById('modalRoleContent');
    const modalFullProfileBtn = document.getElementById('modalFullProfileBtn');
    const modalExtraActionBtn = document.getElementById('modalExtraActionBtn');

    let currentRole = 'all';

    // Filter & Sort Logic
    function applyFilters() {
        const query = (searchInput.value || '').trim().toLowerCase();
        const status = statusFilter.value;
        const sort = sortFilter.value;

        // Toggle clear button
        searchClear.style.display = query ? 'block' : 'none';

        let visibleCount = 0;
        const visibleItems = [];

        userItems.forEach(item => {
            const name = (item.dataset.nombre || '').toLowerCase();
            const usuario = (item.dataset.usuario || '').toLowerCase();
            const email = (item.dataset.email || '').toLowerCase();
            const tel = (item.dataset.telefono || '').toLowerCase();
            const role = item.dataset.role || '';
            const itemStatus = item.dataset.estado || '';
            const prof = (item.dataset.profesion || '').toLowerCase();
            const razon = (item.dataset.razonSocial || '').toLowerCase();
            const rubro = (item.dataset.rubro || '').toLowerCase();
            const ciudad = (item.dataset.candCiudad || item.dataset.empCiudad || '').toLowerCase();

            // Matches Role?
            let roleMatch = (currentRole === 'all') || (role === currentRole);

            // Matches Status?
            let statusMatch = (status === 'all') || (itemStatus === status);

            // Matches Query?
            let queryMatch = true;
            if (query) {
                queryMatch = name.includes(query) ||
                             usuario.includes(query) ||
                             email.includes(query) ||
                             tel.includes(query) ||
                             prof.includes(query) ||
                             razon.includes(query) ||
                             rubro.includes(query) ||
                             ciudad.includes(query);
            }

            if (roleMatch && statusMatch && queryMatch) {
                item.style.display = 'grid';
                visibleCount++;
                visibleItems.push(item);
            } else {
                item.style.display = 'none';
            }
        });

        // Sorting
        visibleItems.sort((a, b) => {
            if (sort === 'recent') {
                return (parseInt(b.dataset.timestamp) || 0) - (parseInt(a.dataset.timestamp) || 0);
            } else if (sort === 'oldest') {
                return (parseInt(a.dataset.timestamp) || 0) - (parseInt(b.dataset.timestamp) || 0);
            } else if (sort === 'alpha_asc') {
                return (a.dataset.nombre || '').localeCompare(b.dataset.nombre || '');
            } else if (sort === 'alpha_desc') {
                return (b.dataset.nombre || '').localeCompare(a.dataset.nombre || '');
            }
            return 0;
        });

        // Reorder DOM
        visibleItems.forEach(el => userList.appendChild(el));

        // Update counter & empty state
        counter.textContent = `Mostrando ${visibleCount} de ${userItems.length} usuarios`;
        if (visibleCount === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }

    // Role Tab Click
    roleTabs?.addEventListener('click', function(e) {
        const btn = e.target.closest('.role-tab-btn');
        if (!btn) return;
        roleTabs.querySelectorAll('.role-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentRole = btn.dataset.role || 'all';
        applyFilters();
    });

    // Inputs change
    searchInput?.addEventListener('input', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);
    sortFilter?.addEventListener('change', applyFilters);

    searchClear?.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.focus();
        applyFilters();
    });

    resetBtn?.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = 'all';
        sortFilter.value = 'recent';
        currentRole = 'all';
        roleTabs.querySelectorAll('.role-tab-btn').forEach(b => {
            b.classList.toggle('active', b.dataset.role === 'all');
        });
        applyFilters();
    });

    // Open Profile Modal for an item
    function openUserProfile(item) {
        const d = item.dataset;
        const userId = d.id;
        const role = d.role;

        modalUserId.textContent = '#' + userId;
        modalUserName.textContent = d.nombre;
        modalUserHandle.textContent = '@' + d.usuario;
        modalCreatedDate.textContent = d.created || '-';
        modalLastLogin.textContent = d.login || 'Nunca';
        modalProfileComplete.textContent = (d.perfilCompleto === '1') ? 'Sí (100%)' : 'No';

        // Avatar
        if (d.avatar) {
            modalAvatarWrap.innerHTML = `<img src="${d.avatar}" alt="${d.nombre}">`;
        } else {
            modalAvatarWrap.innerHTML = `<div class="user-avatar-initials role-${role}">${d.initial}</div>`;
        }

        // Role badge
        modalRoleBadge.className = `badge-role ${role}`;
        modalRoleBadge.textContent = d.roleNombre || role;

        // Status badge
        if (d.estado === 'activo') {
            modalStatusBadge.className = 'badge-status activo';
            modalStatusText.textContent = 'Activo';
        } else {
            modalStatusBadge.className = 'badge-status inactivo';
            modalStatusText.textContent = 'Inactivo';
        }

        // Email & Phone
        modalEmailText.textContent = d.email;
        modalEmailLink.href = 'mailto:' + d.email;

        if (d.telefono) {
            modalPhoneText.textContent = d.telefono;
            modalPhoneLink.href = 'tel:' + d.telefono;
            modalPhoneLink.style.display = 'inline-flex';
        } else {
            modalPhoneLink.style.display = 'none';
        }

        // Link to full profile page
        modalFullProfileBtn.href = `<?= base_url('admin/usuarios/perfil/') ?>/${userId}`;

        // Role specific info block
        let roleHtml = '';
        modalExtraActionBtn.style.display = 'none';

        if (role === 'candidato') {
            const candId = d.candidatoId;
            const prof = d.profesion || 'No especificada';
            const ciudad = d.candCiudad || '';
            const pais = d.candPais || '';
            const disp = d.disponibilidad || 'A convenir';
            const sobreMi = d.sobreMi || '';
            const ofreceServ = d.servicios === '1';

            roleHtml = `
                <h4>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Perfil de Candidato ${candId ? '(ID Candidato: #' + candId + ')' : ''}
                </h4>
                <div class="modal-info-grid" style="margin-bottom:12px;">
                    <div class="modal-info-box">
                        <span class="label">Profesión / Ocupación</span>
                        <span class="value">${escapeHtml(prof)}</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Ubicación</span>
                        <span class="value">${escapeHtml([ciudad, pais].filter(Boolean).join(', ') || 'No especificada')}</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Disponibilidad</span>
                        <span class="value">${escapeHtml(disp)}</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Servicios Independientes</span>
                        <span class="value">${ofreceServ ? '<span style="color:#16A34A;font-weight:700;">Sí, ofrece servicios</span>' : 'No'}</span>
                    </div>
                </div>
            `;

            if (sobreMi) {
                roleHtml += `
                    <div style="margin-top:8px;">
                        <span class="label" style="font-size:11.5px;color:var(--text-muted);font-weight:600;text-transform:uppercase;">Sobre el candidato</span>
                        <p style="margin-top:4px;font-size:13.5px;line-height:1.5;color:var(--text-primary);background:#fff;padding:10px 12px;border-radius:10px;border:1px solid #E2E8F0;">
                            ${escapeHtml(sobreMi)}
                        </p>
                    </div>
                `;
            }

            if (candId) {
                modalExtraActionBtn.href = `<?= base_url('admin/postulantes/perfil/') ?>/${candId}`;
                modalExtraActionBtn.textContent = 'Ver expediente y postulaciones';
                modalExtraActionBtn.style.display = 'inline-flex';
            }

            // Fetch extra skills/stats dynamically
            fetch(`<?= base_url('admin/usuarios/api-detalle/') ?>/${userId}`)
                .then(r => r.json())
                .then(data => {
                    if (data && data.extra) {
                        const skills = data.extra.habilidades || [];
                        const postCount = data.extra.postulaciones_count || 0;
                        const eduCount = data.extra.educacion_count || 0;
                        const expCount = data.extra.experiencia_count || 0;

                        let extraInfo = `
                            <div style="margin-top:14px;padding-top:12px;border-top:1px dashed #CBD5E1;">
                                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:10px;font-size:12.5px;color:var(--text-secondary);">
                                    <span><strong>${postCount}</strong> postulaciones</span> &middot;
                                    <span><strong>${expCount}</strong> experiencias</span> &middot;
                                    <span><strong>${eduCount}</strong> estudios</span>
                                </div>
                        `;

                        if (skills.length > 0) {
                            extraInfo += `
                                <span class="label" style="font-size:11.5px;color:var(--text-muted);font-weight:600;text-transform:uppercase;">Habilidades</span>
                                <div class="modal-skills-wrap">
                                    ${skills.map(s => `<span class="modal-skill-tag">${escapeHtml(s)}</span>`).join('')}
                                </div>
                            `;
                        }
                        extraInfo += `</div>`;
                        modalRoleContent.innerHTML = roleHtml + extraInfo;
                    }
                })
                .catch(() => {});

        } else if (role === 'empresa') {
            const empId = d.empresaId;
            const razon = d.razonSocial || 'No especificada';
            const rubro = d.rubro || 'No especificado';
            const ruc = d.ruc || 'No registrado';
            const web = d.sitioWeb || '';
            const ciudad = d.empCiudad || '';
            const verificada = d.verificada === '1';

            roleHtml = `
                <h4>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Perfil de Empresa ${empId ? '(ID Empresa: #' + empId + ')' : ''}
                </h4>
                <div class="modal-info-grid">
                    <div class="modal-info-box">
                        <span class="label">Razón Social</span>
                        <span class="value">${escapeHtml(razon)}</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Rubro / Sector</span>
                        <span class="value">${escapeHtml(rubro)}</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">RUC / Identificación</span>
                        <span class="value">${escapeHtml(ruc)}</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Ubicación</span>
                        <span class="value">${escapeHtml(ciudad || 'No especificada')}</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Estado de Verificación</span>
                        <span class="value">${verificada ? '<span style="color:#0D9488;font-weight:700;">Verificada ✓</span>' : 'Sin verificar'}</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Sitio Web</span>
                        <span class="value">${web ? `<a href="${escapeHtml(web)}" target="_blank" rel="noopener" style="color:var(--accent);">${escapeHtml(web)} &#8599;</a>` : 'No registrado'}</span>
                    </div>
                </div>
            `;

            modalExtraActionBtn.href = `<?= base_url('admin/vacantes') ?>`;
            modalExtraActionBtn.textContent = 'Ver vacantes en sistema';
            modalExtraActionBtn.style.display = 'inline-flex';

        } else {
            // Admin or Asesor
            roleHtml = `
                <h4>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Permisos de Cuenta Staff (${escapeHtml(d.roleNombre)})
                </h4>
                <p>Este usuario cuenta con privilegios administrativos para acceder al panel de control, gestionar vacantes, procesos de candidatos y configuración del sistema.</p>
                <div class="modal-info-grid">
                    <div class="modal-info-box">
                        <span class="label">Tipo de Staff</span>
                        <span class="value">${escapeHtml(d.roleNombre)}</span>
                    </div>
                    <div class="modal-info-box">
                        <span class="label">Acceso al Panel</span>
                        <span class="value" style="color:#16A34A;font-weight:700;">Habilitado</span>
                    </div>
                </div>
            `;
        }

        modalRoleContent.innerHTML = roleHtml;

        // Show modal
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    // Attach click events on items
    userItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // If click was on a link inside item, let it behave normally
            if (e.target.closest('a')) return;
            openUserProfile(item);
        });

        item.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openUserProfile(item);
            }
        });
    });

    // Close modal triggers
    modalClose?.addEventListener('click', closeModal);
    modalCancel?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Check URL hash (e.g. #user-10) to open automatically
    if (window.location.hash && window.location.hash.startsWith('#user-')) {
        const targetId = window.location.hash.replace('#user-', '');
        const targetItem = userItems.find(el => el.dataset.id === targetId);
        if (targetItem) {
            setTimeout(() => openUserProfile(targetItem), 100);
        }
    }
});
</script>
