<?php
$data = array();
$data['page_title'] = 'Tombstone DOI ' . $pi_id;

echo view('Brapci/Headers/header', $data);
?>
<div class="container">
    <div class="row">
        <div class="col-2">
            <img src="<?= PATH; ?>/img/thema/tombstone.png" class="img-fluid">
        </div>
        <div class="col-10">
            <h4 class="text-danger"><?= lang('doi.this_doi_is_dead'); ?></h4>
            <h1><?= $pi_id; ?></h1>
            <h3><?= $pi_title; ?></h3>
            <p>Ativo: <?= lang('doi.active_' . $pi_active); ?></p>
            <p>Situação: <?= $pi_status; ?></p>
            <p>URL: <?= ($pi_url); ?></p>

            <p>URL: <?= ($pi_citation); ?></p>
        </div>
    </div>
</div>

<?php exit; ?>