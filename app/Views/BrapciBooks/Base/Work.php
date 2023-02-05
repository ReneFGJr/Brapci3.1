<div class="container">
    <div class="row">
        <div class="col-2">
            <?= view("Benancib/Base/Header"); ?>
        </div>
        <div class="col-10">
            <?= view("Benancib/Base/Header_proceeding"); ?>

        </div>
    </div>

    <div class="row">
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

    <div class="row">
        <div class="col-12">
            <?php
            /******************************** TITULO */
            $H = 1;
            foreach ($Title as $idioma => $titulo) {
                echo 'xx<h' . $H . '>' . $titulo . '</h' . $H . '>';
                $H++;
            }
            ?>
        </div>
        <!------------------------ Authors ---------------------->
        <div class="col-10">
            <div class="text-end" id="authors">
                <?php
                $id = 0;
                foreach ($authors as $id => $author) {
                    if ($id > 0) {
                        echo '; ';
                    }
                    if (strpos($author,';'))
                        {
                            $name = explode(';',$author);
                            $url = PATH.COLLECTION.'/v/'.$name[1];
                            echo anchor($url,$name[0]);
                        } else {
                            echo $author;
                        }

                    $id++;
                }
                echo '.';
                ?>
            </div>


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
        </div>

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