<!-- Hero Banner del Agente -->
<div class="agente-banner">
    <div class="agente-banner-info">
        <h1>¡Hola, <?= esc(session()->get('nombre') ?? 'Agente') ?>! 👋</h1>
        <p>Bienvenido a tu consola operativa CONEX. Gestiona prospectos, registra candidatos en campo y verifica postulantes.</p>
    </div>
    <div class="agente-banner-actions">
        <a href="<?= base_url('agente/registro-campo') ?>" class="btn-agente-cta">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            Registro en Campo
        </a>
        <a href="<?= base_url('agente/captar') ?>" class="btn-agente-sec">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            Captar Empresa/Socio
        </a>
        <a href="<?= base_url('agente/verificacion') ?>" class="btn-agente-sec">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c2.39 0 4.68.94 6.36 2.64"/><path d="M21 3v6h-6"/></svg>
            Verificar Candidatos
        </a>
    </div>
</div>

<!-- KPI Stats Grid -->
<div class="agente-stats-grid">
    <div class="agente-kpi-card">
        <div class="agente-kpi-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <div class="agente-kpi-info">
            <span class="agente-kpi-num"><?= (int)($stats['totalLeads'] ?? 0) ?></span>
            <span class="agente-kpi-label">Empresas / Socios en Pipeline</span>
        </div>
    </div>

    <div class="agente-kpi-card">
        <div class="agente-kpi-icon orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
        </div>
        <div class="agente-kpi-info">
            <span class="agente-kpi-num"><?= (int)($stats['postulacionesPendientes'] ?? 0) ?></span>
            <span class="agente-kpi-label">Postulantes por Verificar</span>
        </div>
    </div>

    <div class="agente-kpi-card">
        <div class="agente-kpi-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="agente-kpi-info">
            <span class="agente-kpi-num"><?= (int)($stats['totalColocados'] ?? 0) ?></span>
            <span class="agente-kpi-label">Candidatos Colocados</span>
        </div>
    </div>

    <div class="agente-kpi-card">
        <div class="agente-kpi-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="agente-kpi-info">
            <span class="agente-kpi-num"><?= (int)($stats['totalCandidatos'] ?? 0) ?></span>
            <span class="agente-kpi-label">Base Total de Candidatos</span>
        </div>
    </div>

    <div class="agente-kpi-card">
        <div class="agente-kpi-icon teal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        </div>
        <div class="agente-kpi-info">
            <span class="agente-kpi-num"><?= (int)($stats['prestadoresPendientes'] ?? 0) ?></span>
            <span class="agente-kpi-label">Prestadores sin Verificar</span>
        </div>
    </div>
</div>

