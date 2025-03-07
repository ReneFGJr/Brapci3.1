<div class="container">
    <div class="row">
        <div class="col-1">
        </div>
        <div class="col-2">
            <?= $photo; ?>
        </div>
        <div class="col-8 border-start border-secondary">
            <?= h($Identifier, 2); ?>
        </div>
        <div class="col-1">
            <a href="<?= PATH . '/autoridade/v/' . $ID; ?>" class="btn btn-outline-secondary">brp:<?= $ID; ?></a>
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-1 border-5 bg-authority border-end small">
            <?= $Class; ?>
        </div>
        <div class="col-10">
            <?= $Identifier; ?>
        </div>
        <div class="col-1 text-end">
            <a href="<?= PATH . '/autoridade/v/' . $ID; ?>" class="btn btn-outline-secondary">brp:<?= $ID; ?></a>
        </div>
    </div>

    <div class="row">

        <div class="col-11">
            <?= $Affiliation; ?>
        </div>

        <div class="col-1 text-end">
            <?= $photo; ?>
            <?= $edit; ?> <?= count($altLabels); ?> variantes
        </div>

        <div class="col-12" id="folder" style="display: none;">
            <?= $folder; ?>
        </div>

        <div class="col-12" id="folder">
            <?= $cloud; ?>
        </div>

        <div class="col-12">
            <?= $vdata; ?>
        </div>
    </div>
</div>


USE [<?= $concept['cc_use']; ?>]