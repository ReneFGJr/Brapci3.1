<?php
$titles = '';
foreach ($Title as $lang => $value) {
    if ($titles != '') {
        $titles .= '<br>';
    }
    $titles .= $value;
}

$subs = '';
foreach ($subject as $lang => $value) {
    if ($subs != '') {
        $subs .= '.';
    }
    $subs .= $value;
}

?>
<div class="container">
    <div class="row">
        <div class="col-2">
            <img src="<?= $cover; ?>" class="img-fluid shadow border border-secondary">
        </div>

        <div class="col-8">
            <h1 class="text-center"><?= $titles; ?></h1>
            <h5 class="text-end"><i><?= troca($authors, '$', '<br>'); ?></i></h5>
            <p><?= $isbn; ?></p>
            <p><?= $idioma; ?></p>
            <p>Editora: <?= $editora; ?></p>
            <p>Palavras-chave: <?= $subs; ?></p>
            <p>Data: <?= $year; ?></p>
            <p>Pages: <?= $pages; ?></p>
        </div>

        <div class="col-2">PDF
            <?php
            /************************************************************ PDF */
            if (($PDF != '') and (isset($PDF[0]['id']))) {
                $url = PATH . '/download/' . $PDF[0]['id'];
                $data['pdf'] = $url;
                echo view('Brapci/Base/PDF', $data);
            } else {
                /*************************** DOWNLOAD PDF - AUTOBOT */
                $DownloadBot = new \App\Models\Bots\DownloadPDF();
                echo $DownloadBot->toHarvesting($id_cc);
                for ($r = 0; $r < count($URL); $r++) {
                    $data['URL'] = $URL[$r];
                    echo view('Brapci/Base/PDFno', $data);
                }
            }
            ?>
        </div>
    </div>
</div>