<?php
$val = fn(string $campo, $default = '') => esc(old($campo, $default));
?>

<div class="page-header">
    <div>
        <h1>Crear Usuario</h1>
        <p>Alta manual de usuarios. Para agentes selecciona el rol <strong>Asesor</strong>: luego podras asignarle tareas (leads) desde su perfil.</p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="dash-card" style="border-left:4px solid #ef4444;margin-bottom:16px;">
        <p style="color:#ef4444;margin:0;"><?= esc(session()->getFlashdata('error')) ?></p>
    </div>
<?php endif; ?>

<form action="<?= base_url('admin/usuarios/crear') ?>" method="post" style="max-width:560px;">
    <?= csrf_field() ?>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Datos de la cuenta</h2>
            <p style="font-size:13px;color:var(--text-secondary);">Credenciales de acceso al panel.</p>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label" for="role_id">Rol</label>
            <select id="role_id" name="role_id" class="form-control" required>
                <option value="">— Selecciona un rol —</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= (int)$r['id'] ?>" <?= old('role_id') == $r['id'] ? 'selected' : '' ?>>
                        <?= esc($r['nombre']) ?> (<?= esc($r['slug']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label" for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" class="form-control" value="<?= $val('usuario') ?>"
                   placeholder="Ej: agente2" required autocomplete="off">
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= $val('email') ?>"
                   placeholder="Ej: agente2@conex.com" required autocomplete="off">
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="password">Contrasena (min. 8 caracteres)</label>
            <input type="password" id="password" name="password" class="form-control" minlength="8" required
                   autocomplete="new-password" placeholder="Minimo 8 caracteres">
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Datos personales</h2>
            <p style="font-size:13px;color:var(--text-secondary);">Se muestran en el perfil y en asignaciones.</p>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label" for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= $val('nombre') ?>"
                   placeholder="Ej: Maria" required>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label" for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" class="form-control" value="<?= $val('apellido') ?>"
                   placeholder="Ej: Gomez">
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="telefono">Telefono (opcional)</label>
            <input type="tel" id="telefono" name="telefono" class="form-control" value="<?= $val('telefono') ?>"
                   placeholder="Ej: 8888-1234">
        </div>
    </div>

    <div style="display:flex;gap:12px;align-items:center;">
        <button type="submit" class="btn btn-primary" style="min-height:48px;">
            Crear usuario
        </button>
        <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-ghost" style="min-height:48px;display:inline-flex;align-items:center;">
            Cancelar
        </a>
    </div>
</form>
