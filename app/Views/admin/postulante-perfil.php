<div class="page-header">
    <div>
        <a href="<?= base_url('admin/postulantes') ?>" class="btn btn-ghost btn-sm" style="margin-bottom:8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
            Volver
        </a>
        <h1><?= esc($candidato['nombre'] . ' ' . $candidato['apellido']) ?></h1>
        <p><?= esc($candidato['email']) ?> &middot; <?= esc($candidato['telefono'] ?? 'Sin telefono') ?></p>
    </div>
    <a href="<?= base_url('admin/postulantes/ver/' . $candidato['id']) ?>" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="vertical-align:middle;margin-right:6px;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Ir a verificar
    </a>
</div>

<div class="tabs">
    <button class="tab-btn active" data-tab="perfil">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Perfil</span>
    </button>
    <button class="tab-btn" data-tab="postulaciones">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        <span>Postulaciones (<?= count($postulaciones) ?>)</span>
    </button>
    <button class="tab-btn" data-tab="experiencia">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        <span>Experiencia</span>
    </button>
    <button class="tab-btn" data-tab="educacion">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        <span>Educacion</span>
    </button>
    <button class="tab-btn" data-tab="skills">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <span>Skills</span>
    </button>
    <button class="tab-btn" data-tab="cvs">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <span>CVs</span>
    </button>
</div>

