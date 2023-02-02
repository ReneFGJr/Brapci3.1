<?php
require("_process.php");

if (!isset($MidiasSociais)) {
    $MidiasSociais = '';
}
$style = ' style="border-bottom: 1px solid #000;" ';

$langs = array('pt-BR','en','es','fr');

if (!isset($logo)) $logo = '';
if (!isset($Title)) $Title = array();
if (!isset($sub_header)) $sub_header = '';
if (!isset($edit)) $edit = '';
?>

<div class="container">
    <?= $sub_header; ?>
    <div class="row" <?= $style; ?>>
        <div class="col-2">
            <?= $logo; ?>
            <?= $edit; ?>
        </div>
        <div class="col-6">
            <?= $issue; ?>
        </div>

        <div class="col-4 mb-4 text-end p-2">
            <!--- LEGEND ------------------------------------------->
            <span class="btn btn-primary"><?= $Section; ?></span>
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
                if (isset($Title[$idioma]))
                    {
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
                    if (isset($Abstract[$idioma]))
                        {
                        echo '<b>' . lang('brapci.abstract_' . $idioma) . '</b>';
                        echo '<div style="text-align: justify; font-size: 0.9em; line-height: 120%;"
                                id="abstract_' . $idioma . '">' .
                        $Abstract[$idioma] . '</div>';
                        }

                    if (isset($Keywords[$idioma]))
                        {
                            echo '<b>' . lang('brapci.keywords_' . $idioma) . '</b>: ';
                            echo $Keywords[$idioma];
                            echo '<br><br>';
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