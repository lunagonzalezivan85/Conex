<section class="page-hero">
    <div class="page-hero-inner">
        <span class="page-hero-eyebrow">Precios</span>
        <h1>Nuestros planes</h1>
        <p>Elige el plan que mejor se adapte a tus necesidades</p>
    </div>
</div>

<div class="container planes-page">
    <div class="planes-switcher">
        <button class="planes-switch-btn active" data-plan-type="postulante">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Postulante</span>
        </button>
        <button class="planes-switch-btn" data-plan-type="empresarial">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            <span>Empresarial</span>
        </button>
    </div>

    <!-- Planes Postulante -->
    <div class="planes-grid planes-postulante active reveal">
        <div class="plan-card">
            <div class="plan-card-header">
                <h3>Free</h3>
                <p class="plan-price">$0<span>/mes</span></p>
                <p class="plan-desc">Empieza tu busqueda de empleo gratis</p>
            </div>
            <ul class="plan-features">
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Buscar y postularse a vacantes ilimitadas</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Crear perfil profesional</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Subir 1 CV</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Recibir notificaciones de nuevas vacantes</li>
                <li class="plan-disabled"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span> Postularse a vacantes premium</li>
                <li class="plan-disabled"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span> Destacar perfil ante empresas</li>
                <li class="plan-disabled"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span> Asesoria de CV personalizada</li>
            </ul>
            <a href="<?= base_url('registro') ?>" class="btn btn-outline btn-block plan-btn">Registrarse gratis</a>
        </div>

        <div class="plan-card plan-card-popular">
            <div class="plan-badge-popular">Mas popular</div>
            <div class="plan-card-header">
                <h3>Plus</h3>
                <p class="plan-price">$5<span>/mes</span></p>
                <p class="plan-desc">Potencia tu busqueda de empleo</p>
            </div>
            <ul class="plan-features">
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Todo lo del plan Free</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Subir hasta 5 CVs</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Postularse a vacantes premium</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Destacar perfil ante empresas</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Estadisticas de postulaciones</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Notificaciones prioritarias</li>
                <li class="plan-disabled"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span> Asesoria de CV personalizada</li>
            </ul>
            <a href="<?= base_url('registro') ?>" class="btn btn-primary btn-block plan-btn">Elegir Plus</a>
        </div>

        <div class="plan-card">
            <div class="plan-card-header">
                <h3>Premium</h3>
                <p class="plan-price">$12<span>/mes</span></p>
                <p class="plan-desc">Maximiza tus oportunidades</p>
            </div>
            <ul class="plan-features">
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Todo lo del plan Plus</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> CVs ilimitados</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Asesoria de CV personalizada</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Simulacion de entrevistas</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Acceso a cursos de capacitacion</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Soporte prioritario 24/7</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Perfil verificado con insignia</li>
            </ul>
            <a href="<?= base_url('registro') ?>" class="btn btn-outline btn-block plan-btn">Elegir Premium</a>
        </div>
    </div>

    <!-- Planes Empresarial -->
    <div class="planes-grid planes-empresarial">
        <div class="plan-card">
            <div class="plan-card-header">
                <h3>Freemium</h3>
                <p class="plan-price">$0<span>/mes</span></p>
                <p class="plan-desc">Empieza a publicar vacantes gratis</p>
            </div>
            <ul class="plan-features">
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Publicar hasta 3 vacantes activas</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Ver postulantes recibidos</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Perfil de empresa basico</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Filtros basicos de postulantes</li>
                <li class="plan-disabled"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span> Destacar vacantes</li>
                <li class="plan-disabled"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span> Proceso de verificacion</li>
                <li class="plan-disabled"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span> Reportes avanzados</li>
            </ul>
            <a href="<?= base_url('registro') ?>" class="btn btn-outline btn-block plan-btn">Registrarse gratis</a>
        </div>

        <div class="plan-card plan-card-popular">
            <div class="plan-badge-popular">Mas popular</div>
            <div class="plan-card-header">
                <h3>Business</h3>
                <p class="plan-price">$29<span>/mes</span></p>
                <p class="plan-desc">Gestiona tu reclutamiento profesional</p>
            </div>
            <ul class="plan-features">
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Todo lo del plan Freemium</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Vacantes activas ilimitadas</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Destacar vacantes en busquedas</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Proceso de verificacion completo</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Filtros avanzados de postulantes</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Reportes de postulaciones</li>
                <li class="plan-disabled"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span> Soporte prioritario 24/7</li>
            </ul>
            <a href="<?= base_url('registro') ?>" class="btn btn-primary btn-block plan-btn">Elegir Business</a>
        </div>

        <div class="plan-card">
            <div class="plan-card-header">
                <h3>Enterprise</h3>
                <p class="plan-price">$99<span>/mes</span></p>
                <p class="plan-desc">Solucion completa para empresas</p>
            </div>
            <ul class="plan-features">
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Todo lo del plan Business</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Multi-usuario (hasta 10 reclutadores)</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Soporte prioritario 24/7</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> API de integracion</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Reportes avanzados y analiticas</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Marca personalizada en vacantes</li>
                <li><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Manager de cuenta dedicado</li>
            </ul>
            <a href="<?= base_url('registro') ?>" class="btn btn-outline btn-block plan-btn">Elegir Enterprise</a>
        </div>
    </div>

    <div class="planes-faq">
        <h2>Preguntas frecuentes</h2>
        <div class="faq-item">
            <div class="faq-q">Puedo cambiar de plan en cualquier momento?</div>
            <div class="faq-a">Si, puedes actualizar o cancelar tu plan cuando quieras. Los cambios se aplican inmediatamente.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q">Hay algun costo oculto?</div>
            <div class="faq-a">No. El precio que ves es el precio que pagas. Sin comisiones adicionales ni cargos ocultos.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q">Que metodos de pago aceptan?</div>
            <div class="faq-a">Aceptamos tarjetas de credito, debito y transferencias bancarias.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q">Puedo probar un plan pago antes de pagar?</div>
            <div class="faq-a">Si, ofrecemos 14 dias de prueba gratuita en todos los planes de pago sin necesidad de tarjeta.</div>
        </div>
    </div>
</div>
