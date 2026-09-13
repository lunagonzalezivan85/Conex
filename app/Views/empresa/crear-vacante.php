<div class="page-header">
    <div>
        <h1>Publicar Vacante</h1>
        <p>Crea una nueva vacante para recibir candidatos.</p>
    </div>
</div>

<form action="<?= base_url('empresa/vacante/crear') ?>" method="post">
    <?= csrf_field() ?>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Informacion general</h2>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label">Titulo de la vacante *</label>
            <input type="text" name="titulo" class="form-control" required placeholder="Ej: Desarrollador Backend PHP">
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
            <div class="form-group" style="flex:1;min-width:200px;">
                <label class="form-label">Categoria *</label>
                <select name="categoria_id" class="form-control" required>
                    <option value="">Selecciona...</option>
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= esc($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group" style="min-width:180px;">
                <label class="form-label">Modalidad *</label>
                <select name="modalidad" class="form-control" required>
                    <option value="presencial">Presencial</option>
                    <option value="remoto">Remoto</option>
                    <option value="hibrido">Hibrido</option>
                </select>
            </div>
        </div>

    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Ubicacion de la vacante</h2>
            <p style="font-size:13px;color:var(--text-secondary);">Busca o haz clic en el mapa para seleccionar el departamento y ciudad.</p>
        </div>

        <div id="ubicacionContainer"></div>

        <input type="hidden" name="ciudad" id="ext_ciudad" value="<?= esc($empresa['ciudad'] ?? '') ?>">
        <input type="hidden" name="region" id="ext_region" value="<?= esc($empresa['region'] ?? '') ?>">
        <input type="hidden" name="latitud" id="ext_latitud" value="">
        <input type="hidden" name="longitud" id="ext_longitud" value="">

        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
            <div class="form-group" style="flex:1;min-width:150px;">
                <label class="form-label">Salario minimo (USD)</label>
                <input type="number" name="salario_min" class="form-control" placeholder="0" min="0">
            </div>
            <div class="form-group" style="flex:1;min-width:150px;">
                <label class="form-label">Salario maximo (USD)</label>
                <input type="number" name="salario_max" class="form-control" placeholder="0" min="0">
            </div>
            <div class="form-group" style="min-width:120px;">
                <label class="form-label">Años de experiencia</label>
                <input type="number" name="anios_experiencia" class="form-control" placeholder="0" min="0" value="0">
            </div>
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
            <div class="form-group" style="min-width:120px;">
                <label class="form-label">Vacantes disponibles</label>
                <input type="number" name="vacantes_disponibles" class="form-control" placeholder="1" min="1" value="1">
            </div>
            <div class="form-group" style="min-width:120px;">
                <label class="form-label">Max. postulantes (0 = ilimitado)</label>
                <input type="number" name="max_postulantes" class="form-control" placeholder="0" min="0" value="0">
            </div>
            <div class="form-group" style="min-width:160px;">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="publicada">Publicar ahora</option>
                    <option value="borrador">Guardar como borrador</option>
                </select>
            </div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Descripcion y funciones</h2>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <label class="form-label" style="margin-bottom:0;">Descripcion de la vacante *</label>
                <button type="button" class="btn-sugerir" id="btnSugerirDesc">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.3h6c0-1 .4-1.8 1-2.3A7 7 0 0 0 12 2z"/></svg>
                    Sugerir
                </button>
            </div>
            <textarea name="descripcion" id="textareaDescripcion" class="form-control" rows="5" required placeholder="Describe la vacante, requisitos, responsabilidades..."></textarea>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <label class="form-label" style="margin-bottom:0;">Funciones principales</label>
                <button type="button" class="btn-sugerir" id="btnSugerirFunc">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.3h6c0-1 .4-1.8 1-2.3A7 7 0 0 0 12 2z"/></svg>
                    Sugerir
                </button>
            </div>
            <textarea name="funciones" id="textareaFunciones" class="form-control" rows="4" placeholder="Lista las funciones principales del puesto..."></textarea>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Requisitos</h2>
            <p style="font-size:13px;color:var(--text-secondary);">Busca y selecciona los requisitos necesarios.</p>
        </div>

        <div class="tag-selector" id="reqTagSelector">
            <div class="tag-input-wrap">
                <input type="text" id="reqSearch" class="tag-search-input" placeholder="Buscar requisito..." autocomplete="off">
                <div class="tag-dropdown" id="reqDropdown"></div>
            </div>
            <div class="tag-selected" id="reqSelected"></div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>Habilidades requeridas</h2>
            <p style="font-size:13px;color:var(--text-secondary);">Busca y selecciona las habilidades con su nivel.</p>
        </div>

        <div class="tag-selector" id="habTagSelector">
            <div class="tag-input-wrap">
                <input type="text" id="habSearch" class="tag-search-input" placeholder="Buscar habilidad..." autocomplete="off">
                <div class="tag-dropdown" id="habDropdown"></div>
            </div>
            <div class="tag-selected" id="habSelected"></div>
        </div>
    </div>

    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
            Publicar vacante
        </button>
        <a href="<?= base_url('empresa/vacantes') ?>" class="btn btn-ghost">Cancelar</a>
    </div>
</form>

<script>
    window.VAC_DATA = {
        requisitos: <?= json_encode($requisitos ?? []) ?>,
        habilidades: <?= json_encode($habilidades ?? []) ?>
    };
</script>
