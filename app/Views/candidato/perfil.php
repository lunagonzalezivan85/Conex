<div class="page-header">
    <div>
        <h1>Completar Perfil</h1>
        <p>Mantén tu información actualizada para mejorar tus oportunidades.</p>
    </div>
</div>

<div class="profile-progress" style="margin-bottom: 32px;">
    <div class="profile-progress-header">
        <h3>Progreso del perfil</h3>
        <span><?= $porcentajePerfil ?? 0 ?>%</span>
    </div>
    <div class="profile-progress-bar">
        <div class="profile-progress-fill" style="width: <?= $porcentajePerfil ?? 0 ?>%"></div>
    </div>
    <p class="profile-progress-text">Completa cada seccion para aumentar tu visibilidad ante las empresas.</p>
</div>

<div class="tabs" id="profileTabs">
    <button type="button" class="tab-btn active" data-tab="personal" style="flex:1;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Personal</span>
    </button>
    <button type="button" class="tab-btn" data-tab="experiencia" style="flex:1;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        <span>Experiencia</span>
    </button>
    <button type="button" class="tab-btn" data-tab="educacion" style="flex:1;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        <span>Educacion</span>
    </button>
    <button type="button" class="tab-btn" data-tab="habilidades" style="flex:1;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <span>Habilidades</span>
    </button>
    <button type="button" class="tab-btn" data-tab="idiomas" style="flex:1;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        <span>Idiomas</span>
    </button>
    <button type="button" class="tab-btn" data-tab="cv" style="flex:1;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <span>CV y IA</span>
    </button>
</div>

<!-- Tab: Informacion Personal -->
<div class="tab-pane active" data-pane="personal">
    <div class="dash-card">
        <div class="dash-card-header">
            <h2>Informacion personal</h2>
        </div>
        <form action="<?= base_url('candidato/perfil') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= esc($user['nombre'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="<?= esc($user['apellido'] ?? '') ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Correo electronico</label>
                    <input type="email" name="email" class="form-control" value="<?= esc($user['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Telefono</label>
                    <input type="tel" name="telefono" class="form-control" value="<?= esc($user['telefono'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Profesion</label>
                <input type="text" name="profesion" class="form-control" value="<?= esc($candidato['profesion'] ?? '') ?>" placeholder="Ej: Desarrollador Web">
            </div>
            <div class="form-group">
                <label class="form-label">Sobre mi</label>
                <textarea name="sobre_mi" class="form-control" rows="4" placeholder="Cuentanos sobre ti, tus logros y objetivos..."><?= esc($candidato['sobre_mi'] ?? '') ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" value="<?= esc($candidato['ciudad'] ?? '') ?>" placeholder="Tu ciudad">
                </div>
                <div class="form-group">
                    <label class="form-label">Region</label>
                    <input type="text" name="region" class="form-control" value="<?= esc($candidato['region'] ?? '') ?>" placeholder="Tu region">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Disponibilidad</label>
                    <select name="disponibilidad" class="form-control">
                        <option value="inmediata" <?= ($candidato['disponibilidad'] ?? '') === 'inmediata' ? 'selected' : '' ?>>Inmediata</option>
                        <option value="15_dias" <?= ($candidato['disponibilidad'] ?? '') === '15_dias' ? 'selected' : '' ?>>15 dias</option>
                        <option value="30_dias" <?= ($candidato['disponibilidad'] ?? '') === '30_dias' ? 'selected' : '' ?>>30 dias</option>
                        <option value="a_convenir" <?= ($candidato['disponibilidad'] ?? '') === 'a_convenir' ? 'selected' : '' ?>>A convenir</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Modalidad preferida</label>
                    <select name="modalidad_preferida" class="form-control">
                        <option value="presencial" <?= ($candidato['modalidad_preferida'] ?? '') === 'presencial' ? 'selected' : '' ?>>Presencial</option>
                        <option value="remoto" <?= ($candidato['modalidad_preferida'] ?? '') === 'remoto' ? 'selected' : '' ?>>Remoto</option>
                        <option value="hibrido" <?= ($candidato['modalidad_preferida'] ?? '') === 'hibrido' ? 'selected' : '' ?>>Hibrido</option>
                        <option value="indiferente" <?= ($candidato['modalidad_preferida'] ?? '') === 'indiferente' ? 'selected' : '' ?>>Indiferente</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Salario esperado (USD)</label>
                    <input type="number" name="salario_esperado_usd" class="form-control" value="<?= esc($candidato['salario_esperado_usd'] ?? '') ?>" placeholder="0.00" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">URL del portafolio</label>
                    <input type="url" name="portafolio_url" class="form-control" value="<?= esc($candidato['portafolio_url'] ?? '') ?>" placeholder="https://...">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">URL de LinkedIn</label>
                <input type="url" name="linkedin_url" class="form-control" value="<?= esc($candidato['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/in/...">
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</div>

