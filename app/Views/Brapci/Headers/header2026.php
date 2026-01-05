<!DOCTYPE html>
<html lang="pt-BR">
<?php
$page_title = $page_title ?? 'Brapci - Homepage';

$ga = [
    'GOOGLEID' => 'G-HSS9RYF8ZS'
];

define('URL', base_url());
?>

<head>
    <meta charset="UTF-8">
    <title><?= esc($page_title) ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification"
        content="VZpzNVBfl5kOEtr9Upjmed96smfsO9p4N79DZT38toA">

    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('favicon.png') ?>" type="image/png">

    <!-- Metadata dinâmica -->
    <?= $metadata ?? '' ?>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/bootstrap-datepicker.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/brapci.css?v=0.0.4') ?>">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $ga['GOOGLEID'] ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '<?= $ga['GOOGLEID'] ?>');
    </script>

    <?= view('Google/Analytics', $ga) ?>

    <!-- JS (carregamento controlado) -->
    <script src="<?= base_url('js/jquery.min.js?v=3.6') ?>" defer></script>
    <script src="<?= base_url('js/popper.min.js') ?>" defer></script>
    <script src="<?= base_url('js/bootstrap.min.js') ?>" defer></script>
    <script src="<?= base_url('js/bootstrap-datepicker.min.js?v=1.9.0') ?>" defer></script>
    <script src="<?= base_url('js/brapci.js?v=0.1') ?>" defer></script>
    <script src="<?= base_url('js/sisdoc_form.js?v=0.1') ?>" defer></script>
</head>

<body style="min-height: 500px;">

    <!-- Loader global -->
    <div id="loading" aria-live="polite" aria-busy="true"
        style="display:none; position:fixed; top:10px; right:10px; z-index:9999;">
        <img src="<?= base_url('img/thema/wait.gif') ?>"
            alt="Carregando..."
            style="height:42px;">
        <span style="margin-left:10px;">Carregando…</span>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $(document)
                .ajaxStart(() => $('#loading').fadeIn(200))
                .ajaxStop(() => $('#loading').fadeOut(200));
        });
    </script>