<main class="bg-light pt-5">
    <div class="container-fluid px-3 px-lg-5 py-5">
        <div class="card shadow-lg p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h2 mb-1">Artigos para submissão</h1>
                    <p class="text-muted mb-0">Registros ainda não vinculados a uma submissão no OJS.</p>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-success" href="<?= base_url('ojs/articles_submied') ?>">
                        <i class="bi bi-check2-circle"></i> Artigos submetidos
                    </a>
                    <a class="btn btn-outline-secondary" href="<?= base_url('ojs/') ?>">
                        <i class="bi bi-arrow-left"></i> Voltar ao OJS
                    </a>
                </div>
            </div>

            <?= view('OJS/partials/selected_journal', ['journal' => $journal]) ?>

            <form class="card card-body bg-light mb-4" method="get" action="<?= base_url('ojs/articles_to_submit') ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label" for="year">Filtrar por ano</label>
                        <input class="form-control" type="number" id="year" name="year" min="1000" max="9999"
                            placeholder="Ex.: 2025" value="<?= esc($selectedYear, 'attr') ?>">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-funnel me-1"></i>Filtrar
                        </button>
                    </div>
                    <?php if ($selectedYear !== ''): ?>
                        <div class="col-auto">
                            <a class="btn btn-outline-secondary" href="<?= base_url('ojs/articles_to_submit') ?>">Limpar filtro</a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php if ($error !== null): ?>
                <div class="alert alert-warning" role="alert"><?= esc($error) ?></div>
            <?php else: ?>
                <form method="post" action="<?= base_url('ojs/submit') ?>">
                    <?= csrf_field() ?>
                <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
                    <span class="badge bg-primary fs-6"><?= count($articles) ?> artigo(s)</span>
                    <?php if ($articles !== []): ?>
                        <button class="btn btn-sm btn-outline-primary" type="button" id="toggle-visible-articles">
                            <i class="bi bi-check2-square me-1"></i><span>Selecionar tudo</span>
                        </button>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">
                                    <input class="form-check-input" type="checkbox" id="select-all-articles" title="Selecionar todos">
                                </th>
                                <th>idR</th>
                                <th>ID</th>
                                <th style="min-width: 360px;">Título</th>
                                <th style="min-width: 260px;">Autores</th>
                                <th>Ano</th>
                                <th>Volume</th>
                                <th>Número</th>
                                <th>Páginas</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($articles === []): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        Nenhum artigo aguardando submissão para esta revista.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($articles as $article): ?>
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input article-checkbox" type="checkbox" name="articles[]" value="<?= esc($article['idR'], 'attr') ?>" aria-label="Selecionar artigo <?= esc($article['idR'], 'attr') ?>">
                                    </td>
                                    <td><?= esc($article['idR']) ?></td>
                                    <td><?= esc($article['ID'] ?? '-') ?></td>
                                    <td><?= esc($article['Title'] ?? '-') ?></td>
                                    <td><?= esc($article['Authors'] ?? '-') ?></td>
                                    <td><?= esc($article['Year'] ?? '-') ?></td>
                                    <td><?= esc($article['Vol'] ?? '-') ?></td>
                                    <td><?= esc($article['Num'] ?? '-') ?></td>
                                    <td><?= esc(trim(($article['PagINI'] ?? '') . '-' . ($article['PagEND'] ?? ''), '-')) ?: '-' ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($article['status'] ?? 0) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-success btn-lg" type="submit" id="submit-articles" disabled>
                            <i class="bi bi-cloud-arrow-up me-2"></i>Submeter no OJS
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main><script>
(() => {
    const selectAll = document.getElementById('select-all-articles');
    const toggleVisible = document.getElementById('toggle-visible-articles');
    const checkboxes = [...document.querySelectorAll('.article-checkbox')];
    const submitButton = document.getElementById('submit-articles');
    if (!selectAll || !submitButton) return;

    const refresh = () => {
        const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
        submitButton.disabled = selected === 0;
        selectAll.checked = selected > 0 && selected === checkboxes.length;
        selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
        if (toggleVisible) {
            const allSelected = checkboxes.length > 0 && selected === checkboxes.length;
            toggleVisible.querySelector('span').textContent = allSelected ? 'Desmarcar tudo' : 'Selecionar tudo';
        }
    };

    selectAll.addEventListener('change', () => {
        checkboxes.forEach((checkbox) => checkbox.checked = selectAll.checked);
        refresh();
    });    if (toggleVisible) {
        toggleVisible.addEventListener('click', () => {
            const shouldSelect = checkboxes.some((checkbox) => !checkbox.checked);
            checkboxes.forEach((checkbox) => checkbox.checked = shouldSelect);
            refresh();
        });
    }
    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', refresh));
})();
</script>