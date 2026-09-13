<div class="auth-wrapper">
    <video class="auth-video" autoplay muted loop playsinline>
        <source src="<?= base_url('video/v-01.mp4') ?>" type="video/mp4">
    </video>
    <div class="auth-overlay"></div>
    <div class="auth-card registro-wrapper">
        <div class="auth-title">
            <h1>Crear cuenta</h1>
            <p>Registrate gratis y empieza hoy</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <div><?= esc($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="toggle-group" id="tipoCuentaToggle">
                <button type="button" class="toggle-btn active" data-tipo="candidato">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Candidato
                </button>
                <button type="button" class="toggle-btn" data-tipo="empresa">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>
                    Empresa
                </button>
            </div>

            <div class="wizard-steps">
                <div class="wizard-step-indicator active" data-step="1">
                    <span class="wizard-step-num">1</span>
                    <span class="wizard-step-label">Datos personales</span>
                </div>
                <div class="wizard-step-line"></div>
                <div class="wizard-step-indicator" data-step="2">
                    <span class="wizard-step-num">2</span>
                    <span class="wizard-step-label">Datos de cuenta</span>
                </div>
            </div>

            <form action="<?= base_url('registro') ?>" method="post" id="registroForm">
                <?= csrf_field() ?>
                <input type="hidden" name="tipo_cuenta" id="tipo_cuenta" value="candidato">

                <!-- Paso 1: Datos personales -->
                <div class="wizard-panel active" data-panel="1">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="<?= old('nombre') ?>" placeholder="Tu nombre" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Apellido</label>
                            <input type="text" name="apellido" class="form-control" value="<?= old('apellido') ?>" placeholder="Tu apellido" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Correo electronico</label>
                            <input type="email" name="email" class="form-control" value="<?= old('email') ?>" placeholder="email@ejemplo.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Numero telefonico</label>
                            <input type="tel" name="telefono" class="form-control" value="<?= old('telefono') ?>" placeholder="+505 8888 8888" required>
                        </div>
                    </div>
                    <div class="form-group hidden" id="razonSocialGroup">
                        <label class="form-label">Razon social</label>
                        <input type="text" name="razon_social" class="form-control" value="<?= old('razon_social') ?>" placeholder="Nombre de la empresa">
                    </div>
                    <button type="button" class="btn btn-primary btn-block btn-lg wizard-next" data-next="2">Continuar</button>
                </div>

                <!-- Paso 2: Datos de cuenta -->
                <div class="wizard-panel" data-panel="2">
                    <div class="form-group">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="usuario" class="form-control" value="<?= old('usuario') ?>" placeholder="nombre_usuario" required>
                        <p class="form-text">Este sera tu nombre de usuario unico en CONEX.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contrasena</label>
                        <div class="password-field">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Minimo 10 caracteres" required>
                            <button type="button" class="password-toggle" aria-label="Mostrar/ocultar contrasena">
                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        <div class="password-suggest">
                            <button type="button" class="password-suggest-btn" id="suggestPasswordBtn">Sugerir contrasena</button>
                            <div class="password-suggest-value hidden" id="suggestPasswordValueWrapper">
                                <span id="suggestPasswordValue"></span>
                            </div>
                        </div>
                        <div class="password-strength" id="passwordStrength">
                            <div class="password-strength-bar">
                                <div class="password-strength-fill" id="strengthFill"></div>
                            </div>
                            <span class="password-strength-label" id="strengthLabel"></span>
                            <ul class="password-requirements">
                                <li class="unmet" data-req="length"><span class="check-icon"></span> Minimo 10 caracteres</li>
                                <li class="unmet" data-req="upper"><span class="check-icon"></span> Una mayuscula</li>
                                <li class="unmet" data-req="lower"><span class="check-icon"></span> Una minuscula</li>
                                <li class="unmet" data-req="number"><span class="check-icon"></span> Un numero</li>
                                <li class="unmet" data-req="special"><span class="check-icon"></span> Un caracter especial</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmar contrasena</label>
                        <div class="password-field">
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Repite tu contrasena" required>
                            <button type="button" class="password-toggle" aria-label="Mostrar/ocultar contrasena">
                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="terms-margin">
                        <label class="terms-check">
                            <input type="checkbox" required>
                            <span>Acepto los <a href="#">Terminos y Condiciones</a> y la <a href="#">Politica de Privacidad</a></span>
                        </label>
                    </div>

                    <div class="wizard-actions">
                        <button type="button" class="btn btn-outline wizard-prev" data-prev="1">Volver</button>
                        <button type="submit" class="btn btn-primary btn-lg">Crear cuenta</button>
                    </div>
                </div>
            </form>

            <div class="divider">
                <hr>
                <span>o registrate con</span>
            </div>

            <a href="<?= base_url('auth/google') ?>" class="btn btn-outline btn-block btn-google">
                <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Registrarse con Google
            </a>
        </div>

        <p class="auth-footer">Ya tienes cuenta? <a href="<?= base_url('login') ?>">Inicia sesion</a></p>
    </div>
</div>
