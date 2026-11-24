<div class="page-header">
    <div>
        <h1>Notificaciones</h1>
        <p>Avisos sobre tus postulaciones, mensajes y solicitudes.</p>
    </div>
    <?php
        $noLeidas = count(array_filter($notificaciones, fn($n) => (int)$n['leida'] === 0));
    ?>
    <?php if ($noLeidas > 0): ?>
        <form method="post" action="<?= base_url('notificaciones/leer') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-ghost" style="align-self:center;">
                Marcar todas como leidas (<?= $noLeidas ?>)
            </button>
        </form>
    <?php endif; ?>
</div>

<div class="dash-card" style="padding:8px 0;">
    <?php if (!empty($notificaciones)): ?>
        <?php foreach ($notificaciones as $n): ?>
            <?php
                $noLeida = (int)$n['leida'] === 0;
                $fecha = !empty($n['created_at']) ? date('d/m/Y H:i', strtotime($n['created_at'])) : '';
                $icono = match ($n['tipo'] ?? '') {
                    'mensaje'     => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#0FC6C2" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
                    'postulacion' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#F97316" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
                    default       => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#1B2A4A" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
                };
                $href = !empty($n['url']) ? base_url($n['url']) : null;
            ?>
            <<?= $href ? 'a href="' . esc($href) . '"' : 'div' ?>
                class="notif-item"
                style="display:flex;gap:14px;align-items:flex-start;padding:14px 20px;text-decoration:none;color:inherit;border-bottom:1px solid var(--cx-border,#EEF1F6);<?= $noLeida ? 'background:#F0FBFB;' : '' ?>">
                <span style="flex-shrink:0;width:38px;height:38px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:var(--cx-surface,#F8FAFC);border:1px solid var(--cx-border,#EEF1F6);">
                    <?= $icono ?>
                </span>
                <span style="flex:1;min-width:0;">
                    <span style="display:block;font-size:14px;font-weight:<?= $noLeida ? '700' : '600' ?>;color:var(--cx-navy,#1B2A4A);">
                        <?= esc($n['titulo']) ?>
                        <?php if ($noLeida): ?>
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#0FC6C2;margin-left:6px;vertical-align:middle;"></span>
                        <?php endif; ?>
                    </span>
                    <span style="display:block;font-size:13px;color:#4b5563;margin-top:2px;line-height:1.45;">
                        <?= esc($n['mensaje']) ?>
                    </span>
                    <span style="display:block;font-size:12px;color:#9ca3af;margin-top:4px;"><?= esc($fecha) ?></span>
                </span>
            </<?= $href ? 'a' : 'div' ?>>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align:center;padding:48px 20px;color:#6b7280;">
            <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.4;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <p style="margin:12px 0 0;font-size:14px;">No tienes notificaciones todavia.</p>
        </div>
    <?php endif; ?>
</div>
