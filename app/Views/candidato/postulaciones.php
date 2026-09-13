<div class="page-header">
    <div>
        <h1>Mis Postulaciones</h1>
        <p>Estado de tus postulaciones a vacantes.</p>
    </div>
</div>

<?php
$pasos = [
    1 => 'En asignacion',
    2 => 'Contactandote',
    3 => 'Revision de documentos',
    4 => 'Entrevista',
    5 => 'Evaluacion',
    6 => 'Presentacion a empresa',
];
?>

<?php if (!empty($postulaciones)): ?>
    <?php foreach ($postulaciones as $p):
        $puntaje = (int)($p['puntaje_match'] ?? 0);
        $puntajeColor = $puntaje >= 80 ? 'var(--success)' : ($puntaje >= 50 ? 'var(--warning)' : 'var(--danger)');
        $pasoActual = (int)($p['paso_actual'] ?? 0);
        $procesoEstado = $p['proceso_estado'] ?? null;
        if ($procesoEstado && $pasoActual > 0 && isset($pasos[$pasoActual])) {
            $estadoTexto = $pasos[$pasoActual];
        } elseif ($procesoEstado === 'completado') {
            $estadoTexto = 'Proceso completado';
        } else {
            $estadoTexto = 'Enviada, en espera';
        }
    ?>
        <a href="<?= base_url('candidato/vacante/' . ($p['vacante_slug'] ?? '')) ?>" class="dash-card postulacion-card">
            <div class="postulacion-score" style="background: conic-gradient(<?= $puntajeColor ?> <?= $puntaje * 3.6 ?>deg, var(--border) 0deg);">
                <span><?= $puntaje ?>%</span>
            </div>
            <div class="postulacion-body">
                <h3><?= esc($p['vacante_titulo']) ?></h3>
                <p><?= esc($p['empresa_nombre']) ?> &middot; Postulado: <?= date('d/m/Y', strtotime($p['created_at'])) ?></p>
                <span class="postulacion-estado-proceso"><?= esc($estadoTexto) ?></span>
            </div>
            <span class="verif-badge verif-badge-<?= $p['estado'] === 'aceptada' ? 'green' : ($p['estado'] === 'rechazada' ? 'red' : 'orange') ?>">
                <?= ucfirst(str_replace('_', ' ', $p['estado'])) ?>
            </span>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <div class="dash-card">
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <h3>No tienes postulaciones</h3>
            <p>Cuando te postules a una vacante, aparecera aqui con su estado.</p>
            <a href="<?= base_url('buscar-empleo') ?>" class="btn btn-primary">Buscar vacantes</a>
        </div>
    </div>
<?php endif; ?>