<!-- Tab: Experiencia -->
<div class="tab-pane" data-pane="experiencia">
    <form action="<?= base_url('candidato/experiencia') ?>" method="post" id="experienceForm">
        <?= csrf_field() ?>
        <div class="dash-card">
            <div class="section-header">
                <h2>Experiencia laboral</h2>
                <button type="button" class="btn btn-outline btn-sm" onclick="addExperience()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Agregar
                </button>
            </div>
            <div class="timeline" id="experienceTimeline">
                <?php if (!empty($experiencias)): ?>
                    <?php foreach ($experiencias as $exp): ?>
                    <div class="timeline-item">
                        <div class="dash-card" style="margin-bottom: 0; padding: 20px;">
                            <div class="form-row">
                                <div class="form-group"><label class="form-label">Empresa</label><input type="text" class="form-control" name="empresa[]" value="<?= esc($exp['empresa']) ?>" placeholder="Nombre de la empresa"></div>
                                <div class="form-group"><label class="form-label">Cargo</label><input type="text" class="form-control" name="cargo[]" value="<?= esc($exp['cargo']) ?>" placeholder="Ej: Desarrollador Senior"></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" class="form-control" name="fecha_inicio[]" value="<?= esc($exp['fecha_inicio']) ?>"></div>
                                <div class="form-group"><label class="form-label">Fecha fin</label><input type="date" class="form-control" name="fecha_fin[]" value="<?= esc($exp['fecha_fin']) ?>" <?= $exp['actual'] ? 'disabled' : '' ?>></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><input type="checkbox" name="actual[]" value="1" <?= $exp['actual'] ? 'checked' : '' ?> onchange="this.closest('.timeline-item').querySelector('input[name=\'fecha_fin[]\']').disabled = this.checked;"> Empleo actual</label>
                            </div>
                            <div class="form-group"><label class="form-label">Descripcion</label><textarea class="form-control" rows="3" name="exp_descripcion[]" placeholder="Describe tus responsabilidades y logros..."><?= esc($exp['descripcion'] ?? '') ?></textarea></div>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="this.closest('.timeline-item').remove();">Eliminar</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="empty-state" id="experienceEmpty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    <h3>Sin experiencia registrada</h3>
                    <p>Agrega tu experiencia laboral para que las empresas conozcan tu trayectoria.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Guardar experiencia</button>
        </div>
    </form>
</div>

<!-- Tab: Educacion -->
<div class="tab-pane" data-pane="educacion">
    <form action="<?= base_url('candidato/educacion') ?>" method="post" id="educationForm">
        <?= csrf_field() ?>
        <div class="dash-card">
            <div class="section-header">
                <h2>Formacion academica</h2>
                <button type="button" class="btn btn-outline btn-sm" onclick="addEducation()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Agregar
                </button>
            </div>
            <div id="educationList">
                <?php if (!empty($educaciones)): ?>
                    <?php foreach ($educaciones as $edu): ?>
                    <div class="edu-item">
                        <div class="form-row">
                            <div class="form-group"><label class="form-label">Institucion</label><input type="text" class="form-control" name="institucion[]" value="<?= esc($edu['institucion']) ?>" placeholder="Nombre de la institucion"></div>
                            <div class="form-group"><label class="form-label">Titulo</label><input type="text" class="form-control" name="titulo[]" value="<?= esc($edu['titulo']) ?>" placeholder="Ej: Ingenieria en Sistemas"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" class="form-control" name="edu_fecha_inicio[]" value="<?= esc($edu['fecha_inicio']) ?>"></div>
                            <div class="form-group"><label class="form-label">Fecha fin</label><input type="date" class="form-control" name="edu_fecha_fin[]" value="<?= esc($edu['fecha_fin']) ?>" <?= $edu['en_curso'] ? 'disabled' : '' ?>></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><input type="checkbox" name="en_curso[]" value="1" <?= $edu['en_curso'] ? 'checked' : '' ?> onchange="this.closest('.edu-item').querySelector('input[name=\'edu_fecha_fin[]\']').disabled = this.checked;"> En curso</label>
                        </div>
                        <button type="button" class="btn btn-ghost btn-sm" style="margin-top:8px;" onclick="this.closest('.edu-item').remove();">Eliminar</button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="empty-state" id="educationEmpty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    <h3>Sin formacion registrada</h3>
                    <p>Agrega tus estudios para completar tu perfil.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Guardar educacion</button>
        </div>
    </form>
</div>

<!-- Tab: Habilidades -->
<div class="tab-pane" data-pane="habilidades">
    <form action="<?= base_url('candidato/habilidades') ?>" method="post" id="skillsForm">
        <?= csrf_field() ?>
        <div class="dash-card">
            <div class="section-header">
                <h2>Habilidades y competencias</h2>
            </div>
            <div class="skills-container" id="skillsContainer">
                <?php if (!empty($habilidades)): ?>
                    <?php foreach ($habilidades as $hab): ?>
                    <span class="skill-tag"><?= esc($hab['nombre'] ?? '') ?>
                        <span class="skill-remove" onclick="this.parentElement.remove();"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>
                    </span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="skill-input-group">
                <input type="text" id="skillInput" placeholder="Escribe una habilidad y presiona Enter (Ej: PHP, Laravel, Git...)" onkeydown="if(event.key==='Enter'){event.preventDefault();addSkill();}">
                <button type="button" class="btn btn-primary" onclick="addSkill()">Agregar</button>
            </div>
            <p class="form-text" style="margin-top: 12px;">Agrega al menos 5 habilidades para mejorar tu coincidencia con las vacantes.</p>
        </div>
        <div id="skillsHidden"></div>
        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Guardar habilidades</button>
        </div>
    </form>
