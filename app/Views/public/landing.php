<section class="hero">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="<?= base_url('video/v-01.mp4') ?>" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-inner">
        <h1>Encuentra tu proximo empleo</h1>
        <p>Miles de vacantes disponibles. Postulate en segundos y conecta con las mejores empresas.</p>
        <div class="hero-cta">
            <a href="<?= base_url('login') ?>" class="hero-cta-btn hero-btn-orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                <span>Iniciar sesion</span>
            </a>
            <a href="<?= base_url('registro') ?>" class="hero-cta-btn hero-btn-teal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                <span>Unirse a CONEX</span>
            </a>
        </div>
        <form action="<?= base_url('buscar-empleo') ?>" method="get" class="hero-search-form">
            <input type="text" name="q" placeholder="Buscar por titulo, empresa o palabra clave...">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
        <div class="hero-stats">
            <div class="hero-stat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <span class="hero-stat-num">500</span>
                <span class="hero-stat-label">Vacantes</span>
            </div>
            <div class="hero-stat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                <span class="hero-stat-num">200</span>
                <span class="hero-stat-label">Empresas</span>
            </div>
            <div class="hero-stat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span class="hero-stat-label">Postulacion</span>
                <span class="hero-stat-label">Gratuita</span>
            </div>
        </div>
    </div>
</section>

<section class="container section-padding">
    <div class="section-header-center">
        <h2>Categorias populares</h2>
    </div>
    <div class="categoria-grid">
        <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $cat): ?>
                <a href="<?= base_url('buscar-empleo') ?>?categoria=<?= $cat['id'] ?>" class="categoria-card">
                    <span class="categoria-icon"><?= $cat['icono'] ?? '&#128188;' ?></span>
                    <span class="categoria-name"><?= esc($cat['nombre']) ?></span>
                    <span class="categoria-arrow">&rarr;</span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card empty-state">
                <p class="empty-state-icon">&#128188;</p>
                <h3>No hay categorias disponibles</h3>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="container section-padding-bottom">
    <div class="section-header">
        <h2>Vacantes recientes</h2>
        <a href="<?= base_url('buscar-empleo') ?>">Ver todas &rarr;</a>
    </div>
    <div class="vacante-grid">
        <?php if (!empty($vacantes)): ?>
            <?php foreach ($vacantes as $v): ?>
                <a href="<?= base_url('vacante/' . $v['slug']) ?>" class="card vacante-card">
                    <div class="vacante-card-header">
                        <div class="vacante-card-icon"><?= strtoupper(substr($v['titulo'], 0, 1)) ?></div>
                        <div>
                            <h3 class="vacante-card-title"><?= esc($v['titulo']) ?></h3>
                            <p class="vacante-card-location"><?= esc($v['ciudad'] ?? 'Ubicacion no especificada') ?></p>
                        </div>
                    </div>
                    <div class="vacante-card-badges">
                        <?php if (isset($v['modalidad'])): ?>
                            <span class="badge badge-accent"><?= ucfirst($v['modalidad']) ?></span>
                        <?php endif; ?>
                        <?php if (isset($v['salario_min']) && $v['salario_min']): ?>
                            <span class="badge badge-success">$<?= number_format($v['salario_min'], 0) ?>+ USD</span>
                        <?php endif; ?>
                    </div>
                    <p class="vacante-card-date">Publicada el <?= date('d M, Y', strtotime($v['fecha_publicacion'] ?? $v['created_at'] ?? 'now')) ?></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card empty-state">
                <p class="empty-state-icon">&#128188;</p>
                <h3>No hay vacantes publicadas aun</h3>
                <p>Las vacantes apareceran aqui cuando las empresas empiecen a publicar.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="cta-section">
    <div class="container cta-inner">
        <div>
            <h2>Eres una empresa?</h2>
            <p>Publica tus vacantes, revisa candidatos y encuentra al talento que necesitas. Empieza gratis con nuestro plan Freemium.</p>
            <ul class="cta-list">
                <li><span>&#10003;</span> Publica hasta 3 vacantes gratis</li>
                <li><span>&#10003;</span> Ve quien se ha postulado</li>
                <li><span>&#10003;</span> Mejora tu plan cuando quieras</li>
            </ul>
            <a href="<?= base_url('registro') ?>" class="btn btn-primary btn-lg">Crear cuenta empresa</a>
        </div>
        <div class="cta-icon">
            <div>&#127970;</div>
        </div>
    </div>
</section>
