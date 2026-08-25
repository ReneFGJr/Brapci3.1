<?php
$title = $submission['publications'][0]['fullTitle']['pt_BR']
    ?? $submission['publications'][0]['title']['pt_BR']
    ?? $article['Title']
    ?? '-';
?>
<main class="bg-light pt-5">
    <div class="container-fluid px-3 px-lg-5 py-5">
        <div class="card shadow-lg p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h2 mb-1">Visualizar submissão</h1>
                    <p class="text-muted mb-0">Dados locais e informações atuais consultadas na API do OJS.</p>
                </div>
                <a class="btn btn-outline-secondary" href="<?= base_url('ojs/articles_submied') ?>">
                    <i class="bi bi-arrow-left"></i> Voltar aos submetidos
                </a>
            </div>

            <?= view('OJS/partials/selected_journal', ['journal' => $journal]) ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <form method="post" action="<?= base_url('ojs/articles_submied/update_ojs/' . $article['idR']) ?>" onsubmit="return confirm('Atualizar os dados desta submissão no OJS?');">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-cloud-arrow-up me-1"></i>Atualizar dados da submissão
                    </button>
                </form>
                <a class="btn btn-success" href="<?= base_url('ojs/submit/edit/' . $article['idR']) ?>">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Artigo</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">ID local</dt><dd class="col-sm-9"><?= esc($article['idR']) ?></dd>
                        <dt class="col-sm-3">ID no OJS</dt><dd class="col-sm-9"><span class="badge bg-success"><?= esc($article['journal_submit_id']) ?></span></dd>
                        <dt class="col-sm-3">Título</dt><dd class="col-sm-9"><?= esc($title) ?></dd>
                        <dt class="col-sm-3">Autores</dt><dd class="col-sm-9"><?= esc($article['Authors'] ?? '-') ?></dd>
                        <dt class="col-sm-3">Ano</dt><dd class="col-sm-9"><?= esc($article['Year'] ?? '-') ?></dd>
                        <dt class="col-sm-3">Enviado em</dt><dd class="col-sm-9"><?= esc($article['submit_data'] ?? '-') ?></dd>
                    </dl>
                </div>
            </div>

            <?php if ($apiError !== null): ?>
                <div class="alert alert-danger" role="alert"><?= esc($apiError) ?></div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">Resposta da API do OJS</div>
                    <div class="card-body">
                        <pre class="bg-light border rounded p-3 mb-0" style="white-space:pre-wrap;max-height:600px;overflow:auto"><?= esc(json_encode($submission, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>