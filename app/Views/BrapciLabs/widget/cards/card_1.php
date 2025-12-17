        <?php
        $mask = 'col-md-' . $size;
        ?>
        <div class="<?= $mask ?>">
            <div class="card card-dashboard p-3">
                <h6><?= $title ?? 'title'; ?></h6>
                <h4><?= $info ?? 'info'; ?></h4>
                <?= $content ?? '' ?>
            </div>
        </div>