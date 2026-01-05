<?php

use App\Models\Socials;

$admin  = '';
$acesso = '';

$Socials = new Socials();
$session = session();

/* ================= PERFIL / ACESSO ================= */
if ($session->has('id') && $session->get('id')) {

    $acesso = $Socials->nav_user();

    if ($Socials->getAccess('#ADM#CAT')) {
        $admin .= view('partials/navbar_item', [
            'title' => lang('brapci.administrator'),
            'url'   => site_url('admin/' . COLLECTION),
            'icon'  => bsicone('gear')
        ]);
    }

    $admin .= view('partials/navbar_item', [
        'title' => lang('brapci.tools_bibliografic'),
        'url'   => site_url('tools/' . COLLECTION),
        'icon'  => bsicone('tools')
    ]);
} else {

    $acesso = view('partials/navbar_login_button');
}
?>
<nav class="navbar navbar-expand-lg <?= esc($bg ?? '') ?> fixed-top d-print-none">
    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand" href="<?= base_url() ?>">
            <img src="<?= base_url('favicon.png') ?>" alt="Brapci" height="32">
        </a>

        <!-- Toggle -->
        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="<?= lang('brapci.menu_toggle') ?>">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- Menu principal -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <?php if (!empty($menu)): ?>
                    <?php foreach ($menu as $path => $label): ?>
                        <li class="nav-item">
                            <a class="nav-link-brp" href="<?= esc($path) ?>">
                                <?= esc($label) ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                <?php endif ?>

                <?= view('_navbar_index') ?>
            </ul>

            <!-- Menu lateral -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link-brp"
                        href="<?= site_url('about/brapci') ?>">
                        <?= lang('brapci.about') ?>
                    </a>
                </li>
                <?= $admin ?>
            </ul>

            <?= $acesso ?>
        </div>
    </div>
</nav>

<!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Titillium Web', sans-serif;
        font-size: 120%;
    }
</style>