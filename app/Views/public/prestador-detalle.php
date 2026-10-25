<?php
$precioTipo = ['por_hora' => '/hora', 'por_servicio' => ' por servicio', 'a_convenir' => ''];
?>
<div class="container srv-detalle">
    <a href="<?= base_url('servicios') ?>" class="srv-back">&larr; Volver al directorio</a>

    <div class="srv-detalle-grid reveal">
        <div class="card srv-perfil">
            <div class="srv-card-head srv-card-head-lg">
                <?php if (!empty($servicio['avatar_url'])): ?>
                    <img src="<?= esc($servicio['avatar_url']) ?>" alt="" class="srv-avatar srv-avatar-lg">
                <?php else: ?>
                    <div class="srv-avatar srv-avatar-ini srv-avatar-lg"><?= esc($servicio['iniciales']) ?></div>
                <?php endif; ?>
                <div class="srv-card-id">
                    <span class="srv-nombre srv-nombre-lg">
                        <?= esc($servicio['nombre_publico']) ?>
                        <?php if (!empty($servicio['prestador_verificado'])): ?>
                            <span class="srv-verified" title="Identidad verificada por CONEX">
                                <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M12 2l2.4 2.4 3.4-.4 1 3.3 3 1.7-1.3 3.1 1.3 3.1-3 1.7-1 3.3-3.4-.4L12 22l-2.4-2.4-3.4.4-1-3.3-3-1.7L3.5 12 2.2 8.9l3-1.7 1-3.3 3.4.4z"/><path d="M9.5 12.2l1.8 1.8 3.5-3.8" stroke="#fff" stroke-width="2" fill="none"/></svg>
                                Verificado
                            </span>
                        <?php endif; ?>
                    </span>
                    <h1 class="srv-oficio-lg"><?= esc($servicio['oficio']) ?></h1>
                    <p class="be-card-location">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?= esc($servicio['zona'] ?: 'Zona por confirmar') ?><?= !empty($servicio['ciudad']) ? ' · ' . esc($servicio['ciudad']) : '' ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($servicio['descripcion'])): ?>
                <p class="srv-desc-lg"><?= nl2br(esc($servicio['descripcion'])) ?></p>
            <?php endif; ?>

            <div class="be-card-badges">
                <?php if (!empty($servicio['precio_desde'])): ?>
                    <span class="badge badge-success">Desde $<?= number_format($servicio['precio_desde'], 0) ?><?= $precioTipo[$servicio['precio_tipo']] ?? '' ?></span>
                <?php else: ?>
                    <span class="badge badge-accent">Precio a convenir</span>
                <?php endif; ?>
                <span class="badge badge-accent">Ubicacion aproximada</span>
            </div>

            <?php if (!empty($otros)): ?>
                <div class="srv-otros">
                    <h4>Otros servicios de <?= esc($servicio['nombre_publico']) ?></h4>
                    <?php foreach ($otros as $o): ?>
                        <a href="<?= base_url('servicios/p/' . (int)$o['id']) ?>" class="srv-otro-link">
                            <?= esc($o['oficio']) ?> <span><?= esc($o['zona'] ?: $o['ciudad']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card srv-contacto">
            <h3>Solicitar contacto</h3>
            <p class="srv-contacto-nota">
                <?= esc($servicio['nombre_publico']) ?> vera tu telefono <strong>solo si acepta</strong> tu solicitud.
                CONEX no muestra datos del prestador.
            </p>
            <form action="<?= base_url('servicios/' . (int)$servicio['id'] . '/contacto') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">Tu nombre</label>
                    <input type="text" name="nombre" class="form-control" maxlength="120" required
                           value="<?= esc(session()->get('nombre') ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Tu telefono / WhatsApp</label>
                    <input type="tel" name="telefono" class="form-control" maxlength="30" required placeholder="8888-8888">
                </div>
                <div class="form-group">
                    <label class="form-label">Que necesitas?</label>
                    <textarea name="mensaje" class="form-control" rows="3" maxlength="500"
                              placeholder="Ej: necesito revisar el breaker principal, vivo en..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Enviar solicitud</button>
            </form>

            <div class="srv-report">
                <form action="<?= base_url('servicios/' . (int)$servicio['id'] . '/reporte') ?>" method="post"
                      onsubmit="return confirm('Reportar este perfil?');">
                    <?= csrf_field() ?>
                    <select name="motivo" class="form-control srv-report-motivo">
                        <option value="datos_falsos">Datos falsos</option>
                        <option value="fraude">Posible fraude</option>
                        <option value="comportamiento">Comportamiento inapropiado</option>
                        <option value="spam">Spam</option>
                        <option value="otro">Otro</option>
                    </select>
                    <input type="hidden" name="detalle" value="">
                    <button type="submit" class="srv-report-btn">Reportar perfil</button>
                </form>
            </div>
        </div>
    </div>
</div>
