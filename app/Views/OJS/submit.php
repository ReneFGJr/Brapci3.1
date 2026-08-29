<?php
$successCount = count(array_filter($results, static fn (array $result): bool => $result['success']));
$errorCount = count($results) - $successCount;
?>
<main class="bg-light pt-5">
    <div class="container-fluid px-3 px-lg-5 py-5">
        <div class="card shadow-lg p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h2 mb-1">Resultado do envio ao OJS</h1>
                    <p class="text-muted mb-0">Foram criados apenas rascunhos; nenhuma submissão foi concluída.</p>
                </div>
                <a class="btn btn-outline-secondary" href="<?= base_url('ojs/articles_to_submit') ?>">
                    <i class="bi bi-arrow-left"></i> Voltar aos artigos
                </a>
            </div>

            <?= view('OJS/partials/selected_journal', ['journal' => $journal]) ?>

            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="alert alert-primary mb-0"><strong><?= count($results) ?></strong> processado(s)</div>
                </div>
                <div class="col-sm-4">
                    <div class="alert alert-success mb-0"><strong><?= $successCount ?></strong> rascunho(s) criado(s)</div>
                </div>
                <div class="col-sm-4">
                    <div class="alert <?= $errorCount ? 'alert-danger' : 'alert-secondary' ?> mb-0"><strong><?= $errorCount ?></strong> erro(s)</div>
                </div>
            </div>

            <?php foreach ($results as $index => $result): ?>
                <article class="card mb-3 border-<?= $result['success'] ? 'success' : 'danger' ?>">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <strong>#<?= esc($result['article_id']) ?> — <?= esc($result['title']) ?></strong>
                        </div>
                        <div>
                            <span class="badge bg-secondary">HTTP <?= esc($result['http_code']) ?></span>
                            <?php if ($result['success']): ?>
                                <span class="badge bg-success">Rascunho OJS #<?= esc($result['submission_id']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">Falha</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($result['error']): ?>
                            <div class="alert alert-danger"><?= esc($result['error']) ?></div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-lg-6">
                                <h2 class="h6">Dados enviados</h2>
                                <pre class="bg-light border rounded p-3 mb-0 small" style="white-space: pre-wrap;"><?= esc(json_encode($result['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
                            </div>
                            <div class="col-lg-6">
                                <h2 class="h6">Resposta do OJS</h2>
                                <pre class="bg-light border rounded p-3 mb-0 small" style="white-space: pre-wrap; max-height: 360px; overflow: auto;"><?= esc(json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</main>