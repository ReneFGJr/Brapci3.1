<?php
$data = array();
$data['page_title'] = 'Tombstone DOI ' . $DOI;

echo view('Brapci/Headers/header', $data);
?>
<div class="container">
    <div class="row">
        <div class="col-2">
            <img src="<?= PATH; ?>/img/thema/tombstone.jpg" class="img-fluid">
        </div>
        <div class="col-10">
            <p><?= lang('doi.this_doi_is_dead'); ?></p>
            <h1><?= $DOI; ?></h1>
            <h3><?= $title; ?></h3>
            <p><?= $description; ?></p>
        </div>
    </div>
</div>

<?php exit; ?>