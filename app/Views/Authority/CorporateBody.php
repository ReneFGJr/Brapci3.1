<div class="container">
    <div class="row">
        <div class="col-2 border-5 bg-authority border-end small text-end">
            <?= $Class; ?>
        </div>
        <div class="col-9">
            <?= $Identifier; ?>
        </div>
        <div class="col-1 text-end">
            <a href="<?= PATH . '/autoridade/v/' . $ID; ?>" class="btn btn-outline-secondary">brp:<?= $ID; ?></a>
        </div>
    </div>

    <div class="row">

        <div class="col-11">
            <?=count($AffiliationR);?> Vinculados
            <?=$Aff;?>
        </div>

        <div class="col-1 text-end">
            <?= $logo; ?>
            <?= $edit;?> <?=count($altLabels);?> variantes
        </div>
    </div>
</div>