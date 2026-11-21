<div class="container vacante-detalle">
    <a href="<?= base_url('buscar-empleo') ?>" class="vd-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a buscar
    </a>

    <div class="card vd-header-card reveal">
        <div class="vd-header-top">
            <div class="vd-header-icon">
                <?php if (!empty($vacante['empresa_logo'])): ?>
                    <img src="<?= base_url('uploads/' . $vacante['empresa_logo']) ?>" alt="<?= esc($vacante['empresa_nombre'] ?? '') ?>" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                <?php else: ?>
                    <?= strtoupper(substr($vacante['empresa_nombre'] ?? $vacante['titulo'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div class="vd-header-info">
                <h1 class="vd-title"><?= esc($vacante['titulo']) ?></h1>
                <?php if (!empty($vacante['empresa_nombre'])): ?>
                    <p class="vd-company">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <strong><?= esc($vacante['empresa_nombre']) ?></strong>
                        <?php if (!empty($vacante['empresa_verificada'])): ?>
                            <span class="vd-badge-verified" title="Empresa verificada" style="display:inline-flex;align-items:center;margin-left:4px;color:#0FC6C2;">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($vacante['empresa_rubro'])): ?>
                            <span class="vd-rubro"> · <?= esc($vacante['empresa_rubro']) ?></span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <p class="vd-location">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <?= esc($vacante['ciudad'] ?? 'Ubicacion no especificada') ?><?= isset($vacante['region']) ? ' · ' . esc($vacante['region']) : '' ?>
                </p>
                <div class="vd-badges">
                    <?php if (isset($vacante['modalidad'])): ?>
                        <span class="vd-badge vd-badge-blue"><?= ucfirst($vacante['modalidad']) ?></span>
                    <?php endif; ?>
                    <?php if (isset($vacante['salario_min']) && $vacante['salario_min']): ?>
                        <span class="vd-badge vd-badge-green">$<?= number_format($vacante['salario_min'], 0) ?> - $<?= number_format($vacante['salario_max'] ?? $vacante['salario_min'], 0) ?> USD</span>
                    <?php endif; ?>
                    <?php if (isset($vacante['anios_experiencia']) && $vacante['anios_experiencia'] > 0): ?>
                        <span class="vd-badge vd-badge-yellow"><?= $vacante['anios_experiencia'] ?> ano(s) exp.</span>
                    <?php endif; ?>
                    <?php if (isset($vacante['vacantes_disponibles'])): ?>
                        <span class="vd-badge vd-badge-blue-light"><?= $vacante['vacantes_disponibles'] ?> vacante(s)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="vd-meta">
            <div class="vd-meta-item">
                <p class="vd-meta-label">Publicado</p>
                <p class="vd-meta-value"><?= isset($vacante['fecha_publicacion']) ? date('d M, Y', strtotime($vacante['fecha_publicacion'])) : 'N/A' ?></p>
            </div>
            <div class="vd-meta-item">
                <p class="vd-meta-label">Cierra</p>
                <p class="vd-meta-value"><?= isset($vacante['fecha_cierre']) ? date('d M, Y', strtotime($vacante['fecha_cierre'])) : 'Abierto' ?></p>
            </div>
            <div class="vd-meta-item">
                <p class="vd-meta-label">Max. postulantes</p>
                <p class="vd-meta-value"><?= $vacante['max_postulantes'] > 0 ? $vacante['max_postulantes'] : 'Ilimitado' ?></p>
            </div>
        </div>

        <div class="vd-metrics" data-vacante-id="<?= (int)$vacante['id'] ?>" data-mi-reaccion="<?= esc($vacante['mi_reaccion'] ?? '') ?>">
          <div class="vd-metrics-pill">
            <div class="vd-metrics-group">
                <button type="button" class="vd-metric-btn vd-like<?= ($vacante['mi_reaccion'] ?? '') === 'me_gusta' ? ' active' : '' ?>" data-tipo="me_gusta" aria-pressed="<?= ($vacante['mi_reaccion'] ?? '') === 'me_gusta' ? 'true' : 'false' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="17" height="17"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                    <span class="vd-count-like"><?= (int)($vacante['me_gusta'] ?? 0) ?></span>
                </button>
                <button type="button" class="vd-metric-btn vd-dislike<?= ($vacante['mi_reaccion'] ?? '') === 'no_me_gusta' ? ' active' : '' ?>" data-tipo="no_me_gusta" aria-pressed="<?= ($vacante['mi_reaccion'] ?? '') === 'no_me_gusta' ? 'true' : 'false' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="17" height="17"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H6.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3z"/><path d="M17 2h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"/></svg>
                    <span class="vd-count-dislike"><?= (int)($vacante['no_me_gusta'] ?? 0) ?></span>
                </button>
                <button type="button" class="vd-metric-btn vd-share" id="vdShareBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="17" height="17"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    <span class="vd-count-share"><?= (int)($vacante['compartidos'] ?? 0) ?></span>
                </button>
            </div>
            <div class="vd-metrics-views">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <span><?= (int)($vacante['vistas'] ?? 0) ?> vistas</span>
            </div>
          </div>
        </div>
    </div>

    <?php if (!empty($vacante['latitud']) && !empty($vacante['longitud'])): ?>
    <div class="card vd-section">
        <h2 class="vd-section-title">Ubicacion</h2>
        <div id="vacanteMapa"
             data-lat="<?= esc($vacante['latitud']) ?>"
             data-lng="<?= esc($vacante['longitud']) ?>"
             data-titulo="<?= esc($vacante['ciudad'] ?? '') ?><?= !empty($vacante['region']) ? ', ' . esc($vacante['region']) : '' ?>"
             style="height:320px;border-radius:10px;z-index:0;">
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($vacante['poster_url'])): ?>
    <div class="card vd-section">
        <h2 class="vd-section-title">Anuncio original</h2>
        <img class="vd-poster" src="<?= esc($vacante['poster_url']) ?>" alt="Anuncio de la vacante" loading="lazy">
    </div>
    <?php endif; ?>

    <div class="card vd-section">
        <h2 class="vd-section-title">Descripcion</h2>
        <div class="vd-section-content"><?= nl2br(esc($vacante['descripcion'])) ?></div>
    </div>

    <?php if (!empty($vacante['funciones'])): ?>
    <div class="card vd-section">
        <h2 class="vd-section-title">Funciones</h2>
        <div class="vd-section-content"><?= nl2br(esc($vacante['funciones'])) ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($vacante['empresa_nombre'])): ?>
    <div class="card vd-section">
        <h2 class="vd-section-title">Sobre la Empresa</h2>
        <div class="vd-section-content" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div style="width:48px;height:48px;border-radius:12px;background:#1B2A4A;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:20px;flex-shrink:0;">
                <?php if (!empty($vacante['empresa_logo'])): ?>
                    <img src="<?= base_url('uploads/' . $vacante['empresa_logo']) ?>" alt="<?= esc($vacante['empresa_nombre']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                <?php else: ?>
                    <?= strtoupper(substr($vacante['empresa_nombre'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div>
                <strong style="font-size:16px;color:#1B2A4A;display:block;"><?= esc($vacante['empresa_nombre']) ?></strong>
                <span style="font-size:13px;color:#64748B;">
                    <?= !empty($vacante['empresa_rubro']) ? esc($vacante['empresa_rubro']) . ' · ' : '' ?>Publicada en CONEX
                </span>
                <?php if (!empty($vacante['empresa_sitio_web'])): ?>
                    <div style="margin-top:4px;">
                        <a href="<?= esc($vacante['empresa_sitio_web']) ?>" target="_blank" rel="noopener noreferrer" style="font-size:13px;color:#0F766E;font-weight:600;text-decoration:none;">
                            Sitio web &rarr;
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card vd-cta">
        <div class="vd-cta-content">
            <h2>Interesado en esta vacante?</h2>
            <p>Inicia sesion o registrate para postularte</p>
        </div>
        <div class="vd-cta-actions">
            <a href="<?= base_url('login') ?>" class="btn btn-primary">Iniciar sesion</a>
            <a href="<?= base_url('registro') ?>" class="btn btn-outline">Registrarse</a>
        </div>
    </div>
</div>

<script>
(function () {
    const bar = document.querySelector('.vd-metrics');
    if (!bar) return;
    const vacanteId = bar.dataset.vacanteId;
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';

    async function postMetric(url, tipo) {
        const body = new URLSearchParams();
        if (tipo) body.set('tipo', tipo);
        body.set(csrfName, csrfHash);
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body,
            });
            return await res.json();
        } catch (e) {
            return null;
        }
    }

    bar.querySelectorAll('.vd-like, .vd-dislike').forEach(btn => {
        btn.addEventListener('click', async () => {
            const data = await postMetric(`<?= base_url() ?>vacante/${vacanteId}/reaccion`, btn.dataset.tipo);
            if (!data || data.error) return;
            bar.querySelector('.vd-count-like').textContent = data.me_gusta;
            bar.querySelector('.vd-count-dislike').textContent = data.no_me_gusta;
            bar.querySelector('.vd-like').classList.toggle('active', data.mi_reaccion === 'me_gusta');
            bar.querySelector('.vd-dislike').classList.toggle('active', data.mi_reaccion === 'no_me_gusta');
            bar.querySelector('.vd-like').setAttribute('aria-pressed', data.mi_reaccion === 'me_gusta');
            bar.querySelector('.vd-dislike').setAttribute('aria-pressed', data.mi_reaccion === 'no_me_gusta');
        });
    });

    document.getElementById('vdShareBtn').addEventListener('click', async () => {
        const url = window.location.href;
        if (navigator.share) {
            try { await navigator.share({ title: document.title, url }); } catch (e) {}
        } else if (navigator.clipboard) {
            try { await navigator.clipboard.writeText(url); } catch (e) {}
        }
        const data = await postMetric(`<?= base_url() ?>vacante/${vacanteId}/compartir`);
        if (data && data.compartidos !== undefined) {
            bar.querySelector('.vd-count-share').textContent = data.compartidos;
        }
    });
})();
</script>
