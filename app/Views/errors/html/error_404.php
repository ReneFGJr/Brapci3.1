<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>404 Page Not Found</title>
</head>
<body>
    <div class="wrap text-center">
        <h1>Concept Not Found - Conceito não localizado</h1>
        <img src="<?=URL.'/img/thema/404.png';?>">

        <p>
            <?php if (! empty($message) && $message !== '(null)') : ?>
                <?= nl2br(esc($message)) ?>
            <?php else : ?>
                Sorry! Cannot seem to find the page you were looking for.
            <?php endif ?>
        </p>
    </div>
</body>
</html>
