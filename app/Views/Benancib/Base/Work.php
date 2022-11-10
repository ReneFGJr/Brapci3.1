<?php
if (!isset($MidiasSociais)) {
    $MidiasSociais = '';
}
?>

<div class="container">
    <div class="row">
        <div class="col-2">
            <?= view("Benancib/Base/Header"); ?>
        </div>
        <div class="col-10">
            <?= view("Benancib/Base/Header_proceeding"); ?>

        </div>
    </div>
</div>
<!--ISSUE -->
<div class="container">
    <div class="col-12 mb-5 text-center">
        <h4><?= $issue; ?></h4>
        <!--- LEGEND ------------------------------------------->
        <?php
        $sect = '';
        for ($r = 0; $r < count($Section); $r++) {
            if (strlen($sect) > 0) {
                $sect .= ' - ';
            }
            $sect .= $Section[$r];
        }
        ?>
        <?= $sect; ?>
    </div>
</div>
</div>

<!--TITLE -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <?php
            /******************************** TITULO */
            $H = 1;
            foreach ($Title as $idioma => $titulo) {
                echo '<h' . $H . '>' . $titulo . '</h' . $H . '>';
                $H++;
            }
            ?>
        </div>
    </div>
</div>

<!--CONTENT -->
<div class="container">
    <div class="row">
        <div class="col-10">
            <!-- AUTHORS -->
            <div class="text-end" id="authors">
                <?php
                $authors = troca($authors, '$', '; ');
                $authors = substr($authors, 0, strlen($authors) - 2) . '.';
                echo $authors;
                ?>
            </div>

            <!-- ABSTRACT -->
            <p>
                <?php
                /******************************** ABSTRACT */
                if (isset($Abstract)) {
                    foreach ($Abstract as $idioma => $abstract) {
                        echo '<b>' . lang('brapci.abstract_' . $idioma) . '</b>';
                        echo '<div style="text-align: justify;" id="abstract_' . $idioma . '>' . $abstract . '</div>';

                        if (isset($keywords[$idioma])) {
                            echo '<b>' . lang('brapci.keywords_' . $idioma) . '</b>: ';
                            $keys = '';
                            foreach ($keywords[$idioma] as $id => $keyword) {
                                $keys .= trim($keyword) . '. ';
                            }
                            echo $keys;
                            echo '<br><br>';
                        }
                    }
                }
                ?>
            </p>

            <!-- COMPARTILHE -->
            <div class="mt-5 mb-5">
                <?php
                echo $MidiasSociais;
                ?>
            </div>
        </div>

        <!-- PDF -->
        <div class="col-2">
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