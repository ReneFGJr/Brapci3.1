<?php
require("book_metadata.php");
?>
<div class="container">
    <div class="row">
        <div class="col-8">
            <h1><?= $title; ?></h1>
            <h4><i><?= $authors; ?></i></h4>
            <p><?= $isbn; ?></p>
            <p><?= $idioma; ?></p>
            <p>Editora: <?= $editora; ?></p>
            <p>Palavras-chave: <?= $subject; ?></p>
        </div>
        <div class="col-4">
            <img src="<?= $cover; ?>" class="img-fluid shadow border border-secondary">
        </div>
    </div>
</div>