<?php
$finalizado = ($contacto['estado'] ?? '') === 'finalizado';
$rechazado  = ($contacto['estado'] ?? '') === 'rechazado';
$volverUrl  = $rol === 'empresa' ? 'empresa/chat' : 'candidato/mensajes';
$miTipo     = $lado; // 'cliente' o 'prestador'
?>
<div class="chat-panel">
    <div class="card chat-hilo-card">
        <div class="chat-hilo-head">
            <a href="<?= base_url($volverUrl) ?>" class="chat-back">&larr; Volver</a>
            <div class="chat-hilo-title">
                <span class="chat-item-avatar"><?= esc(strtoupper(mb_substr($otro, 0, 1))) ?></span>
                <div>
                    <strong><?= esc(cxNombre($otro)) ?></strong>
                    <span class="chat-hilo-sub"><?= esc($contacto['oficio'] ?? '') ?> · <?= esc(ucfirst($contacto['estado'])) ?></span>
                </div>
            </div>
            <?php if (!$finalizado && !$rechazado): ?>
                <form action="<?= base_url('chat/' . (int)$contacto['id'] . '/finalizar') ?>" method="post"
                      onsubmit="return confirm('Finalizar la conversacion? Sera permanente y nadie podra seguir escribiendo.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-ghost btn-sm chat-btn-fin">Finalizar</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($contacto['estado'] === 'aceptado' && !empty($contacto['telefono']) && $lado === 'prestador'): ?>
            <div class="chat-hilo-tel">
                Telefono de contacto:
                <a href="https://wa.me/<?= preg_replace('/\D/', '', $contacto['telefono']) ?>" target="_blank" rel="noopener">
                    <?= esc($contacto['telefono']) ?> (WhatsApp)
                </a>
            </div>
        <?php endif; ?>

        <div class="chat-hilo-msgs" id="chatMsgs">
            <?php foreach ($mensajes as $m): ?>
                <?php if ($m['emisor_tipo'] === 'sistema'): ?>
                    <div class="chat-msg chat-msg-sis"><?= esc($m['mensaje']) ?></div>
                <?php else: ?>
                    <div class="chat-msg <?= $m['emisor_tipo'] === $miTipo ? 'chat-msg-me' : 'chat-msg-otro' ?>">
                        <span class="chat-msg-autor"><?= esc(cxNombre($m['emisor_nombre'])) ?></span>
                        <p class="chat-msg-texto"><?= nl2br(esc($m['mensaje'])) ?></p>
                        <?php if (!empty($m['precio_propuesto'])): ?>
                            <span class="chat-msg-precio">Precio propuesto: C$ <?= number_format((float)$m['precio_propuesto'], 0) ?></span>
                        <?php endif; ?>
                        <span class="chat-msg-hora"><?= date('d/m H:i', strtotime($m['created_at'])) ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if (empty($mensajes) && !empty($contacto['mensaje'])): ?>
                <div class="chat-msg <?= $lado === 'cliente' ? 'chat-msg-me' : 'chat-msg-otro' ?>">
                    <span class="chat-msg-autor"><?= esc($contacto['nombre']) ?></span>
                    <p class="chat-msg-texto"><?= nl2br(esc($contacto['mensaje'])) ?></p>
                    <span class="chat-msg-hora"><?= date('d/m H:i', strtotime($contacto['created_at'])) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($finalizado): ?>
            <div class="chat-hilo-cerrado">Conversacion finalizada. No se pueden enviar mas mensajes.</div>
        <?php elseif ($rechazado): ?>
            <div class="chat-hilo-cerrado">Esta solicitud fue rechazada.</div>
        <?php else: ?>
            <form class="chat-hilo-form" action="<?= base_url('chat/' . (int)$contacto['id'] . '/mensaje') ?>" method="post">
                <?= csrf_field() ?>
                <?php if ($lado === 'prestador'): ?>
                    <input type="number" name="precio_propuesto" class="chat-input chat-input-precio" placeholder="C$ (opcional)" min="0" step="any" aria-label="Precio propuesto en cordobas">
                <?php endif; ?>
                <input type="text" name="mensaje" class="chat-input" placeholder="Escribe un mensaje..." maxlength="1000" required autocomplete="off">
                <button type="submit" class="btn btn-primary chat-send" aria-label="Enviar mensaje">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
<script>
(function () {
    var el = document.getElementById('chatMsgs');
    if (el) { el.scrollTop = el.scrollHeight; }
})();
</script>
