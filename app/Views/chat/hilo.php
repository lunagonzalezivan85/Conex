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
            <div class="chat-hilo-actions">
            <?php if ($lado === 'prestador' && $contacto['estado'] === 'aceptado' && !empty($contacto['telefono'])): ?>
                <?php $tel = preg_replace('/\D/', '', $contacto['telefono']); ?>
                <a class="chat-ico-btn" href="tel:<?= $tel ?>" aria-label="Llamar" title="Llamar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </a>
                <a class="chat-ico-btn chat-ico-wa" href="https://wa.me/<?= $tel ?>" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.47-2.4-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.44-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.06 2.87 1.21 3.07c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.7.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2-1.41.25-.7.25-1.29.18-1.42-.08-.13-.28-.2-.57-.35zM12.05 21.79h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.85 9.85 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88a9.82 9.82 0 0 1 9.88 9.89c0 5.45-4.43 9.88-9.88 9.88zm8.42-18.3A11.82 11.82 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.14 1.59 5.95L.05 24l6.3-1.65a11.87 11.87 0 0 0 5.7 1.45h.01c6.55 0 11.89-5.33 11.89-11.89 0-3.18-1.24-6.16-3.48-8.41z"/></svg>
                </a>
            <?php endif; ?>
            <?php if (!$finalizado && !$rechazado): ?>
                <form action="<?= base_url('chat/' . (int)$contacto['id'] . '/finalizar') ?>" method="post"
                      onsubmit="return confirm('Finalizar la conversacion? Sera permanente y nadie podra seguir escribiendo.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-ghost btn-sm chat-btn-fin">Finalizar</button>
                </form>
            <?php endif; ?>
            </div>
        </div>

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
