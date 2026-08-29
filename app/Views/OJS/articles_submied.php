<main class="bg-light pt-5">
    <div class="container-fluid px-3 px-lg-5 py-5">
        <div class="card shadow-lg p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h2 mb-1">Artigos submetidos</h1>
                    <p class="text-muted mb-0">Registros que já possuem uma submissão vinculada no OJS.</p>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary" href="<?= base_url('ojs/articles_to_submit') ?>">
                        <i class="bi bi-hourglass-split"></i> Aguardando submissão
                    </a>
                    <a class="btn btn-outline-secondary" href="<?= base_url('ojs/') ?>">
                        <i class="bi bi-arrow-left"></i> Voltar ao OJS
                    </a>
                </div>
            </div>

            <?= view('OJS/partials/selected_journal', ['journal' => $journal]) ?>

            <form class="card card-body bg-light mb-4" method="get" action="<?= base_url('ojs/articles_submied') ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label" for="year">Filtrar por ano</label>
                        <input class="form-control" type="number" id="year" name="year" min="1000" max="9999"
                            placeholder="Ex.: 1953" value="<?= esc($selectedYear, 'attr') ?>">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    </div>
                    <?php if ($selectedYear !== ''): ?>
                        <div class="col-auto">
                            <a class="btn btn-outline-secondary" href="<?= base_url('ojs/articles_submied') ?>">Limpar filtro</a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>

            <?php if ($error !== null): ?>
                <div class="alert alert-warning" role="alert"><?= esc($error) ?></div>
            <?php else: ?>
                <div class="d-flex justify-content-end mb-3">
                    <span class="badge bg-success fs-6"><?= count($articles) ?> artigo(s) submetido(s)</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>idR</th><th>ID OJS</th><th>ID</th>
                                <th style="min-width:360px">Título</th>
                                <th style="min-width:260px">Autores</th>
                                <th>Ano</th><th>Volume</th><th>Número</th><th>Enviado em</th><th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($articles === []): ?>
                                <tr><td colspan="10" class="text-center text-muted py-5">Nenhum artigo submetido encontrado.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($articles as $article): ?>
                                <tr>
                                    <td><?= esc($article['idR']) ?></td>
                                    <td><span class="badge bg-success"><?= esc($article['journal_submit_id']) ?></span></td>
                                    <td><?= esc($article['ID'] ?? '-') ?></td>
                                    <td><?= esc($article['Title'] ?? '-') ?></td>
                                    <td><?= esc($article['Authors'] ?? '-') ?></td>
                                    <td><?= esc($article['Year'] ?? '-') ?></td>
                                    <td><?= esc($article['Vol'] ?? '-') ?></td>
                                    <td><?= esc($article['Num'] ?? '-') ?></td>
                                    <td><?= esc($article['submit_data'] ?? '-') ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="<?= base_url('ojs/articles_submied/view/' . $article['idR']) ?>" title="Visualizar submissão" aria-label="Visualizar submissão">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>