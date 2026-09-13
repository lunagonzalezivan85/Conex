<?php
$filtros = $filtros ?? ['q' => '', 'categoria' => '', 'modalidad' => '', 'tipo_contrato' => '', 'ciudad' => ''];
?>
<div class="container buscar-empleo">
    <h1>Buscar Empleo</h1>

    <div class="buscar-layout">
        <!-- Filtros -->
        <aside class="card filtros">
            <h3>Filtros</h3>
            <form action="<?= base_url('buscar-empleo') ?>" method="get" id="filterForm">
                <div class="form-group">
                    <label class="form-label">Busqueda</label>
                    <input type="text" name="q" class="form-control" placeholder="Titulo, empresa..." value="<?= esc($filtros['q']) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Categoria</label>
                    <select name="categoria" class="form-control">
                        <option value="">Todas</option>
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $filtros['categoria'] == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Modalidad</label>
                    <select name="modalidad" class="form-control">
                        <option value="">Todas</option>
                        <option value="presencial" <?= $filtros['modalidad'] === 'presencial' ? 'selected' : '' ?>>Presencial</option>
                        <option value="remoto" <?= $filtros['modalidad'] === 'remoto' ? 'selected' : '' ?>>Remoto</option>
                        <option value="hibrido" <?= $filtros['modalidad'] === 'hibrido' ? 'selected' : '' ?>>Hibrido</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" placeholder="Ciudad..." value="<?= esc($filtros['ciudad']) ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Aplicar filtros</button>
                <a href="<?= base_url('buscar-empleo') ?>" class="btn btn-ghost btn-block">Limpiar</a>
            </form>
        </aside>

        <!-- Resultados -->
        <div>
            <p class="resultados-count"><?= count($vacantes) ?> resultado(s) encontrado(s)</p>

            <div class="resultados-list">
                <?php if (!empty($vacantes)): ?>
                    <?php foreach ($vacantes as $v): ?>
                        <a href="<?= base_url('vacante/' . $v['slug']) ?>" class="card vacante-item">
                            <div class="vacante-item-icon"><?= strtoupper(substr($v['titulo'], 0, 1)) ?></div>
                            <div class="vacante-item-body">
                                <h3 class="vacante-item-title"><?= esc($v['titulo']) ?></h3>
                                <p class="vacante-item-location"><?= esc($v['ciudad'] ?? '') ?> <?= isset($v['ciudad']) && $v['region'] ? '· ' . esc($v['region']) : '' ?></p>
                                <div class="vacante-item-badges">
                                    <?php if (isset($v['modalidad'])): ?>
                                        <span class="badge badge-accent"><?= ucfirst($v['modalidad']) ?></span>
                                    <?php endif; ?>
                                    <?php if (isset($v['salario_min']) && $v['salario_min']): ?>
                                        <span class="badge badge-success">$<?= number_format($v['salario_min'], 0) ?> - $<?= number_format($v['salario_max'] ?? $v['salario_min'], 0) ?> USD</span>
                                    <?php endif; ?>
                                    <?php if (isset($v['anios_experiencia']) && $v['anios_experiencia'] > 0): ?>
                                        <span class="badge badge-warning"><?= $v['anios_experiencia'] ?> año(s) exp.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="vacante-item-date">
                                <p><?= isset($v['fecha_publicacion']) ? date('d M', strtotime($v['fecha_publicacion'])) : '' ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card resultados-empty">
                        <p class="empty-state-icon">&#128269;</p>
                        <h3>No se encontraron vacantes</h3>
                        <p>Intenta ajustar los filtros de busqueda.</p>
                        <a href="<?= base_url('buscar-empleo') ?>" class="btn btn-outline">Limpiar filtros</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isset($pager) && $pager): ?>
                <div class="pagination-wrapper">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
