<div class="page-header">
    <h1><?= isset($noticia) ? 'Editar noticia' : 'Nueva noticia' ?></h1>
    <a href="<?= base_url('admin/noticias') ?>" class="btn btn-ghost">&larr; Volver</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <form action="<?= isset($noticia) ? base_url('admin/noticias/actualizar/' . $noticia['id']) : base_url('admin/noticias/guardar') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label">Titulo</label>
            <input type="text" name="titulo" class="form-control" value="<?= isset($noticia) ? esc($noticia['titulo']) : old('titulo') ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Resumen</label>
            <textarea name="resumen" class="form-control" rows="2" required><?= isset($noticia) ? esc($noticia['resumen']) : old('resumen') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Contenido</label>
            <textarea name="contenido" class="form-control" rows="10" required><?= isset($noticia) ? esc($noticia['contenido']) : old('contenido') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Imagen</label>
            <?php if (isset($noticia) && !empty($noticia['imagen'])): ?>
                <div style="margin-bottom:12px;">
                    <img src="<?= base_url('uploads/noticias/' . $noticia['imagen']) ?>" alt="Imagen actual" style="max-width:200px;border-radius:8px;">
                </div>
            <?php endif; ?>
            <input type="file" name="imagen" accept="image/*" class="form-control">
        </div>

        <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-control">
                <option value="borrador" <?= (isset($noticia) && $noticia['estado'] === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                <option value="publicado" <?= (isset($noticia) && $noticia['estado'] === 'publicado') ? 'selected' : '' ?>>Publicado</option>
                <option value="archivado" <?= (isset($noticia) && $noticia['estado'] === 'archivado') ? 'selected' : '' ?>>Archivado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg"><?= isset($noticia) ? 'Actualizar' : 'Publicar' ?></button>
    </form>
</div>
