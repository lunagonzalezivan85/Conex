<div class="page-header">
    <div>
        <h1>Registro Express en Campo y Ferias</h1>
        <p>Captura rápida de candidatos en stands, ferias de empleo o prospección en campo sin formularios largos.</p>
    </div>
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

<!-- Tarjeta de Credenciales Entregable al Candidato -->
<?php if (!empty($candidatoRegistrado)): ?>
    <?php
        $c = $candidatoRegistrado;
        $telNum = preg_replace('/[^0-9]/', '', $c['telefono']);
        $waMsg = urlencode("¡Hola {$c['nombre']}! Ya estás registrado en CONEX.\n\nAcceso a la plataforma:\nUsuario: {$c['usuario']}\nContraseña temporal: {$c['password']}\nEnlace: " . base_url('login') . ($c['postulacion'] ? "\n\nYa quedaste postulado a: {$c['postulacion']['titulo']}" : ''));
        $waLink = "https://api.whatsapp.com/send?phone={$telNum}&text={$waMsg}";
    ?>
    <div class="credenciales-card">
        <div class="credenciales-header">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#15803D" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3>Credenciales del Candidato Registrado</h3>
        </div>
        <p style="font-size:13.5px;color:#166534;margin:0 0 14px 0;">
            Muéstrale esta tarjeta para que le tome una foto o envíasela directamente por WhatsApp a su celular.
        </p>

        <div class="credenciales-box">
            <div class="cred-item">
                <span class="label">Candidato</span>
                <span class="value" style="font-family:inherit;"><?= esc($c['nombre']) ?></span>
            </div>
            <div class="cred-item">
                <span class="label">Profesión / Oficio</span>
                <span class="value" style="font-family:inherit;"><?= esc($c['profesion'] ?: 'Candidato') ?></span>
            </div>
            <div class="cred-item">
                <span class="label">Usuario</span>
                <span class="value"><?= esc($c['usuario']) ?></span>
            </div>
            <div class="cred-item">
                <span class="label">Contraseña Temporal</span>
                <span class="value" style="color:#B91C1C;"><?= esc($c['password']) ?></span>
            </div>
            <div class="cred-item">
                <span class="label">Teléfono</span>
                <span class="value" style="font-family:inherit;"><?= esc($c['telefono']) ?></span>
            </div>
            <div class="cred-item">
                <span class="label">Acceso Web</span>
                <span class="value" style="font-family:inherit;font-size:13px;"><?= base_url('login') ?></span>
            </div>
        </div>

        <?php if (!empty($c['postulacion'])): ?>
            <div style="background:#ffffff;border:1px solid #86EFAC;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
                <strong style="color:#15803D;font-size:13.5px;">✓ Auto-postulación realizada con éxito:</strong>
                <span style="font-size:13.5px;color:var(--text-primary);display:block;margin-top:2px;"><?= esc($c['postulacion']['titulo']) ?></span>
            </div>
        <?php endif; ?>

        <div class="credenciales-actions">
            <?php if (!empty($telNum)): ?>
                <a href="<?= $waLink ?>" target="_blank" rel="noopener" class="btn-whatsapp">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Enviar credenciales por WhatsApp
                </a>
            <?php endif; ?>
            <a href="<?= base_url('admin/postulantes/perfil/' . $c['candidato_id']) ?>" class="btn btn-outline btn-sm">
                Ver expediente en sistema
            </a>
            <a href="<?= base_url('agente/registro-campo') ?>" class="btn btn-ghost btn-sm">
                + Registrar otro candidato
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Formulario de Registro Express -->
<div class="dash-card">
    <div class="dash-card-header">
        <h2>Datos Rápidos del Candidato</h2>
    </div>

    <form action="<?= base_url('agente/registro-campo/guardar') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="agente-form-grid">
            <div class="agente-form-group">
                <label for="nombre">Nombre(s) *</label>
                <input type="text" id="nombre" name="nombre" class="agente-input" placeholder="Ej: Roberto Carlos" required value="<?= old('nombre') ?>">
            </div>

            <div class="agente-form-group">
                <label for="apellido">Apellido(s)</label>
                <input type="text" id="apellido" name="apellido" class="agente-input" placeholder="Ej: Mendoza Morales" value="<?= old('apellido') ?>">
            </div>

            <div class="agente-form-group">
                <label for="telefono">Teléfono / WhatsApp *</label>
                <input type="tel" id="telefono" name="telefono" class="agente-input" placeholder="Ej: 8888 7777" required value="<?= old('telefono') ?>">
            </div>

            <div class="agente-form-group">
                <label for="cedula">Cédula de Identidad (Opcional)</label>
                <input type="text" id="cedula" name="cedula" class="agente-input" placeholder="Ej: 001-200590-0012A" value="<?= old('cedula') ?>">
            </div>

            <div class="agente-form-group">
                <label for="profesion">Profesión / Ocupación Principal *</label>
                <input type="text" id="profesion" name="profesion" class="agente-input" placeholder="Ej: Conductor, Electricista, Cajero, Desarrollador..." required value="<?= old('profesion') ?>">
            </div>

            <div class="agente-form-group">
                <label for="ciudad">Ciudad / Municipio</label>
                <input type="text" id="ciudad" name="ciudad" class="agente-input" placeholder="Managua, Masaya, León..." value="<?= old('ciudad', 'Managua') ?>">
            </div>

            <div class="agente-form-group">
                <label for="salario_esperado_usd">Salario Esperado Estimado (USD)</label>
                <input type="number" step="10" min="0" id="salario_esperado_usd" name="salario_esperado_usd" class="agente-input" placeholder="Ej: 350" value="<?= old('salario_esperado_usd') ?>">
            </div>

            <div class="agente-form-group">
                <label for="disponibilidad">Disponibilidad</label>
                <select id="disponibilidad" name="disponibilidad" class="agente-select">
                    <option value="inmediata">Inmediata</option>
                    <option value="a_convenir">A convenir / Tiempo parcial</option>
                </select>
            </div>

            <div class="agente-form-group">
                <label for="email">Correo Electrónico (Opcional)</label>
                <input type="email" id="email" name="email" class="agente-input" placeholder="Si no tiene, se generará uno automático" value="<?= old('email') ?>">
            </div>

            <!-- Auto-postulación Express en Feria -->
            <div class="agente-form-group">
                <label for="vacante_id" style="color:var(--brand-navy, #123D73);font-weight:700;">
                    ⭐ Postular de Inmediato a Vacante Activa
                </label>
                <select id="vacante_id" name="vacante_id" class="agente-select" style="border-color:#3B82F6;background:#EFF6FF;">
                    <option value="">-- No postular ahora (Solo registrar perfil) --</option>
                    <?php foreach ($vacantes as $v): ?>
                        <option value="<?= $v['id'] ?>">
                            <?= esc($v['titulo']) ?> (<?= esc($v['empresa_nombre']) ?> - <?= esc($v['ciudad'] ?? 'Nicaragua') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Ofrece servicios independientes -->
            <div class="agente-form-group full">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;background:#F8FAFC;padding:12px 16px;border-radius:10px;border:1px solid #E2E8F0;">
                    <input type="checkbox" name="ofrece_servicios" value="1" style="width:18px;height:18px;accent-color:var(--accent);">
                    <div>
                        <strong style="color:var(--text-primary);font-size:14px;display:block;">¿Ofrece servicios técnicos u oficios independientes?</strong>
                        <span style="color:var(--text-secondary);font-size:12.5px;">Habilita su perfil para aparecer en el directorio de prestadores (electricistas, fontanería, albañilería, etc.)</span>
                    </div>
                </label>
            </div>
        </div>

        <div style="margin-top:24px;display:flex;justify-content:flex-end;gap:12px;">
            <button type="submit" class="btn btn-primary btn-lg">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:6px;vertical-align:middle;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Registrar y Generar Credenciales
            </button>
        </div>
    </form>
</div>
