document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.btn-estado');
    const actionsContainer = document.getElementById('pd-estado-actions');

    if (!buttons.length) return;

    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (btn.disabled) return;

            const estado = btn.dataset.estado;
            const postulacionId = btn.dataset.postulacion;
            const estadoLabels = {
                'en_proceso': 'En proceso',
                'rechazado': 'Rechazado',
                'contratado': 'Contratado'
            };

            Swal.fire({
                title: 'Confirmar',
                text: '¿Marcar como "' + estadoLabels[estado] + '"?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Si, confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4361EE',
                cancelButtonColor: '#6b7280'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                fetch('/CONEX/empresa/postulante/estado/' + postulacionId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ estado: estado })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Estado actualizado',
                            text: 'El postulante ahora esta: ' + estadoLabels[data.estado],
                            timer: 1800,
                            showConfirmButton: false
                        });

                        setTimeout(function() { location.reload(); }, 1800);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo actualizar el estado'
                        });
                    }
                })
                .catch(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrio un error al actualizar el estado'
                    });
                });
            });
        });
    });
});
