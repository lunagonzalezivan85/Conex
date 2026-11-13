<?php
    $fullName = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''));
    if (empty($fullName)) $fullName = $usuario['usuario'] ?? 'Usuario sin nombre';
    $roleSlug = $usuario['role_slug'] ?? 'candidato';
    $roleName = $usuario['role_nombre'] ?? ucfirst($roleSlug);
    $initial = strtoupper(substr($usuario['nombre'] ?: ($usuario['usuario'] ?: 'U'), 0, 1));
    $hasAvatar = !empty($usuario['avatar']);
    $avatarUrl = $hasAvatar ? base_url('uploads/avatars/' . $usuario['avatar']) : '';
    $regDate = !empty($usuario['created_at']) ? date('d/m/Y H:i', strtotime($usuario['created_at'])) : '-';
    $lastLogin = !empty($usuario['last_login']) ? date('d/m/Y H:i', strtotime($usuario['last_login'])) : 'Nunca';
?>

<div class="perfil-usuario-page">
    <!-- Top Nav / Back -->
    <div class="perfil-top-nav">
        <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-ghost btn-sm">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;vertical-align:middle;"><polyline points="15 18 9 12 15 6"/></svg>
            Volver a la lista de usuarios
        </a>
        <div style="display:flex;gap:8px;">
            <?php if ($roleSlug === 'candidato' && !empty($candidato['id'])): ?>
                <a href="<?= base_url('admin/postulantes/perfil/' . $candidato['id']) ?>" class="btn btn-primary btn-sm">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    Expediente de Verificación
                </a>
            <?php elseif ($roleSlug === 'empresa'): ?>
                <a href="<?= base_url('admin/vacantes') ?>" class="btn btn-primary btn-sm">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:middle;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Gestionar Vacantes
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="perfil-layout-grid">
        <!-- Left Column: User Card -->
        <aside class="perfil-sidebar-card">
            <div class="perfil-sidebar-cover"></div>
            <div class="perfil-sidebar-user">
                <div class="perfil-large-avatar">
                    <?php if ($hasAvatar): ?>
                        <img src="<?= $avatarUrl ?>" alt="<?= esc($fullName) ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="user-avatar-initials role-<?= esc($roleSlug) ?>" style="display:none;"><?= $initial ?></div>
                    <?php else: ?>
                        <div class="user-avatar-initials role-<?= esc($roleSlug) ?>"><?= $initial ?></div>
                    <?php endif; ?>
                </div>

                <h2 class="perfil-full-name"><?= esc($fullName) ?></h2>
                <div class="perfil-handle">@<?= esc($usuario['usuario']) ?></div>

                <div class="perfil-badges-cluster">
                    <span class="badge-role <?= esc($roleSlug) ?>">
                        <?= esc($roleName) ?>
                    </span>
                    <span class="badge-status <?= $usuario['estado'] === 'activo' ? 'activo' : 'inactivo' ?>">
                        <span class="badge-status-dot"></span>
                        <?= $usuario['estado'] === 'activo' ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>

                <!-- Contact & Account Details -->
                <div class="perfil-contact-list">
                    <div class="perfil-contact-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <a href="mailto:<?= esc($usuario['email']) ?>"><?= esc($usuario['email']) ?></a>
                    </div>
                    <?php if (!empty($usuario['telefono'])): ?>
                        <div class="perfil-contact-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <a href="tel:<?= esc($usuario['telefono']) ?>"><?= esc($usuario['telefono']) ?></a>
                        </div>
                    <?php endif; ?>
                    <div class="perfil-contact-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Registro: <?= $regDate ?></span>
                    </div>
                    <div class="perfil-contact-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        <span>Último acceso: <?= $lastLogin ?></span>
                    </div>
                    <div class="perfil-contact-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <span>ID Usuario: #<?= (int)$usuario['id'] ?></span>
                    </div>
                </div>

                <div class="perfil-sidebar-actions">
                    <a href="mailto:<?= esc($usuario['email']) ?>" class="btn btn-outline btn-sm btn-block">
                        Enviar correo electrónico
                    </a>
                </div>
            </div>
        </aside>

        <!-- Right Column: Profile Content -->
        <main class="perfil-main-column">
            <!-- CANDIDATO PROFILE -->
            <?php if ($roleSlug === 'candidato'): ?>
                <!-- Basic Candidate Info -->
                <div class="perfil-section-card">
                    <div class="perfil-section-header">
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Información del Candidato
                        </h3>
                        <?php if (!empty($candidato['porcentaje_perfil'])): ?>
                            <span class="badge-role candidato">
                                <?= (int)$candidato['porcentaje_perfil'] ?>% perfil completado
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="modal-info-grid" style="margin-bottom:16px;">
                        <div class="modal-info-box">
                            <span class="label">Profesión / Ocupación</span>
                            <span class="value"><?= esc($candidato['profesion'] ?? 'No especificada') ?></span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Nivel de Experiencia</span>
                            <span class="value"><?= esc($candidato['nivel_exp_nombre'] ?? 'No especificado') ?></span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Ubicación</span>
                            <span class="value">
                                <?= esc(implode(', ', array_filter([$candidato['ciudad'] ?? '', $candidato['region'] ?? '', $candidato['pais'] ?? ''])) ?: 'No especificada') ?>
                            </span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Disponibilidad</span>
                            <span class="value"><?= esc($candidato['disponibilidad'] ?? 'A convenir') ?></span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Modalidad Preferida</span>
                            <span class="value"><?= esc(ucfirst($candidato['modalidad_preferida'] ?? 'Indiferente')) ?></span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Salario Esperado</span>
                            <span class="value">
                                <?= !empty($candidato['salario_esperado_usd']) ? '$' . number_format($candidato['salario_esperado_usd'], 2) . ' USD' : 'No especificado' ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($candidato['sobre_mi'])): ?>
                        <div style="margin-top:12px;">
                            <span class="label" style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);">Sobre mí</span>
                            <p style="margin-top:6px;font-size:14px;line-height:1.6;color:var(--text-primary);background:#F8FAFC;padding:14px 16px;border-radius:12px;border:1px solid #E2E8F0;">
                                <?= nl2br(esc($candidato['sobre_mi'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Social / Portfolio links -->
                    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;">
                        <?php if (!empty($candidato['linkedin_url'])): ?>
                            <a href="<?= esc($candidato['linkedin_url']) ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
                                LinkedIn &#8599;
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($candidato['portafolio_url'])): ?>
                            <a href="<?= esc($candidato['portafolio_url']) ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
                                Portafolio / Web &#8599;
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Habilidades e Idiomas -->
                <div class="perfil-section-card">
                    <div class="perfil-section-header">
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            Habilidades e Idiomas
                        </h3>
                    </div>

                    <div style="margin-bottom:14px;">
                        <span class="label" style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);display:block;margin-bottom:8px;">Habilidades</span>
                        <?php if (!empty($habilidades)): ?>
                            <div class="modal-skills-wrap">
                                <?php foreach ($habilidades as $h): ?>
                                    <span class="modal-skill-tag"><?= esc($h['habilidad_nombre'] ?? $h['nombre'] ?? '') ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color:var(--text-secondary);font-size:13.5px;margin:0;">Sin habilidades registradas.</p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($idiomas)): ?>
                        <div style="border-top:1px solid #F1F5F9;padding-top:14px;">
                            <span class="label" style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);display:block;margin-bottom:8px;">Idiomas</span>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <?php foreach ($idiomas as $i): ?>
                                    <span class="badge-sub">
                                        <?= esc($i['idioma_nombre'] ?? 'Idioma') ?>: <?= esc($i['nivel'] ?? '') ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Experiencia Laboral -->
                <div class="perfil-section-card">
                    <div class="perfil-section-header">
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            Experiencia Laboral
                        </h3>
                    </div>

                    <?php if (!empty($experiencias)): ?>
                        <div class="perfil-timeline">
                            <?php foreach ($experiencias as $exp): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <h4 class="timeline-title"><?= esc($exp['cargo']) ?></h4>
                                    <div class="timeline-sub"><?= esc($exp['empresa']) ?> &middot; <?= esc($exp['ciudad'] ?? '') ?></div>
                                    <div class="timeline-dates">
                                        <?= esc(date('m/Y', strtotime($exp['fecha_inicio']))) ?> -
                                        <?= !empty($exp['actual']) ? 'Presente' : (!empty($exp['fecha_fin']) ? esc(date('m/Y', strtotime($exp['fecha_fin']))) : '-') ?>
                                    </div>
                                    <?php if (!empty($exp['descripcion'])): ?>
                                        <p class="timeline-desc"><?= nl2br(esc($exp['descripcion'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color:var(--text-secondary);font-size:13.5px;margin:0;">Sin experiencia laboral registrada.</p>
                    <?php endif; ?>
                </div>

                <!-- Educación -->
                <div class="perfil-section-card">
                    <div class="perfil-section-header">
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            Educación
                        </h3>
                    </div>

                    <?php if (!empty($educacion)): ?>
                        <div class="perfil-timeline">
                            <?php foreach ($educacion as $edu): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <h4 class="timeline-title"><?= esc($edu['titulo']) ?></h4>
                                    <div class="timeline-sub"><?= esc($edu['institucion']) ?> &middot; <?= esc($edu['nivel_nombre'] ?? '') ?></div>
                                    <div class="timeline-dates">
                                        <?= esc(date('m/Y', strtotime($edu['fecha_inicio']))) ?> -
                                        <?= !empty($edu['en_curso']) ? 'En curso' : (!empty($edu['fecha_fin']) ? esc(date('m/Y', strtotime($edu['fecha_fin']))) : '-') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color:var(--text-secondary);font-size:13.5px;margin:0;">Sin estudios registrados.</p>
                    <?php endif; ?>
                </div>

                <!-- CVs Adjuntos -->
                <?php if (!empty($cvs)): ?>
                    <div class="perfil-section-card">
                        <div class="perfil-section-header">
                            <h3>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                Currículums Vitae (<?= count($cvs) ?>)
                            </h3>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <?php foreach ($cvs as $cv): ?>
                                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border:1px solid #E2E8F0;border-radius:10px;background:#FAFAFA;">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" style="color:#EF4444;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        <div>
                                            <div style="font-size:13.5px;font-weight:600;color:var(--text-primary);"><?= esc($cv['nombre_archivo'] ?? 'CV Documento') ?></div>
                                            <div style="font-size:12px;color:var(--text-secondary);"><?= esc(date('d/m/Y', strtotime($cv['created_at']))) ?></div>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('uploads/cvs/' . ($cv['ruta_archivo'] ?? '')) ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
                                        Descargar / Ver &#8599;
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Historial de Postulaciones -->
                <div class="perfil-section-card">
                    <div class="perfil-section-header">
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Postulaciones Realizadas (<?= count($postulaciones) ?>)
                        </h3>
                    </div>

                    <?php if (!empty($postulaciones)): ?>
                        <div style="overflow-x:auto;">
                            <table class="perfil-mini-table">
                                <thead>
                                    <tr>
                                        <th>Vacante</th>
                                        <th>Empresa</th>
                                        <th>Fecha Postulación</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($postulaciones as $post): ?>
                                        <tr>
                                            <td><strong><?= esc($post['vacante_titulo'] ?? 'Vacante') ?></strong></td>
                                            <td><?= esc($post['empresa_nombre'] ?? 'Empresa') ?></td>
                                            <td><?= esc(date('d/m/Y', strtotime($post['created_at']))) ?></td>
                                            <td>
                                                <span class="badge-role candidato">
                                                    <?= esc(ucfirst($post['estado'] ?? 'pendiente')) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="color:var(--text-secondary);font-size:13.5px;margin:0;">No registra postulaciones activas.</p>
                    <?php endif; ?>
                </div>

            <!-- EMPRESA PROFILE -->
            <?php elseif ($roleSlug === 'empresa'): ?>
                <!-- Basic Company Info -->
                <div class="perfil-section-card">
                    <div class="perfil-section-header">
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            Información de la Empresa
                        </h3>
                        <?php if (!empty($empresa['verificada'])): ?>
                            <span class="badge-sub verified">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Empresa Verificada
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="modal-info-grid" style="margin-bottom:16px;">
                        <div class="modal-info-box">
                            <span class="label">Razón Social</span>
                            <span class="value"><?= esc($empresa['razon_social'] ?? 'No especificada') ?></span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">RUC / Identificación Fiscal</span>
                            <span class="value"><?= esc($empresa['ruc'] ?? 'No registrado') ?></span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Rubro / Sector</span>
                            <span class="value"><?= esc($empresa['rubro'] ?? 'No especificado') ?></span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Ubicación</span>
                            <span class="value">
                                <?= esc(implode(', ', array_filter([$empresa['ciudad'] ?? '', $empresa['region'] ?? '', $empresa['pais'] ?? ''])) ?: 'No especificada') ?>
                            </span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Sitio Web</span>
                            <span class="value">
                                <?php if (!empty($empresa['sitio_web'])): ?>
                                    <a href="<?= esc($empresa['sitio_web']) ?>" target="_blank" rel="noopener" style="color:var(--accent);">
                                        <?= esc($empresa['sitio_web']) ?> &#8599;
                                    </a>
                                <?php else: ?>
                                    No registrado
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Total Vacantes / Postulaciones</span>
                            <span class="value">
                                <?= count($vacantes) ?> vacantes &middot; <?= (int)$totalPostulacionesEmpresa ?> postulantes
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($empresa['descripcion'])): ?>
                        <div style="margin-top:12px;">
                            <span class="label" style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);">Descripción Corporativa</span>
                            <p style="margin-top:6px;font-size:14px;line-height:1.6;color:var(--text-primary);background:#F8FAFC;padding:14px 16px;border-radius:12px;border:1px solid #E2E8F0;">
                                <?= nl2br(esc($empresa['descripcion'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Vacantes de la Empresa -->
                <div class="perfil-section-card">
                    <div class="perfil-section-header">
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            Vacantes Publicadas (<?= count($vacantes) ?>)
                        </h3>
                    </div>

                    <?php if (!empty($vacantes)): ?>
                        <div style="overflow-x:auto;">
                            <table class="perfil-mini-table">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Modalidad</th>
                                        <th>Ciudad</th>
                                        <th>Estado</th>
                                        <th style="text-align:center;">Postulantes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vacantes as $vac): ?>
                                        <tr>
                                            <td><strong><?= esc($vac['titulo']) ?></strong></td>
                                            <td><?= esc(ucfirst($vac['modalidad'] ?? '-')) ?></td>
                                            <td><?= esc($vac['ciudad'] ?? '-') ?></td>
                                            <td>
                                                <span class="badge-role empresa">
                                                    <?= esc(ucfirst($vac['estado'] ?? 'publicada')) ?>
                                                </span>
                                            </td>
                                            <td style="text-align:center;">
                                                <span class="badge-sub">
                                                    <?= (int)($vac['total_postulantes'] ?? 0) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="color:var(--text-secondary);font-size:13.5px;margin:0;">No ha publicado vacantes aún.</p>
                    <?php endif; ?>
                </div>

            <!-- ADMIN / ASESOR STAFF PROFILE -->
            <?php else: ?>
                <div class="perfil-section-card">
                    <div class="perfil-section-header">
                        <h3>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Privilegios de Administración (<?= esc($roleName) ?>)
                        </h3>
                    </div>

                    <p style="font-size:14px;color:var(--text-secondary);line-height:1.6;margin-bottom:16px;">
                        <?= esc($usuario['role_descripcion'] ?? 'Este usuario es miembro del personal administrativo de la plataforma CONEX.') ?>
                    </p>

                    <div class="modal-info-grid">
                        <div class="modal-info-box">
                            <span class="label">Nivel de Acceso</span>
                            <span class="value"><?= esc($roleName) ?></span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Acceso a Panel Administrativo</span>
                            <span class="value" style="color:#16A34A;">Habilitado</span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Gestión de Vacantes</span>
                            <span class="value" style="color:#16A34A;">Habilitado</span>
                        </div>
                        <div class="modal-info-box">
                            <span class="label">Verificación de Postulantes</span>
                            <span class="value" style="color:#16A34A;">Habilitado</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

