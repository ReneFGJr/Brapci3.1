<?php
$statusLabels = [
    0 => 'Aguardando submissão',
    1 => 'Em preparação',
    2 => 'Submetido',
    3 => 'Em avaliação',
    4 => 'Edição de texto',
    5 => 'Arquivo enviado',
    10 => 'Concluído',
];
$statusClasses = [0 => 'secondary', 1 => 'info', 2 => 'primary', 3 => 'warning', 4 => 'success', 5 => 'dark', 10 => 'success'];
?>
<main class="bg-light pt-5">
    <div class="container-fluid px-3 px-lg-5 py-5">
        <div class="card shadow-lg p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h2 mb-1">Resumo dos artigos</h1>
                    <p class="text-muted mb-0">Quantidade de registros Article por status local.</p>
                </div>
                <a class="btn btn-outline-secondary" href="<?= base_url('ojs/') ?>"><i class="bi bi-arrow-left me-1"></i>Voltar ao OJS</a>
            </div>

            <?= view('OJS/partials/selected_journal', ['journal' => $journal]) ?>

            <?php if ($error !== null): ?>
                <div class="alert alert-warning" role="alert"><?= esc($error) ?></div>
            <?php else: ?>
                <div class="alert alert-primary d-flex justify-content-between align-items-center" role="status">
                    <span>Total de artigos da revista selecionada</span>
                    <strong class="fs-4"><?= esc($totalArticles) ?></strong>
                </div>

                <div class="row g-3">
                    <?php foreach ($articlesByStatus as $status => $articles): ?>
                        <?php
                        $statusLabel = $statusLabels[$status] ?? 'Status ' . $status;
                        $statusClass = $statusClasses[$status] ?? 'secondary';
                        ?>
                        <div class="col-6 col-md-4 col-xl-3">
                            <a class="card h-100 text-decoration-none border-<?= esc($statusClass) ?>" href="<?= base_url('ojs/submit/' . $status) ?>">
                                <div class="card-body">
                                    <div class="text-muted small mb-1"><?= esc($statusLabel) ?></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="display-6 text-dark"><?= count($articles) ?></strong>
                                        <span class="badge bg-<?= esc($statusClass) ?>">Status <?= esc($status) ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($articlesByStatus === []): ?>
                    <div class="text-center text-muted py-5">Nenhum artigo encontrado para a revista selecionada.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</main>