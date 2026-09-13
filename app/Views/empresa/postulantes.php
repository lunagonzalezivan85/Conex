<div class="page-header">
    <div>
        <h1>Postulantes</h1>
        <p>Candidatos que se han postulado a tus vacantes.</p>
    </div>
</div>

<?php if (!empty($postulaciones)): ?>
<div class="postulantes-list">
    <?php foreach ($postulaciones as $p): ?>
    <div class="dash-card postulante-card" onclick="window.location.href='<?= base_url('empresa/postulante/ver/' . $p['id']) ?>'">
        <div class="postulante-avatar">
            <?= strtoupper(substr($p['nombre'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="postulante-info">
            <h3 class="postulante-name"><?= esc(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></h3>
            <p class="postulante-meta">
                <?= esc($p['email'] ?? '') ?>
                <?php if (!empty($p['profesion'])): ?> · <?= esc($p['profesion']) ?><?php endif; ?>
            </p>
            <div class="postulante-tags">
                <span class="tag tag-vacante"><?= esc($p['vacante_titulo'] ?? '') ?></span>
                <?php
                    $estadoColors = [
                        'enviada' => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
                        'en_revision' => ['bg' => '#fef3c7', 'color' => '#d97706'],
                        'aceptada' => ['bg' => '#dcfce7', 'color' => '#16a34a'],
                        'rechazada' => ['bg' => '#fee2e2', 'color' => '#ef4444'],
                        'descartada' => ['bg' => '#f3f4f6', 'color' => '#9ca3af'],
                    ];
                    $ec = $estadoColors[$p['estado'] ?? 'enviada'] ?? $estadoColors['enviada'];
                    $estadoLabel = ['enviada' => 'Enviada', 'en_revision' => 'En revision', 'aceptada' => 'Aceptada', 'rechazada' => 'Rechazada', 'descartada' => 'Descartada'];
                ?>
                <span class="tag" style="background:<?= $ec['bg'] ?>;color:<?= $ec['color'] ?>;"><?= esc($estadoLabel[$p['estado'] ?? 'enviada'] ?? $p['estado']) ?></span>
                <?php if (!empty($p['puntaje_match'])): ?>
                <span class="tag tag-match">Match: <?= number_format($p['puntaje_match'], 0) ?>%</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="postulante-score">
            <?php if (!empty($p['proceso']) && $p['puntaje_total'] > 0): ?>
                <div class="score-circle score-circle-<?= min(5, max(1, ceil($p['puntaje_total'] / 20))) ?>">
                    <span class="score-num"><?= $p['puntaje_total'] ?></span>
                    <span class="score-den">/100</span>
                </div>
                <?php
                    $recLabels = [1 => 'No recomendado', 2 => 'Recomendado', 3 => 'Altamente recomendado'];
                    $recColors = [1 => '#ef4444', 2 => '#f97316', 3 => '#16a34a'];
                    $rec = $p['recomendacion'] ?? null;
                ?>
                <?php if ($rec !== null && isset($recLabels[$rec])): ?>
                <span class="postulante-rec" style="background:<?= $recColors[$rec] ?>1a;color:<?= $recColors[$rec] ?>;"><?= esc($recLabels[$rec]) ?></span>
                <?php endif; ?>
                <?php if (!empty($p['fecha_entrevista_empresa'])): ?>
                <span class="postulante-fecha">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;vertical-align:middle;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?= date('d M Y, H:i', strtotime($p['fecha_entrevista_empresa'])) ?>
                </span>
                <?php endif; ?>
            <?php else: ?>
                <span class="postulante-sin-eval">Sin evaluar</span>
            <?php endif; ?>
        </div>
        <div class="postulante-actions">
            <p class="postulante-fecha-post"><?= isset($p['created_at']) ? date('d M Y', strtotime($p['created_at'])) : '' ?></p>
            <span class="postulante-ver-link">Ver perfil
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:middle;"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="dash-card">
    <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <h3>No tienes postulantes</h3>
        <p>Cuando los candidatos se postulen a tus vacantes, apareceran aqui.</p>
        <a href="<?= base_url('empresa/vacante/crear') ?>" class="btn btn-primary">Publicar vacante</a>
    </div>
</div>
<?php endif; ?>
