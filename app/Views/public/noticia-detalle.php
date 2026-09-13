<div class="container noticia-detalle">
    <a href="<?= base_url('noticias') ?>" class="vacante-back">&larr; Volver a noticias</a>

    <article class="card noticia-detalle-card">
        <?php if (!empty($noticia['imagen'])): ?>
        <img src="<?= base_url('uploads/noticias/' . $noticia['imagen']) ?>" alt="<?= esc($noticia['titulo']) ?>" class="noticia-detalle-img">
        <?php endif; ?>

        <span class="noticia-detalle-date"><?= date('d M, Y', strtotime($noticia['created_at'])) ?></span>
        <h1><?= esc($noticia['titulo']) ?></h1>
        <p class="noticia-detalle-resumen"><?= esc($noticia['resumen']) ?></p>

        <div class="noticia-detalle-content"><?= nl2br(esc($noticia['contenido'])) ?></div>
    </article>
</div>
