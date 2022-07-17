<?php

$Source = new \App\Models\Base\Sources();
$submit = '<input type="submit" name="action" class="btn btn-primary shadow p-3 mb-0 text-lg" type="button" value="' . lang('main.search') . '">';
$input_field = '<input type="text" name="query" value="' . get("query") . '" class="form-control shadow" placeholder="Digite aqui!">';
$types = array('all', 'article', 'proceeding', 'benancib', 'authority');
$select_type = '<input type="hidden" id="type" name="collection" value="75">';


/*********************************** ORDER */
$order = array('relevance', 'newest', 'older');
$sord = '';
$ord = get("ord");
if ($ord == '') {
    $ord = 0;
}

for ($r = 0; $r < count($order); $r++) {
    $check = '';
    if ($ord == $r) {
        $check = 'selected';
    }
    $sord .= '<option value="' . $r . '" ' . $check . '>' .  lang('brapci.' . $order[$r]) . '</option>' . cr();
}

/*********************************** ORDER */
$fields = array('all', 'title', 'abstract', 'keyword', 'authors');
$sfield = '';
$field = get("field");
if ($ord == '') {
    $ord = '1';
}
for ($r = 0; $r < count($fields); $r++) {
    $check = '';
    if ($field == $r) {
        $check = 'selected';
    }
    $sfield .= '<option value="' . $r . '" ' . $check . '>' . lang('brapci.' . $fields[$r]) . '</option>' . cr();
}

/*********************************** SDI */
$ini = 1994;
$di = get("di");
if ($di == '') {
    $di = $ini;
}
$sdi = '';
for ($r = $ini; $r <= (date("Y") + 1); $r++) {
    $check = '';
    if ($r == $di) {
        $check = 'selected';
    }
    $sdi .= '<option value="' . $r . '" ' . $check . '>' . $r . '</option>' . cr();
}
/*********************************** SDF */
$df = get("df");
if ($df == '') {
    $df = date("Y");
}
$sdf = '';
$sdf = '[' . $df . ']';
for ($r = (date("Y") + 1); $r >= $ini; $r--) {
    $check = '';
    if ($r == $df) {
        $check = 'selected';
    }
    $sdf .= '<option value="' . $r . '" ' . $check . '>' . $r . '</option>' . cr();
}

?>

<div class=" col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-center">
    <h3 class="" style="">O que você está procurando na Benancib?</h3>
</div>
<form method="get">
    <div class="container" style="margin-bottom: 20px" ;>
        <!-------------------------- SMALL SCREEN ----------->
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
        <!-------------------------- BIG SCREEN ----------->
        <div class="row d-none d-lg-block">
            <div class=" col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">

                <div class="input-group input-group-lg mb-0 p-3" style="border: 0px solid #0093DD;">
                    <?= $input_field; ?>
                    <?= $select_type; ?>
                    <?= $submit; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container" style="margin-bottom: 20px" ;>
        <!-------------------------- ADVANCED SCREEN ----------->
        <div class="row border p-2 mb-2 rounded-3 m-3 shadow" id="advanced_search" style="border-color: #FFF;">
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-2">
                <?= lang('brapci.delimitation'); ?>&nbsp;
                <select name="di" class="border-0 fw-bold"><?= $sdi; ?></select>&nbsp;
                <select name="df" class="border-0 fw-bold"><?= $sdf; ?></select>&nbsp;
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-2">
                <?= lang('brapci.ordenation'); ?>&nbsp;
                <select name="ord" class="border-0 fw-bold"><?= $sord; ?></select>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-2">
                <?= lang('brapci.fields'); ?>&nbsp;
                <select name="field" class="border-0 fw-bold"><?= $sfield; ?></select>
            </div>
        </div>
    </div>
</form>