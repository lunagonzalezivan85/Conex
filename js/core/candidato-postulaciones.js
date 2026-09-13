(function() {
    var modal = document.getElementById('modalSubirDoc');
    if (!modal) return;

    var form = document.getElementById('formSubirDoc');

    document.querySelectorAll('.btn-subir-doc').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('inputPostulacionId').value = this.dataset.postulacion;
            document.getElementById('inputRequisitoId').value = this.dataset.requisito;
            document.getElementById('inputNombreDoc').value = this.dataset.nombre;
            document.getElementById('modalSubirTitulo').textContent = 'Subir: ' + this.dataset.nombre;
            modal.classList.add('active');
        });
    });

    function closeModal() {
        modal.classList.remove('active');
        form.reset();
    }

    document.getElementById('closeModalSubir').addEventListener('click', closeModal);
    document.getElementById('cancelModalSubir').addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(form);
        var csrfInput = document.querySelector('input[name^="csrf"]');
        if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

        var submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Subiendo...';

        fetch('/CONEX/candidato/subir-documento', {
            method: 'POST',
            body: formData,
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Completado', text: 'Documento subido correctamente', confirmButtonColor: '#4361EE' }).then(function() { location.reload(); });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'Error al subir documento', confirmButtonColor: '#4361EE' });
                submitBtn.disabled = false;
                submitBtn.textContent = 'Subir documento';
            }
        })
        .catch(function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexion', confirmButtonColor: '#4361EE' });
            submitBtn.disabled = false;
            submitBtn.textContent = 'Subir documento';
        });
    });
})();
