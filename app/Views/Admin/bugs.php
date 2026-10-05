<?php
$base = rtrim(PATH, '/') . '/admin/bugs';
$labels = [
    'pdf_not' => 'PDF indisponível', 'pdfInaccessible' => 'PDF inacessível',
    'pdf_err' => 'PDF incorreto', 'pdfIncorrect' => 'PDF incorreto',
    'abstract' => 'Resumo', 'key' => 'Palavras-chave', 'title' => 'Título',
    'authors' => 'Autores', 'authorincorrect' => 'Autor incorreto', 'other' => 'Outro',
];
$pageUrl = static function ($number) use ($base, $status, $problem, $search) {
    return $base . '?' . http_build_query(['status' => $status, 'problem' => $problem, 'q' => $search, 'page' => $number]);
};
?>
<section class="py-4" aria-labelledby="bugs-title">
    <h1 id="bugs-title" class="h3">Tratamento de BUGs</h1>
    <p class="text-muted">Revise os problemas relatados e registre a solução aplicada.</p>
    <div class="row g-3 mb-4">
        <?php foreach (['1' => 'Pendentes', '2' => 'Resolvidos'] as $value => $label): ?>
            <div class="col-6">
                <a class="card text-decoration-none text-body" href="<?= esc($base . '?status=' . $value, 'attr') ?>">
                    <div class="card-body"><span class="text-muted"><?= $label ?></span><strong class="d-block fs-3"><?= $totals[$value] ?></strong></div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
    <form method="get" action="<?= esc($base, 'attr') ?>" class="card card-body mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="bugs-search" class="form-label">Buscar por ID, solicitante ou tipo</label>
                <input id="bugs-search" name="q" class="form-control" value="<?= esc($search, 'attr') ?>" maxlength="200" type="search">
            </div>
            <div class="col-md-2">
                <label for="bugs-status" class="form-label">Status</label>
                <select id="bugs-status" name="status" class="form-select">
                    <?php foreach (['1' => 'Pendentes', '2' => 'Resolvidos', 'all' => 'Todos'] as $value => $label): ?>
                        <option value="<?= $value ?>" <?= (string) $value === $status ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="bugs-type" class="form-label">Tipo de problema</label>
                <select id="bugs-type" name="problem" class="form-select">
                    <option value="">Todos os tipos</option>
                    <?php foreach ($types as $type): $value = $type['bug_problem']; ?>
                        <option value="<?= esc($value, 'attr') ?>" <?= $value === $problem ? 'selected' : '' ?>><?= esc($labels[$value] ?? $value) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Filtrar</button>
                <a class="btn btn-outline-secondary" href="<?= esc($base, 'attr') ?>">Limpar</a>
            </div>
        </div>
    </form>
    <p class="text-muted"><?= $total ?> relato(s) encontrado(s) · Página <?= $page ?> de <?= $pages ?></p>
    <?php if (!$reports): ?>
        <div class="alert alert-info" role="status">Nenhum relato encontrado para os filtros selecionados.</div>
    <?php endif ?>
    <?php foreach ($reports as $report): $pending = (int) $report['bug_status'] === 1; $id = (int) $report['id_bug']; ?>
        <article class="card mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h2 class="h6 mb-0">#<?= $id ?> · <?= esc($labels[$report['bug_problem']] ?? $report['bug_problem']) ?></h2>
                <span class="badge <?= $pending ? 'bg-warning text-dark' : 'bg-success' ?>"><?= $pending ? 'Pendente' : 'Resolvido' ?></span>
            </div>
            <div class="card-body">
                <p><strong>Registro:</strong> <a href="<?= esc(rtrim(PATH, '/') . '/v/' . (int) $report['bug_v'], 'attr') ?>" target="_blank" rel="noopener">#<?= (int) $report['bug_v'] ?> (abrir)</a>
                    <span class="ms-3"><strong>Solicitante:</strong> <?= esc($report['bug_name'] ?: 'Não informado') ?></span></p>
                <?php if (!empty($report['bug_description'])): ?>
                    <p class="mb-1"><strong>Descrição</strong></p>
                    <div class="mb-3 text-break" style="white-space: pre-wrap"><?= esc($report['bug_description']) ?></div>
                <?php endif ?>
                <?php $url = $report['bug_url'] ?? ''; if (filter_var($url, FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($url, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true)): ?>
                    <p class="text-break"><strong>URL relatada:</strong> <a href="<?= esc($url, 'attr') ?>" target="_blank" rel="noopener noreferrer"><?= esc($url) ?></a></p>
                <?php endif ?>
                <?php if ($pending): ?>
                    <details>
                        <summary class="text-primary">Registrar solução e concluir</summary>
                        <form class="mt-3" method="post" action="<?= esc($base . '/corrected/' . $id . '?' . http_build_query(['status' => $status, 'problem' => $problem, 'q' => $search, 'page' => $page]), 'attr') ?>">
                            <?= csrf_field() ?>
                            <label class="form-label" for="solution-<?= $id ?>">Solução aplicada (opcional)</label>
                            <textarea class="form-control mb-2" id="solution-<?= $id ?>" name="solution" rows="3" maxlength="2000"></textarea>
                            <button type="submit" class="btn btn-success">Marcar como resolvido</button>
                        </form>
                    </details>
                <?php elseif (!empty($report['bug_solution'])): ?>
                    <p class="mb-1"><strong>Solução</strong></p>
                    <div class="text-break" style="white-space: pre-wrap"><?= esc($report['bug_solution']) ?></div>
                <?php endif ?>
            </div>
        </article>
    <?php endforeach ?>
    <?php if ($pages > 1): ?>
        <nav aria-label="Paginação de relatos" class="d-flex gap-2 align-items-center">
            <?php if ($page > 1): ?><a class="btn btn-outline-primary" href="<?= esc($pageUrl($page - 1), 'attr') ?>">Anterior</a><?php endif ?>
            <span><?= $page ?> / <?= $pages ?></span>
            <?php if ($page < $pages): ?><a class="btn btn-outline-primary" href="<?= esc($pageUrl($page + 1), 'attr') ?>">Próxima</a><?php endif ?>
        </nav>
    <?php endif ?>
</section>
