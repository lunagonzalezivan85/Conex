<?php
$stats = $stats ?? ['vacantes' => 0, 'empresas' => 0, 'postulantes' => 0, 'servicios' => 0];
$busquedasPopulares = ['Contador', 'Electricista', 'Ventas', 'Desarrollador', 'Chofer', 'Enfermeria'];
?>

<!-- ============ HERO (split, estilo index-3) ============ -->
<section class="hero hero-v2">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="<?= base_url('video/v-01.mp4') ?>" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>

    <div class="hero-inner hero-split">
        <div class="hero-copy">
            <h1>Encuentra trabajo <span class="hero-accent">facil y rapido.</span><br><span class="hero-accent">Haz CONEX.</span></h1>

            <form action="<?= base_url('buscar-empleo') ?>" method="get" class="hero-search-form hero-search-v2" id="heroSearch">
                <div class="hero-search-field">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" name="q" placeholder="Titulo, oficio o palabra clave">
                </div>
                <div class="hero-search-field hero-search-loc">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <input type="text" name="ciudad" placeholder="Ciudad">
                </div>
                <button type="submit" class="btn btn-primary hero-search-btn">Buscar</button>
            </form>

            <div class="hero-popular">
                <span class="hero-popular-label">Popular:</span>
                <?php foreach ($busquedasPopulares as $b): ?>
                    <a href="<?= base_url('buscar-empleo') ?>?q=<?= urlencode($b) ?>" class="hero-popular-chip"><?= esc($b) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="diamond-field">
                <div class="diamond d1">
                    <span class="diamond-txt">
                        <strong><span class="counter" data-target="<?= (int)$stats['vacantes'] ?>">0</span>+</strong>
                        Vacantes activas
                    </span>
                </div>
                <div class="diamond d2">
                    <span class="diamond-txt">
                        <strong>100% gratis</strong>
                        Postulacion
                    </span>
                </div>
                <div class="diamond d3">
                    <span class="diamond-txt">
                        <strong>Servicios</strong>
                        cerca de ti
                    </span>
                </div>
                <div class="diamond d4">
                    <span class="diamond-txt">
                        <strong>Empleo</strong>
                        facil de encontrar
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ BROWSE BY CATEGORY ============ -->
<section class="container section-padding reveal">
    <div class="section-header-center">
        <span class="section-eyebrow">Explora</span>
        <h2>Explora por categoria</h2>
        <p class="section-sub">Encuentra el tipo de trabajo que necesitas, claramente definido.</p>
    </div>
    <div class="categoria-grid">
        <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $cat): ?>
                <a href="<?= base_url('buscar-empleo') ?>?categoria=<?= $cat['id'] ?>" class="categoria-card">
                    <span class="categoria-icon"><?= $cat['icon_svg'] ?? $cat['icono'] ?? '' ?></span>
                    <span class="categoria-body">
                        <span class="categoria-name"><?= esc($cat['nombre']) ?></span>
                        <span class="categoria-count"><?= (int)$cat['total'] ?> vacante<?= (int)$cat['total'] === 1 ? '' : 's' ?></span>
                    </span>
                    <span class="categoria-arrow">&rarr;</span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card empty-state">
                <p class="empty-state-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></p>
                <h3>No hay categorias disponibles</h3>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ STATS BAND ============ -->
<section class="stats-band reveal">
    <div class="container stats-band-grid">
        <div class="stats-band-lead">
            <span class="stats-band-num"><span class="counter" data-target="<?= (int)$stats['vacantes'] ?>">0</span><span class="stats-band-plus">+</span></span>
            <p>Oportunidades te estan esperando</p>
        </div>
        <div class="stats-band-item">
            <span class="stats-band-value counter" data-target="<?= (int)$stats['empresas'] ?>">0</span>
            <span class="stats-band-label">Empresas</span>
        </div>
        <div class="stats-band-item">
            <span class="stats-band-value counter" data-target="<?= (int)$stats['postulantes'] ?>">0</span>
            <span class="stats-band-label">Postulantes</span>
        </div>
        <div class="stats-band-item">
            <span class="stats-band-value counter" data-target="<?= (int)$stats['servicios'] ?>">0</span>
            <span class="stats-band-label">Prestadores</span>
        </div>
    </div>
</section>