</div>

<!-- Tab: Idiomas -->
<div class="tab-pane" data-pane="idiomas">
    <form action="<?= base_url('candidato/idiomas') ?>" method="post" id="idiomasForm">
        <?= csrf_field() ?>
        <div class="dash-card">
            <div class="section-header">
                <h2>Idiomas</h2>
            </div>

            <div id="idiomasList" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
                <?php if (!empty($idiomas)): ?>
                    <?php foreach ($idiomas as $i => $idi): ?>
                    <div class="idioma-tag" style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:20px;background:#f0f4ff;border:1px solid #c7d2fe;">
                        <span style="font-size:14px;font-weight:600;color:#3730a3;"><?= esc($idi['nombre']) ?></span>
                        <span style="font-size:11px;padding:2px 8px;border-radius:10px;background:#4361EE;color:#fff;font-weight:500;"><?= esc(ucfirst($idi['nivel'] ?? 'intermedio')) ?></span>
                        <button type="button" onclick="removeIdiomaTag(this)" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;display:flex;" title="Eliminar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                        <input type="hidden" name="idioma[]" value="<?= esc($idi['nombre']) ?>">
                        <input type="hidden" name="nivel[]" value="<?= esc($idi['nivel'] ?? 'intermedio') ?>">
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="empty-state" id="idiomasEmpty" style="width:100%;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <h3>Sin idiomas registrados</h3>
                    <p>Agrega los idiomas que dominas y tu nivel de cada uno.</p>
                </div>
                <?php endif; ?>
            </div>

            <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border-color,#e5e7eb);">
                <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
                    <label class="form-label">Agregar idioma</label>
                    <input type="text" class="form-control" id="idiomaInput" placeholder="Ej: Espanol, Ingles, Frances..." style="margin-bottom:0;">
                </div>
                <div class="form-group" style="min-width:160px;margin-bottom:0;">
                    <label class="form-label">Nivel</label>
                    <select class="form-control" id="idiomaNivel" style="margin-bottom:0;">
                        <option value="basico">Basico</option>
                        <option value="intermedio" selected>Intermedio</option>
                        <option value="avanzado">Avanzado</option>
                        <option value="nativo">Nativo</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary" onclick="addIdiomaTag()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Agregar
                </button>
            </div>
        </div>
        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Guardar idiomas</button>
        </div>
    </form>
</div>

