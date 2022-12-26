<div class="row" style="border: 1px solid #888; border-radius: 10px;">
    <div class="col-12">
        <h2><a href="<?=PATH.COLLECTION.'/source/'.$id_jnl;?>" target="_new"><?= trim($roman.' '.$jnl_name); ?></a></h2>
    </div>

    <!-- New colunm -->
    <div class="col-1">
        <?= $is_year; ?>
    </div>
    <div class="col-1">
        <?= $volume; ?>
    </div>
    <div class="col-4">
        <?= $is_place; ?>
    </div>
    <div class="col-6">
        <i><?= $is_thema; ?></i>
    </div>
    <!-- New colunm -->
    <div class="col-6 text-start">
        <img src="<?= URL . '/' . $img1; ?>" style="height: 80px;">
    </div>

    <div class="col-6 text-end">
        <img src="<?= URL . '/' . $img2; ?>" style="height: 80px;">
    </div>
</div>