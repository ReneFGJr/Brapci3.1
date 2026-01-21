<div class="sidebar p-4">
    <a href="<?= base_url('/labs') ?>">
        <img src="<?= base_url('assets/brapciLabs/logo_brapci_labs.png') ?>" class="img-fluid mb-4" alt="Brapci Labs Logo">
    </a>
    <?php
    echo '<a class="" href="' . base_url('labs') . '"><i class="bi bi-house"></i> Home</a>';
    ?>
    <a class="active" href="<?= base_url('labs/projects/select') ?>"><i class="bi bi-house"></i> Projetos</a>
    <a href="<?= base_url('labs/authority') ?>"><i class="bi bi-people"></i> Autoridades</a>
    <a href="<?= base_url('labs/api-library') ?>"><i class="bi bi-clipboard-check"></i> Biblioteca API</a>
    <a href="<?= base_url('labs/oai') ?>"><i class="bi bi-tools"></i> OAI/PMH Tools</a>
    <a><i class="bi bi-bar-chart"></i> Reports</a>

    <hr>

    <a><i class="bi bi-gear"></i> Settings</a>
    <a><i class="bi bi-box-arrow-right"></i> Log out</a>
</div>