<!-- Two-Column Operational Layout -->
<div class="agente-dash-grid">
    <!-- Columna Izquierda: Acción y Pipeline -->
    <div>
        <!-- Postulantes prioritarios para verificar -->
        <div class="agente-card">
            <div class="agente-card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c2.39 0 4.68.94 6.36 2.64"/><path d="M21 3v6h-6"/></svg>
                    Postulantes por Verificar (Prioridad)
                </h3>
                <a href="<?= base_url('agente/verificacion') ?>">Ver consola completa &rarr;</a>
            </div>

            <?php if (!empty($postulantesUrgentes)): ?>
                <div class="agente-item-list">
                    <?php foreach ($postulantesUrgentes as $p): ?>
                        <div class="agente-list-row">
                            <div class="agente-row-main">
                                <span class="agente-row-title"><?= esc($p['nombre'] . ' ' . $p['apellido']) ?></span>
                                <span class="agente-row-sub">
                                    <span><?= esc($p['profesion'] ?: 'Candidato') ?></span> &middot;
                                    <strong>Vacante:</strong> <?= esc($p['vacante_titulo']) ?> (<?= esc($p['empresa_nombre'] ?? 'Empresa') ?>)
                                </span>
                            </div>
                            <div>
                                <a href="<?= base_url('admin/postulantes/ver/' . $p['candidato_id']) ?>" class="btn btn-sm btn-primary">
                                    Verificar
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state" style="padding:20px;text-align:center;">
                    <p style="color:var(--text-secondary);font-size:13.5px;margin:0;">No hay postulantes pendientes de revisión en este momento.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Leads y Empresas Recientes Asignadas -->
        <div class="agente-card">
            <div class="agente-card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Mis Empresas y Socios en Captación
                </h3>
                <a href="<?= base_url('agente/captar') ?>">Gestionar prospectos &rarr;</a>
            </div>

            <?php if (!empty($leadsRecientes)): ?>
                <div class="agente-item-list">
                    <?php foreach ($leadsRecientes as $l): ?>
                        <div class="agente-list-row">
                            <div class="agente-row-main">
                                <span class="agente-row-title"><?= esc($l['empresa_nombre']) ?></span>
                                <span class="agente-row-sub">
                                    <span>Contacto: <?= esc($l['nombre_contacto']) ?></span>
                                    <?php if (!empty($l['telefono_contacto'])): ?>
                                        &middot; <a href="tel:<?= esc($l['telefono_contacto']) ?>"><?= esc($l['telefono_contacto']) ?></a>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div>
                                <span class="badge badge-accent" style="text-transform:capitalize;">
                                    <?= esc($l['estado']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state" style="padding:20px;text-align:center;">
                    <p style="color:var(--text-secondary);font-size:13.5px;margin:0 0 12px 0;">Aún no tienes prospectos registrados.</p>
                    <a href="<?= base_url('agente/captar') ?>" class="btn btn-outline btn-sm">Captar primer prospecto</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Columna Derecha: Accesos rápidos y Demanda -->
    <div>
        <!-- Tarjeta de Acción Rápida en Campo -->
        <div class="agente-card" style="border-left: 4px solid var(--brand-orange, #FF8400);">
            <h3 style="font-size:16px;font-weight:700;margin:0 0 8px 0;color:var(--text-primary);">
                ⚡ Registro Express en Campo
            </h3>
            <p style="font-size:13.5px;color:var(--text-secondary);margin:0 0 16px 0;line-height:1.5;">
                ¿Estás en una feria de empleo o en la calle con un candidato? Regístralo en 1 minuto, asígnale credenciales y postúlalo al instante.
            </p>
            <a href="<?= base_url('agente/registro-campo') ?>" class="btn btn-primary btn-block">
                Abrir formulario express
            </a>
        </div>

        <!-- Vacantes activas con demanda -->
        <div class="agente-card">
            <div class="agente-card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Vacantes que Necesitan Candidatos
                </h3>
                <a href="<?= base_url('admin/vacantes') ?>">Ver todas &rarr;</a>
            </div>

            <?php if (!empty($vacantesActivas)): ?>
                <div class="agente-item-list">
                    <?php foreach ($vacantesActivas as $v): ?>
                        <div class="agente-list-row">
                            <div class="agente-row-main">
                                <span class="agente-row-title"><?= esc($v['titulo']) ?></span>
                                <span class="agente-row-sub">
                                    <span><?= esc($v['empresa_nombre']) ?></span> &middot;
                                    <span><?= esc($v['ciudad'] ?? 'Nicaragua') ?></span>
                                </span>
                            </div>
                            <span class="badge badge-teal">
                                <?= (int)$v['total_postulantes'] ?> post.
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Atajo a Prestadores de Servicios -->
        <div class="agente-card">
            <div class="agente-card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    Validación de Prestadores
                </h3>
                <a href="<?= base_url('agente/prestadores') ?>">Ver módulo &rarr;</a>
            </div>
            <p style="font-size:13.5px;color:var(--text-secondary);margin:0 0 14px 0;line-height:1.5;">
                Revisa los prestadores de oficios independientes (albañiles, fontaneros, etc.) y otórgales el sello de verificación oficial.
            </p>
            <a href="<?= base_url('agente/prestadores') ?>" class="btn btn-outline btn-sm btn-block">
                Validar prestadores pendientes (<?= (int)$stats['prestadoresPendientes'] ?>)
            </a>
        </div>
    </div>
</div>