<!-- ============ EXPLORER: que buscas (pills) ============ -->
<section class="container section-padding reveal" id="explorer">
    <div class="section-header-center">
        <span class="section-eyebrow">Encuentra lo tuyo</span>
        <h2>Que estas buscando?</h2>
        <p class="section-sub">Empleo de empresas verificadas — o profesionales que ofrecen sus servicios cerca de ti.</p>
    </div>

    <div class="explore-tabs" role="tablist">
        <button type="button" class="explore-tab active" data-tab="empleos" role="tab" aria-selected="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Busco empleo
        </button>
        <button type="button" class="explore-tab" data-tab="servicios" role="tab" aria-selected="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            Busco un servicio
        </button>
    </div>

    <!-- Panel: empleos -->
    <div class="explore-panel active" id="panel-empleos" role="tabpanel">
        <div class="explore-panel-head">
            <h3>Vacantes recientes</h3>
            <a href="<?= base_url('buscar-empleo') ?>">Ver todas &rarr;</a>
        </div>
        <div class="vacante-grid">
        <?php if (!empty($vacantes)): ?>
            <?php foreach ($vacantes as $v): ?>
                <a href="<?= base_url('vacante/' . $v['slug']) ?>" class="card vacante-card">
                    <div class="vacante-card-header">
                        <?php if (!empty($v['empresa_logo'])): ?>
                            <img src="<?= base_url('uploads/' . $v['empresa_logo']) ?>" alt="" class="vacante-card-logo">
                        <?php else: ?>
                            <div class="vacante-card-icon"><?= strtoupper(substr($v['empresa_nombre'] ?? $v['titulo'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <div>
                            <h3 class="vacante-card-title"><?= esc($v['titulo']) ?></h3>
                            <p class="vacante-card-location">
                                <?= esc($v['empresa_nombre'] ?? '') ?><?= !empty($v['ciudad']) ? ' - ' . esc($v['ciudad']) : '' ?>
                            </p>
                        </div>
                    </div>
                    <div class="vacante-card-badges">
                        <?php if (!empty($v['modalidad'])): ?>
                            <span class="badge badge-accent"><?= ucfirst($v['modalidad']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($v['salario_min'])): ?>
                            <span class="badge badge-success">$<?= number_format($v['salario_min'], 0) ?>+ USD</span>
                        <?php endif; ?>
                    </div>
                    <p class="vacante-card-date">Publicada el <?= date('d M, Y', strtotime($v['fecha_publicacion'] ?? $v['created_at'] ?? 'now')) ?></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card empty-state">
                <p class="empty-state-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></p>
                <h3>No hay vacantes publicadas aun</h3>
                <p>Las vacantes apareceran aqui cuando las empresas empiecen a publicar.</p>
            </div>
        <?php endif; ?>
        </div>
    </div>

    <!-- Panel: servicios -->
    <div class="explore-panel" id="panel-servicios" role="tabpanel" hidden>
        <div class="explore-panel-head">
            <h3>Profesionales y oficios cerca de ti</h3>
            <a href="<?= base_url('servicios') ?>">Ver directorio &rarr;</a>
        </div>
        <div class="srv-grid">
            <?php if (!empty($servicios)): ?>
                <?php foreach ($servicios as $s): ?>
                    <a href="<?= base_url('servicios/p/' . (int)$s['id']) ?>" class="srv-mini-card">
                        <?php if (!empty($s['avatar_url'])): ?>
                            <img src="<?= esc($s['avatar_url']) ?>" alt="" class="srv-mini-avatar">
                        <?php else: ?>
                            <span class="srv-mini-avatar srv-mini-ini"><?= esc($s['iniciales']) ?></span>
                        <?php endif; ?>
                        <span class="srv-mini-body">
                            <span class="srv-mini-nombre">
                                <?= esc($s['nombre_publico']) ?>
                                <?php if (!empty($s['prestador_verificado'])): ?><span class="srv-mini-check" title="Verificado"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2"><polyline points="20 6 9 17 4 12"/></svg></span><?php endif; ?>
                            </span>
                            <span class="srv-mini-oficio"><?= esc($s['oficio']) ?></span>
                            <span class="srv-mini-zona"><?= esc($s['zona'] ?: $s['ciudad'] ?: 'Nicaragua') ?></span>
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            <a href="<?= base_url('registro') ?>" class="srv-mini-card srv-mini-cta">
                <span class="srv-mini-cta-ic">+</span>
                <span class="srv-mini-body">
                    <span class="srv-mini-oficio">Ofreces un servicio?</span>
                    <span class="srv-mini-zona">Publicate gratis</span>
                </span>
            </a>
        </div>
    </div>
</section>

<!-- ============ CTA EMPRESA ============ -->
<section class="cta-section reveal">
    <div class="container cta-inner">
        <div>
            <span class="section-eyebrow">Para empresas</span>
            <h2>Publica vacantes y encuentra talento</h2>
            <p>Revisa candidatos verificados y contrata mas rapido. Empieza gratis con el plan Freemium.</p>
            <ul class="cta-list">
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Publica hasta 3 vacantes gratis</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Ve quien se ha postulado</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Mejora tu plan cuando quieras</li>
            </ul>
            <div class="cta-actions">
                <a href="<?= base_url('registro') ?>" class="btn btn-primary btn-lg">Crear cuenta empresa</a>
                <a href="<?= base_url('planes') ?>" class="btn btn-outline btn-lg">Ver planes</a>
            </div>
        </div>
        <div class="cta-visual">
            <div class="cta-visual-card cta-visual-card-1"><strong>3</strong> vacantes gratis</div>
            <div class="cta-visual-card cta-visual-card-2"><strong>CV</strong> candidatos listos</div>
        </div>
    </div>
</section>