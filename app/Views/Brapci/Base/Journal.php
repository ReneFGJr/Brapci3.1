<div class="container">
    <div class="row">
        <div class="col-12">
            <?= h($source['jnl_name'], 1, 'm-0'); ?>
        </div>
        <div class="col-6">
            <?= h($source['jnl_name_abrev'] . $links,6, 'm-0 mb-3'); ?>
            <p class="m-0">ISSN: <?= $source['jnl_issn']; ?> <?= $source['jnl_eissn']; ?> </p>
            <p class="m-0">Publicação de: <?= $source['jnl_ano_inicio'] . '-' . $source['jnl_ano_final']; ?></p>

        </div>
        <div class="col-4">
            <?php
            foreach ($Collections as $id => $name) {
                echo $name . '<br>';
            }
            ?>
        </div>
        <div class="col-2">
            <img src="<?= $cover; ?>" class="img-fluid">
        </div>
    </div>
</div>