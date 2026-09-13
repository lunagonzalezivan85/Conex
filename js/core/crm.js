document.addEventListener('DOMContentLoaded', function() {
    var cards = document.querySelectorAll('.crm-card');
    var columns = document.querySelectorAll('.crm-cards');

    cards.forEach(function(card) {
        card.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', card.dataset.leadId);
            card.classList.add('dragging');
        });

        card.addEventListener('dragend', function() {
            card.classList.remove('dragging');
        });
    });

    columns.forEach(function(col) {
        col.addEventListener('dragover', function(e) {
            e.preventDefault();
            col.classList.add('drag-over');
        });

        col.addEventListener('dragleave', function() {
            col.classList.remove('drag-over');
        });

        col.addEventListener('drop', function(e) {
            e.preventDefault();
            col.classList.remove('drag-over');

            var leadId = e.dataTransfer.getData('text/plain');
            var nuevoEstado = col.dataset.estado;

            fetch(base_url + 'admin/crm/cambiar-estado/' + leadId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'estado=' + nuevoEstado
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var card = document.querySelector('[data-lead-id="' + leadId + '"]');
                    if (card) {
                        col.appendChild(card);
                        updateCounts();
                    }
                }
            })
            .catch(function(err) { console.error('Error:', err); });
        });
    });

    function updateCounts() {
        document.querySelectorAll('.crm-column').forEach(function(col) {
            var estado = col.dataset.estado;
            var count = col.querySelectorAll('.crm-card').length;
            var badge = col.querySelector('.crm-count');
            if (badge) badge.textContent = count;
            var empty = col.querySelector('.crm-empty-column');
            if (count > 0 && empty) empty.remove();
            else if (count === 0 && !empty) {
                var div = document.createElement('div');
                div.className = 'crm-empty-column';
                div.textContent = 'Sin leads';
                col.querySelector('.crm-cards').appendChild(div);
            }
        });
    }
});
