document.addEventListener('DOMContentLoaded', function() {
    // Navbar scroll effect
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 40) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        });
    }

    const error = document.querySelector('meta[name="flash-error"]')?.content;
    const info = document.querySelector('meta[name="flash-info"]')?.content;

    if (error) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: error,
            confirmButtonColor: '#4361EE',
            confirmButtonText: 'Entendido',
        });
    }
    if (info) {
        Swal.fire({
            icon: 'success',
            title: 'Hecho!',
            text: info,
            confirmButtonColor: '#4361EE',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
        });
    }

    const headerSearch = document.getElementById('headerSearchInput');
    if (headerSearch) {
        headerSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                window.location.href = this.dataset.url;
            }
        });
    }

    // Tab navigation
    const tabBtns = document.querySelectorAll('.tabs .tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const target = this.dataset.tab;
            tabBtns.forEach(function(b) { b.classList.remove('active'); });
            tabPanes.forEach(function(p) { p.classList.remove('active'); });
            this.classList.add('active');
            const pane = document.querySelector('[data-pane="' + target + '"]');
            if (pane) pane.classList.add('active');
        });
    });

    // Modal open/close
    document.querySelectorAll('[data-modal]').forEach(function(card) {
        card.addEventListener('click', function() {
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('active');
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modalId = this.dataset.closeModal;
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.remove('active');
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    // Planes switcher
    const planSwitchBtns = document.querySelectorAll('.planes-switch-btn');
    const planGrids = document.querySelectorAll('.planes-grid');

    planSwitchBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const type = this.dataset.planType;
            planSwitchBtns.forEach(function(b) { b.classList.remove('active'); });
            planGrids.forEach(function(g) { g.classList.remove('active'); });
            this.classList.add('active');
            const grid = document.querySelector('.planes-' + type);
            if (grid) grid.classList.add('active');
        });
    });

    // FAQ accordion
    document.querySelectorAll('.faq-q').forEach(function(q) {
        q.addEventListener('click', function() {
            const item = this.parentElement;
            item.classList.toggle('open');
        });
    });

    // Animate stat counters
    const statCards = document.querySelectorAll('.stat-card[data-target]');
    if (statCards.length > 0) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    const target = parseInt(card.dataset.target);
                    const suffix = card.dataset.suffix || '';
                    const numEl = card.querySelector('.stat-number');
                    let current = 0;
                    const step = Math.ceil(target / 50);
                    const timer = setInterval(function() {
                        current += step;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        numEl.textContent = current.toLocaleString() + suffix;
                    }, 30);
                    observer.unobserve(card);
                }
            });
        }, { threshold: 0.5 });
        statCards.forEach(function(c) { observer.observe(c); });
    }

    // ===== V2: reveal on scroll (todas las paginas publicas) =====
    var revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window && revealEls.length) {
        var revIO = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    revIO.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        revealEls.forEach(function(el) { revIO.observe(el); });
    } else {
        revealEls.forEach(function(el) { el.classList.add('reveal-visible'); });
    }

    // Fallback de seguridad: asegurar visibilidad total tras 900ms
    setTimeout(function() {
        document.querySelectorAll('.reveal').forEach(function(el) {
            el.classList.add('reveal-visible');
        });
    }, 900);

    // ===== V2: contadores .counter[data-target] =====
    function cxAnimateCounter(el) {
        var target = parseInt(el.dataset.target, 10) || 0;
        var duration = 1600;
        var start = null;
        function tick(now) {
            if (!start) start = now;
            var p = Math.min((now - start) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * eased).toLocaleString('es-NI');
            if (p < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }
    var cxCounters = document.querySelectorAll('.counter[data-target]');
    if ('IntersectionObserver' in window && cxCounters.length) {
        var cIO = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    cxAnimateCounter(entry.target);
                    cIO.unobserve(entry.target);
                }
            });
        }, { threshold: 0.4 });
        cxCounters.forEach(function(el) { cIO.observe(el); });
    } else {
        cxCounters.forEach(cxAnimateCounter);
    }
});