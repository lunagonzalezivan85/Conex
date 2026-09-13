<div class="page-header">
    <div>
        <a href="<?= base_url('empresa/postulantes') ?>" class="btn btn-ghost btn-sm" style="margin-bottom:8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"><polyline points="15 18 9 12 15 6"/></svg>
            Volver
        </a>
        <h1>Perfil del Postulante</h1>
        <p>Informacion del candidato para la vacante <?= esc($postulacion['vacante_titulo'] ?? '') ?></p>
    </div>
</div>

<?php if (!empty($candidato)): ?>
<div class="pd-container">
    <!-- Fecha entrevista y estado -->
    <div class="dash-card pd-estado-card">
        <div class="pd-estado-left">
            <div class="pd-estado-info">
                <span class="pd-estado-label">Estado actual</span>
                <?php
                    $estadoActual = $postulacion['estado'] ?? 'enviada';
                    $estadoLabels = [
                        'enviada' => ['text' => 'Enviada', 'color' => '#6b7280', 'bg' => '#f3f4f6'],
                        'en_proceso' => ['text' => 'En proceso', 'color' => '#4361EE', 'bg' => '#eef2ff'],
                        'rechazado' => ['text' => 'Rechazado', 'color' => '#ef4444', 'bg' => '#fee2e2'],
                        'contratado' => ['text' => 'Contratado', 'color' => '#16a34a', 'bg' => '#dcfce7'],
                    ];
                    $ea = $estadoLabels[$estadoActual] ?? $estadoLabels['enviada'];
                ?>
                <span class="pd-estado-badge" style="background:<?= $ea['bg'] ?>;color:<?= $ea['color'] ?>;"><?= esc($ea['text']) ?></span>
            </div>
            <?php if (!empty($fechaEntrevista)): ?>
            <div class="pd-estado-info">
                <span class="pd-estado-label">Fecha de entrevista</span>
                <span class="pd-entrevista-fecha">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?= date('d M Y, H:i', strtotime($fechaEntrevista)) ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
        <div class="pd-estado-actions" id="pd-estado-actions">
            <?php if ($estadoActual === 'contratado'): ?>
                <span class="pd-estado-locked">Contratado - No se pueden realizar cambios</span>
            <?php else: ?>
                <button type="button" class="btn btn-estado btn-estado-proceso" data-estado="en_proceso" data-postulacion="<?= $postulacion['id'] ?>" <?= $estadoActual === 'en_proceso' ? 'disabled' : '' ?>>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    En proceso
                </button>
                <button type="button" class="btn btn-estado btn-estado-rechazar" data-estado="rechazado" data-postulacion="<?= $postulacion['id'] ?>" <?= $estadoActual === 'rechazado' ? 'disabled' : '' ?>>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    Rechazar
                </button>
                <button type="button" class="btn btn-estado btn-estado-contratar" data-estado="contratado" data-postulacion="<?= $postulacion['id'] ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c2.39 0 4.68.94 6.36 2.64"/></svg>
                    Contratar
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Header del perfil -->
    <div class="dash-card pd-header-card">
        <div class="pd-header-top">
        <div class="pd-avatar">
            <?php if (!empty($candidato['avatar'])): ?>
            <img src="<?= base_url('uploads/avatars/' . $candidato['avatar']) ?>" alt="avatar">
            <?php else: ?>
            <?= strtoupper(substr($candidato['nombre'] ?? 'U', 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div class="pd-header-info">
            <h2 class="pd-name"><?= esc(($candidato['nombre'] ?? '') . ' ' . ($candidato['apellidos'] ?? '')) ?></h2>
            <p class="pd-profesion"><?= esc($candidato['profesion'] ?? 'Sin profesion') ?></p>
            <div class="pd-contact">
                <?php if (!empty($candidato['email'])): ?>
                <span class="pd-contact-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <?= esc($candidato['email']) ?>
                </span>
                <?php endif; ?>
                <?php if (!empty($candidato['telefono'])): ?>
                <span class="pd-contact-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <?= esc($candidato['telefono']) ?>
                </span>
                <?php endif; ?>
                <?php if (!empty($candidato['ciudad'])): ?>
                <span class="pd-contact-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <?= esc($candidato['ciudad']) ?><?= !empty($candidato['region']) ? ', ' . esc($candidato['region']) : '' ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="pd-header-extra">
            <?php
                $dispLabels = ['inmediata' => 'Inmediata', '15_dias' => '15 dias', '30_dias' => '30 dias', 'a_convenir' => 'A convenir'];
                $modLabels = ['presencial' => 'Presencial', 'remoto' => 'Remoto', 'hibrido' => 'Hibrido', 'indiferente' => 'Indiferente'];
            ?>
            <?php if (!empty($candidato['disponibilidad'])): ?>
            <div class="pd-extra-item">
                <span class="pd-extra-label">Disponibilidad</span>
                <span class="pd-extra-value"><?= esc($dispLabels[$candidato['disponibilidad']] ?? $candidato['disponibilidad']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($candidato['modalidad_preferida'])): ?>
            <div class="pd-extra-item">
                <span class="pd-extra-label">Modalidad</span>
                <span class="pd-extra-value"><?= esc($modLabels[$candidato['modalidad_preferida']] ?? $candidato['modalidad_preferida']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($candidato['nivel_exp_nombre'])): ?>
            <div class="pd-extra-item">
                <span class="pd-extra-label">Experiencia</span>
                <span class="pd-extra-value"><?= esc($candidato['nivel_exp_nombre']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($candidato['salario_esperado_usd'])): ?>
            <div class="pd-extra-item">
                <span class="pd-extra-label">Salario esperado</span>
                <span class="pd-extra-value">$<?= number_format($candidato['salario_esperado_usd'], 0) ?> USD</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($candidato['sobre_mi'])): ?>
    <div class="dash-card pd-section">
        <h3 class="pd-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            Sobre mi
        </h3>
        <p class="pd-sobre-mi"><?= esc($candidato['sobre_mi']) ?></p>
    </div>
    <?php endif; ?>

    <!-- CVs -->
    <?php if (!empty($cvs)): ?>
    <div class="dash-card pd-section">
        <h3 class="pd-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Curriculum Vitae
        </h3>
        <div class="pd-cv-list">
            <?php foreach ($cvs as $cv): ?>
            <a href="<?= base_url('uploads/cv/' . $cv['archivo_path']) ?>" target="_blank" class="pd-cv-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;flex-shrink:0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <div class="pd-cv-info">
                    <span class="pd-cv-name"><?= esc($cv['archivo_nombre']) ?></span>
                    <?php if ($cv['es_principal']): ?>
                    <span class="pd-cv-badge">Principal</span>
                    <?php endif; ?>
                </div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Experiencia -->
    <?php if (!empty($experiencias)): ?>
    <div class="dash-card pd-section">
        <h3 class="pd-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Experiencia laboral
        </h3>
        <div class="pd-timeline">
            <?php foreach ($experiencias as $exp): ?>
            <div class="pd-timeline-item">
                <div class="pd-timeline-dot"></div>
                <div class="pd-timeline-content">
                    <h4 class="pd-timeline-title"><?= esc($exp['cargo']) ?></h4>
                    <p class="pd-timeline-company"><?= esc($exp['empresa']) ?></p>
                    <p class="pd-timeline-date">
                        <?= date('M Y', strtotime($exp['fecha_inicio'])) ?> -
                        <?php if ($exp['actual']): ?>
                        Actual
                        <?php else: ?>
                        <?= !empty($exp['fecha_fin']) ? date('M Y', strtotime($exp['fecha_fin'])) : 'N/A' ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($exp['descripcion'])): ?>
                    <p class="pd-timeline-desc"><?= esc($exp['descripcion']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Educacion -->
    <?php if (!empty($educacion)): ?>
    <div class="dash-card pd-section">
        <h3 class="pd-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1 2 3 6 3s6-2 6-3v-5"/></svg>
            Educacion
        </h3>
        <div class="pd-timeline">
            <?php foreach ($educacion as $edu): ?>
            <div class="pd-timeline-item">
                <div class="pd-timeline-dot"></div>
                <div class="pd-timeline-content">
                    <h4 class="pd-timeline-title"><?= esc($edu['titulo']) ?></h4>
                    <p class="pd-timeline-company"><?= esc($edu['institucion']) ?></p>
                    <p class="pd-timeline-date">
                        <?= !empty($edu['nivel_nombre']) ? esc($edu['nivel_nombre']) : '' ?>
                        · <?= date('M Y', strtotime($edu['fecha_inicio'])) ?> -
                        <?php if ($edu['en_curso']): ?>
                        En curso
                        <?php else: ?>
                        <?= !empty($edu['fecha_fin']) ? date('M Y', strtotime($edu['fecha_fin'])) : 'N/A' ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="pd-two-col">
        <!-- Habilidades -->
        <?php if (!empty($habilidades)): ?>
        <div class="dash-card pd-section">
            <h3 class="pd-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Habilidades
            </h3>
            <div class="pd-skills">
                <?php foreach ($habilidades as $hab): ?>
                <span class="pd-skill-tag pd-skill-<?= esc($hab['nivel']) ?>">
                    <?= esc($hab['habilidad_nombre'] ?? 'N/A') ?>
                    <span class="pd-skill-level"><?= esc($hab['nivel']) ?></span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Idiomas -->
        <?php if (!empty($idiomas)): ?>
        <div class="dash-card pd-section">
            <h3 class="pd-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Idiomas
            </h3>
            <div class="pd-skills">
                <?php foreach ($idiomas as $idi): ?>
                <span class="pd-skill-tag pd-skill-idioma">
                    <?= esc($idi['idioma_nombre'] ?? 'N/A') ?>
                    <span class="pd-skill-level"><?= esc($idi['nivel']) ?></span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Links -->
    <?php if (!empty($candidato['linkedin_url']) || !empty($candidato['portafolio_url'])): ?>
    <div class="dash-card pd-section">
        <h3 class="pd-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            Enlaces
        </h3>
        <div class="pd-links">
            <?php if (!empty($candidato['linkedin_url'])): ?>
            <a href="<?= esc($candidato['linkedin_url']) ?>" target="_blank" class="pd-link-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                LinkedIn
            </a>
            <?php endif; ?>
            <?php if (!empty($candidato['portafolio_url'])): ?>
            <a href="<?= esc($candidato['portafolio_url']) ?>" target="_blank" class="pd-link-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Portafolio
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="dash-card">
    <div class="empty-state">
        <h3>Postulante no encontrado</h3>
        <p>No se pudo encontrar la informacion del candidato.</p>
        <a href="<?= base_url('empresa/postulantes') ?>" class="btn btn-primary">Volver a postulantes</a>
    </div>
</div>
<?php endif; ?>
