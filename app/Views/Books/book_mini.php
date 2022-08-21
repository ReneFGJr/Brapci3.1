<?php
require("book_metadata.php");
?>

<?= $link; ?>
<div class="container-a2">
    <ul class="caption-style-2">
        <li>
            <img src=" <?= $cover; ?>" class="img-fluidx" style="width: 100%;">
            <div class="caption">
                <div class="blur"></div>
                <div class="caption-text">
                    <b><?= $title; ?></b>
                    <br>
                    <i><?= $authors; ?></i>
                    <br>
                    (<?= $data; ?>)
                </div>
            </div>
        </li>
    </ul>
</div>
<?= $linka; ?>