<div class="container py-5" style="max-width: 680px;">
    <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="mb-3">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background: rgba(239, 68, 68, 0.12); color: #EF4444;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </span>
            </div>
            <h1 class="fw-bold fs-3" style="color: #1B2A4A;">Eliminación Definitiva de Cuenta</h1>
            <p class="text-muted small">CONEX — Cumplimiento de la Política de Privacidad (Cláusula 6)</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success border-0 rounded-3 p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <strong>Baja procesada</strong>
                </div>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-0 rounded-3 p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <strong>No se pudo procesar</strong>
                </div>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning border-0 rounded-3 small mb-4" style="background: rgba(249, 115, 22, 0.12); color: #c2410c;">
            <strong>Consecuencias de la eliminación:</strong> Al confirmar esta acción, tu cuenta, tus currículums vitae en PDF/Word, tus fotografías de perfil y tus fichas de servicio serán eliminados de forma permanente de nuestra base de datos activa. No podrás recuperar tus datos ni postulaciones.
        </div>

        <form action="<?= base_url('eliminar-cuenta') ?>" method="POST">
            <?= csrf_field() ?>

            <?php
                $loggedEmail = session()->get('email') ?? '';
            ?>

            <div class="mb-3">
                <label for="identificador" class="form-label fw-semibold" style="color: #1B2A4A;">Correo electrónico o Teléfono registrado</label>
                <input type="text" class="form-control form-control-lg rounded-3" id="identificador" name="identificador"
                       value="<?= esc(old('identificador', $loggedEmail)) ?>"
                       placeholder="ejemplo@correo.com o número de teléfono" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold" style="color: #1B2A4A;">Contraseña de tu cuenta</label>
                <input type="password" class="form-control form-control-lg rounded-3" id="password" name="password"
                       placeholder="Ingresa tu contraseña actual para confirmar tu identidad" required>
                <div class="form-text small text-muted">Por seguridad y para proteger tu privacidad, requerimos tu contraseña para validar que eres el titular de la cuenta.</div>
            </div>

            <div class="mb-3">
                <label for="motivo" class="form-label fw-semibold" style="color: #1B2A4A;">Motivo de la baja (opcional)</label>
                <textarea class="form-control rounded-3" id="motivo" name="motivo" rows="2" placeholder="Cuéntanos brevemente la razón de tu baja..."><?= esc(old('motivo')) ?></textarea>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="confirmacion_terminos" name="confirmacion_terminos" value="1" required>
                <label class="form-check-label small text-muted" for="confirmacion_terminos">
                    Confirmo que deseo dar de baja definitiva mi cuenta y autorizo la purga permanente de mis currículums, fotografías y fichas de servicios.
                </label>
            </div>

            <button type="submit" class="btn btn-danger btn-lg w-100 rounded-3 fw-bold" onclick="return confirm('¿Estás totalmente seguro de que deseas eliminar permanentemente tu cuenta y todos tus datos? Esta acción es irreversible.');">
                Confirmar y Eliminar Definitivamente
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="<?= base_url() ?>" class="text-decoration-none small text-muted">← Volver al inicio de CONEX</a>
        </div>
    </div>
</div>
