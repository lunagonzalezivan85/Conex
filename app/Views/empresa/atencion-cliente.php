<div class="page-header">
    <div>
        <h1>Atencion al Cliente</h1>
        <p>Reporta incidencias y solicita soporte.</p>
    </div>
    <button type="button" class="btn btn-primary" id="btnNuevaIncidencia">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nueva incidencia
    </button>
</div>

<div class="modal-overlay" id="modalIncidencia">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Nueva incidencia</h2>
            <button type="button" class="modal-close" id="closeModalIncidencia" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form action="<?= base_url('empresa/atencion/guardar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Titulo *</label>
                <input type="text" name="titulo" class="form-control" required placeholder="Describe brevemente el problema">
            </div>

            <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
                <div class="form-group" style="flex:1;min-width:200px;">
                    <label class="form-label">Categoria *</label>
                    <select name="categoria" class="form-control" required>
                        <option value="tecnico">Problema tecnico</option>
                        <option value="facturacion">Facturacion / Pagos</option>
                        <option value="cuenta">Cuenta / Acceso</option>
                        <option value="vacante">Vacantes</option>
                        <option value="postulante">Postulante</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="form-group" style="min-width:180px;">
                    <label class="form-label">Prioridad *</label>
                    <select name="prioridad" class="form-control" required>
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:24px;">
                <label class="form-label">Descripcion *</label>
                <textarea name="descripcion" class="form-control" rows="4" required placeholder="Describe el problema en detalle..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" id="cancelModalIncidencia">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
                    Enviar incidencia
                </button>
            </div>
        </form>
    </div>
</div>

<div class="dash-card" style="margin-bottom:24px;">
    <div class="section-header" style="margin-bottom:20px;">
        <h2>Mis incidencias</h2>
    </div>

    <?php if (!empty($incidencias)): ?>
        <div class="incidencia-list">
            <?php foreach ($incidencias as $inc): ?>
                <div class="incidencia-item">
                    <div class="incidencia-item-header">
                        <div class="incidencia-item-titulo">
                            <?= esc($inc['titulo']) ?>
                            <span class="incidencia-badge inc-badge-<?= $inc['estado'] ?>">
                                <?php
                                $estados = [
                                    'abierta' => 'Abierta',
                                    'en_proceso' => 'En proceso',
                                    'resuelta' => 'Resuelta',
                                    'cerrada' => 'Cerrada',
                                ];
                                echo $estados[$inc['estado']] ?? $inc['estado'];
                                ?>
                            </span>
                            <span class="incidencia-prioridad inc-prioridad-<?= $inc['prioridad'] ?>">
                                <?= ucfirst($inc['prioridad']) ?>
                            </span>
                        </div>
                        <div class="incidencia-item-fecha">
                            <?= date('d/m/Y H:i', strtotime($inc['created_at'])) ?>
                        </div>
                    </div>
                    <div class="incidencia-item-desc">
                        <?= nl2br(esc($inc['descripcion'])) ?>
                    </div>
                    <div class="incidencia-item-meta">
                        <span class="incidencia-categoria">
                            <?php
                            $categorias = [
                                'tecnico' => 'Problema tecnico',
                                'facturacion' => 'Facturacion / Pagos',
                                'cuenta' => 'Cuenta / Acceso',
                                'vacante' => 'Vacantes',
                                'postulante' => 'Postulante',
                                'otro' => 'Otro',
                            ];
                            echo $categorias[$inc['categoria']] ?? $inc['categoria'];
                            ?>
                        </span>
                        <span>Incidencia #<?= $inc['id'] ?></span>
                    </div>
                    <?php if (!empty($inc['respuesta'])): ?>
                        <div class="incidencia-respuesta">
                            <div class="incidencia-respuesta-label">Respuesta del equipo de soporte:</div>
                            <?= nl2br(esc($inc['respuesta'])) ?>
                            <?php if (!empty($inc['respondido_at'])): ?>
                                <div class="incidencia-respuesta-fecha">
                                    Respondido el <?= date('d/m/Y H:i', strtotime($inc['respondido_at'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color:var(--text-secondary);text-align:center;padding:32px 0;">
            No has registrado incidencias. Haz clic en "Nueva incidencia" para reportar un problema.
        </p>
    <?php endif; ?>
</div>

<script>
(function() {
    var modal = document.getElementById('modalIncidencia');
    var btnOpen = document.getElementById('btnNuevaIncidencia');
    var btnClose = document.getElementById('closeModalIncidencia');
    var btnCancel = document.getElementById('cancelModalIncidencia');

    function openModal() { modal.classList.add('active'); }
    function closeModal() { modal.classList.remove('active'); }

    btnOpen.addEventListener('click', openModal);
    btnClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
    });
})();
</script>
