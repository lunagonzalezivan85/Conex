<div class="noticias-hero">
    <div class="noticias-hero-inner">
        <h1>Noticias</h1>
        <p>Mantente al dia sobre empleo, reclutamiento y mercado laboral</p>
    </div>
</div>

<div class="container noticias-page">
    <?php if (!empty($noticias)): ?>
        <div class="noticias-list">
            <?php $i = 0; foreach ($noticias as $n): ?>
                <article class="noticia-item <?= $i % 2 === 1 ? 'noticia-item-reverse' : '' ?>">
                    <?php if (!empty($n['imagen'])): ?>
                    <a href="<?= base_url('noticias/' . $n['slug']) ?>" class="noticia-item-img">
                        <img src="<?= base_url('uploads/noticias/' . $n['imagen']) ?>" alt="<?= esc($n['titulo']) ?>">
                    </a>
                    <?php else: ?>
                    <a href="<?= base_url('noticias/' . $n['slug']) ?>" class="noticia-item-img noticia-item-img-placeholder">
                        <span>Sin imagen</span>
                    </a>
                    <?php endif; ?>
                    <div class="noticia-item-body">
                        <span class="noticia-item-date"><?= date('d M, Y', strtotime($n['created_at'])) ?></span>
                        <h2><a href="<?= base_url('noticias/' . $n['slug']) ?>"><?= esc($n['titulo']) ?></a></h2>
                        <p><?= esc($n['resumen']) ?></p>
                        <a href="<?= base_url('noticias/' . $n['slug']) ?>" class="noticia-item-link">Leer mas &rarr;</a>
                    </div>
                </article>
                <?php $i++; endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card empty-state">
            <p class="empty-state-icon">&#128240;</p>
            <h3>No hay noticias publicadas</h3>
            <p>Las noticias apareceran aqui cuando se publiquen.</p>
        </div>
    <?php endif; ?>
</div>
