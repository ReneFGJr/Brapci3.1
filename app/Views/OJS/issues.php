<?php
$localized = static function ($value): string {
    if (is_string($value) || is_numeric($value)) {
        return (string) $value;
    }
    if (is_object($value)) {
        $translations = get_object_vars($value);
        return (string) ($value->pt_BR ?? $value->en ?? $value->en_US ?? reset($translations) ?: '');
    }
    if (is_array($value)) {
        return (string) ($value['pt_BR'] ?? $value['en'] ?? $value['en_US'] ?? reset($value) ?? '');
    }
    return '';
};

$renderIssues = static function (array $items) use ($localized): void {
    if ($items === []) {
        echo '<div class="alert alert-info mb-0">Nenhuma edição encontrada nesta categoria.</div>';
        return;
    }
    echo '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
    echo '<thead class="table-light"><tr><th>ID</th><th>Edição</th><th>Volume</th><th>Número</th><th>Ano</th><th>Publicação</th></tr></thead><tbody>';
    foreach ($items as $issue) {
        $identification = $localized($issue->identification ?? $issue->title ?? '');
        echo '<tr>';
        echo '<td>' . esc($issue->id ?? '-') . '</td>';
        echo '<td><strong>' . esc($identification !== '' ? $identification : 'Sem identificação') . '</strong></td>';
        echo '<td>' . esc($issue->volume ?? '-') . '</td>';
        echo '<td>' . esc($issue->number ?? '-') . '</td>';
        echo '<td>' . esc($issue->year ?? '-') . '</td>';
        echo '<td>' . esc($issue->datePublished ?? '-') . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
};
?>
<main class="bg-light pt-5">
    <div class="container-fluid px-3 px-lg-5 py-5">
        <div class="card shadow-lg p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <h1 class="h2 mb-0">Edições do OJS</h1>
                <a class="btn btn-outline-secondary" href="<?= base_url('ojs/submissoes') ?>">
                    <i class="bi bi-arrow-left"></i> Voltar às submissões
                </a>
            </div>

            <?= view('OJS/partials/selected_journal', ['journal' => $journal]) ?>

            <?php if ($apiError !== null): ?>
                <div class="alert alert-danger" role="alert"><?= esc($apiError) ?></div>
            <?php else: ?>
                <section class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-globe2 me-2"></i>Edições publicadas
                        <span class="badge bg-light text-success ms-2"><?= count($publishedIssues) ?></span>
                    </div>
                    <div class="card-body p-0"><?php $renderIssues($publishedIssues); ?></div>
                </section>

                <section class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="bi bi-unlock me-2"></i>Edições abertas / não publicadas
                        <span class="badge bg-dark ms-2"><?= count($openIssues) ?></span>
                    </div>
                    <div class="card-body p-0"><?php $renderIssues($openIssues); ?></div>
                </section>
            <?php endif; ?>
        </div>
    </div>
</main>