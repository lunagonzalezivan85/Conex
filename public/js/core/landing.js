/* Landing - explorer pills (reveal/counters/sticky ya estan en app.js global) */
(function () {
    'use strict';

    var tabs = document.querySelectorAll('.explore-tab');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var target = tab.dataset.tab;
            tabs.forEach(function (t) {
                var on = t === tab;
                t.classList.toggle('active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            document.querySelectorAll('.explore-panel').forEach(function (panel) {
                var on = panel.id === 'panel-' + target;
                panel.classList.toggle('active', on);
                if (on) { panel.removeAttribute('hidden'); } else { panel.setAttribute('hidden', ''); }
            });
        });
    });

    // Stagger en grids del landing
    var cardsIO = 'IntersectionObserver' in window ? new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.querySelectorAll('.categoria-card, .srv-mini-card, .vacante-card').forEach(function (el) {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                });
                cardsIO.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 }) : null;

    if (cardsIO) {
        document.querySelectorAll('.reveal .categoria-card, .reveal .srv-mini-card, .reveal .vacante-card').forEach(function (el, i) {
            el.style.transitionDelay = ((i % 8) * 60) + 'ms';
            el.style.opacity = '0';
            el.style.transform = 'translateY(18px)';
            el.style.transition = 'opacity .5s ease, transform .5s ease';
        });
        document.querySelectorAll('.categoria-grid, .srv-grid, .vacante-grid').forEach(function (grid) {
            cardsIO.observe(grid);
        });
    }

    // Fallback de seguridad: asegurar visibilidad total de vacantes y tarjetas
    setTimeout(function () {
        document.querySelectorAll('.categoria-card, .srv-mini-card, .vacante-card').forEach(function (el) {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    }, 800);
})();