<!-- Tab: CV y Analisis IA -->
<div class="tab-pane" data-pane="cv">
    <div class="dash-card" style="margin-bottom: 24px;">
        <div class="section-header" style="display:flex;justify-content:space-between;align-items:center;">
            <h2>Curriculum Vitae</h2>
            <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalCV()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Subir CV
            </button>
        </div>

        <div id="cvList">
            <?php if (!empty($cvs)): ?>
                <?php foreach ($cvs as $cv): ?>
                    <?php
                        $tieneAnalisis = isset($analisisPorCV[$cv['id']]);
                        $analisisCV = $tieneAnalisis ? $analisisPorCV[$cv['id']] : null;
                    ?>
                    <div class="cv-card" id="cv-card-<?= $cv['id'] ?>" style="flex-wrap:wrap;">
                        <div class="cv-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="cv-card-info" style="flex:1;min-width:200px;">
                            <div class="cv-card-name"><?= esc($cv['archivo_nombre']) ?></div>
                            <div class="cv-card-meta">Subido <?= esc(date('d/m/Y', strtotime($cv['created_at']))) ?></div>
                            <?php if ($tieneAnalisis): ?>
                                <span style="display:inline-flex;align-items:center;gap:4px;margin-top:6px;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#dcfce7;color:#16a34a;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><polyline points="20 6 9 17 4 12"/></svg>
                                    Analizado - Score <?= $analisisCV['score_match'] ?? 0 ?>/100
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="cv-card-actions">
                            <button type="button" class="btn-icon" title="Analizar con IA" onclick="analizarCV(<?= $cv['id'] ?>)" style="color:#4361EE;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10"/><path d="M12 6v6l4 2"/></svg>
                            </button>
                            <?php if ($tieneAnalisis): ?>
                            <button type="button" class="btn-icon" title="Ver analisis" onclick="toggleAnalisis(<?= $cv['id'] ?>)" style="color:#6366f1;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="toggle-icon-<?= $cv['id'] ?>"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <?php endif; ?>
                            <button type="button" class="btn-icon" title="Eliminar CV" onclick="eliminarCV(<?= $cv['id'] ?>, '<?= esc($cv['archivo_nombre']) ?>')" style="color:#ef4444;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </div>

                        <?php if ($tieneAnalisis && $analisisCV):
                            $parsedCV = json_decode($analisisCV['respuesta'] ?? '{}', true);
                            if (!is_array($parsedCV)) $parsedCV = [];
                        ?>
                        <div class="cv-analisis-dropdown" id="analisis-dropdown-<?= $cv['id'] ?>" style="display:none;width:100%;margin-top:16px;padding-top:16px;border-top:1px solid var(--border-color,#e5e7eb);">
                            <div class="ai-analysis-score">
                                <div class="ai-score-circle" style="--score: <?= $analisisCV['score_match'] ?? 0 ?>%;">
                                    <div>
                                        <div class="ai-score-value"><?= $analisisCV['score_match'] ?? 0 ?></div>
                                        <div class="ai-score-label">/ 100</div>
                                    </div>
                                </div>
                                <div class="ai-score-details">
                                    <div class="ai-score-title">Analisis completado</div>
                                    <div class="ai-score-desc"><?= esc($analisisCV['recomendacion'] ?? 'Sin recomendaciones') ?></div>
                                </div>
                            </div>
                            <?php if (!empty($analisisCV['fortalezas'])): ?>
                            <div style="margin-top:16px;">
                                <h4 style="font-size:13px;font-weight:600;margin-bottom:6px;color:#22c55e;">Fortalezas</h4>
                                <p style="font-size:13px;color:var(--text-secondary);line-height:1.5;"><?= esc($analisisCV['fortalezas']) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($analisisCV['debilidades'])): ?>
                            <div style="margin-top:12px;">
                                <h4 style="font-size:13px;font-weight:600;margin-bottom:6px;color:#f59e0b;">Areas de mejora</h4>
                                <p style="font-size:13px;color:var(--text-secondary);line-height:1.5;"><?= esc($analisisCV['debilidades']) ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($parsedCV['datos_personales'])):
                                $dp = $parsedCV['datos_personales']; ?>
                            <div style="margin-top:12px;padding:10px;background:#f0f4ff;border-radius:8px;">
                                <h4 style="font-size:13px;font-weight:600;margin-bottom:6px;">Datos personales</h4>
                                <ul style="list-style:none;padding:0;margin:0;font-size:13px;color:var(--text-secondary);line-height:1.8;">
                                    <?php if (!empty($dp['nombre'])): ?><li><strong>Nombre:</strong> <?= esc($dp['nombre']) ?></li><?php endif; ?>
                                    <?php if (!empty($dp['email'])): ?><li><strong>Email:</strong> <?= esc($dp['email']) ?></li><?php endif; ?>
                                    <?php if (!empty($dp['telefono'])): ?><li><strong>Telefono:</strong> <?= esc($dp['telefono']) ?></li><?php endif; ?>
                                    <?php if (!empty($dp['ubicacion'])): ?><li><strong>Ubicacion:</strong> <?= esc($dp['ubicacion']) ?></li><?php endif; ?>
                                    <?php if (!empty($dp['linkedin'])): ?><li><strong>LinkedIn:</strong> <?= esc($dp['linkedin']) ?></li><?php endif; ?>
                                    <?php if (!empty($dp['resumen'])): ?><li><strong>Resumen:</strong> <?= esc($dp['resumen']) ?></li><?php endif; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($parsedCV['experiencias'])): ?>
                            <div style="margin-top:12px;">
                                <h4 style="font-size:13px;font-weight:600;margin-bottom:6px;">Experiencias laborales</h4>
                                <ul style="list-style:none;padding:0;margin:0;font-size:13px;color:var(--text-secondary);line-height:1.8;">
                                    <?php foreach ($parsedCV['experiencias'] as $exp): ?>
                                    <li><strong><?= esc($exp['cargo'] ?? '?') ?></strong> en <?= esc($exp['empresa'] ?? '?') ?>
                                    <?php if (!empty($exp['fecha_inicio'])): ?>
                                    <span style="font-size:12px;color:#999;"> (<?= esc($exp['fecha_inicio']) ?> - <?= !empty($exp['actual']) ? 'Actual' : (esc($exp['fecha_fin'] ?? '?')) ?>)</span>
                                    <?php endif; ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($parsedCV['educaciones'])): ?>
                            <div style="margin-top:12px;">
                                <h4 style="font-size:13px;font-weight:600;margin-bottom:6px;">Educacion</h4>
                                <ul style="list-style:none;padding:0;margin:0;font-size:13px;color:var(--text-secondary);line-height:1.8;">
                                    <?php foreach ($parsedCV['educaciones'] as $edu): ?>
                                    <li><strong><?= esc($edu['titulo'] ?? '?') ?></strong> - <?= esc($edu['institucion'] ?? '?') ?>
                                    <?php if (!empty($edu['nivel'])): ?> <span style="font-size:11px;background:#e0e7ff;color:#3730a3;padding:2px 6px;border-radius:4px;"><?= esc(ucfirst($edu['nivel'])) ?></span><?php endif; ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($parsedCV['idiomas'])): ?>
                            <div style="margin-top:12px;">
                                <h4 style="font-size:13px;font-weight:600;margin-bottom:6px;">Idiomas</h4>
                                <ul style="list-style:none;padding:0;margin:0;font-size:13px;color:var(--text-secondary);line-height:1.8;">
                                    <?php foreach ($parsedCV['idiomas'] as $idi): ?>
                                    <li><?= esc($idi['nombre'] ?? '?') ?> <span style="font-size:11px;background:#f0fdf4;color:#16a34a;padding:2px 8px;border-radius:4px;"><?= esc(ucfirst($idi['nivel'] ?? 'intermedio')) ?></span></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($parsedCV['habilidades_detectadas'])): ?>
                            <div style="margin-top:12px;">
                                <h4 style="font-size:13px;font-weight:600;margin-bottom:6px;">Habilidades detectadas</h4>
                                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                    <?php foreach ($parsedCV['habilidades_detectadas'] as $hab): ?>
                                    <span style="background:#e0e7ff;color:#3730a3;padding:4px 10px;border-radius:6px;font-size:12px;"><?= esc($hab) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="empty-state" id="cvEmpty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <h3>No tienes CVs subidos</h3>
                <p>Sube tu CV en formato PDF o DOCX para que las empresas lo revisen.</p>
                <button type="button" class="btn btn-primary" onclick="abrirModalCV()">Subir mi primer CV</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Subir CV -->
