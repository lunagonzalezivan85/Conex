<div class="page-header">
    <div>
        <h1>Encuestas</h1>
        <p>Califica a los postulantes seleccionados al finalizar un proceso.</p>
    </div>
</div>

<?php if (session()->getFlashdata('info')): ?>
<div class="dash-card" style="margin-bottom:16px;padding:16px;background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;color:#065f46;">
    <?= esc(session()->getFlashdata('info')) ?>
</div>
<?php endif; ?>

<?php if (!empty($encuestas)): ?>
    <div class="encuesta-list">
        <?php foreach ($encuestas as $enc): ?>
            <div class="dash-card encuesta-card" style="margin-bottom:16px;">
                <div class="encuesta-card-header">
                    <div>
                        <h2><?= esc($enc['vacante_titulo']) ?></h2>
                        <p class="encuesta-card-meta">
                            Encuesta #<?= $enc['id'] ?> &middot;
                            <?= $enc['total_postulantes'] ?> postulante(s) seleccionado(s) &middot;
                            Creada el <?= date('d/m/Y', strtotime($enc['created_at'])) ?>
                        </p>
                    </div>
                    <span class="encuesta-badge enc-badge-<?= $enc['estado'] ?>">
                        <?php
                        $estadosEnc = [
                            'abierta' => 'Abierta',
                            'respondida' => 'Respondida',
                            'cerrada' => 'Cerrada',
                        ];
                        echo $estadosEnc[$enc['estado']] ?? $enc['estado'];
                        ?>
                    </span>
                </div>

                <?php if ($enc['estado'] === 'abierta'): ?>
                    <button type="button" class="btn btn-primary btn-responder-encuesta" data-id="<?= $enc['id'] ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Responder encuesta
                    </button>
                <?php else: ?>
                    <span style="color:var(--text-secondary);font-size:14px;">Encuesta completada.</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="dash-card" style="text-align:center;padding:48px 24px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;color:var(--text-secondary);margin-bottom:16px;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        <p style="color:var(--text-secondary);font-size:15px;">
            No tienes encuestas pendientes. Al cerrar una vacante con postulantes seleccionados,
            se generara una encuesta para calificar a los candidatos.
        </p>
    </div>
<?php endif; ?>

<div class="modal-overlay" id="modalEncuesta">
    <div class="modal-box modal-box-lg">
        <div class="modal-header">
            <h2>Encuesta de postulantes</h2>
            <button type="button" class="modal-close" id="closeModalEncuesta" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body" id="modalEncuestaBody">
            <p style="text-align:center;padding:32px;color:var(--text-secondary);">Cargando...</p>
        </div>
    </div>
</div>