<!-- Tab: Perfil -->
<div class="tab-pane active" data-pane="perfil">
    <div class="dash-card pa-profile-card">
        <div class="pa-profile-top">
            <div class="pa-avatar">
                <?php if (!empty($candidato['avatar'])): ?>
                <img src="<?= base_url('uploads/avatars/' . $candidato['avatar']) ?>" alt="avatar">
                <?php else: ?>
                <?= strtoupper(substr($candidato['nombre'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div class="pa-profile-info">
                <h2><?= esc($candidato['nombre'] . ' ' . $candidato['apellido']) ?></h2>
                <p class="pa-profesion"><?= esc($candidato['profesion'] ?? 'Sin profesion') ?></p>
                <div class="pa-contact-grid">
                    <div class="pa-contact-item">
                        <span class="pa-contact-label">Email</span>
                        <span class="pa-contact-value"><?= esc($candidato['email']) ?></span>
                    </div>
                    <div class="pa-contact-item">
                        <span class="pa-contact-label">Telefono</span>
                        <span class="pa-contact-value"><?= esc($candidato['telefono'] ?? '-') ?></span>
                    </div>
                    <div class="pa-contact-item">
                        <span class="pa-contact-label">Ciudad</span>
                        <span class="pa-contact-value"><?= esc($candidato['ciudad'] ?? '-') ?></span>
                    </div>
                    <div class="pa-contact-item">
                        <span class="pa-contact-label">Nivel experiencia</span>
                        <span class="pa-contact-value"><?= esc($candidato['nivel_exp_nombre'] ?? '-') ?></span>
                    </div>
                    <div class="pa-contact-item">
                        <span class="pa-contact-label">Disponibilidad</span>
                        <span class="pa-contact-value"><?= esc($candidato['disponibilidad'] ?? '-') ?></span>
                    </div>
                    <div class="pa-contact-item">
                        <span class="pa-contact-label">Linkedin</span>
                        <?php if (!empty($candidato['linkedin_url'])): ?>
                        <a href="<?= esc($candidato['linkedin_url']) ?>" target="_blank" class="pa-contact-link"><?= esc($candidato['linkedin_url']) ?></a>
                        <?php else: ?>
                        <span class="pa-contact-value">-</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($candidato['resumen_profesional'])): ?>
        <div class="pa-resumen">
            <h3>Resumen profesional</h3>
            <p><?= nl2br(esc($candidato['resumen_profesional'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tab: Postulaciones -->
<div class="tab-pane" data-pane="postulaciones">
    <?php if (!empty($postulaciones)): ?>
    <div class="pa-postulaciones-grid">
        <?php foreach ($postulaciones as $p):
            $proceso = $p['proceso'] ?? null;
            $pasoActual = $proceso['paso_actual'] ?? 0;
            $estadoProceso = $proceso['estado'] ?? 'pendiente';

            $puntaje = 0;
            $totalEvals = 0;
            if (!empty($p['evaluaciones'])):
                foreach ($p['evaluaciones'] as $ev):
                    if ($ev['calificacion'] > 0 && $ev['criterio'] !== 'recomendacion_final'):
                        $puntaje += $ev['calificacion'];
                        $totalEvals++;
                    endif;
                endforeach;
            endif;
            $puntajePromedio = $totalEvals > 0 ? round($puntaje / $totalEvals, 1) : 0;

            $estadoColors = [
                'enviada' => 'orange',
                'en_proceso' => 'blue',
                'rechazado' => 'red',
                'contratado' => 'green',
            ];
            $colorClass = $estadoColors[$p['estado']] ?? 'orange';
            $totalDocs = count($p['documentos']);
            $totalReqs = count($p['requisitos']);
        ?>
        <div class="pa-postulacion-card" data-modal="postulacion-<?= $p['postulacion_id'] ?>">
            <div class="pa-postulacion-top">
                <div class="pa-postulacion-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div class="pa-postulacion-info">
                    <h3><?= esc($p['vacante_titulo']) ?></h3>
                    <p><?= esc($p['empresa_nombre']) ?></p>
                </div>
                <span class="verif-badge verif-badge-<?= $colorClass ?>">
                    <?= ucfirst(str_replace('_', ' ', $p['estado'])) ?>
                </span>
            </div>
            <div class="pa-postulacion-stats">
                <div class="pa-postulacion-stat">
                    <span class="pa-postulacion-stat-label">Paso</span>
                    <span class="pa-postulacion-stat-value"><?= $pasoActual > 0 ? $pasoActual . '/6' : '-' ?></span>
                </div>
                <div class="pa-postulacion-stat">
                    <span class="pa-postulacion-stat-label">Puntaje</span>
                    <span class="pa-postulacion-stat-value <?= $puntajePromedio >= 4 ? 'pa-score-high' : ($puntajePromedio >= 3 ? 'pa-score-mid' : 'pa-score-low') ?>"><?= $puntajePromedio > 0 ? $puntajePromedio . '/5' : '-' ?></span>
                </div>
                <div class="pa-postulacion-stat">
                    <span class="pa-postulacion-stat-label">Docs</span>
                    <span class="pa-postulacion-stat-value"><?= $totalDocs ?>/<?= $totalReqs ?></span>
                </div>
                <div class="pa-postulacion-stat">
                    <span class="pa-postulacion-stat-label">Verificacion</span>
                    <span class="pa-postulacion-stat-value">
                        <?php if ($estadoProceso === 'completado'): ?>
                            <span class="pa-verif-done">Completado</span>
                        <?php elseif ($estadoProceso === 'en_proceso'): ?>
                            <span class="pa-verif-active">En proceso</span>
                        <?php else: ?>
                            <span class="pa-verif-pending">Pendiente</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="pa-postulacion-footer">
                <span class="pa-postulacion-fecha"><?= date('d/m/Y', strtotime($p['created_at'])) ?></span>
                <span class="pa-postulacion-click">
                    Ver detalle
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="dash-card">
        <div class="empty-state"><p>Este candidato no tiene postulaciones.</p></div>
    </div>
    <?php endif; ?>
</div>

<!-- Tab: Experiencia -->
<div class="tab-pane" data-pane="experiencia">
    <div class="dash-card">
        <div class="section-header">
            <h2>Experiencia laboral</h2>
        </div>
        <?php if (!empty($experiencias)): ?>
        <div class="timeline">
            <?php foreach ($experiencias as $exp): ?>
            <div class="timeline-item">
                <div class="timeline-item-header">
                    <div>
                        <div class="timeline-item-title"><?= esc($exp['cargo'] ?? '-') ?></div>
                        <div class="timeline-item-company"><?= esc($exp['empresa'] ?? '-') ?></div>
                    </div>
                    <span class="timeline-item-dates">
                        <?= esc(date('M Y', strtotime($exp['fecha_inicio'])) . ' - ' . (!empty($exp['fecha_fin']) ? date('M Y', strtotime($exp['fecha_fin'])) : 'Actual')) ?>
                    </span>
                </div>
                <?php if (!empty($exp['descripcion'])): ?>
                <div class="timeline-item-desc"><?= nl2br(esc($exp['descripcion'])) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><p>Sin experiencia registrada.</p></div>
        <?php endif; ?>
    </div>
</div>

<!-- Tab: Educacion -->
<div class="tab-pane" data-pane="educacion">
    <div class="dash-card">
        <div class="section-header">
            <h2>Educacion</h2>
        </div>
        <?php if (!empty($educacion)): ?>
        <?php foreach ($educacion as $edu): ?>
        <div class="edu-item">
            <div class="edu-item-header">
                <div>
                    <div class="edu-item-title"><?= esc($edu['titulo'] ?? '-') ?></div>
                    <div class="edu-item-institution"><?= esc($edu['institucion'] ?? '-') ?></div>
                </div>
                <span class="timeline-item-dates">
                    <?= esc(!empty($edu['fecha_inicio']) ? date('M Y', strtotime($edu['fecha_inicio'])) : '') ?>
                    <?= !empty($edu['fecha_fin']) ? ' - ' . date('M Y', strtotime($edu['fecha_fin'])) : '' ?>
                </span>
            </div>
            <?php if (!empty($edu['nivel_nombre'])): ?>
            <span class="verif-badge verif-badge-blue" style="margin-top:6px;"><?= esc($edu['nivel_nombre']) ?></span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state"><p>Sin educacion registrada.</p></div>
        <?php endif; ?>
    </div>
</div>

<!-- Tab: Skills -->
<div class="tab-pane" data-pane="skills">
    <div class="dash-card">
        <div class="section-header">
            <h2>Habilidades</h2>
        </div>
        <?php if (!empty($habilidades)): ?>
        <div class="skills-container">
            <?php foreach ($habilidades as $h): ?>
            <span class="skill-tag"><?= esc($h['habilidad_nombre'] ?? '-') ?> <span style="opacity:0.6;font-size:12px;"><?= esc($h['nivel'] ?? '') ?></span></span>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><p>Sin habilidades registradas.</p></div>
        <?php endif; ?>
    </div>
    <div class="dash-card" style="margin-top:16px;">
        <div class="section-header">
            <h2>Idiomas</h2>
        </div>
        <?php if (!empty($idiomas)): ?>
        <div class="skills-container">
            <?php foreach ($idiomas as $i): ?>
            <span class="skill-tag"><?= esc($i['idioma_nombre'] ?? '-') ?> <span style="opacity:0.6;font-size:12px;"><?= esc($i['nivel'] ?? '') ?></span></span>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><p>Sin idiomas registrados.</p></div>
        <?php endif; ?>
    </div>
</div>

<!-- Tab: CVs -->
<div class="tab-pane" data-pane="cvs">
    <div class="dash-card">
        <div class="section-header">
            <h2>Curriculums</h2>
        </div>
        <?php if (!empty($cvs)): ?>
        <?php foreach ($cvs as $cv): ?>
        <div class="cv-card">
            <div class="cv-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="cv-card-info">
                <div class="cv-card-name"><?= esc($cv['nombre_archivo'] ?? 'CV') ?></div>
                <div class="cv-card-meta"><?= esc(date('d/m/Y', strtotime($cv['created_at']))) ?></div>
            </div>
            <div class="cv-card-actions">
                <a href="<?= base_url('uploads/cvs/' . ($cv['ruta_archivo'] ?? '')) ?>" target="_blank" class="btn-icon" title="Ver">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state"><p>Sin CVs registrados.</p></div>
        <?php endif; ?>
    </div>
</div>

<!-- Modales de postulaciones -->
<?php foreach ($postulaciones as $p):
    $proceso = $p['proceso'] ?? null;
    $pasoActual = $proceso['paso_actual'] ?? 1;
    $estadoProceso = $proceso['estado'] ?? 'pendiente';
    $estadoColors = [
        'enviada' => 'orange',
        'en_proceso' => 'blue',
        'rechazado' => 'red',
        'contratado' => 'green',
    ];
    $colorClass = $estadoColors[$p['estado']] ?? 'orange';
?>
<div class="modal-overlay" id="postulacion-<?= $p['postulacion_id'] ?>">
    <div class="modal-box modal-box-lg">
        <div class="modal-header">
            <div>
                <h2><?= esc($p['vacante_titulo']) ?></h2>
                <p style="font-size:13px;color:var(--text-secondary);margin-top:2px;">
                    <?= esc($p['empresa_nombre']) ?> &middot; Postulacion: <?= date('d/m/Y', strtotime($p['created_at'])) ?>
                </p>
            </div>
            <button type="button" class="modal-close" data-close-modal="postulacion-<?= $p['postulacion_id'] ?>" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="pa-modal-status-bar">
                <div class="pa-modal-status-item">
                    <span class="pa-modal-status-label">Estado empresa</span>
                    <span class="verif-badge verif-badge-<?= $colorClass ?>"><?= ucfirst(str_replace('_', ' ', $p['estado'])) ?></span>
                </div>
                <div class="pa-modal-status-item">
                    <span class="pa-modal-status-label">Verificacion</span>
                    <span class="verif-badge verif-badge-<?= $estadoProceso === 'completado' ? 'green' : ($estadoProceso === 'en_proceso' ? 'blue' : 'orange') ?>"><?= ucfirst(str_replace('_', ' ', $estadoProceso)) ?></span>
                </div>
                <div class="pa-modal-status-item">
                    <span class="pa-modal-status-label">Paso actual</span>
                    <span class="pa-modal-status-value"><?= $pasoActual ?>/6</span>
                </div>
            </div>

            <div class="pipeline-steps">
                <?php foreach ($pasos as $num => $label): ?>
                    <?php
                    $clase = 'pipeline-step';
                    if ($proceso && $num < $pasoActual) $clase .= ' pipeline-step-done';
                    elseif ($proceso && $num == $pasoActual) $clase .= ' pipeline-step-current';
                    else $clase .= ' pipeline-step-pending';
                    ?>
                    <div class="<?= $clase ?>">
                        <div class="pipeline-step-num"><?= $num ?></div>
                        <div class="pipeline-step-label"><?= esc($label) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($proceso): ?>
            <div class="pipeline-info">
                <?php
                $fechas = [
                    1 => ['fecha_asignacion', 'Asignacion'],
                    2 => ['fecha_contacto', 'Contacto'],
                    3 => ['fecha_documentos', 'Documentos'],
                    4 => ['fecha_entrevista', 'Entrevista'],
                    5 => ['fecha_evaluacion', 'Evaluacion'],
                    6 => ['fecha_presentacion', 'Presentacion'],
                ];
                foreach ($fechas as $num => $info):
                    if (!empty($proceso[$info[0]])):
                ?>
                <div class="pipeline-info-row">
                    <span class="pipeline-info-label"><?= $info[1] ?>:</span>
                    <span class="pipeline-info-value"><?= date('d/m/Y H:i', strtotime($proceso[$info[0]])) ?></span>
                </div>
                <?php endif; endforeach; ?>
            </div>

            <?php if (!empty($p['historial'])): ?>
            <div class="pipeline-timeline">
                <div class="pipeline-timeline-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Historial de pasos completados
                </div>
                <?php foreach ($p['historial'] as $h): ?>
                <div class="pipeline-timeline-item">
                    <div class="pipeline-timeline-dot"></div>
                    <div class="pipeline-timeline-content">
                        <div class="pipeline-timeline-header">
                            <span class="pipeline-timeline-paso">Paso <?= $h['paso'] ?>: <?= esc($pasos[$h['paso']] ?? '') ?></span>
                            <span class="pipeline-timeline-fecha"><?= date('d/m/Y H:i', strtotime($h['fecha'])) ?></span>
                        </div>
                        <?php if (!empty($h['notas'])): ?>
                        <div class="pipeline-timeline-notas"><?= esc($h['notas']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($p['evaluaciones'])): ?>
            <div class="eval-section" style="margin-top:16px;">
                <div class="eval-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M9 11H5a2 2 0 0 0-2 2v7h18v-7a2 2 0 0 0-2-2h-4"/><path d="M15 11V5a3 3 0 0 0-6 0v6"/></svg>
                    <span>Evaluaciones registradas</span>
                </div>
                <?php foreach ($p['evaluaciones'] as $ev): ?>
                <div class="pa-eval-row">
                    <span class="pa-eval-criterio"><?= esc(ucfirst(str_replace('_', ' ', $ev['criterio']))) ?></span>
                    <span class="pa-eval-calif"><?= $ev['calificacion'] ?>/5</span>
                    <?php if (!empty($ev['observacion'])): ?>
                    <span class="pa-eval-obs"><?= esc($ev['observacion']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div class="empty-state" style="padding:24px;">
                <p>El proceso de verificacion no ha sido iniciado.</p>
                <a href="<?= base_url('admin/postulantes/ver/' . $candidato['id']) ?>" class="btn btn-primary">Iniciar verificacion</a>
            </div>
            <?php endif; ?>

            <?php if (!empty($p['requisitos'])): ?>
            <div class="pipeline-docs-section" style="margin-top:16px;">
                <div class="pipeline-docs-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span>Documentos de la vacante</span>
                    <?php $totalDocs = count($p['documentos']); $totalReqs = count($p['requisitos']); ?>
                    <span class="pipeline-docs-progress <?= $totalDocs == $totalReqs ? 'pipeline-docs-progress-complete' : '' ?>">
                        <?= $totalDocs ?>/<?= $totalReqs ?> documentos
                    </span>
                </div>
                <div class="verif-requisitos-list">
                <?php foreach ($p['requisitos'] as $req): ?>
                    <?php
                    $docAsociado = null;
                    foreach ($p['documentos'] as $doc) {
                        if ($doc['requisito_id'] == $req['requisito_id']) {
                            $docAsociado = $doc;
                            break;
                        }
                    }
                    ?>
                    <div class="verif-requisito-item">
                        <div class="verif-requisito-check">
                            <?php if ($docAsociado): ?>
                                <span class="verif-doc-status verif-doc-status-<?= $docAsociado['estado'] ?>">
                                    <?php if ($docAsociado['estado'] === 'aprobado'): ?>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    <?php elseif ($docAsociado['estado'] === 'rechazado'): ?>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    <?php else: ?>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="verif-doc-status verif-doc-status-faltante">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="verif-requisito-content">
                            <div class="verif-requisito-nombre">
                                <?= esc($req['requisito_nombre']) ?>
                                <span class="verif-requisito-tipo"><?= esc($req['tipo']) ?></span>
                            </div>
                            <?php if ($docAsociado): ?>
                            <div class="verif-doc-info">
                                <span style="font-size:13px;color:var(--text-secondary);"><?= esc($docAsociado['nombre_documento']) ?></span>
                                <span class="verif-doc-estado verif-doc-estado-<?= $docAsociado['estado'] ?>"><?= ucfirst($docAsociado['estado']) ?></span>
                            </div>
                            <?php else: ?>
                            <div class="verif-doc-faltante" style="margin-bottom:8px;">Pendiente de entrega</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="modal-footer">
            <a href="<?= base_url('admin/postulantes/ver/' . $candidato['id']) ?>" class="btn btn-primary">Gestionar verificacion</a>
            <button type="button" class="btn btn-ghost" data-close-modal="postulacion-<?= $p['postulacion_id'] ?>">Cerrar</button>
        </div>
    </div>
</div>
<?php endforeach; ?>
