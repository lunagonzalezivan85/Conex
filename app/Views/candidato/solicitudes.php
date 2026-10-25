<?php
$estadoLabel = ['pendiente' => 'Pendiente', 'aceptado' => 'Aceptada', 'rechazado' => 'Rechazada'];
$estadoClass = ['pendiente' => 'badge-warning', 'aceptado' => 'badge-success', 'rechazado' => 'badge-accent'];
?>
<div class="srv-panel">
    <div class="card">
        <h3 class="srv-card-h">Solicitudes de contacto (<?= count($solicitudes) ?>)</h3>
        <p class="srv-sol-nota">
            El telefono del cliente solo se muestra cuando <strong>aceptas</strong> la solicitud.
            Si rechazas, sus datos quedan ocultos permanentemente.
        </p>

        <?php if (!empty($solicitudes)): ?>
            <div class="srv-list">
                <?php foreach ($solicitudes as $s): ?>
                    <div class="srv-item">
                        <div class="srv-item-info">
                            <strong><?= esc($s['nombre']) ?></strong>
                            <span class="srv-item-meta">
                                <?= esc($s['oficio']) ?> · <?= date('d/m/Y H:i', strtotime($s['created_at'])) ?>
                            </span>
                            <?php if (!empty($s['mensaje'])): ?>
                                <span class="srv-item-desc">"<?= esc($s['mensaje']) ?>"</span>
                            <?php endif; ?>
                            <?php if ($s['estado'] === 'aceptado' && !empty($s['telefono'])): ?>
                                <span class="srv-item-tel">
                                    Telefono:
                                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $s['telefono']) ?>" target="_blank" rel="noopener">
                                        <?= esc($s['telefono']) ?> (WhatsApp)
                                    </a>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="srv-item-actions">
                            <span class="badge <?= $estadoClass[$s['estado']] ?? 'badge-accent' ?>">
                                <?= $estadoLabel[$s['estado']] ?? $s['estado'] ?>
                            </span>
                            <?php if ($s['estado'] === 'pendiente'): ?>
                                <form action="<?= base_url('candidato/solicitudes/' . (int)$s['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="accion" value="aceptar">
                                    <button type="submit" class="btn btn-primary btn-sm">Aceptar y ver contacto</button>
                                </form>
                                <form action="<?= base_url('candidato/solicitudes/' . (int)$s['id']) ?>" method="post"
                                      onsubmit="return confirm('Rechazar esta solicitud?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="accion" value="rechazar">
                                    <button type="submit" class="btn btn-ghost btn-sm">Rechazar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="srv-empty">Sin solicitudes todavia. Cuando alguien quiera contratarte aparecera aqui.</p>
        <?php endif; ?>
    </div>
</div>
