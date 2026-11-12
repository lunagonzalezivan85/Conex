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
            <h1 class="fw-bold fs-3" style="color: #1B2A4A;">Solicitud de Eliminación de Cuenta</h1>
            <p class="text-muted small">CONEX — Uniendo a los trabajadores</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success border-0 rounded-3">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-0 rounded-3">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning border-0 rounded-3 small mb-4" style="background: rgba(249, 115, 22, 0.12); color: #c2410c;">
            <strong>Importante:</strong> Al eliminar tu cuenta se borrarán permanentemente tu perfil profesional, fotografías, currículums subidos, solicitudes y fichas de servicios. Esta acción no se puede deshacer.
        </div>

        <form action="<?= base_url('eliminar-cuenta') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="identificador" class="form-label fw-semibold" style="color: #1B2A4A;">Correo electrónico o Teléfono</label>
                <input type="text" class="form-control form-control-lg rounded-3" id="identificador" name="identificador" placeholder="ejemplo@correo.com o número de teléfono" required>
            </div>

            <div class="mb-4">
                <label for="motivo" class="form-label fw-semibold" style="color: #1B2A4A;">Motivo de la baja (opcional)</label>
                <textarea class="form-control rounded-3" id="motivo" name="motivo" rows="3" placeholder="Cuéntanos brevemente por qué deseas eliminar tu cuenta..."></textarea>
            </div>

            <button type="submit" class="btn btn-danger btn-lg w-100 rounded-3 fw-bold" onclick="return confirm('¿Estás seguro de que deseas solicitar la eliminación definitiva de tu cuenta y datos asociados?');">
                Solicitar eliminación definitiva
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="<?= base_url() ?>" class="text-decoration-none small text-muted">← Volver al inicio de CONEX</a>
        </div>
    </div>
</div>

