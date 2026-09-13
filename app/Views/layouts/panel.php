<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - CONEX' : 'CONEX Panel' ?></title>
    <meta name="description" content="Panel de gestion CONEX">
    <?php if (session()->getFlashdata('error')): ?>
    <meta name="flash-error" content="<?= esc(session()->getFlashdata('error')) ?>">
    <?php endif; ?>
    <?php if (session()->getFlashdata('info')): ?>
    <meta name="flash-info" content="<?= esc(session()->getFlashdata('info')) ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/core/publico.css') ?>?v=<?= filemtime(FCPATH . 'css/core/publico.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/core/dashboard.css') ?>?v=<?= filemtime(FCPATH . 'css/core/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/core/sweetalert2.min.css') ?>?v=<?= filemtime(FCPATH . 'css/core/sweetalert2.min.css') ?>">
    <?php if (isset($css)): ?>
        <?php foreach ($css as $file): ?>
    <link rel="stylesheet" href="<?= base_url('css/core/' . $file) ?>?v=<?= filemtime(FCPATH . 'css/core/' . $file) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div class="panel">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?= base_url() ?>" class="sidebar-logo">CON<span class="logo-e">E</span><span class="logo-x">X</span></a>
                <button type="button" class="sidebar-toggle" id="sidebarClose" aria-label="Cerrar menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    <?= strtoupper(substr(session()->get('nombre') ?? 'U', 0, 1)) ?>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?= esc(session()->get('nombre') . ' ' . session()->get('apellido')) ?></div>
                    <div class="sidebar-user-role"><?= esc($roleSlug ?? 'usuario') ?></div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php if (isset($sidebarSections) && is_array($sidebarSections)): ?>
                    <?php foreach ($sidebarSections as $section): ?>
                        <div class="sidebar-section">
                            <?php if (!empty($section['title'])): ?>
                                <div class="sidebar-section-title"><?= esc($section['title']) ?></div>
                            <?php endif; ?>
                            <?php foreach ($section['links'] as $link): ?>
                                <a href="<?= base_url($link['url']) ?>" class="sidebar-link <?= ($activeSection ?? '') === ($link['key'] ?? '') ? 'active' : '' ?>">
                                    <?= $link['icon'] ?>
                                    <span><?= esc($link['label']) ?></span>
                                    <?php if (!empty($link['badge'])): ?>
                                        <span class="sidebar-link-badge"><?= esc($link['badge']) ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= base_url('logout') ?>" class="sidebar-link sidebar-link-logout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Cerrar sesion</span>
                </a>
            </div>
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="panel-main">
            <header class="panel-topbar">
                <div class="panel-topbar-left">
                    <button type="button" class="panel-topbar-toggle" id="sidebarOpen" aria-label="Abrir menu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <div class="panel-topbar-title"><?= esc($pageTitle ?? 'Panel') ?></div>
                </div>
                <div class="panel-topbar-right">
                    <a href="<?= base_url() ?>" class="btn btn-ghost">Ver sitio</a>
                </div>
            </header>

            <main class="panel-content">
                <?= $content ?>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('sidebarOpen')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('active');
        });
        document.getElementById('sidebarClose')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        });
        document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            this.classList.remove('active');
        });
    </script>
    <script src="<?= base_url('js/core/sweetalert2.min.js') ?>?v=<?= filemtime(FCPATH . 'js/core/sweetalert2.min.js') ?>"></script>
    <script src="<?= base_url('js/core/app.js') ?>?v=<?= filemtime(FCPATH . 'js/core/app.js') ?>"></script>
    <?php if (isset($js)): ?>
        <?php foreach ($js as $file): ?>
    <script src="<?= base_url('js/core/' . $file) ?>?v=<?= filemtime(FCPATH . 'js/core/' . $file) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
