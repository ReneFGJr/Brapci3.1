<div class="container">
    <div class="row">
        <div class="col-10">
            <h1><?= $mk_name; ?></h1>
            <h5>
                Created at: <?= stodbr($mk_created_at); ?>
                Update at: <?= stodbr($mk_update_at); ?>
            </h5>
            <?=lang("brapci.own").': '. $us_nome;?>
            <hr>
            <?php
            echo count($data).' '.lang('brapci.registers');
            ?>

        </div>
    </div>
</div>