<?php if ($pager->hasPrevious() || $pager->hasNext()): ?>

    <nav aria-label="Paginação">
        <ul class="pagination pagination-sm justify-content-center">

            <?php if ($pager->hasPrevious()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getPrevious() ?>">&laquo;</a>
                </li>
            <?php endif; ?>

            <?php foreach ($pager->links() as $link): ?>
                <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link['uri'] ?>">
                        <?= $link['title'] ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <?php if ($pager->hasNext()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getNext() ?>">&raquo;</a>
                </li>
            <?php endif; ?>

        </ul>
    </nav>

<?php endif; ?>