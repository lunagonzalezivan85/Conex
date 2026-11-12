<!-- ========================================================
     HERO DEDICADO DE LA APP CONEX
     ======================================================== -->
<section class="cx-app-hero">
    <div class="cx-app-hero-inner">
        <span class="cx-app-hero-eyebrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            Aplicación Oficial para Android
        </span>
        <h1>
            Encuentra trabajo y servicios<br>
            <span class="highlight">en la palma de tu mano</span>
        </h1>
        <p class="lead">
            Postúlate a cientos de vacantes en Nicaragua, contacta técnicos y clientes cerca de ti y gestiona tu perfil profesional desde cualquier lugar con la app oficial de CONEX.
        </p>

        <div class="cx-app-hero-actions">
            <a href="https://play.google.com/store/apps/details?id=com.softlutionic.conex" target="_blank" rel="noopener" class="cx-btn-googleplay" style="max-width: 250px;">
                <svg class="cx-btn-gp-icon" viewBox="0 0 512 512">
                    <path fill="#4285F4" d="M32.5 13.5c-4.2 4.5-6.5 11.2-6.5 19.5v446c0 8.3 2.3 15 6.5 19.5l239.5-242.5L32.5 13.5z"/>
                    <path fill="#34A853" d="M352.5 315.5l-80.5-80.5-239.5 242.5c5.3 2.8 11.6 4.5 18.5 4.5 9.8 0 20.1-4.2 30-10.5l271.5-156z"/>
                    <path fill="#FBBC04" d="M352.5 196.5L81 40.5C71.1 34.2 60.8 30 51 30c-6.9 0-13.2 1.7-18.5 4.5L272 277l80.5-80.5z"/>
                    <path fill="#EA4335" d="M470.5 237.5l-118-68.5-80.5 80.5 80.5 80.5 118-68.5c16.5-9.5 25.5-22.5 25.5-37s-9-27.5-25.5-37z"/>
                </svg>
                <div class="cx-btn-gp-text">
                    <span class="cx-gp-small">DISPONIBLE EN</span>
                    <span class="cx-gp-big">Google Play</span>
                </div>
            </a>

            <a href="<?= base_url('descargar-apk') ?>" class="cx-btn-apk" style="max-width: 250px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <div>
                    <span>Descargar APK</span>
                    <span class="cx-btn-apk-sub">v1.0.0 • 35.4 MB</span>
                </div>
            </a>
        </div>

        <div class="cx-app-hero-stats">
            <div class="cx-app-hero-stat-item">
                <span class="cx-app-hero-stat-num">10,000+</span>
                <span class="cx-app-hero-stat-lbl">Descargas</span>
            </div>
            <div class="cx-app-hero-stat-item">
                <span class="cx-app-hero-stat-num" style="color: #FFB703;">★ 4.9</span>
                <span class="cx-app-hero-stat-lbl">Calificación</span>
            </div>
            <div class="cx-app-hero-stat-item">
                <span class="cx-app-hero-stat-num" style="color: #0FC6C2;">100% Gratis</span>
                <span class="cx-app-hero-stat-lbl">Sin comisiones</span>
            </div>
            <div class="cx-app-hero-stat-item">
                <span class="cx-app-hero-stat-num" style="color: #2ECC71;">Protegida</span>
                <span class="cx-app-hero-stat-lbl">Google Play Protect</span>
            </div>
        </div>
    </div>
</section>

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
