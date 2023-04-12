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

            <div class="container-fluid">
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="row bg-destaque">
                            <div class="col-12">
                                <?= lang('brapci.BookChapter'); ?>
                                <h1 class="h4 fw-bold"><?= $Chapter['title']; ?></h1>
                                <i><?= $authorsCP; ?></i>
                            </div>

                            <?php
                            ############################### KeywordsLN
                            if ($KeywordsLN != '') {
                            ?>
                                <div class="col-2 mt-2">
                                    <?php
                                    if (isset($Pages)) { ?>
                                        <p><b><?= lang('brapci.pages'); ?></b>
                                            <br /><?= $Pages; ?>
                                        </p>
                                    <?php } ?>
                                </div>
                                <div class="col-10 mt-2">

                                    <p><b><?= lang('brapci.keywords'); ?></b>
                                        <br /><?= $KeywordsLN; ?>
                                    </p>
                                </div>
                            <?php
                            }
                            ?>
                        </div>

                        <!--- PART II - BOOK -->
                        <div class="row mt-2  bg-destaque">
                            <div class="col-12">
                                <?= lang('brapci.Book'); ?>
                                <a href="<?= PATH . '/v/' . $ID; ?>">
                                    <h5 class="fw-bold"><?= $title; ?></h5>
                                </a>
                                <i><?= $authorsLN; ?></i>
                            </div>
                        </div>

                        <?php
                        if (isset($Chapter)) {
                            if (isset($Chapter['Fulltext'])) {
                                $FullText = (array)$Chapter['Fulltext'];
                            } else {
                                $FullText = [];
                            }

                            foreach ($FullText as $id => $txt) {
                                $ln = explode(chr(13), $txt);
                                $n = 0;
                                $more = '';
                                foreach ($ln as $id => $l) {
                                    if ($n < 4) {
                                        echo '<p class="text-justify">' . $l . '</p>';
                                    } else {
                                        $more .= '<p>' . $l . '</p>';
                                    }
                                    $n++;
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="col-12 mt-3" style="background-color: #fff;">
                        <?php
                        if (isset($Chapter['Abstract']))
                        {
                        $Chap = $Chapter['Abstract'];
                        foreach($Chap as $lang=>$text)
                            {
                                echo '<span class="fw-bold">'.lang('brapci.abstract_'.$lang).'</span>';
                                echo '<p>'.$text.'</p>';
                            }
                        }
                        ?>
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
            echo $edit;
            echo '</td>';

            echo '<td>';
            echo $license;
            echo '</td>';

            echo '</tr>';
            echo '</table>';

            ?>

        </div>
    </div>
</div>