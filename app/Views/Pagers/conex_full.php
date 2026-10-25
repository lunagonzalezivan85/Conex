<?php
/**
 * Pager CONEX: flechas SVG, texto en espanol, estado activo/deshabilitado explicito.
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);

// Una sola pagina: no mostrar paginador
if ($pager->getPageCount() <= 1) {
    return;
}

$iconPrev = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15"><polyline points="15 18 9 12 15 6"/></svg>';
$iconNext = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15"><polyline points="9 18 15 12 9 6"/></svg>';
$iconFirst = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg>';
$iconLast = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg>';

$links = $pager->links();
$first = $links[0]['title'] ?? 1;
$last  = $links[count($links) - 1]['title'] ?? 1;
$current = null;
foreach ($links as $l) { if ($l['active']) { $current = $l['title']; break; } }
?>
<nav class="cx-pager" aria-label="Paginacion">
    <ul class="cx-pager-list">
        <?php if ($pager->hasPrevious()): ?>
            <li><a class="cx-page cx-page-nav" href="<?= $pager->getFirst() ?>" aria-label="Primera pagina" title="Primera"><?= $iconFirst ?></a></li>
            <li><a class="cx-page cx-page-nav" href="<?= $pager->getPrevious() ?>" aria-label="Pagina anterior" title="Anterior"><?= $iconPrev ?></a></li>
        <?php else: ?>
            <li><span class="cx-page cx-page-nav is-disabled" aria-disabled="true"><?= $iconFirst ?></span></li>
            <li><span class="cx-page cx-page-nav is-disabled" aria-disabled="true"><?= $iconPrev ?></span></li>
        <?php endif; ?>

        <?php if ($first > 1): ?>
            <li><span class="cx-page cx-page-ellipsis" aria-hidden="true">…</span></li>
        <?php endif; ?>

        <?php foreach ($links as $link): ?>
            <li>
                <?php if ($link['active']): ?>
                    <span class="cx-page is-active" aria-current="page"><?= $link['title'] ?></span>
                <?php else: ?>
                    <a class="cx-page" href="<?= $link['uri'] ?>" aria-label="Pagina <?= $link['title'] ?>"><?= $link['title'] ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNext()): ?>
            <li><a class="cx-page cx-page-nav" href="<?= $pager->getNext() ?>" aria-label="Pagina siguiente" title="Siguiente"><?= $iconNext ?></a></li>
            <li><a class="cx-page cx-page-nav" href="<?= $pager->getLast() ?>" aria-label="Ultima pagina" title="Ultima"><?= $iconLast ?></a></li>
        <?php else: ?>
            <li><span class="cx-page cx-page-nav is-disabled" aria-disabled="true"><?= $iconNext ?></span></li>
            <li><span class="cx-page cx-page-nav is-disabled" aria-disabled="true"><?= $iconLast ?></span></li>
        <?php endif; ?>
    </ul>
    <?php if ($current !== null): ?>
        <span class="cx-pager-info">Pagina <strong><?= $current ?></strong> de <strong><?= $pager->getPageCount() ?></strong></span>
    <?php endif; ?>
</nav>
