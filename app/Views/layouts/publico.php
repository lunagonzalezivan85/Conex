<!DOCTYPE html>
<html lang="es-NI">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title><?= isset($title) ? $title . ' - CONEX' : 'CONEX - Conectando talentos con oportunidades' ?></title>
    <meta name="description" content="<?= isset($meta_description) ? esc($meta_description) : 'CONEX es la plataforma de reclutamiento de personal en Nicaragua. Busca vacantes, postulate gratis y conecta con las mejores empresas. Encuentra tu proximo empleo hoy.' ?>">
    <meta name="keywords" content="<?= isset($meta_keywords) ? esc($meta_keywords) : 'empleos Nicaragua, buscar empleo, vacantes, reclutamiento, ofertas de trabajo, portal de empleo, CONEX, trabajo Managua, empleo Nicaragua' ?>">
    <meta name="author" content="CONEX">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Spanish">
    <meta name="revisit-after" content="7 days">
    <meta name="rating" content="general">
    <meta name="distribution" content="global">
    <meta name="geo.region" content="NI-MN">
    <meta name="geo.placename" content="Managua, Nicaragua">
    <meta name="geo.position" content="12.1325;-86.2504">
    <meta name="ICBM" content="12.1325, -86.2504">

    <!-- Canonical -->
    <link rel="canonical" href="<?= isset($canonical_url) ? esc($canonical_url) : current_url() ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="CONEX">
    <meta property="og:title" content="<?= isset($title) ? esc($title) . ' - CONEX' : 'CONEX - Conectando talentos con oportunidades' ?>">
    <meta property="og:description" content="<?= isset($meta_description) ? esc($meta_description) : 'Plataforma de reclutamiento de personal en Nicaragua. Busca vacantes y postulate gratis.' ?>">
    <meta property="og:url" content="<?= isset($canonical_url) ? esc($canonical_url) : current_url() ?>">
    <meta property="og:image" content="<?= base_url('img/conex-og.jpg') ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="es_NI">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= isset($title) ? esc($title) . ' - CONEX' : 'CONEX - Conectando talentos con oportunidades' ?>">
    <meta name="twitter:description" content="<?= isset($meta_description) ? esc($meta_description) : 'Plataforma de reclutamiento de personal en Nicaragua. Busca vacantes y postulate gratis.' ?>">
    <meta name="twitter:image" content="<?= base_url('img/conex-og.jpg') ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('img/apple-touch-icon.png') ?>">

    <!-- Theme Color -->
    <meta name="theme-color" content="#123D73">

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('error')): ?>
    <meta name="flash-error" content="<?= esc(session()->getFlashdata('error')) ?>">
    <?php endif; ?>
    <?php if (session()->getFlashdata('info')): ?>
    <meta name="flash-info" content="<?= esc(session()->getFlashdata('info')) ?>">
    <?php endif; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= base_url('css/core/publico.css') ?>?v=<?= filemtime(FCPATH . 'css/core/publico.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/core/sweetalert2.min.css') ?>?v=<?= filemtime(FCPATH . 'css/core/sweetalert2.min.css') ?>">
    <?php if (isset($css)): ?>
        <?php foreach ($css as $file): ?>
    <link rel="stylesheet" href="<?= base_url('css/core/' . $file) ?>?v=<?= filemtime(FCPATH . 'css/core/' . $file) ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Structured Data: Organization -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "CONEX",
        "description": "CONEX - Conectando talentos con oportunidades. Plataforma de reclutamiento de personal en Nicaragua.",
        "url": "<?= base_url() ?>",
        "logo": "<?= base_url('img/conex-logo.png') ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Residencial Cielos Dorados km 17 Carretera a Masaya",
            "addressLocality": "Managua",
            "addressRegion": "Managua",
            "addressCountry": "NI"
        },
        "areaServed": "Nicaragua",
        "sameAs": []
    }
    </script>

    <!-- Structured Data: WebSite with SearchAction -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "CONEX",
        "url": "<?= base_url() ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "<?= base_url('buscar-empleo') ?>?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>
</head>
<body>
    <header class="header">
        <a href="<?= base_url() ?>" class="header-logo">CON<span class="logo-e">E</span><span class="logo-x">X</span></a>
        <nav class="header-nav">
            <div class="header-links">
                <a href="<?= base_url() ?>">Inicio</a>
                <a href="<?= base_url('noticias') ?>">Noticias</a>
                <a href="<?= base_url('nosotros') ?>">Nosotros</a>
                <a href="<?= base_url('planes') ?>">Planes</a>
                <a href="<?= base_url('buscar-empleo') ?>">Empleos</a>
            </div>
            <div class="header-actions">
                <a href="<?= base_url('login') ?>" class="btn btn-ghost">Iniciar Sesion</a>
                <a href="<?= base_url('registro') ?>" class="btn btn-primary">Registrarse</a>
            </div>
        </nav>
    </header>

    <main class="main">
        <?= $content ?>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h3>CON<span class="logo-e">E</span><span class="logo-x">X</span></h3>
                <p>Conectando talentos con oportunidades. Plataforma de reclutamiento de personal en Nicaragua.</p>
                <p class="footer-address">Residencial Cielos Dorados km 17 Carretera a Masaya, Managua, Nicaragua</p>
            </div>
            <div class="footer-col">
                <h4>Empresa</h4>
                <a href="#">Sobre nosotros</a>
                <a href="#">Contacto</a>
                <a href="#">Terminos y condiciones</a>
            </div>
            <div class="footer-col">
                <h4>Candidatos</h4>
                <a href="<?= base_url('buscar-empleo') ?>">Buscar empleos</a>
                <a href="<?= base_url('registro') ?>">Crear cuenta</a>
                <a href="#">Consejos de CV</a>
            </div>
            <div class="footer-col">
                <h4>Empresas</h4>
                <a href="<?= base_url('registro') ?>">Publicar vacante</a>
                <a href="#">Planes y precios</a>
                <a href="#">Soporte</a>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 CONEX. Todos los derechos reservados.
        </div>
    </footer>

    <script src="<?= base_url('js/core/sweetalert2.min.js') ?>?v=<?= filemtime(FCPATH . 'js/core/sweetalert2.min.js') ?>"></script>
    <script src="<?= base_url('js/core/app.js') ?>?v=<?= filemtime(FCPATH . 'js/core/app.js') ?>"></script>
    <?php if (isset($js)): ?>
        <?php foreach ($js as $file): ?>
    <script src="<?= base_url('js/core/' . $file) ?>?v=<?= filemtime(FCPATH . 'js/core/' . $file) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
