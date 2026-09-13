<div class="page-header">
    <h1>Noticias</h1>
    <a href="<?= base_url('admin/noticias/crear') ?>" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M12 5v14M5 12h14"/></svg>
        Nueva noticia
    </a>
</div>

<?php if (session()->getFlashdata('info')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('info') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Titulo</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($noticias)): ?>
                <?php foreach ($noticias as $n): ?>
                <tr>
                    <td><?= esc($n['titulo']) ?></td>
                    <td>
                        <?php if ($n['estado'] === 'publicado'): ?>
                            <span class="badge badge-success">Publicado</span>
                        <?php elseif ($n['estado'] === 'borrador'): ?>
                            <span class="badge badge-warning">Borrador</span>
                        <?php else: ?>
                            <span class="badge badge-info">Archivado</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d M, Y', strtotime($n['created_at'])) ?></td>
                    <td>
                        <a href="<?= base_url('admin/noticias/editar/' . $n['id']) ?>" class="btn btn-sm btn-outline">Editar</a>
                        <a href="<?= base_url('admin/noticias/eliminar/' . $n['id']) ?>" class="btn btn-sm btn-ghost" onclick="return confirm('Eliminar esta noticia?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;color:var(--text-secondary);padding:32px;">No hay noticias creadas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
