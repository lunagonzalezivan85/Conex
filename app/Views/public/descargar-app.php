<!-- ========================================================
     PÁGINA DEDICADA DE DESCARGA DE LA APP CONEX
     ======================================================== -->
<div style="background: var(--bg-primary); padding: 40px 0 10px 0;">
    <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
        <nav style="display: flex; gap: 8px; font-size: 13px; color: #64748B; margin-bottom: 20px;">
            <a href="<?= base_url() ?>" style="color: #64748B; text-decoration: none;">Inicio</a>
            <span>/</span>
            <span style="color: var(--brand-navy); font-weight: 600;">Descargar App Android</span>
        </nav>
    </div>
</div>

<!-- Sección Play Store -->
<?= view('public/seccion-playstore') ?>

<!-- Guía de Instalación del APK -->
<section style="padding: 60px 20px; background: #FFFFFF; border-top: 1px solid var(--border);">
    <div class="container" style="max-width: 900px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 40px;">
            <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--brand-teal);">Instalación Rápida</span>
            <h3 style="font-size: 28px; font-weight: 800; color: var(--brand-navy); margin-top: 6px;">¿Cómo instalar el archivo APK en tu teléfono Android?</h3>
            <p style="color: #64748B; font-size: 15px; max-width: 600px; margin: 8px auto 0 auto;">Si descargas el APK directamente sin pasar por Play Store, sigue estos 3 sencillos pasos:</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
            <div style="background: #F8FAFD; border: 1px solid #E2E8F0; border-radius: 18px; padding: 26px; text-align: center;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: #E8EEF5; color: var(--brand-navy); font-size: 20px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">1</div>
                <h4 style="font-size: 16px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Descarga el archivo</h4>
                <p style="font-size: 13px; color: #64748B; line-height: 1.5; margin: 0;">Presiona el botón <strong style="color: var(--brand-navy);">Descargar APK Directo</strong> o escanea el código QR con la cámara de tu celular.</p>
            </div>

            <div style="background: #F8FAFD; border: 1px solid #E2E8F0; border-radius: 18px; padding: 26px; text-align: center;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: #FFF3E6; color: var(--brand-orange); font-size: 20px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">2</div>
                <h4 style="font-size: 16px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Permite la instalación</h4>
                <p style="font-size: 13px; color: #64748B; line-height: 1.5; margin: 0;">Cuando Android te pregunte, toca en <strong style="color: var(--brand-orange);">Permitir fuentes desconocidas</strong> para tu navegador Chrome.</p>
            </div>

            <div style="background: #F8FAFD; border: 1px solid #E2E8F0; border-radius: 18px; padding: 26px; text-align: center;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: #E0F5F6; color: var(--brand-teal); font-size: 20px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">3</div>
                <h4 style="font-size: 16px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Abre CONEX</h4>
                <p style="font-size: 13px; color: #64748B; line-height: 1.5; margin: 0;">Toca en <strong style="color: var(--brand-teal);">Instalar</strong> y luego en <strong style="color: var(--brand-teal);">Abrir</strong> para iniciar sesión o registrarte gratis.</p>
            </div>
        </div>

        <div style="margin-top: 40px; text-align: center;">
            <a href="<?= base_url('descargar-apk') ?>" class="btn btn-primary" style="padding: 14px 32px; font-size: 15px; font-weight: 700; border-radius: 12px;">
                Descargar Instalador APK Ahora (35.4 MB)
            </a>
        </div>
    </div>
</section>
