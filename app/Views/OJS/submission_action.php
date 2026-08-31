<?php
$currentStatus = (int) ($article['status'] ?? 0);
$target = $flow[$targetStatus] ?? null;
$displayResult = $result === null ? null : [
    'httpCode' => $result['httpCode'] ?? null,
    'response' => $result['response'] ?? null,
    'payload' => $result['payload'] ?? null,
    'steps' => $result['steps'] ?? null,
];
?>
<main class="bg-light pt-5">
    <div class="container px-3 py-5">
        <div class="card shadow-lg p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h2 mb-1">Ação da submissão</h1>
                    <p class="text-muted mb-0"><?= esc($article['Title'] ?? 'Artigo ' . $article['idR']) ?></p>
                </div>
                <a class="btn btn-outline-secondary" href="<?= base_url('ojs/view/' . $article['idR']) ?>">
                    <i class="bi bi-arrow-left me-1"></i>Voltar para visualizar o trabalho
                </a>
            </div>

            <?= view('OJS/partials/selected_journal', ['journal' => $journal]) ?>

            <div class="row g-2 mb-4">
                <?php foreach ($flow as $status => $step): ?>
                    <?php
                    $active = $status === $currentStatus;
                    $requested = $status === $targetStatus;
                    ?>
                    <div class="col-6 col-lg-3">
                        <div class="card h-100 <?= $active ? 'border-success' : ($requested ? 'border-primary' : '') ?>">
                            <div class="card-body">
                                <span class="badge <?= $active ? 'bg-success' : ($requested ? 'bg-primary' : 'bg-secondary') ?> mb-2">Status <?= esc($status) ?></span>
                                <div class="fw-semibold"><?= esc($step['label']) ?></div>
                                <small class="text-muted">Etapa OJS <?= esc($step['stageId']) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($error !== null): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>A ação não foi concluída.</strong><br><?= esc($error) ?>
                </div>
            <?php elseif ($executed && $result !== null): ?>
                <div class="alert alert-success" role="alert">
                    <?php if ($uploadPublicationOnly): ?>
                        <strong>Arquivo enviado.</strong> O PDF foi incluído no OJS como Publicação: Composição Final.
                    <?php else: ?>
                        <strong>Ação concluída.</strong> O trabalho avançou para <?= esc($target['label'] ?? 'o status solicitado') ?>.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    <?php if ($uploadPublicationOnly): ?>
                        O PDF submetido será enviado ao OJS como Publicação: Composição Final, com o rótulo PDF.
                    <?php else: ?>
                        O trabalho será enviado do status <?= esc($currentStatus) ?> para o status <?= esc($targetStatus) ?> — <?= esc($target['label'] ?? 'ação desconhecida') ?>.
                    <?php endif; ?>
                </div>
                <form method="post" action="<?= base_url('ojs/action/' . $article['idR'] . '/' . $targetStatus . '/execute') ?>" onsubmit="return confirm('Confirma esta ação no fluxo editorial do OJS?');">
                    <?= csrf_field() ?>
                    <?php if ($targetStatus === 6): ?>
                        <div class="card card-body bg-light mb-3">
                            <div class="fw-semibold mb-1">Publicação: Composição Final</div>
                            <?php if ($articlePdf['path'] !== null): ?>
                                <div><span class="badge bg-danger me-2">PDF</span><?= esc($articlePdf['name']) ?></div>
                                <small class="text-muted">O mesmo arquivo submetido será enviado pela API como Composição Final, com o rótulo PDF.</small>
                            <?php else: ?>
                                <div class="text-danger">PDF não encontrado: <?= esc($articlePdf['expected'] ?? '-') ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (!$uploadPublicationOnly): ?>
                        <div class="mb-3">
                            <label class="form-label" for="issue_id">Edição para publicação — ano <?= esc($article['Year'] ?? '-') ?></label>
                            <select class="form-select" id="issue_id" name="issue_id" required>
                                <option value="">Selecione uma edição aberta</option>
                                <?php foreach ($openIssues as $issue): ?>
                                    <?php
                                    $issueId = is_object($issue) ? (int) ($issue->id ?? 0) : (int) ($issue['id'] ?? 0);
                                    $issueTitle = is_object($issue) ? ($issue->identification ?? $issue->title ?? null) : ($issue['identification'] ?? $issue['title'] ?? null);
                                    if (is_object($issueTitle)) {
                                        $issueTitle = reset($issueTitle = get_object_vars($issueTitle));
                                    } elseif (is_array($issueTitle)) {
                                        $issueTitle = reset($issueTitle);
                                    }
                                    ?>
                                    <option value="<?= esc($issueId, 'attr') ?>" <?= $selectedIssueId === $issueId ? 'selected' : '' ?>>
                                        <?= esc($issueTitle ?: 'Edição ' . $issueId) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($openIssues === []): ?>
                                <div class="form-text text-danger">Nenhuma edição aberta foi encontrada para o ano <?= esc($article['Year'] ?? '-') ?>.</div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <button class="btn btn-primary" type="submit" <?= $targetStatus === 6 && ($articlePdf['path'] === null || (!$uploadPublicationOnly && $openIssues === [])) ? 'disabled' : '' ?>>
                        <i class="bi bi-play-circle me-1"></i><?= $uploadPublicationOnly ? 'Enviar arquivo de publicação' : 'Executar ação' ?>
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($displayResult !== null): ?>
                <div class="card mt-4">
                    <div class="card-header fw-semibold">Resultado retornado pelo OJS</div>
                    <div class="card-body">
                        <pre class="bg-dark text-light p-3 rounded mb-0" style="white-space: pre-wrap;"><?= esc(json_encode($displayResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($submissionBefore !== [] || $submissionAfter !== []): ?>
                <div class="row g-3 mt-1">
                    <div class="col-md-6"><div class="card card-body"><strong>Antes</strong><span>Etapa OJS: <?= esc($submissionBefore['stageId'] ?? '-') ?></span></div></div>
                    <div class="col-md-6"><div class="card card-body"><strong>Depois</strong><span>Etapa OJS: <?= esc($submissionAfter['stageId'] ?? '-') ?></span></div></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>