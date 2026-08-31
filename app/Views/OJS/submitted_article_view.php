<?php
$titleCandidates = [
    $submission['publications'][0]['fullTitle']['pt_BR'] ?? null,
    $submission['publications'][0]['title']['pt_BR'] ?? null,
    $article['Title'] ?? null,
];
$title = '-';
foreach ($titleCandidates as $titleCandidate) {
    if (trim((string) $titleCandidate) !== '') {
        $title = trim((string) $titleCandidate);
        break;
    }
}

$articleStatus = (int) ($article['status'] ?? 0);
$ojsSubmissionUrl = rtrim((string) ($journal['base_url'] ?? ''), '/')
    . '/index.php/' . trim((string) ($journal['context_path'] ?? ''), '/')
    . '/dashboard/editorial?submissionId=' . (int) ($article['journal_submit_id'] ?? 0);
$submissionStatus = (int) ($submission['status'] ?? 0);
$submissionStage = (int) ($submission['stageId'] ?? 0);
$statusLabels = [
    1 => 'Em fluxo editorial',
    3 => 'Publicado',
    4 => 'Recusado',
    5 => 'Agendado',
];
$stageLabels = [
    1 => 'Submissão',
    2 => 'Avaliação interna',
    3 => 'Avaliação',
    4 => 'Editoração',
    5 => 'Produção',
];
$statusClasses = [
    1 => 'btn-warning',
    3 => 'btn-success',
    4 => 'btn-danger',
    5 => 'btn-info',
];
$statusLabel = $apiError !== null
    ? 'Status OJS indisponível'
    : ($statusLabels[$submissionStatus] ?? 'Status OJS ' . ($submissionStatus ?: 'desconhecido'));
