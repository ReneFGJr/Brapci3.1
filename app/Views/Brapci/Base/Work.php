<?php
require("_process.php");

if (!isset($MidiasSociais)) {
    $MidiasSociais = '';
}
$style = ' style="border-bottom: 1px solid #000;" ';

$langs = array('pt-BR', 'en', 'es', 'fr');

if (!isset($logo)) $logo = '';
if (!isset($Title)) $Title = array();
if (!isset($sub_header)) $sub_header = '';
if (!isset($edit)) $edit = '';
if (!isset($Sections)) $Sections = array();


?>

<div class="container">
    <?= $sub_header; ?>
    <div class="row" <?= $style; ?>>
        <div class="col-1">
            <?= $edit; ?>
        </div>
        <div class="col-4">
            <?= $issue; ?>
        </div>

        <div class="col-7 mb-4 text-end p-2">
            <!--- LEGEND ------------------------------------------->
            <?php
            foreach ($Sections as $id => $sectn) {
                if (strpos($sectn,';'))
                    {
                        $sectn = explode(';',$sectn);
                        echo '<span class="btn btn-primary ms-2">' .
                        '<a href="' . PATH . 'v/' . $sectn[1] . '">' .
                        $sectn[0] . '</a>'.
                        '</span>';
                    } else {
                        echo '<span class="btn btn-primary ms-2">' .

                            $sectn
                             . '</a></span>';
                    }

            }
            ?>
        </div>

    </div>
</div>


<!--TITLE -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <?php
            /******************************** TITULO */
            $H = 2;
            foreach ($langs as $idioma) {
                if (isset($Title[$idioma])) {
                    echo '<h' . $H . ' class="text-center p-3">' . $Title[$idioma] . '</h' . $H . '>';
                    $H++;
                }
            }
            ?>
        </div>
    </div>
</div>

<!--CONTENT -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- AUTHORS -->
            <div class="text-end" id="authors">
                <?php
                $authors = troca($authors, '$', '; ');
                $authors = substr($authors, 0, strlen($authors) - 2) . '.';
                echo $authors;
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-10">
            <!-- ABSTRACT -->
            <p>
                <?php
                /******************************** ABSTRACT */
                foreach ($langs as $idioma) {
                    if (isset($Abstract[$idioma])) {

                        echo '<div style="text-align: justify; font-size: 1em; line-height: 120%;"
                                id="abstract_' . $idioma . '">';
                        echo '<b>' . lang('brapci.abstract_' . $idioma) . '</b> ';
                        echo $Abstract[$idioma] . '</div>';
                    }
                    $lgi = 0;
                    foreach($Keywords as $term=>$lg)
                        {
                            if ($lg == $idioma) {
                                if ($lgi == 0)
                                    {
                                        echo '<br/><b>' . lang('brapci.keywords_' . $idioma) . '</b>: ';
                                        $lgi++;
                                    }
                                $term = explode(';',$term);
                                $url = PATH.COLLECTION.'/v/'.$term[1];
                                echo anchor($url,$term[0]);
                                echo '. ';
                            }
                        }
                    if ($lgi > 0) {
                        echo '<br/><br/>';
                    }
                }
                ?>
            </p>

            <p><?= $reference; ?></p>

            <!-- COMPARTILHE -->
            <div class="mt-5 mb-5">
                <?php
                echo $MidiasSociais;
                ?>
            </div>

            <!-- COMPARTILHE -->
            <div class="mt-5 mb-5">
                <?php
                echo $Citation;
                ?>
            </div>
        </div>

        <!-- PDF -->
        <div class="col-2">
            <?php
            /************************************************************ PDF */
            echo $files;
            ?>

            <div class="p-0" id="bug"><?= $bugs; ?></div>

            <div class="p-0" id="links" style="display: none;"><?= $links; ?></div>

            <div class="p-0" id="nlp"><?= $nlp; ?></div>

            <div class="p-0" id="views"><?= $views; ?></div>

            <div class="p-0" id="vited"><?= $cited; ?></div>
        </div>
    </div>
</div>