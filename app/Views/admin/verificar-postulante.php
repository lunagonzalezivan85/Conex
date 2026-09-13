<div class="page-header">
    <div>
        <h1>Verificacion de Postulante</h1>
        <p>Gestiona el proceso de verificacion paso a paso.</p>
    </div>
    <a href="<?= base_url('admin/postulantes') ?>" class="btn btn-ghost">Volver</a>
</div>

<div class="dash-card" style="margin-bottom:24px;">
    <div class="verif-candidato-info">
        <div class="verif-candidato-avatar">
            <?= strtoupper(substr($candidato['nombre'], 0, 1)) ?>
        </div>
        <div>
            <h2><?= esc($candidato['nombre'] . ' ' . $candidato['apellido']) ?></h2>
            <p><?= esc($candidato['email']) ?> &middot; <?= esc($candidato['telefono'] ?? 'Sin telefono') ?></p>
            <p style="font-size:13px;color:var(--text-secondary);">
                Profesion: <?= esc($candidato['profesion'] ?? 'No especificada') ?> &middot;
                Ciudad: <?= esc($candidato['ciudad'] ?? '-') ?>
            </p>
        </div>
    </div>
</div>

<?php if (!empty($postulaciones)): ?>
    <?php foreach ($postulaciones as $p): ?>
        <div class="dash-card" style="margin-bottom:24px;">
            <div class="verif-vacante-header">
                <div>
                    <h3><?= esc($p['vacante_titulo']) ?></h3>
                    <p style="font-size:13px;color:var(--text-secondary);">
                        Empresa: <?= esc($p['empresa_nombre']) ?> &middot;
                        Postulacion: <?= date('d/m/Y', strtotime($p['created_at'])) ?>
                    </p>
                </div>
                <span class="verif-badge verif-badge-<?= $p['estado'] === 'aceptada' ? 'green' : ($p['estado'] === 'rechazada' ? 'red' : 'orange') ?>">
                    <?= ucfirst(str_replace('_', ' ', $p['estado'])) ?>
                </span>
            </div>

            <?php
            $proceso = $p['proceso'] ?? null;
            $pasoActual = $proceso['paso_actual'] ?? 1;
            $estadoProceso = $proceso['estado'] ?? 'pendiente';
            ?>

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

            <?php if (!$proceso): ?>
                <div class="pipeline-action">
                    <form class="form-iniciar-proceso" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                        <input type="hidden" name="postulacion_id" value="<?= $p['postulacion_id'] ?>">
                        <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
                            <label class="form-label">Asignar a usuario</label>
                            <select name="asignado_a" class="form-control" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($asesores as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= esc($a['nombre'] . ' ' . $a['apellido']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Iniciar proceso</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="pipeline-info">
                    <div class="pipeline-info-row">
                        <span class="pipeline-info-label">Asignado a:</span>
                        <span class="pipeline-info-value">
                            <?php
                            $asignadoNombre = '-';
                            foreach ($asesores as $a) {
                                if ($a['id'] == $proceso['asignado_a']) {
                                    $asignadoNombre = $a['nombre'] . ' ' . $a['apellido'];
                                    break;
                                }
                            }
                            ?>
                            <?= esc($asignadoNombre) ?>
                        </span>
                    </div>
                    <div class="pipeline-info-row">
                        <span class="pipeline-info-label">Estado:</span>
                        <span class="verif-badge verif-badge-<?= $estadoProceso === 'completado' ? 'green' : ($estadoProceso === 'en_proceso' ? 'blue' : 'orange') ?>">
                            <?= ucfirst(str_replace('_', ' ', $estadoProceso)) ?>
                        </span>
                    </div>
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
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

                <?php if ($pasoActual == 4): ?>
                <div class="eval-section">
                    <div class="eval-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;flex-shrink:0;"><path d="M9 11H5a2 2 0 0 0-2 2v7h18v-7a2 2 0 0 0-2-2h-4"/><path d="M15 11V5a3 3 0 0 0-6 0v6"/><line x1="12" y1="11" x2="12" y2="11"/></svg>
                        <span>Evaluacion de entrevista</span>
                    </div>
                    <form class="form-evaluacion">
                        <input type="hidden" name="proceso_id" value="<?= $proceso['id'] ?>">
                        <div class="eval-criterios">
                            <?php foreach ($criterios as $key => $label): ?>
                                <?php
                                $evalExistente = null;
                                foreach ($p['evaluaciones'] as $ev) {
                                    if ($ev['criterio'] === $key) {
                                        $evalExistente = $ev;
                                        break;
                                    }
                                }
                                $calActual = $evalExistente['calificacion'] ?? 0;
                                ?>
                                <div class="eval-criterio-item">
                                    <div class="eval-criterio-left">
                                        <div class="eval-criterio-label"><?= esc($label) ?></div>
                                        <input type="text" name="evaluaciones[<?= $key ?>][observacion]" class="form-control eval-observacion" placeholder="Observacion (opcional)" value="<?= esc($evalExistente['observacion'] ?? '') ?>">
                                    </div>
                                    <div class="eval-criterio-right">
                                        <div class="eval-criterio-stars" data-criterio="<?= $key ?>" data-current="<?= $calActual ?>">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eval-star <?= $s <= $calActual ? 'eval-star-filled' : '' ?>" data-value="<?= $s ?>"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            <?php endfor; ?>
                                            <span class="eval-star-label"><?= $calificaciones[$calActual] ?? 'Sin calificar' ?></span>
                                        </div>
                                        <input type="hidden" name="evaluaciones[<?= $key ?>][calificacion]" value="<?= $calActual ?>" class="eval-input-calificacion">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top:16px;">Guardar evaluacion</button>
                    </form>
                </div>
                <?php endif; ?>

                <?php if ($pasoActual == 5): ?>
                <div class="eval-section eval-section-final">
                    <div class="eval-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;flex-shrink:0;"><path d="M9 11H5a2 2 0 0 0-2 2v7h18v-7a2 2 0 0 0-2-2h-4"/><path d="M15 11V5a3 3 0 0 0-6 0v6"/><line x1="12" y1="11" x2="12" y2="11"/></svg>
                        <span>Evaluacion final del postulante</span>
                    </div>

                    <?php
                    // Calcular promedio de entrevista automaticamente
                    $promedioEntrevista = 0;
                    $totalCriteriosEntrevista = 0;
                    foreach ($criterios as $cKey => $cLabel) {
                        foreach ($p['evaluaciones'] as $ev) {
                            if ($ev['criterio'] === $cKey && $ev['calificacion'] > 0) {
                                $promedioEntrevista += $ev['calificacion'];
                                $totalCriteriosEntrevista++;
                                break;
                            }
                        }
                    }
                    $promedioEntrevista = $totalCriteriosEntrevista > 0 ? round($promedioEntrevista / $totalCriteriosEntrevista, 1) : 0;
                    $pctEntrevista = $totalCriteriosEntrevista > 0 ? round(($promedioEntrevista / 5) * 100) : 0;

                    // Criterios de evaluacion final
                    $criteriosFinal = [
                        'match_perfil' => 'Match del perfil con la vacante',
                        'puntaje_entrevista' => 'Puntaje de entrevista (auto-calculado)',
                        'anos_experiencia' => 'Anos de experiencia (cumple o no)',
                        'disponibilidad' => 'Disponibilidad para iniciar',
                        'ubicacion' => 'Ubicacion geografica',
                    ];
                    $autoKeys = ['match_perfil', 'puntaje_entrevista', 'anos_experiencia', 'disponibilidad', 'ubicacion'];
                    ?>

                    <div class="eval-promedio-entrevista">
                        <div class="eval-promedio-label">Resultado de entrevista (paso 4):</div>
                        <div class="eval-score-circle <?= $promedioEntrevista > 0 ? 'eval-score-circle-' . round($promedioEntrevista) : 'eval-score-circle-0' ?>">
                            <span class="eval-score-num"><?= $promedioEntrevista > 0 ? $promedioEntrevista : '0' ?></span>
                            <span class="eval-score-den">/5</span>
                        </div>
                    </div>

                    <form class="form-evaluacion-final">
                        <input type="hidden" name="proceso_id" value="<?= $proceso['id'] ?>">
                        <input type="hidden" name="promedio_entrevista" value="<?= $promedioEntrevista ?>">
                        <div class="eval-criterios">
                            <?php foreach ($criteriosFinal as $key => $label): ?>
                                <?php
                                $evalExistente = null;
                                foreach ($p['evaluaciones'] as $ev) {
                                    if ($ev['criterio'] === $key) {
                                        $evalExistente = $ev;
                                        break;
                                    }
                                }

                                $esAuto = in_array($key, $autoKeys);

                                if ($key === 'puntaje_entrevista') {
                                    $calActual = $totalCriteriosEntrevista > 0 ? round($promedioEntrevista) : 0;
                                    $detalle = $promedioEntrevista > 0 ? $promedioEntrevista . ' / 5 (promedio del paso 4)' : 'Sin evaluar';
                                } elseif ($esAuto && isset($p['eval_auto'][$key])) {
                                    $calActual = $p['eval_auto'][$key]['score'];
                                    $detalle = $p['eval_auto'][$key]['detalle'];
                                } else {
                                    $calActual = $evalExistente['calificacion'] ?? 0;
                                    $detalle = '';
                                }
                                ?>
                                <div class="eval-criterio-item <?= $esAuto ? 'eval-criterio-auto' : '' ?>">
                                    <div class="eval-criterio-left">
                                        <div class="eval-criterio-label">
                                            <?= esc($label) ?>
                                            <?php if ($esAuto): ?>
                                            <span class="eval-auto-badge">Auto</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($esAuto && !empty($detalle)): ?>
                                        <div class="eval-auto-detalle"><?= esc($detalle) ?></div>
                                        <?php elseif (!$esAuto): ?>
                                        <input type="text" name="evaluaciones[<?= $key ?>][observacion]" class="form-control eval-observacion" placeholder="Observacion (opcional)" value="<?= esc($evalExistente['observacion'] ?? '') ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="eval-criterio-right">
                                        <div class="eval-score-circle eval-score-circle-<?= $calActual ?>" data-criterio="<?= $key ?>" data-current="<?= $calActual ?>">
                                            <span class="eval-score-num"><?= $calActual ?></span>
                                            <span class="eval-score-den">/5</span>
                                        </div>
                                        <input type="hidden" name="evaluaciones[<?= $key ?>][calificacion]" value="<?= $calActual ?>" class="eval-input-calificacion">
                                        <?php if ($esAuto): ?>
                                        <input type="hidden" name="evaluaciones[<?= $key ?>][observacion]" value="<?= esc($detalle) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="eval-recomendacion">
                            <label class="eval-criterio-label">Recomendacion final</label>
                            <div class="eval-recomendacion-options">
                                <?php
                                $recExistente = null;
                                foreach ($p['evaluaciones'] as $ev) {
                                    if ($ev['criterio'] === 'recomendacion_final') {
                                        $recExistente = $ev;
                                        break;
                                    }
                                }
                                $recActual = $recExistente['calificacion'] ?? 0;
                                $recOptions = [1 => 'No recomendado', 2 => 'Recomendado', 3 => 'Altamente recomendado'];
                                ?>
                                <?php foreach ($recOptions as $val => $label): ?>
                                <button type="button" class="eval-rec-btn <?= $recActual == $val ? 'eval-rec-selected' : '' ?>" data-value="<?= $val ?>">
                                    <?= $label ?>
                                </button>
                                <?php endforeach; ?>
                                <input type="hidden" name="evaluaciones[recomendacion_final][calificacion]" value="<?= $recActual ?>" class="eval-input-recomendacion">
                                <input type="hidden" name="evaluaciones[recomendacion_final][observacion]" value="">
                            </div>
                        </div>

                        <div class="eval-final-actions">
                            <button type="submit" class="btn btn-primary">Guardar evaluacion final</button>
                            <button type="button" class="btn btn-ghost btn-recalcular-eval" data-proceso-id="<?= $proceso['id'] ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                Calcular
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <?php if ($pasoActual == 6): ?>
                <div class="eval-section eval-section-presentacion">
                    <div class="eval-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>Presentar a la empresa</span>
                    </div>

                    <form class="form-presentacion-empresa">
                        <input type="hidden" name="proceso_id" value="<?= $proceso['id'] ?>">
                        <div class="presentacion-info">
                            <div class="presentacion-info-item">
                                <div class="presentacion-info-label">Vacante</div>
                                <div class="presentacion-info-value"><?= esc($p['vacante_titulo'] ?? 'N/A') ?></div>
                            </div>
                            <div class="presentacion-info-item">
                                <div class="presentacion-info-label">Empresa</div>
                                <div class="presentacion-info-value"><?= esc($p['empresa_nombre'] ?? 'N/A') ?></div>
                            </div>
                        </div>

                        <div class="presentacion-form-row">
                            <div class="form-group">
                                <label class="form-label">Fecha de entrevista con la empresa</label>
                                <input type="datetime-local" name="fecha_entrevista_empresa" class="form-control" value="<?= !empty($proceso['fecha_presentacion']) ? date('Y-m-d\TH:i', strtotime($proceso['fecha_presentacion'])) : '' ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Notas para la empresa (opcional)</label>
                                <textarea name="notas_empresa" class="form-control" rows="2" placeholder="Informacion adicional para la empresa..."></textarea>
                            </div>
                        </div>

                        <div class="eval-final-actions">
                            <button type="submit" class="btn btn-primary">Asignar fecha y completar</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <?php if ($estadoProceso !== 'completado'): ?>
                <div class="pipeline-action">
                    <form class="form-avanzar-paso" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                        <input type="hidden" name="proceso_id" value="<?= $proceso['id'] ?>">
                        <div class="form-group" style="flex:1;min-width:300px;margin-bottom:0;">
                            <label class="form-label">Notas del paso actual (<?= $pasos[$pasoActual] ?? '' ?>)</label>
                            <textarea name="notas" class="form-control" rows="2" placeholder="Agregar notas sobre este paso..."></textarea>
                        </div>
                        <div class="pipeline-action-buttons">
                            <?php if ($pasoActual > 2): ?>
                            <button type="button" class="btn btn-ghost btn-retroceder-paso" data-proceso-id="<?= $proceso['id'] ?>" data-paso-actual="<?= $pasoActual ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"><polyline points="15 18 9 12 15 6"/></svg>
                                Retroceder
                            </button>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary">
                                Completar paso <?= $pasoActual ?> y avanzar
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

            <?php endif; ?>

            <?php if (!empty($p['requisitos']) && (!$proceso || $pasoActual == 3)): ?>
                <div class="pipeline-docs-section">
                    <div class="pipeline-docs-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;flex-shrink:0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span>Documentos de la vacante</span>
                        <?php
                        $totalDocs = count($p['documentos']);
                        $totalReqs = count($p['requisitos']);
                        $pctDocs = $totalReqs > 0 ? round(($totalDocs / $totalReqs) * 100) : 0;
                        ?>
                        <span class="pipeline-docs-progress <?= $pctDocs == 100 ? 'pipeline-docs-progress-complete' : '' ?>">
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
                                        <span style="font-size:13px;color:var(--text-secondary);">
                                            <?= esc($docAsociado['nombre_documento']) ?>
                                        </span>
                                        <span class="verif-doc-estado verif-doc-estado-<?= $docAsociado['estado'] ?>">
                                            <?= ucfirst($docAsociado['estado']) ?>
                                        </span>
                                        <?php if ($docAsociado['estado'] === 'pendiente'): ?>
                                            <div class="verif-doc-actions">
                                                <button type="button" class="btn btn-sm btn-primary btn-verificar-doc" data-id="<?= $docAsociado['id'] ?>" data-estado="aprobado">Aprobar</button>
                                                <button type="button" class="btn btn-sm btn-ghost btn-verificar-doc" data-id="<?= $docAsociado['id'] ?>" data-estado="rechazado">Rechazar</button>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($docAsociado['comentario_verificacion'])): ?>
                                            <div class="verif-doc-comentario"><?= esc($docAsociado['comentario_verificacion']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="verif-doc-faltante" style="margin-bottom:8px;">Pendiente de entrega</div>
                                    <button type="button" class="btn btn-sm btn-primary btn-admin-subir-doc" data-postulacion="<?= $p['postulacion_id'] ?>" data-requisito="<?= $req['requisito_id'] ?>" data-nombre="<?= esc($req['requisito_nombre']) ?>">
                                        Subir documento
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="dash-card">
        <div class="empty-state">
            <p>Este candidato no tiene postulaciones registradas.</p>
        </div>
    </div>
<?php endif; ?>

<div class="modal-overlay" id="modalVerificarDoc">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="modalVerificarTitulo">Verificar documento</h2>
            <button type="button" class="modal-close" id="closeModalVerificar" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="formVerificarDoc">
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Comentario (opcional)</label>
                <textarea name="comentario" class="form-control" rows="3" placeholder="Agregar un comentario sobre la verificacion..."></textarea>
            </div>
            <input type="hidden" name="estado" id="verificarEstado" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" id="cancelModalVerificar">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modalAdminSubirDoc">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="modalAdminSubirTitulo">Subir documento</h2>
            <button type="button" class="modal-close" id="closeModalAdminSubir" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="formAdminSubirDoc" enctype="multipart/form-data">
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Nombre del documento</label>
                <input type="text" name="nombre_documento" class="form-control" id="adminInputNombreDoc" required>
            </div>
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Archivo (PDF, JPG, PNG, DOC, DOCX - max 5MB)</label>
                <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
            </div>
            <input type="hidden" name="postulacion_id" id="adminInputPostulacionId" value="">
            <input type="hidden" name="requisito_id" id="adminInputRequisitoId" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" id="cancelModalAdminSubir">Cancelar</button>
                <button type="submit" class="btn btn-primary">Subir documento</button>
            </div>
        </form>
    </div>
</div>
