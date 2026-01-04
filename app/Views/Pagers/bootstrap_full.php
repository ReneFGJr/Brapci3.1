<?php $links = $pager->links(); ?>

<?php if (! empty($links)): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">

            <?php if ($pager->hasPrevious()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getPrevious() ?>">&laquo;</a>
                </li>
            <?php endif ?>

            <?php foreach ($links as $link): ?>
                <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link['uri'] ?>">
                        <?= $link['title'] ?>
                    </a>
                </li>
            <?php endforeach ?>

            <?php if ($pager->hasNext()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getNext() ?>">&raquo;</a>
                </li>
            <?php endif ?>

        </ul>
    </nav>
<?php endif; ?>