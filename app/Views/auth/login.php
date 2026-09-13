<div class="auth-wrapper">
    <video class="auth-video" autoplay muted loop playsinline>
        <source src="<?= base_url('video/v-01.mp4') ?>" type="video/mp4">
    </video>
    <div class="auth-overlay"></div>
    <div class="auth-card login-wrapper">
        <div class="auth-title">
            <h1>Bienvenido de nuevo</h1>
            <p>Inicia sesion para continuar</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <div><?= esc($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form action="<?= base_url('login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">Usuario o correo electronico</label>
                    <input type="text" name="usuario" class="form-control <?= session()->getFlashdata('errors') ? 'is-invalid' : '' ?>" value="<?= old('usuario') ?>" placeholder="usuario o email@ejemplo.com" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Contrasena</label>
                    <div class="password-field">
                        <input type="password" name="password" class="form-control" placeholder="Tu contrasena" required>
                        <button type="button" class="password-toggle" aria-label="Mostrar/ocultar contrasena">
                            <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>
                <div class="remember-row">
                    <label>
                        <input type="checkbox" name="remember"> Recordarme
                    </label>
                    <a href="#">Olvidaste tu contrasena?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">Iniciar Sesion</button>
            </form>

            <div class="divider">
                <hr>
                <span>o continua con</span>
            </div>

            <a href="<?= base_url('auth/google') ?>" class="btn btn-outline btn-block btn-google">
                <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Iniciar sesion con Google
            </a>
        </div>

        <p class="auth-footer">No tienes cuenta? <a href="<?= base_url('registro') ?>">Registrate gratis</a></p>
    </div>
</div>
