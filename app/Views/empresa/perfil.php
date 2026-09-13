<div class="page-header">
    <div>
        <h1>Perfil de Empresa</h1>
        <p>Informacion que veran los candidatos al buscar vacantes.</p>
    </div>
</div>

<div class="dash-grid">
    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Datos de la empresa</h2>
        </div>
        <form action="<?= base_url('empresa/perfil') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Razon social</label>
                <input type="text" name="razon_social" class="form-control" value="<?= esc($empresa['razon_social'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">RUC / Identificacion fiscal</label>
                <input type="text" name="ruc" class="form-control" value="<?= esc($empresa['ruc'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Rubro / Sector</label>
                <input type="text" name="rubro" class="form-control" value="<?= esc($empresa['rubro'] ?? '') ?>" placeholder="Ej: Tecnologia">
            </div>
            <div class="form-group">
                <label class="form-label">Sitio web</label>
                <input type="url" name="sitio_web" class="form-control" value="<?= esc($empresa['sitio_web'] ?? '') ?>" placeholder="https://...">
            </div>
            <div class="form-group">
                <label class="form-label">Descripcion</label>
                <textarea name="descripcion" class="form-control" rows="4" placeholder="Describe tu empresa..."><?= esc($empresa['descripcion'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>

    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Ubicacion y contacto</h2>
        </div>
        <div class="form-group">
            <label class="form-label">Telefono</label>
            <input type="tel" name="telefono" class="form-control" value="<?= esc($empresa['telefono'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Direccion</label>
            <input type="text" name="direccion" class="form-control" value="<?= esc($empresa['direccion'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad" class="form-control" value="<?= esc($empresa['ciudad'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Region</label>
            <input type="text" name="region" class="form-control" value="<?= esc($empresa['region'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Pais</label>
            <input type="text" name="pais" class="form-control" value="<?= esc($empresa['pais'] ?? '') ?>">
        </div>
    </div>
</div>
