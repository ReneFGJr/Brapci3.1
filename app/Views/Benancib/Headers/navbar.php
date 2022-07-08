<?php
$Socials = new \App\Models\Socials();
if ((isset($_SESSION['id'])) and ($_SESSION['id'] != '')) {
    $acesso = $Socials->nav_user();
} else {
    $acesso = '<li class="nav-item" style="list-style-type: none;">';
    $acesso .= '<button class="btn btn-outline-danger" ';
    $acesso .= 'onclick="location.href=/social" ';
    $acesso .= 'style="margin-left: 7px;" type="submit">';
    $acesso .= 'ACESSO';
    $acesso .= '</button>';
    $acesso .= '</li>';
}
?>
<nav class="navbar navbar-expand-lg bg-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= URL; ?>"><img src="/favicon.png" style="height: 32px;"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-battery-charging" viewBox="0 0 16 16">
                    <path
                        d="M9.585 2.568a.5.5 0 0 1 .226.58L8.677 6.832h1.99a.5.5 0 0 1 .364.843l-5.334 5.667a.5.5 0 0 1-.842-.49L5.99 9.167H4a.5.5 0 0 1-.364-.843l5.333-5.667a.5.5 0 0 1 .616-.09z" />
                    <path
                        d="M2 4h4.332l-.94 1H2a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h2.38l-.308 1H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                    <path
                        d="M2 6h2.45L2.908 7.639A1.5 1.5 0 0 0 3.313 10H2V6zm8.595-2-.308 1H12a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H9.276l-.942 1H12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.405z" />
                    <path
                        d="M12 10h-1.783l1.542-1.639c.097-.103.178-.218.241-.34V10zm0-3.354V6h-.646a1.5 1.5 0 0 1 .646.646zM16 8a1.5 1.5 0 0 1-1.5 1.5v-3A1.5 1.5 0 0 1 16 8z" />
                </svg>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?= URL . '/' . COLLECTION; ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?= URL . '/' . COLLECTION . '/about'; ?>"><?= lang('benancib.about'); ?></a>
                </li>

            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="<?= lang('benancib.search_placeholder'); ?>"
                    aria-label="<?= lang('benancib.search'); ?>">
                <button class="btn btn-outline-success" type="submit"><?= lang('benancib.search'); ?></button>
            </form>
            <?php echo $acesso; ?>
        </div>
    </div>
</nav>
<div style=" height: 100px; width: 100%">
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