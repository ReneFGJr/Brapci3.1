<!DOCTYPE html>
<?php
if (!isset($page_title)) {
    $page_title = 'Brapci - Homepage';
}
$data['GOOGLEID'] = 'G-HSS9RYF8ZS';
?>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-HSS9RYF8ZS"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-HSS9RYF8ZS');
</script>

<head>
    <title><?= $page_title; ?> </title>

    <meta name="google-site-verification" content="VZpzNVBfl5kOEtr9Upjmed96smfsO9p4N79DZT38toA" />

    <link rel="icon" href="<?= URL; ?>/favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="<?= URL; ?>/favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="<?= URL; ?>/favicon.png" />

    <?php
    if (isset($metadata)) {
        echo $metadata;
    }
    ?>

    <!---- JS -->
    <script src="<?= URL; ?>/js/popper.min.js" crossorigin="anonymous">
    </script>
    <script src="<?= URL; ?>/js/bootstrap.js" crossorigin="anonymous">
    </script>
    <!--    <script src="<?= URL; ?>/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script> -->
    <script src="<?= URL; ?>/js/jquery.min.js?v3.6" crossorigin="anonymous">
    </script>
    <script src="<?= URL; ?>/js/bootstrap-datepicker.min.js?v1.9.0" crossorigin="anonymous">
    </script>
    <script src="<?= URL; ?>/js/brapci.js?v0.1" crossorigin="anonymous"></script>
    <script src="<?= URL; ?>/js/sisdoc_form.js?v0.1" crossorigin="anonymous"></script>


    <!---- CSS -->
    <link rel="stylesheet" href="<?= URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= URL; ?>/css/bootstrap-datepicker">

    <link rel="stylesheet" href="<?= URL; ?>/css/brapci.css?v0.0.4">

    <?= view('Google/Analytics', $data); ?>
</head>
<div id="loading" style="display: none; position:absolute; top: 0px">
    <img src="<?= PATH . '/img/thema/wait.gif'; ?>" style="position: absolute; top: 0px; rigth: 0px; left: 0px; height: 42px;" />
    <span style="margin-left: 50px; margin-top: 30px;">Loading ...</span>
</div>

<script>
    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });
</script>

<body style="min-height: 500px;">