<?php
$estadoLabel = ['pendiente' => 'Pendiente', 'aceptado' => 'En curso', 'rechazado' => 'Rechazada', 'finalizado' => 'Finalizada'];
$estadoClass = ['pendiente' => 'chat-badge-pend', 'aceptado' => 'chat-badge-ok', 'rechazado' => 'chat-badge-rej', 'finalizado' => 'chat-badge-fin'];
?>
<div class="chat-panel">
    <div class="card">
        <h3 class="chat-card-h">Mensajes (<?= count($conversaciones) ?>)</h3>
        <p class="chat-nota">
            Conversaciones con <?= $rol === 'empresa' ? 'candidatos y solicitantes' : 'empresas y clientes' ?>.
            Cuando una conversacion se finaliza, queda cerrada permanentemente.
        </p>

        <?php if (!empty($conversaciones)): ?>
            <div class="chat-list">
                <?php foreach ($conversaciones as $c): ?>
                    <a class="chat-item" href="<?= base_url('chat/' . (int)$c['id']) ?>">
                        <span class="chat-item-avatar"><?= esc(strtoupper(mb_substr($c['otro'], 0, 1))) ?></span>
                        <span class="chat-item-body">
                            <span class="chat-item-top">
                                <strong><?= esc(cxNombre($c['otro'])) ?></strong>
                                <span class="chat-item-date"><?= date('d/m/Y', strtotime($c['updated_at'] ?? $c['created_at'])) ?></span>
                            </span>
                            <span class="chat-item-preview"><?= esc(mb_substr($c['ultimo'] ?? '', 0, 80)) ?></span>
                            <span class="chat-item-meta">
                                <span class="chat-badge <?= $estadoClass[$c['estado']] ?? 'chat-badge-pend' ?>">
                                    <?= $estadoLabel[$c['estado']] ?? esc($c['estado']) ?>
                                </span>
                                <span class="chat-item-oficio"><?= esc($c['oficio']) ?></span>
                            </span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="chat-empty">Sin conversaciones todavia.
                <?= $rol === 'empresa'
                    ? 'Contacta a un postulante desde su perfil para iniciar una conversacion.'
                    : 'Cuando una empresa o cliente te escriba aparecera aqui.' ?>
            </p>
        <?php endif; ?>
    </div>
</div>
