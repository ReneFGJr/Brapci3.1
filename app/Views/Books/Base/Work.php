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
            <span class="btn btn-primary btn-sm"><?= $class; ?></span>
            <h1 class="text-center" style="font-size: 1.6em;"><b><?= $titles; ?></b></h1>
            <h6 class="text-end"><i><?= troca($authors, '$', '<br>'); ?></i></h6>

            <div class="container-fluid" style="background-color: #eee;">
                <div class="row">
                    <div class="col-3">
                        <p><b>ISBN</b><br /><?= $isbn; ?></b></p>
                    </div>

                    <div class="col-3">
                        <p><b>Editora</b>
                            <br /><?= $editora_local; ?>
                        </p>
                    </div>
                    <div class="col-3">
                        <p><b>Ano da publicação</b>
                            <br /><?= $year; ?>
                        </p>
                    </div>
                    <div class="col-3">
                        <p><b>Idioma</b>
                            <br /><?= $idioma; ?>
                        </p>
                    </div>

                    <div class="col-3">
                        <p><b>Páginas</b>
                            <br /><?= $pages; ?>
                        </p>
                    </div>
                </div>
            </div>

            <p><?= $idioma; ?></p>
            <p>Palavras-chave: <?= $subs; ?></p>
            <p>Data: <?= $year; ?></p>
            <p>Pages: <?= $pages; ?></p>

            <div>
                <?= $summary; ?>
            </div>
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

            $WishList = new \App\Models\WishList\Index();
            echo $WishList->wishlist($id_cc);
            ?>
        </div>
    </div>
</div>