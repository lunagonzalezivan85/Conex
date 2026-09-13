document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('modalEncuesta');
    var modalBody = document.getElementById('modalEncuestaBody');
    var btnClose = document.getElementById('closeModalEncuesta');

    if (!modal) return;

    function openModal() {
        modal.classList.add('active');
    }

    function closeModal() {
        modal.classList.remove('active');
        modalBody.innerHTML = '<p style="text-align:center;padding:32px;color:var(--text-secondary);">Cargando...</p>';
    }

    // Abrir modal al clickear "Responder encuesta"
    document.querySelectorAll('.btn-responder-encuesta').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            modalBody.innerHTML = '<p style="text-align:center;padding:32px;color:var(--text-secondary);">Cargando...</p>';
            openModal();

            var formData = new FormData();
            var csrfInput = document.querySelector('form input[name^="csrf"]');
            if (csrfInput) {
                formData.append(csrfInput.name, csrfInput.value);
            }

            fetch('/CONEX/empresa/encuestas/form/' + id, {
                method: 'GET',
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error) {
                    modalBody.innerHTML = '<p style="color:#b91c1c;text-align:center;padding:24px;">' + data.error + '</p>';
                } else if (data.html) {
                    modalBody.innerHTML = data.html;
                    initStars();
                    initCancel();
                }
            })
            .catch(function() {
                modalBody.innerHTML = '<p style="color:#b91c1c;text-align:center;padding:24px;">Error al cargar el formulario.</p>';
            });
        });
    });

    btnClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
    });

    // Sistema de estrellas
    function initStars() {
        document.querySelectorAll('.encuesta-stars').forEach(function(starGroup) {
            var stars = starGroup.querySelectorAll('.encuesta-star');
            var hidden = starGroup.querySelector('input[type="hidden"]');

            stars.forEach(function(star) {
                star.addEventListener('click', function() {
                    var val = parseInt(this.dataset.val);
                    hidden.value = val;
                    stars.forEach(function(s) {
                        if (parseInt(s.dataset.val) <= val) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });

                star.addEventListener('mouseenter', function() {
                    var val = parseInt(this.dataset.val);
                    stars.forEach(function(s) {
                        if (parseInt(s.dataset.val) <= val) {
                            s.style.color = '#f59e0b';
                        } else {
                            s.style.color = '';
                        }
                    });
                });
            });

            starGroup.addEventListener('mouseleave', function() {
                stars.forEach(function(s) { s.style.color = ''; });
            });
        });
    }

    function initCancel() {
        var cancelBtn = document.getElementById('cancelarEncuesta');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }
    }
});
