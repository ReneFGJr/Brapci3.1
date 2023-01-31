<?php
require("_process.php");
?>
<link rel="stylesheet" href="<?= URL . '/css/academicons.css'; ?>">
<div class="container">
    <div class="row">
        <div class="col-9">
            <span class="btn btn-primary btn-sm"><?= lang($class); ?></span>
            <h1 class="text-center" style="font-size: 1.6em; font-weight: 700;"><?= $titleChapet; ?></h1>
            <h6 class="text-end"><i><?= troca($authorsChapet, '$', '<br>'); ?></i></h6>

            <div class="container-fluid">
                <div class="row" style="background-color: #eee;">
                    <div class="col-2">
                        <p><b>Idioma</b>
                            <br /><?= lang('brapci.' . trim($idiomaChapet)); ?>
                        </p>
                    </div>

                    <div class="col-2">
                        <p><b>Páginas</b>
                            <br /><?= $pages; ?>
                        </p>
                    </div>
                    <div class="col-8">
                        <?php
                        ############################### DOI
                        if ((isset($DOIChapet)) and (strlen(trim($DOIChapet)) > 0)) {
                        ?>
                            <p><b>DOI</b>
                                <br /><?= $DOIChapet; ?>
                            </p>
                        <?php } ?>
                    </div>

                    <!--------- Abstract Chapter--->
                    <? if ($abstractChapt != '') { ?>
                        <div class="col-12 mt-3">
                            <b><?=lang('brapci.abstract');?></b>
                            <p><?= $abstractChapt; ?></p>
                        </div>
                    <? } ?>


                    <div class="col-12">
                        <p><b>Palavras-chave</b>
                            <br /><?= $subject; ?>
                        </p>
                    </div>


                </div>

                <!--- PART II - CHAPTER -->

                <div class="row mt-3" style="background-color: #eee;">
                    <div class="col-12">
                        <?= lang('brapci.Book'); ?>
                        <h5><?= $title; ?></h5>
                        <i><?= troca($authors, '$', '; '); ?></i>
                    </div>
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

                    <?php
                    ############################### DOI
                    if (isset($DOI)) {
                    ?>
                        <div class="col-9">
                            <p><b>DOI</b>
                                <br /><?= $DOI; ?>
                            </p>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <div class="col-12 mt-3" style="background-color: #fff;">
                    <?= $reference; ?>
                </div>

            </div>
        </div>
        <!------------------------------------------------ RIGHT --->
        <div class="col-3">
            <?= h(lang('brapci.cover'), 5); ?>
            <img src="<?= $cover; ?>" class="img-fluid shadow border border-secondary">

            <?= h(lang('brapci.access'), 5, 'mt-3'); ?>
            <?php
            echo $files;
            $WishList = new \App\Models\WishList\Index();

            echo '<table width="100%">';
            echo '<tr>';
            echo '<td>';
            echo $WishList->wishlist($id_cc);
            echo '</td>';

            echo '<td>';
            echo $license;
            echo '</td>';

            echo '<td>';
            $Socials = new \App\Models\Socials();
            if ($Socials->getAccess("#ADM#BOK#CAT")) {
                echo '<a style="display: inline;" href="' . PATH . COLLECTION . '/a/' . $id_cc . '">' . bsicone('edit', 32) . '</a>';
            }
            echo '</td>';


            echo '</tr>';
            echo '</table>';

            ?>
        </div>
    </div>
</div>