<div id="modalSubirCV" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);">
    <div style="background:#fff;border-radius:16px;padding:32px;max-width:500px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:18px;font-weight:700;">Subir nuevo CV</h3>
            <button type="button" onclick="cerrarModalCV()" style="background:none;border:none;cursor:pointer;padding:4px;color:#6b7280;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="cv-upload-zone" id="cvUploadZone" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <h3>Arrastra tu CV aqui</h3>
            <p>o haz clic para seleccionar un archivo</p>
            <p class="cv-upload-hint">Formatos: PDF, DOCX | Tamano maximo: 5MB</p>
            <input type="file" id="cvFileInput" accept=".pdf,.doc,.docx" style="position:absolute;top:0;left:0;right:0;bottom:0;opacity:0;cursor:pointer;">
        </div>
        <div id="cvUploadProgress" style="display:none;">
            <div style="display:flex;align-items:center;gap:12px;padding:12px;background:#f0f4ff;border-radius:8px;">
                <div class="ai-loading-spinner"></div>
                <span style="font-size:14px;color:#4361EE;font-weight:500;">Subiendo CV...</span>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var tabBtns = document.querySelectorAll('#profileTabs .tab-btn');
    var tabPanes = document.querySelectorAll('.tab-pane');

    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = this.dataset.tab;
            tabBtns.forEach(function(b) { b.classList.remove('active'); });
            tabPanes.forEach(function(p) { p.classList.remove('active'); });
            this.classList.add('active');
            var pane = document.querySelector('[data-pane="' + target + '"]');
            if (pane) pane.classList.add('active');
        });
    });

    var cvZone = document.getElementById('cvUploadZone');
    var cvFileInput = document.getElementById('cvFileInput');
    var cvList = document.getElementById('cvList');
    var modalCV = document.getElementById('modalSubirCV');
    var uploadProgress = document.getElementById('cvUploadProgress');

    window.abrirModalCV = function() {
        if (modalCV) {
            modalCV.style.display = 'flex';
            if (uploadProgress) uploadProgress.style.display = 'none';
        }
    };

    window.cerrarModalCV = function() {
        if (modalCV) modalCV.style.display = 'none';
        if (cvFileInput) cvFileInput.value = '';
    };

    window.toggleAnalisis = function(cvId) {
        var dropdown = document.getElementById('analisis-dropdown-' + cvId);
        var icon = document.getElementById('toggle-icon-' + cvId);
        if (dropdown) {
            if (dropdown.style.display === 'none') {
                dropdown.style.display = 'block';
                if (icon) icon.style.transform = 'rotate(180deg)';
            } else {
                dropdown.style.display = 'none';
                if (icon) icon.style.transform = 'rotate(0)';
            }
        }
    };

    window.eliminarCV = function(cvId, nombre) {
        Swal.fire({
            title: 'Eliminar CV',
            html: 'Seguro que deseas eliminar <strong>' + nombre + '</strong>?<br>Esto tambien borrara su analisis IA.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            reverseButtons: true
        }).then(function(result) {
            if (!result.isConfirmed) return;

            Swal.fire({ title: 'Eliminando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

            var formData = new FormData();
            formData.append('cv_id', cvId);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= base_url("candidato/cv/eliminar") ?>');
            xhr.onload = function() {
                var res = JSON.parse(xhr.responseText);
                if (res.success) {
                    var card = document.getElementById('cv-card-' + cvId);
                    if (card) card.remove();
                    if (cvList && cvList.children.length === 0) {
                        cvList.innerHTML = '<div class="empty-state" id="cvEmpty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><h3>No tienes CVs subidos</h3><p>Sube tu CV en formato PDF o DOCX para que las empresas lo revisen.</p><button type="button" class="btn btn-primary" onclick="abrirModalCV()">Subir mi primer CV</button></div>';
                    }
                    Swal.fire({ icon: 'success', title: 'CV eliminado', timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.error || 'No se pudo eliminar', confirmButtonColor: '#4361EE' });
                }
            };
            xhr.send(formData);
        });
    };

    function uploadCV(file) {
        var formData = new FormData();
        formData.append('cv', file);

        if (uploadProgress) uploadProgress.style.display = 'block';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= base_url("candidato/cv/upload") ?>');

        xhr.onload = function() {
            if (uploadProgress) uploadProgress.style.display = 'none';
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                cerrarModalCV();

                var empty = document.getElementById('cvEmpty');
                if (empty) empty.remove();

                var card = document.createElement('div');
                card.className = 'cv-card';
                card.id = 'cv-card-' + res.cv_id;
                card.style.flexWrap = 'wrap';
                card.innerHTML = '<div class="cv-card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>' +
                    '<div class="cv-card-info" style="flex:1;min-width:200px;"><div class="cv-card-name">' + res.nombre + '</div><div class="cv-card-meta">' + res.size + ' | Subido ahora</div></div>' +
                    '<div class="cv-card-actions">' +
                        '<button type="button" class="btn-icon" title="Analizar con IA" onclick="analizarCV(' + res.cv_id + ')" style="color:#4361EE;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10"/><path d="M12 6v6l4 2"/></svg></button>' +
                        '<button type="button" class="btn-icon" title="Eliminar CV" onclick="eliminarCV(' + res.cv_id + ', \'' + res.nombre.replace(/'/g, "\\'") + '\')" style="color:#ef4444;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>' +
                    '</div>';
                cvList.appendChild(card);

                Swal.fire({ icon: 'success', title: 'CV subido', text: 'Tu CV se ha subido correctamente. Ya puedes analizarlo con IA.', confirmButtonColor: '#4361EE', timer: 3000, timerProgressBar: true });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.error || 'No se pudo subir el CV', confirmButtonColor: '#4361EE' });
            }
        };

        xhr.onerror = function() {
            if (uploadProgress) uploadProgress.style.display = 'none';
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexion', confirmButtonColor: '#4361EE' });
        };

        xhr.send(formData);
    }

    if (cvFileInput) {
        cvFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                uploadCV(this.files[0]);
            }
        });
    }

    if (cvZone) {
        cvZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        cvZone.addEventListener('dragleave', function(e) {
            this.classList.remove('dragover');
        });
        cvZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                uploadCV(e.dataTransfer.files[0]);
            }
        });
    }

    window.analizarCV = function(cvId) {
        Swal.fire({
            title: 'Analizando tu CV...',
            html: 'La IA esta procesando tu CV<br><br>',
            allowOutsideClick: false,
            didOpen: function() { Swal.showLoading(); }
        });

        var formData = new FormData();
        formData.append('cv_id', cvId);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= base_url("candidato/cv/analizar") ?>');

        xhr.onload = function() {
            var res = JSON.parse(xhr.responseText);

            if (res.success && res.estado === 'completado') {
                var score = res.score || 0;
                var modalHtml = '<div style="text-align:left;font-size:14px;line-height:1.6;max-height:500px;overflow-y:auto;">';
                modalHtml += '<div style="text-align:center;margin-bottom:16px;"><div style="font-size:42px;font-weight:700;color:#4361EE;">' + score + '</div><div style="font-size:13px;color:#666;">Score / 100</div></div>';
                modalHtml += '<div style="margin-bottom:12px;"><strong style="color:#22c55e;">Fortalezas:</strong><br>' + (res.fortalezas || '-') + '</div>';
                modalHtml += '<div style="margin-bottom:12px;"><strong style="color:#f59e0b;">Areas de mejora:</strong><br>' + (res.debilidades || '-') + '</div>';
                modalHtml += '<div style="margin-bottom:12px;"><strong>Recomendacion:</strong><br>' + (res.recomendacion || '-') + '</div>';

                if (res.datos_personales && Object.keys(res.datos_personales).length > 0) {
                    var dp = res.datos_personales;
                    modalHtml += '<div style="margin-bottom:12px;padding:10px;background:#f0f4ff;border-radius:8px;"><strong>Datos personales:</strong><br>';
                    if (dp.nombre) modalHtml += 'Nombre: ' + dp.nombre + '<br>';
                    if (dp.email) modalHtml += 'Email: ' + dp.email + '<br>';
                    if (dp.telefono) modalHtml += 'Telefono: ' + dp.telefono + '<br>';
                    if (dp.ubicacion) modalHtml += 'Ubicacion: ' + dp.ubicacion + '<br>';
                    if (dp.linkedin) modalHtml += 'LinkedIn: ' + dp.linkedin + '<br>';
                    if (dp.resumen) modalHtml += 'Resumen: ' + dp.resumen + '<br>';
                    modalHtml += '</div>';
                }

                if (res.experiencias && res.experiencias.length > 0) {
                    modalHtml += '<div style="margin-bottom:12px;"><strong>Experiencias laborales:</strong><br>';
                    res.experiencias.forEach(function(e) {
                        modalHtml += '- ' + (e.cargo || '?') + ' en ' + (e.empresa || '?');
                        if (e.fecha_inicio) modalHtml += ' (' + e.fecha_inicio + ' - ' + (e.actual ? 'Actual' : (e.fecha_fin || '?')) + ')';
                        modalHtml += '<br>';
                    });
                    modalHtml += '</div>';
                }

                if (res.educaciones && res.educaciones.length > 0) {
                    modalHtml += '<div style="margin-bottom:12px;"><strong>Educacion:</strong><br>';
                    res.educaciones.forEach(function(e) {
                        modalHtml += '- ' + (e.titulo || '?') + ' - ' + (e.institucion || '?');
                        if (e.nivel) modalHtml += ' [' + e.nivel + ']';
                        if (e.en_curso) modalHtml += ' (En curso)';
                        modalHtml += '<br>';
                    });
                    modalHtml += '</div>';
                }

                if (res.idiomas && res.idiomas.length > 0) {
                    modalHtml += '<div style="margin-bottom:12px;"><strong>Idiomas:</strong><br>';
                    res.idiomas.forEach(function(i) {
                        modalHtml += '- ' + (i.nombre || '?') + ' (' + (i.nivel || 'intermedio') + ')<br>';
                    });
                    modalHtml += '</div>';
                }

                if (res.habilidades_detectadas && res.habilidades_detectadas.length > 0) {
                    modalHtml += '<div style="margin-bottom:12px;"><strong>Habilidades detectadas:</strong><br>';
                    modalHtml += '<div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;">';
                    res.habilidades_detectadas.forEach(function(h) {
                        modalHtml += '<span style="background:#e0e7ff;color:#3730a3;padding:4px 10px;border-radius:6px;font-size:12px;">' + h + '</span>';
                    });
                    modalHtml += '</div></div>';
                }
                modalHtml += '</div>';

                Swal.fire({
                    title: 'Resultados del Analisis IA',
                    html: modalHtml,
                    width: 650,
                    showCancelButton: true,
                    confirmButtonText: 'Si, actualizar mi perfil',
                    cancelButtonText: 'No, solo ver resultados',
                    confirmButtonColor: '#22c55e',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        aplicarAnalisis(res.analisis_id);
                    }
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.error || 'No se pudo analizar el CV', confirmButtonColor: '#4361EE' });
            }
        };

        xhr.onerror = function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexion', confirmButtonColor: '#4361EE' });
        };

        xhr.send(formData);
    };

    function aplicarAnalisis(analisisId) {
        Swal.fire({
            title: 'Actualizando perfil...',
            html: 'Guardando los datos extraidos por la IA',
            allowOutsideClick: false,
            didOpen: function() { Swal.showLoading(); }
        });

        var formData = new FormData();
        formData.append('analisis_id', analisisId);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= base_url("candidato/cv/aplicar") ?>');

        xhr.onload = function() {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                var aplicadoText = (res.aplicado && res.aplicado.length > 0) ? res.aplicado.join(', ') : 'No habia datos nuevos para guardar';
                Swal.fire({
                    icon: 'success',
                    title: 'Perfil actualizado',
                    html: 'Se han guardado los siguientes datos:<br><strong>' + aplicadoText + '</strong><br><br>Recarga la pagina para ver los cambios.',
                    confirmButtonText: 'Recargar ahora',
                    confirmButtonColor: '#4361EE'
                }).then(function() {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.error || 'No se pudo aplicar el analisis',
                    confirmButtonColor: '#4361EE'
                });
            }
        };

        xhr.onerror = function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexion', confirmButtonColor: '#4361EE' });
        };

        xhr.send(formData);
    };

    var skills = [];
    var skillsContainer = document.getElementById('skillsContainer');
    var skillInput = document.getElementById('skillInput');
    var skillsForm = document.getElementById('skillsForm');
    var skillsHidden = document.getElementById('skillsHidden');

    document.querySelectorAll('#skillsContainer .skill-tag').forEach(function(tag) {
        var name = tag.textContent.trim();
        if (name) skills.push(name.toLowerCase());
    });

    window.addSkill = function() {
        var val = (skillInput.value || '').trim();
        if (!val || skills.indexOf(val.toLowerCase()) !== -1) {
            skillInput.value = '';
            return;
        }
        skills.push(val.toLowerCase());
        renderSkills();
        skillInput.value = '';
    };

    function renderSkills() {
        if (!skillsContainer) return;
        skillsContainer.innerHTML = skills.map(function(s, i) {
            return '<span class="skill-tag">' + s +
                ' <span class="skill-remove" onclick="removeSkill(' + i + ')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></span>';
        }).join('');
    }

    window.removeSkill = function(i) {
        skills.splice(i, 1);
        renderSkills();
    };

    if (skillsForm) {
        skillsForm.addEventListener('submit', function() {
            if (!skillsHidden) return;
            skillsHidden.innerHTML = skills.map(function(s) {
                return '<input type="hidden" name="habilidades[]" value="' + s + '">';
            }).join('');
        });
    }

    var expCount = 0;
    window.addExperience = function() {
        expCount++;
        var empty = document.getElementById('experienceEmpty');
        if (empty) empty.style.display = 'none';

        var timeline = document.getElementById('experienceTimeline');
        if (!timeline) return;

        var item = document.createElement('div');
        item.className = 'timeline-item';
        item.innerHTML = '<div class="dash-card" style="margin-bottom: 0; padding: 20px;">' +
            '<div class="form-row">' +
                '<div class="form-group"><label class="form-label">Empresa</label><input type="text" class="form-control" name="empresa[]" placeholder="Nombre de la empresa"></div>' +
                '<div class="form-group"><label class="form-label">Cargo</label><input type="text" class="form-control" name="cargo[]" placeholder="Ej: Desarrollador Senior"></div>' +
            '</div>' +
            '<div class="form-row">' +
                '<div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" class="form-control" name="fecha_inicio[]"></div>' +
                '<div class="form-group"><label class="form-label">Fecha fin</label><input type="date" class="form-control" name="fecha_fin[]"></div>' +
            '</div>' +
            '<div class="form-group"><label class="form-label"><input type="checkbox" name="actual[]" value="1" onchange="var f=this.closest(\'.timeline-item\').querySelector(\'input[name="fecha_fin[]"]\'); if(f) f.disabled=this.checked;"> Empleo actual</label></div>' +
            '<div class="form-group"><label class="form-label">Descripcion</label><textarea class="form-control" rows="3" name="exp_descripcion[]" placeholder="Describe tus responsabilidades y logros..."></textarea></div>' +
            '<button type="button" class="btn btn-ghost btn-sm" onclick="this.closest(\'.timeline-item\').remove();">Eliminar</button>' +
        '</div>';
        timeline.appendChild(item);
    };

    var eduCount = 0;
    window.addEducation = function() {
        eduCount++;
        var empty = document.getElementById('educationEmpty');
        if (empty) empty.style.display = 'none';

        var list = document.getElementById('educationList');
        if (!list) return;

        var item = document.createElement('div');
        item.className = 'edu-item';
        item.innerHTML = '<div class="form-row">' +
                '<div class="form-group"><label class="form-label">Institucion</label><input type="text" class="form-control" name="institucion[]" placeholder="Nombre de la institucion"></div>' +
                '<div class="form-group"><label class="form-label">Titulo</label><input type="text" class="form-control" name="titulo[]" placeholder="Ej: Ingenieria en Sistemas"></div>' +
            '</div>' +
            '<div class="form-row">' +
                '<div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" class="form-control" name="edu_fecha_inicio[]"></div>' +
                '<div class="form-group"><label class="form-label">Fecha fin</label><input type="date" class="form-control" name="edu_fecha_fin[]"></div>' +
            '</div>' +
            '<div class="form-group"><label class="form-label"><input type="checkbox" name="en_curso[]" value="1" onchange="var f=this.closest(\'.edu-item\').querySelector(\'input[name="edu_fecha_fin[]"]\'); if(f) f.disabled=this.checked;"> En curso</label></div>' +
            '<button type="button" class="btn btn-ghost btn-sm" style="margin-top:8px;" onclick="this.closest(\'.edu-item\').remove();">Eliminar</button>';
        list.appendChild(item);
    };

    window.addIdiomaTag = function() {
        var input = document.getElementById('idiomaInput');
        var nivel = document.getElementById('idiomaNivel');
        var list = document.getElementById('idiomasList');
        if (!input || !input.value.trim() || !list) return;

        var empty = document.getElementById('idiomasEmpty');
        if (empty) empty.remove();

        var nombre = input.value.trim();
        var nivelVal = nivel ? nivel.value : 'intermedio';
        var nivelLabel = nivelVal.charAt(0).toUpperCase() + nivelVal.slice(1);

        var tag = document.createElement('div');
        tag.className = 'idioma-tag';
        tag.style.cssText = 'display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:20px;background:#f0f4ff;border:1px solid #c7d2fe;';
        tag.innerHTML = '<span style="font-size:14px;font-weight:600;color:#3730a3;">' + nombre + '</span>' +
            '<span style="font-size:11px;padding:2px 8px;border-radius:10px;background:#4361EE;color:#fff;font-weight:500;">' + nivelLabel + '</span>' +
            '<button type="button" onclick="removeIdiomaTag(this)" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;display:flex;" title="Eliminar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>' +
            '<input type="hidden" name="idioma[]" value="' + nombre + '">' +
            '<input type="hidden" name="nivel[]" value="' + nivelVal + '">';
        list.appendChild(tag);

        input.value = '';
        input.focus();
    };

    window.removeIdiomaTag = function(btn) {
        var tag = btn.closest('.idioma-tag');
        if (tag) tag.remove();
        var list = document.getElementById('idiomasList');
        if (list && list.children.length === 0) {
            list.innerHTML = '<div class="empty-state" id="idiomasEmpty" style="width:100%;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg><h3>Sin idiomas registrados</h3><p>Agrega los idiomas que dominas y tu nivel de cada uno.</p></div>';
        }
    };

    var idiomaInput = document.getElementById('idiomaInput');
    if (idiomaInput) {
        idiomaInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addIdiomaTag();
            }
        });
    };
})();
</script>