if ($apiError === null && isset($stageLabels[$submissionStage]) && $submissionStatus === 1) {
    $statusLabel .= ' — ' . $stageLabels[$submissionStage];
}
$statusClass = $apiError !== null ? 'btn-outline-danger' : ($statusClasses[$submissionStatus] ?? 'btn-outline-secondary');
?>
<main class="bg-light pt-5">
    <div class="container-fluid px-3 px-lg-5 py-5">
        <div class="card shadow-lg p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h2 mb-1">Visualizar submissão</h1>
                    <p class="text-muted mb-0">Dados locais e informações atuais consultadas na API do OJS.</p>
                </div>
                <a class="btn btn-outline-secondary" href="<?= base_url('ojs/submit') ?>">
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
                <?php if ($articleStatus < 2): ?>
                <form method="post" action="<?= base_url('ojs/articles_submied/update_ojs/' . $article['idR']) ?>" onsubmit="return confirm('Atualizar os dados desta submissão no OJS?');">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary" type="submit" title="Atualizar metadados e autores no OJS">
                        <i class="bi bi-cloud-arrow-up me-1"></i>Atualizar dados da submissão
                    </button>
                </form>
                <a class="btn btn-success" href="<?= base_url('ojs/submit/edit/' . $article['idR']) ?>">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
                <?php if ($apiError === null && $submissionStatus === 1): ?>
                    <form method="post" action="<?= base_url('ojs/articles_submied/send_pdf/' . $article['idR']) ?>" onsubmit="return confirm('Enviar o arquivo <?= esc($articlePdf['name']) ?> ao OJS?');">
                        <?= csrf_field() ?>
                        <button
                            class="btn btn-outline-primary"
                            type="submit"
                            <?= $articlePdf['path'] === null ? 'disabled' : '' ?>
                            title="<?= esc($articlePdf['path'] === null ? 'O PDF não foi localizado.' : 'Enviar o PDF como Texto do artigo.') ?>"
                        >
                            <i class="bi bi-file-earmark-arrow-up me-1"></i>Enviar arquivo
                        </button>
                    </form>
                    <form method="post" action="<?= base_url('ojs/articles_submied/finalize/' . $article['idR']) ?>" onsubmit="return confirm('Finalizar esta submissão no OJS?');">
                        <?= csrf_field() ?>
                        <button class="btn btn-dark" type="submit">
                            <i class="bi bi-check-circle me-1"></i>Finalizar submissão
                        </button>
                    </form>
                <?php endif; ?>
                <?php else: ?>
                    <?php if ($articleStatus === 4): ?>
                        <a class="btn btn-info" href="<?= base_url('ojs/action/' . $article['idR'] . '/5') ?>">
                            <i class="bi bi-arrow-right-square me-1"></i>Enviar para Editoração
                        </a>
                    <?php endif; ?>                    <?php if ($articleStatus === 3 && $apiError === null && $submissionStatus === 1 && $submissionStage === 3): ?>
                        <form method="post" action="<?= base_url('ojs/articles_submied/accept_submission/' . $article['idR']) ?>" onsubmit="return confirm('Aceitar esta submissão e enviá-la para Edição de Texto no OJS?');">
                            <?= csrf_field() ?>
                            <button class="btn btn-success" type="submit">
                                <i class="bi bi-check-circle me-1"></i>Aceitar Submissão
                            </button>
                        </form>
                    <?php elseif ($apiError === null && $submissionStatus === 1 && $submissionStage === 1): ?>
                        <form method="post" action="<?= base_url('ojs/articles_submied/send_review/' . $article['idR']) ?>" onsubmit="return confirm('Enviar esta submissão para a etapa de Avaliação no OJS?');">
                            <?= csrf_field() ?>
                            <button class="btn btn-warning" type="submit">
                                <i class="bi bi-arrow-right-circle me-1"></i><?= $articleStatus === 3 ? 'Reenviar para avaliação' : 'Enviar para Avaliação' ?>
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php if ($articleStatus === 5): ?>
                        <a class="btn btn-primary" href="<?= base_url('ojs/action/' . $article['idR'] . '/6') ?>">
                            <i class="bi bi-calendar-check me-1"></i>Agendar para Publicação
                        </a>
                    <?php endif; ?>
                    <?php if ($articleStatus === 6): ?>
                        <a class="btn btn-primary" href="<?= base_url('ojs/action/' . $article['idR'] . '/6') ?>">
                            <i class="bi bi-file-earmark-arrow-up me-1"></i>Enviar arquivo de publicação
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <span
                    class="btn <?= esc($statusClass) ?> disabled"
                    role="status"
                    aria-label="<?= esc($statusLabel) ?>"
                    title="Status consultado na API do OJS ao carregar a página"
                >
                    <i class="bi bi-info-circle me-1"></i><?= esc($statusLabel) ?>
                </span>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                <div class="card-header bg-dark text-white">Artigo</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">ID local</dt><dd class="col-sm-9"><?= esc($article['idR']) ?></dd>
                        <dt class="col-sm-3">ID no OJS</dt><dd class="col-sm-9"><span class="badge bg-success"><?= esc($article['journal_submit_id']) ?></span></dd>
                        <dt class="col-sm-3">Título</dt><dd class="col-sm-9"><?= esc($title) ?></dd>
                        <dt class="col-sm-3">Autores</dt><dd class="col-sm-9"><?= esc($article['Authors'] ?? '-') ?></dd>
                        <dt class="col-sm-3">Ano</dt><dd class="col-sm-9"><?= esc($article['Year'] ?? '-') ?></dd>
                        <dt class="col-sm-3">Volume</dt><dd class="col-sm-9"><?= esc($article['Vol'] ?: '-') ?></dd>
                        <dt class="col-sm-3">Número</dt><dd class="col-sm-9"><?= esc($article['Num'] ?: '-') ?></dd>
                        <dt class="col-sm-3">Página inicial</dt><dd class="col-sm-9"><?= esc($article['PagINI'] ?: '-') ?></dd>
                        <dt class="col-sm-3">Página final</dt><dd class="col-sm-9"><?= esc($article['PagEND'] ?: '-') ?></dd>
                        <dt class="col-sm-3">Enviado em</dt><dd class="col-sm-9"><?= esc($article['submit_data'] ?? '-') ?></dd>
                    </dl>
                </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white">PDF do artigo</div>
                        <div class="card-body">
                            <?php if ($articlePdf['path'] !== null): ?>
                                <p class="small text-muted mb-2"><?= esc($articlePdf['name']) ?></p>
                                <iframe
                                    src="<?= base_url('ojs/articles_submied/pdf/' . $article['idR']) ?>"
                                    title="PDF do artigo"
                                    class="w-100 border rounded"
                                    style="height: 650px;"
                                ></iframe>
                            <?php else: ?>
                                <div class="alert alert-warning mb-0" role="alert">
                                    <strong>Arquivo não encontrado.</strong>
                                    <div class="mt-2">Arquivo esperado:</div>
                                    <code class="text-break"><?= esc($articlePdf['expected']) ?></code>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
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