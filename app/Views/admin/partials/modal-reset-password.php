<!-- Modal Restablecer Contraseña Admin -->
<div class="user-modal-overlay" id="resetPasswordModal" aria-hidden="true" role="dialog" style="z-index: 99999; display: none;">
    <div class="user-modal-dialog" style="max-width: 480px; width: 90%;">
        <!-- Header -->
        <div class="user-modal-header" style="background: linear-gradient(135deg, #1E293B, #0F172A); color: #fff; padding: 18px 20px; border-radius: 16px 16px 0 0; position: relative;">
            <button type="button" class="user-modal-close" id="resetModalCloseBtn" aria-label="Cerrar modal" style="position: absolute; right: 16px; top: 16px; background: rgba(255,255,255,0.1); border: none; color: #fff; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(245,158,11,0.2); display: flex; align-items: center; justify-content: center; color: #F59E0B; flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; color: #fff; font-weight: 700;">Restablecer Contraseña</h3>
                    <span style="font-size: 12px; color: #94A3B8;" id="resetModalSubtitle">Usuario: @usuario</span>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="user-modal-body" style="padding: 22px; background: #fff; border-radius: 0 0 16px 16px;">
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 12px; margin-bottom: 16px; font-size: 12.5px; color: #475569; line-height: 1.5;">
                <strong style="color: #1E293B; display: block; margin-bottom: 2px;">🔑 Restablecimiento Directo por Administrador</strong>
                Asigna una nueva contraseña o genera una clave temporal para el usuario. Podrás copiarla de inmediato para compartírsela por WhatsApp, SMS o llamada.
            </div>

            <form id="adminResetPasswordForm" onsubmit="return false;">
                <input type="hidden" id="resetUserId" value="">

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Nueva Contraseña *
                    </label>
                    <div style="display: flex; gap: 8px;">
                        <input
                            type="text"
                            id="resetNewPasswordInput"
                            class="form-control"
                            placeholder="Escribe contraseña o genera una..."
                            style="flex: 1; font-family: 'Consolas', monospace; font-size: 14px; padding: 10px 14px; border: 1.5px solid #CBD5E1; border-radius: 8px; outline: none;"
                        >
                        <button
                            type="button"
                            class="btn btn-outline btn-sm"
                            id="btnGenerarRandomPass"
                            style="white-space: nowrap; padding: 0 14px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;"
                        >
                            ⚡ Generar
                        </button>
                    </div>
                    <small style="color: #64748B; font-size: 11.5px; display: block; margin-top: 5px;">
                        Mínimo 4 caracteres (o PIN numérico). Puedes escribir una clave manual o hacer clic en "⚡ Generar".
                    </small>
                </div>

                <!-- Error feedback -->
                <div id="resetFeedbackError" style="display: none; background: #FEF2F2; border: 1px solid #FCA5A5; color: #DC2626; padding: 10px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 14px;">
                </div>

                <!-- Success feedback card -->
                <div id="resetSuccessBox" style="display: none; background: #ECFDF5; border: 1.5px solid #6EE7B7; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 6px; color: #065F46; font-weight: 700; font-size: 13px; margin-bottom: 8px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#059669" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>¡Contraseña actualizada con éxito!</span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; background: #fff; padding: 10px 14px; border-radius: 8px; border: 1px dashed #10B981; margin-bottom: 8px;">
                        <span id="resetSuccessPassText" style="font-family: 'Consolas', monospace; font-size: 16px; font-weight: 800; color: #0F172A; letter-spacing: 0.05em;"></span>
                        <button type="button" class="btn btn-sm btn-outline" id="btnCopiarPass" style="font-size: 12px; color: #059669; border-color: #6EE7B7; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                            📋 Copiar
                        </button>
                    </div>
                    <small style="color: #047857; font-size: 11.5px; display: block; line-height: 1.4;">
                        Los accesos previos del usuario han sido revocados para seguridad. Comparte esta nueva clave al usuario.
                    </small>
                </div>

                <!-- Footer buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; border-top: 1px solid #F1F5F9; padding-top: 14px;">
                    <button type="button" class="btn btn-ghost btn-sm" id="btnResetCancel">
                        Cerrar
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="btnSubmitResetPass" style="background: #F59E0B; border: none; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Guardar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('resetPasswordModal');
    const closeBtn = document.getElementById('resetModalCloseBtn');
    const cancelBtn = document.getElementById('btnResetCancel');
    const generateBtn = document.getElementById('btnGenerarRandomPass');
    const submitBtn = document.getElementById('btnSubmitResetPass');
    const copyBtn = document.getElementById('btnCopiarPass');

    const userIdInput = document.getElementById('resetUserId');
    const passInput = document.getElementById('resetNewPasswordInput');
    const subtitleEl = document.getElementById('resetModalSubtitle');
    const errorBox = document.getElementById('resetFeedbackError');
    const successBox = document.getElementById('resetSuccessBox');
    const successText = document.getElementById('resetSuccessPassText');

    let currentPassGenerated = '';

    function generateSecurePassword() {
        const prefix = 'Conex' + (new Date().getFullYear().toString().slice(-2));
        const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        let randStr = '';
        for (let i = 0; i < 4; i++) {
            randStr += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return prefix + '!' + randStr;
    }

    window.openResetPasswordModal = function(user) {
        if (!modal) return;
        userIdInput.value = user.id || '';
        subtitleEl.textContent = `Usuario: @${user.usuario || 'usuario'} (${user.nombre || 'Sin nombre'})`;
        passInput.value = generateSecurePassword();
        errorBox.style.display = 'none';
        errorBox.textContent = '';
        successBox.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Guardar Contraseña
        `;

        modal.style.display = 'flex';
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        passInput.focus();
    };

    function closeResetModal() {
        if (!modal) return;
        modal.style.display = 'none';
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
    }

    closeBtn?.addEventListener('click', closeResetModal);
    cancelBtn?.addEventListener('click', closeResetModal);
    modal?.addEventListener('click', function(e) {
        if (e.target === modal) closeResetModal();
    });

    generateBtn?.addEventListener('click', function() {
        passInput.value = generateSecurePassword();
        passInput.focus();
    });

    copyBtn?.addEventListener('click', async function() {
        const textToCopy = successText.textContent || passInput.value;
        if (!textToCopy) return;

        try {
            await navigator.clipboard.writeText(textToCopy);
            copyBtn.innerHTML = '✓ ¡Copiada!';
            copyBtn.style.color = '#059669';
            setTimeout(() => {
                copyBtn.innerHTML = '📋 Copiar';
            }, 2500);
        } catch {
            // Fallback con execCommand
            const temp = document.createElement('textarea');
            temp.value = textToCopy;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            copyBtn.innerHTML = '✓ ¡Copiada!';
            setTimeout(() => {
                copyBtn.innerHTML = '📋 Copiar';
            }, 2500);
        }
    });

    submitBtn?.addEventListener('click', async function() {
        const userId = userIdInput.value;
        const newPassword = passInput.value.trim();

        if (!userId) {
            errorBox.textContent = 'ID de usuario no especificado.';
            errorBox.style.display = 'block';
            return;
        }

        if (newPassword && newPassword.length < 4) {
            errorBox.textContent = 'La contraseña debe contener al menos 4 caracteres.';
            errorBox.style.display = 'block';
            return;
        }

        errorBox.style.display = 'none';
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Guardando...';

        try {
            const formData = new FormData();
            formData.append('new_password', newPassword);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            const resp = await fetch(`<?= base_url('admin/usuarios/reset-password/') ?>${userId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await resp.json();

            if (!resp.ok || !data.success) {
                throw new Error(data.error || 'No se pudo restablecer la contraseña.');
            }

            // Exito
            currentPassGenerated = data.new_password;
            successText.textContent = data.new_password;
            successBox.style.display = 'block';
            submitBtn.innerHTML = '✓ Contraseña Guardada';
            submitBtn.disabled = false;

            // Trigger custom event so parent page can update if needed
            window.dispatchEvent(new CustomEvent('user-password-reset', { detail: data }));

        } catch (err) {
            errorBox.textContent = err.message || 'Error de conexión al restablecer contraseña.';
            errorBox.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Reintentar';
        }
    });
})();
</script>
