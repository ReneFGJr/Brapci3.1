<?php
require("_process.php");
?>
<link rel="stylesheet" href="<?= URL . '/css/academicons.css'; ?>">
<div class="container">
    <div class="row">
        <div class="col-12">
            <?php
            $bread = array();
            $bread[lang('book.books')] = PATH . COLLECTION;
            $bread[$title] = PATH . COLLECTION . '/v/' . $ID;
            echo breadcrumbs($bread); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-9">
            <span class="btn btn-primary btn-sm"><?= lang($class); ?></span>
            <h1 class="text-center" style="font-size: 1.6em; font-weight: 700;"><?= $title; ?></h1>
            <h6 class="text-end"><i><?= troca($authors, '$', '<br>'); ?></i></h6>

            <div class="container-fluid">
                <div class="row bg-destaque">
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
                        <?php
                        if ($pages != '') {
                            echo '<p><b>Páginas</b><br />' . $pages . '</p>';
                        }
                        ?>
                    </div>

                    <div class="col-9">
                        <?php
                        ############################### DOI
                        if ($KeywordsLN != '') {
                        ?>
                            <p><b><?= lang('brapci.keywords'); ?></b>
                                <br /><?= $KeywordsLN; ?>
                            </p>
                        <?php
                        }
                        ?>
                    </div>



                    <?php
                    ############################### DOI
                    if (isset($Classification)) {
                    ?>
                        <div class="col-3">
                        </div>
                        <div class="col-9">
                            <p>
                            <p><b><?= lang('brapci.classification'); ?></b>
                                <br /><?= $Classification; ?>
                            </p>
                        </div>
                    <?php
                    }
                    ?>

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

                <div class="row">

                    <!--- PART II - SUMMARY -->
                    <div class="col-12 summary mt-3" style="background-color: #fff;">
                        <?= $summary; ?>
                    </div>

                    <div class="col-12 mt-3" style="background-color: #fff;">
                        <?= $reference; ?>
                    </div>

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
            echo $edit;
            echo '</td>';

            echo '</tr>';
            echo '</table>';

            ?>
        </div>
    </div>
</div>