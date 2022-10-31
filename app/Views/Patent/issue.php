<div class="container mt-5">
    <div class="row">
        <div class="col-7" style="font-size: 1.5em;">
            <b><?= $rpi_title; ?></b>
        </div>

        <div class="col-1">
            <?= $action; ?>
        </div>

        <div class="col-2 text-end">
            <?= stodbr($rpi_data); ?>
        </div>

        <div class="col-2 text-end">
            Nr. <?= $rpi_nr; ?>
        </div>
    </div>
    <div class="row" style="border-top: 1px solid #000;">
        <div class="col-6">

        </div>
        <div class="col-6 small">
            <?= $summary; ?>
        </div>
    </div>
</div>