<div class="page-header">
    <div>
        <h1>Gestion de Usuarios</h1>
        <p>Administra los usuarios registrados en la plataforma.</p>
    </div>
</div>

<div class="dash-card">
    <table class="dash-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= esc($u['nombre'] . ' ' . $u['apellido']) ?></td>
                        <td><?= esc($u['usuario']) ?></td>
                        <td><?= esc($u['email']) ?></td>
                        <td>
                            <span class="badge badge-accent"><?= esc($u['role_nombre'] ?? $u['role_slug'] ?? 'N/A') ?></span>
                        </td>
                        <td>
                            <span class="badge badge-<?= $u['estado'] === 'activo' ? 'success' : 'warning' ?>">
                                <?= esc($u['estado']) ?>
                            </span>
                        </td>
                        <td><?= esc($u['created_at'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-secondary);">No hay usuarios registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
