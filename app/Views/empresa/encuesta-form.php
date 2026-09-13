<?php if (empty($postulantes)): ?>
    <p style="text-align:center;padding:32px;color:var(--text-secondary);">
        No hay postulantes seleccionados para esta vacante.
    </p>
<?php else: ?>
    <p style="font-size:14px;color:var(--text-secondary);margin-bottom:24px;">
        Califica a cada postulante seleccionado para la vacante <strong><?= esc($vacante_titulo) ?></strong>.
        Asigna de 1 a 5 estrellas en cada criterio.
    </p>

    <form action="<?= base_url('empresa/encuestas/guardar/' . $encuesta_id) ?>" method="post" id="formEncuesta">
        <?= csrf_field() ?>

        <?php foreach ($postulantes as $i => $p): ?>
            <div class="encuesta-candidato">
                <div class="encuesta-candidato-header">
                    <div class="encuesta-candidato-nombre">
                        <?= esc($p['nombre'] . ' ' . $p['apellido']) ?>
                    </div>
                    <div class="encuesta-candidato-profesion"><?= esc($p['profesion'] ?? '') ?></div>
                </div>

                <input type="hidden" name="postulantes[<?= $i ?>][postulacion_id]" value="<?= $p['postulacion_id'] ?>">
                <input type="hidden" name="postulantes[<?= $i ?>][candidato_id]" value="<?= $p['candidato_id'] ?>">

                <div class="encuesta-criterios">
                    <?php
                    $criterios = [
                        'puntualidad' => 'Puntualidad',
                        'profesionalismo' => 'Profesionalismo',
                        'aptitud_tecnica' => 'Aptitud tecnica',
                        'comunicacion' => 'Comunicacion',
                        'recomendacion' => 'Lo recomendaria',
                    ];
                    foreach ($criterios as $key => $label):
                    ?>
                        <div class="encuesta-criterio">
                            <label class="encuesta-criterio-label"><?= $label ?></label>
                            <div class="encuesta-stars" data-name="postulantes[<?= $i ?>][<?= $key ?>]">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <button type="button" class="encuesta-star" data-val="<?= $s ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    </button>
                                <?php endfor; ?>
                                <input type="hidden" name="postulantes[<?= $i ?>][<?= $key ?>]" value="0">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="encuesta-comentario-wrap">
                    <label class="encuesta-criterio-label">Comentario (opcional)</label>
                    <textarea name="postulantes[<?= $i ?>][comentario]" class="form-control" rows="2" placeholder="Comentarios sobre el postulante..."></textarea>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="modal-footer" style="margin-top:24px;">
            <button type="button" class="btn btn-ghost" id="cancelarEncuesta">Cancelar</button>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
                Enviar encuesta
            </button>
        </div>
    </form>
<?php endif; ?>
