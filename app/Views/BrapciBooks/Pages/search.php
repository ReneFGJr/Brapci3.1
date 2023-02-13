<?php
$Source = new \App\Models\Base\Sources();
$submit = '<input type="submit" name="action" class="btn btn-primary shadow p-3 mb-0 text-lg" type="button" value="' . lang('main.search') . '">';
$input_field = '<input type="text" name="q" value="' . get("q") . '" class="form-control shadow" placeholder="Digite aqui!">';
$input_field_small = '<input type="text" name="qs" value="' . get("qs") . '" class="form-control shadow" placeholder="Digite aqui!">';
?>

<form method="get">
    <div class="container" style="margin-bottom: 20px" ;>
        <!-------------------------- BIG SCREEN ----------->
        <div class="row d-lg-none">
            <div class="col-12">
                <div class="input-group input-group-lg mb-0 p-3" style="border: 0px solid #0093DD;">
                    <?= $input_field; ?>
                </div>
            </div>

            <div class="col-12">
                <div class="input-group input-group-lg mb-0 p-3" style="border: 0px solid #0093DD;">
                    <?= $submit; ?>
                </div>
            </div>
        </div>
        <!-------------------------- SMALL SCREEN ----------->
        <div class="row d-none d-lg-block">
            <div class=" col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">

                <div class="input-group input-group-lg mb-0 p-3" style="border: 0px solid #0093DD;">
                    <?= $input_field_small; ?>
                    <?= $submit; ?>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="<?= date("YmdHis"); ?>">
</form>