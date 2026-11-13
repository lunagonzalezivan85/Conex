<div class="page-header">
    <div>
        <h1>Captación de Empresas y Socios / Aliados</h1>
        <p>Registra y da seguimiento a nuevas empresas que necesitan personal o a instituciones aliadas (universidades, INATEC, gremios).</p>
    </div>
    <a href="<?= base_url('admin/crm') ?>" class="btn btn-outline">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;vertical-align:middle;"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        Ver Pipeline Kanban Completo
    </a>
</div>

<?php if (session()->getFlashdata('info')): ?>
    <div class="dash-card" style="border-left:4px solid #22c55e;margin-bottom:16px;">
        <p style="color:#22c55e;margin:0;font-weight:600;"><?= esc(session()->getFlashdata('info')) ?></p>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="dash-card" style="border-left:4px solid #ef4444;margin-bottom:16px;">
        <p style="color:#ef4444;margin:0;font-weight:600;"><?= esc(session()->getFlashdata('error')) ?></p>
    </div>
<?php endif; ?>

<div class="agente-dash-grid">
    <!-- Formulario de Captación -->
    <div class="agente-card">
        <div class="agente-card-header">
            <h3>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Registrar Nuevo Prospecto o Aliado
            </h3>
        </div>

        <form action="<?= base_url('agente/captar/guardar') ?>" method="POST">
            <?= csrf_field() ?>

            <!-- Selector de Tipo: Empresa vs Socio -->
            <label style="font-size:13px;font-weight:600;margin-bottom:8px;display:block;">Tipo de Organización:</label>
            <div class="tipo-selector">
                <label class="tipo-card-option active" id="optEmpresa">
                    <input type="radio" name="tipo_prospecto" value="empresa" checked>
                    <div>
                        <strong style="display:block;font-size:14px;color:var(--text-primary);">Empresa Cliente</strong>
                        <span style="font-size:12px;color:var(--text-secondary);">Publicación de vacantes y contratación</span>
                    </div>
                </label>
                <label class="tipo-card-option" id="optSocio">
                    <input type="radio" name="tipo_prospecto" value="socio_aliado">
                    <div>
                        <strong style="display:block;font-size:14px;color:var(--text-primary);">Socio / Aliado</strong>
                        <span style="font-size:12px;color:var(--text-secondary);">Universidad, Centro técnico (INATEC), Gremio</span>
                    </div>
                </label>
            </div>

            <div class="agente-form-grid">
                <div class="agente-form-group">
                    <label for="empresa_nombre">Nombre de la Empresa o Institución *</label>
                    <input type="text" id="empresa_nombre" name="empresa_nombre" class="agente-input" placeholder="Ej: Distribuidora S.A. o INATEC Managua" required value="<?= old('empresa_nombre') ?>">
                </div>

                <div class="agente-form-group">
                    <label for="rubro">Rubro o Tipo de Institución</label>
                    <input type="text" id="rubro" name="rubro" class="agente-input" placeholder="Ej: Comercio, Tecnología, Educación técnica" value="<?= old('rubro') ?>">
                </div>

                <div class="agente-form-group">
                    <label for="nombre_contacto">Persona de Contacto *</label>
                    <input type="text" id="nombre_contacto" name="nombre_contacto" class="agente-input" placeholder="Ej: Lic. Carlos Gómez" required value="<?= old('nombre_contacto') ?>">
                </div>

                <div class="agente-form-group">
                    <label for="telefono_contacto">Teléfono / WhatsApp</label>
                    <input type="tel" id="telefono_contacto" name="telefono_contacto" class="agente-input" placeholder="Ej: +505 8888 7777" value="<?= old('telefono_contacto') ?>">
                </div>

                <div class="agente-form-group">
                    <label for="email_contacto">Correo Electrónico</label>
                    <input type="email" id="email_contacto" name="email_contacto" class="agente-input" placeholder="contacto@empresa.com" value="<?= old('email_contacto') ?>">
                </div>

                <div class="agente-form-group">
                    <label for="origen">Origen del Contacto</label>
                    <select id="origen" name="origen" class="agente-select">
                        <option value="directo">Contacto directo / Campo</option>
                        <option value="feria_laboral">Feria laboral</option>
                        <option value="referido">Referido</option>
                        <option value="redes">Redes sociales</option>
                        <option value="web">Sitio web</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <div class="agente-form-group">
                    <label for="tamano_empresa">Tamaño Estimado</label>
                    <select id="tamano_empresa" name="tamano_empresa" class="agente-select">
                        <option value="pequena">Pequeña (1 - 10 personas)</option>
                        <option value="mediana">Mediana (11 - 50 personas)</option>
                        <option value="grande">Grande (50+ personas)</option>
                    </select>
                </div>

                <div class="agente-form-group">
                    <label for="plan_interes_id">Plan de Interés / Oferta</label>
                    <select id="plan_interes_id" name="plan_interes_id" class="agente-select">
                        <option value="">-- Sin plan específico (Alianza / Estándar) --</option>
                        <?php foreach ($planes as $plan): ?>
                            <option value="<?= $plan['id'] ?>"><?= esc($plan['nombre']) ?> ($<?= number_format($plan['precio'], 2) ?> USD)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="agente-form-group full">
                    <label for="notas">Notas / Acuerdos de la Conversación</label>
                    <textarea id="notas" name="notas" rows="3" class="agente-textarea" placeholder="Escribe los detalles importantes: cuántas vacantes buscan, fecha de seguimiento, requerimientos específicos..."><?= old('notas') ?></textarea>
                </div>
            </div>

            <div style="margin-top:20px;text-align:right;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;vertical-align:middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Guardar y Asignar a Mi Pipeline
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Prospectos Asignados -->
    <div class="agente-card">
        <div class="agente-card-header">
            <h3>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Mis Prospectos Registrados (<?= count($leads) ?>)
            </h3>
        </div>

        <?php if (!empty($leads)): ?>
            <div class="agente-item-list">
                <?php foreach ($leads as $l): ?>
                    <div class="agente-list-row">
                        <div class="agente-row-main">
                            <span class="agente-row-title"><?= esc($l['empresa_nombre']) ?></span>
                            <span class="agente-row-sub">
                                <span><?= esc($l['nombre_contacto']) ?></span>
                                <?php if (!empty($l['rubro'])): ?>
                                    &middot; <span><?= esc($l['rubro']) ?></span>
                                <?php endif; ?>
                            </span>
                            <?php if (!empty($l['telefono_contacto'])): ?>
                                <span style="font-size:12px;color:var(--accent);">
                                    Tel: <a href="tel:<?= esc($l['telefono_contacto']) ?>"><?= esc($l['telefono_contacto']) ?></a>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div style="text-align:right;">
                            <span class="badge badge-accent" style="text-transform:capitalize;display:block;margin-bottom:6px;">
                                <?= esc($l['estado']) ?>
                            </span>
                            <a href="<?= base_url('admin/crm/lead/' . $l['id']) ?>" class="btn btn-ghost btn-sm">
                                Ver detalle
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" style="padding:24px;text-align:center;">
                <p style="color:var(--text-secondary);font-size:13.5px;margin:0;">No tienes prospectos registrados todavía. Usa el formulario para captar tu primer cliente o aliado.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const optEmpresa = document.getElementById('optEmpresa');
    const optSocio = document.getElementById('optSocio');
    const radioEmpresa = optEmpresa.querySelector('input');
    const radioSocio = optSocio.querySelector('input');

    optEmpresa.addEventListener('click', function() {
        optEmpresa.classList.add('active');
        optSocio.classList.remove('active');
        radioEmpresa.checked = true;
    });

    optSocio.addEventListener('click', function() {
        optSocio.classList.add('active');
        optEmpresa.classList.remove('active');
        radioSocio.checked = true;
    });
});
</script>
