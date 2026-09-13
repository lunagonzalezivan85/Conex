(function() {
    function showError(msg) {
        Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#4361EE' });
    }

    function showSuccess(msg) {
        return Swal.fire({ icon: 'success', title: 'Completado', text: msg, confirmButtonColor: '#4361EE' });
    }

    // Iniciar proceso
    document.querySelectorAll('.form-iniciar-proceso').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(form);
            var csrfInput = document.querySelector('input[name^="csrf"]');
            if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

            fetch('/CONEX/admin/proceso/iniciar', {
                method: 'POST',
                body: formData,
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccess('Proceso iniciado correctamente').then(function() { location.reload(); });
                } else {
                    showError(data.error || 'Error al iniciar proceso');
                }
            })
            .catch(function() { showError('Error de conexion'); });
        });
    });

    // Evaluacion de entrevista
    document.querySelectorAll('.form-evaluacion').forEach(function(form) {
        // Interaccion de estrellas
        form.querySelectorAll('.eval-criterio-stars').forEach(function(starGroup) {
            var criterio = starGroup.dataset.criterio;
            var current = parseInt(starGroup.dataset.current) || 0;
            var inputCalificacion = form.querySelector('input[name="evaluaciones[' + criterio + '][calificacion]"]');
            var label = starGroup.querySelector('.eval-star-label');
            var labels = {0: 'Sin calificar', 1: 'Malo', 2: 'Regular', 3: 'Bueno', 4: 'Muy bueno', 5: 'Excelente'};
            var stars = starGroup.querySelectorAll('.eval-star');

            starGroup.addEventListener('mouseover', function(e) {
                if (e.target.classList.contains('eval-star')) {
                    var val = parseInt(e.target.dataset.value);
                    stars.forEach(function(s) {
                        if (parseInt(s.dataset.value) <= val) {
                            s.classList.add('eval-star-filled');
                        } else {
                            s.classList.remove('eval-star-filled');
                        }
                    });
                    label.textContent = labels[val];
                }
            });

            starGroup.addEventListener('mouseleave', function() {
                stars.forEach(function(s) {
                    if (parseInt(s.dataset.value) <= current) {
                        s.classList.add('eval-star-filled');
                    } else {
                        s.classList.remove('eval-star-filled');
                    }
                });
                label.textContent = labels[current];
            });

            stars.forEach(function(star) {
                star.addEventListener('click', function() {
                    current = parseInt(star.dataset.value);
                    inputCalificacion.value = current;
                    label.textContent = labels[current];
                    stars.forEach(function(s) {
                        if (parseInt(s.dataset.value) <= current) {
                            s.classList.add('eval-star-filled');
                        } else {
                            s.classList.remove('eval-star-filled');
                        }
                    });
                });
            });
        });

        // Guardar evaluacion
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(form);
            var csrfInput = document.querySelector('input[name^="csrf"]');
            if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

            var btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Guardando...';

            fetch('/CONEX/admin/proceso/evaluacion', {
                method: 'POST',
                body: formData,
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccess('Evaluacion guardada').then(function() { location.reload(); });
                } else {
                    showError(data.error || 'Error al guardar evaluacion');
                    btn.disabled = false;
                    btn.textContent = 'Guardar evaluacion';
                }
            })
            .catch(function() {
                showError('Error de conexion');
                btn.disabled = false;
                btn.textContent = 'Guardar evaluacion';
            });
        });
    });

    // Evaluacion final - recomendacion
    document.querySelectorAll('.form-evaluacion-final').forEach(function(form) {
        // Botones de recomendacion
        var inputRec = form.querySelector('.eval-input-recomendacion');
        form.querySelectorAll('.eval-rec-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                form.querySelectorAll('.eval-rec-btn').forEach(function(b) { b.classList.remove('eval-rec-selected'); });
                btn.classList.add('eval-rec-selected');
                inputRec.value = btn.dataset.value;
            });
        });

        // Guardar evaluacion final
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(form);
            var csrfInput = document.querySelector('input[name^="csrf"]');
            if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

            var btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Guardando...';

            fetch('/CONEX/admin/proceso/evaluacion', {
                method: 'POST',
                body: formData,
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccess('Evaluacion final guardada').then(function() { location.reload(); });
                } else {
                    showError(data.error || 'Error al guardar evaluacion');
                    btn.disabled = false;
                    btn.textContent = 'Guardar evaluacion final';
                }
            })
            .catch(function() {
                showError('Error de conexion');
                btn.disabled = false;
                btn.textContent = 'Guardar evaluacion final';
            });
        });

        // Boton Calcular
        var btnCalcular = form.querySelector('.btn-recalcular-eval');
        if (btnCalcular) {
            btnCalcular.addEventListener('click', function() {
                var procesoId = this.dataset.procesoId;
                var btn = this;

                btn.disabled = true;
                btn.textContent = 'Calculando...';

                var formData = new FormData();
                formData.append('proceso_id', procesoId);
                var csrfInput = document.querySelector('input[name^="csrf"]');
                if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

                fetch('/CONEX/admin/proceso/recalcular', {
                    method: 'POST',
                    body: formData,
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        Object.keys(data.eval_auto).forEach(function(key) {
                            var score = data.eval_auto[key].score;
                            var detalle = data.eval_auto[key].detalle;

                            var circle = form.querySelector('.eval-score-circle[data-criterio="' + key + '"]');
                            if (!circle) return;

                            circle.className = 'eval-score-circle eval-score-circle-' + score;
                            circle.dataset.current = score;
                            var numEl = circle.querySelector('.eval-score-num');
                            if (numEl) numEl.textContent = score;

                            var inputCal = form.querySelector('input[name="evaluaciones[' + key + '][calificacion]"]');
                            if (inputCal) inputCal.value = score;

                            var inputObs = form.querySelector('input[name="evaluaciones[' + key + '][observacion]"]');
                            if (inputObs) inputObs.value = detalle;

                            var detalleEl = circle.closest('.eval-criterio-item').querySelector('.eval-auto-detalle');
                            if (detalleEl) detalleEl.textContent = detalle;
                        });

                        showSuccess('Evaluacion recalculada');
                        btn.disabled = false;
                        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg> Calcular';
                    } else {
                        showError(data.error || 'Error al calcular');
                        btn.disabled = false;
                        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg> Calcular';
                    }
                })
                .catch(function() {
                    showError('Error de conexion');
                    btn.disabled = false;
                    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg> Calcular';
                });
            });
        }
    });

    // Presentar a la empresa - asignar fecha de entrevista
    document.querySelectorAll('.form-presentacion-empresa').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(form);
            var csrfInput = document.querySelector('input[name^="csrf"]');
            if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

            var btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Asignando...';

            fetch('/CONEX/admin/proceso/presentar', {
                method: 'POST',
                body: formData,
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccess('Fecha de entrevista asignada correctamente').then(function() { location.reload(); });
                } else {
                    showError(data.error || 'Error al asignar fecha');
                    btn.disabled = false;
                    btn.textContent = 'Asignar fecha y completar';
                }
            })
            .catch(function() {
                showError('Error de conexion');
                btn.disabled = false;
                btn.textContent = 'Asignar fecha y completar';
            });
        });
    });

    // Retroceder paso
    document.querySelectorAll('.btn-retroceder-paso').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var procesoId = this.dataset.procesoId;
            var pasoActual = this.dataset.pasoActual;

            Swal.fire({
                title: 'Retroceder paso',
                text: 'Seguro que deseas retroceder al paso anterior?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Si, retroceder',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                var formData = new FormData();
                formData.append('proceso_id', procesoId);
                formData.append('paso_actual', pasoActual);
                var csrfInput = document.querySelector('input[name^="csrf"]');
                if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

                btn.disabled = true;
                btn.textContent = 'Retrocediendo...';

                fetch('/CONEX/admin/proceso/retroceder', {
                    method: 'POST',
                    body: formData,
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        showError(data.error || 'Error al retroceder');
                        btn.disabled = false;
                        btn.textContent = 'Retroceder';
                    }
                })
                .catch(function() {
                    showError('Error de conexion');
                    btn.disabled = false;
                    btn.textContent = 'Retroceder';
                });
            });
        });
    });

    // Avanzar paso
    document.querySelectorAll('.form-avanzar-paso').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(form);
            var csrfInput = document.querySelector('input[name^="csrf"]');
            if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

            var btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Avanzando...';

            fetch('/CONEX/admin/proceso/avanzar', {
                method: 'POST',
                body: formData,
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    showError(data.error || 'Error al avanzar paso');
                    btn.disabled = false;
                    btn.textContent = 'Completar y avanzar';
                }
            })
            .catch(function() {
                showError('Error de conexion');
                btn.disabled = false;
                btn.textContent = 'Completar y avanzar';
            });
        });
    });

    // Verificar documento
    var modal = document.getElementById('modalVerificarDoc');
    var form = document.getElementById('formVerificarDoc');
    var currentDocId = null;

    if (modal && form) {
        document.querySelectorAll('.btn-verificar-doc').forEach(function(btn) {
            btn.addEventListener('click', function() {
                currentDocId = this.dataset.id;
                var estado = this.dataset.estado;
                document.getElementById('verificarEstado').value = estado;
                document.getElementById('modalVerificarTitulo').textContent = estado === 'aprobado' ? 'Aprobar documento' : 'Rechazar documento';
                modal.classList.add('active');
            });
        });

        function closeModal() {
            modal.classList.remove('active');
            form.reset();
            currentDocId = null;
        }

        document.getElementById('closeModalVerificar').addEventListener('click', closeModal);
        document.getElementById('cancelModalVerificar').addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!currentDocId) return;

            var formData = new FormData(form);
            var csrfInput = document.querySelector('input[name^="csrf"]');
            if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

            fetch('/CONEX/admin/documentos/verificar/' + currentDocId, {
                method: 'POST',
                body: formData,
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccess('Documento verificado').then(function() { location.reload(); });
                } else {
                    showError(data.error || 'Error al verificar documento');
                }
            })
            .catch(function() { showError('Error de conexion'); });
        });
    }

    // Admin subir documento
    var modalSubir = document.getElementById('modalAdminSubirDoc');
    var formSubir = document.getElementById('formAdminSubirDoc');

    if (modalSubir && formSubir) {
        document.querySelectorAll('.btn-admin-subir-doc').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('adminInputPostulacionId').value = this.dataset.postulacion;
                document.getElementById('adminInputRequisitoId').value = this.dataset.requisito;
                document.getElementById('adminInputNombreDoc').value = this.dataset.nombre;
                document.getElementById('modalAdminSubirTitulo').textContent = 'Subir: ' + this.dataset.nombre;
                modalSubir.classList.add('active');
            });
        });

        function closeModalSubir() {
            modalSubir.classList.remove('active');
            formSubir.reset();
        }

        document.getElementById('closeModalAdminSubir').addEventListener('click', closeModalSubir);
        document.getElementById('cancelModalAdminSubir').addEventListener('click', closeModalSubir);
        modalSubir.addEventListener('click', function(e) { if (e.target === modalSubir) closeModalSubir(); });

        formSubir.addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(formSubir);
            var csrfInput = document.querySelector('input[name^="csrf"]');
            if (csrfInput) formData.append(csrfInput.name, csrfInput.value);

            var submitBtn = formSubir.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Subiendo...';

            fetch('/CONEX/admin/documentos/subir', {
                method: 'POST',
                body: formData,
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccess('Documento subido correctamente').then(function() { location.reload(); });
                } else {
                    showError(data.error || 'Error al subir documento');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Subir documento';
                }
            })
            .catch(function() {
                showError('Error de conexion');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Subir documento';
            });
        });
    }
})();
