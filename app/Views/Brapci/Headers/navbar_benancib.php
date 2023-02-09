<?php
$Socials = new \App\Models\Socials();
if ((isset($_SESSION['id'])) and ($_SESSION['id'] != '')) {
    $acesso = $Socials->nav_user();
} else {
    $acesso = '<li class="nav-item" style="list-style-type: none;">';
    $acesso .= '<button class="btn btn-outline-access" ';
    $acesso .= 'onclick="location.href=\'' . PATH . COLLECTION . '/social/login\'" ';
    $acesso .= 'style="margin-left: 7px;" type="submit">';
    $acesso .= 'ACESSO';
    $acesso .= '</button>';
    $acesso .= '</li>';
}
?>
<nav class="navbar navbar-expand-lg <?= $bg; ?> fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= URL; ?>">
            <img src="<?= URL; ?>/img/logo/logo_benancib_white.png" style="height: 18px;"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="<?= PATH . COLLECTION; ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= PATH . COLLECTION . '/about'; ?>"><?= lang('benancib.about'); ?></a>
                </li>
                <!--
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?= PATH . COLLECTION . '/statistics'; ?>"><?= lang('benancib.statistics'); ?></a>
                </li>
                -->
            </ul>
            <?php echo $acesso; ?>
        </div>
    </div>
</nav>
</div>


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Titillium Web', sans-serif;
        font-size: 120%;
    }
</style>