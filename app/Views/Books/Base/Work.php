<?php
$titles = '';
foreach ($Title as $lang => $value) {
    if ($titles != '') {
        $titles .= '<br>';
    }
    $titles .= $value;
}
?>
<div class="container">
    <div class="row">
        <div class="col-10">
            <span class="btn btn-primary btn-sm"><?= lang($class); ?></span>
            <h1 class="text-center" style="font-size: 1.6em; font-weight: 700;"><?= $titles; ?></h1>
            <h6 class="text-end"><i><?= troca($authors, '$', '<br>'); ?></i></h6>

            <div class="container-fluid" style="background-color: #eee;">
                <div class="row">
                    <div class="col-3">
                        <p><b>ISBN</b><br /><?= $isbn; ?></b></p>
                    </div>

                    <div class="col-5">
                        <p><b>Editora</b>
                            <br /><?= $editora_local; ?>: <?= $editora; ?>
                        </p>
                    </div>
                    <div class="col-1">
                        <p><b>Ano</b>
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

                    <div class="col-9">
                        <p><b>Palavras-chave</b>
                            <br /><?= $subject; ?>
                        </p>
                    </div>

                    <?php
                    ############################### DOI
                    if (isset($DOI)) {
                    ?>
                        <div class="col-3">
                        </div>
                        <div class="col-9">
                            <p><b>DOI</b>
                                <br /><?= $DOI; ?>
                            </p>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <div class="col-12 summary">
                    <?= $summary; ?>
                </div>

                <div class="col-12 mt-3">
                    <?= $reference; ?>
                </div>

            </div>
        </div>

        <div class="col-2">

            <img src="<?= $cover; ?>" class="img-fluid shadow border border-secondary">

            PDF
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