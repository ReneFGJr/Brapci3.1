<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Clusterização das referências</h1>
        <a href="<?= site_url('labs') ?>" class="btn btn-outline-primary">Voltar</a>
    </div>

    <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $color): ?>
        <?php if ($message = session()->getFlashdata($key)): ?>
            <div class="alert alert-<?= esc($color) ?>"><?= esc($message) ?></div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="card card-dashboard border border-dark border-2 p-4">
        <?php if (!isset($selectedReferences)): ?>
            <form method="get" action="<?= site_url('labs/cited/cluter') ?>" class="row g-2 mb-4">
                <div class="col-md-10">
                    <label for="reference-search" class="form-label">Buscar na descrição da referência</label>
                    <input type="search" id="reference-search" name="q" class="form-control"
                        value="<?= esc($query) ?>" placeholder="Digite parte da referência" required>
                    <div class="form-text">Separe os termos por espaços. Todos os termos devem aparecer na referência.</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label invisible" aria-hidden="true">Buscar</label>
                    <button type="submit" class="btn btn-primary w-100 d-block">Buscar</button>
                </div>
            </form>

            <?php if ($query !== ''): ?>
                <h2 class="h5 mb-3">Resultados para “<?= esc($query) ?>”</h2>
                <?php if (empty($references)): ?>
                    <p class="text-muted mb-0">Nenhuma referência encontrada.</p>
                <?php else: ?>
                    <form method="post" action="<?= site_url('labs/cited/cluter/prepare') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="q" value="<?= esc($query) ?>">
                        <p class="text-muted">Selecione pelo menos duas referências. São exibidos até 100 registros.</p>
                        <button type="button" id="select-all-references" class="btn btn-outline-primary btn-sm mb-3">Selecionar todos</button>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">Selecionar</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">Referência</th>
                                        <th scope="col">Ano</th>
                                        <th scope="col">DOI</th>
                                        <th scope="col">Aproximação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $currentGroupKey = null; ?>
                                    <?php foreach ($references as $reference): ?>
                                        <?php
                                            $groupKey = $reference['similarity_group'] === null
                                                ? 'isolated'
                                                : 'group-' . $reference['similarity_group'];
                                        ?>
                                        <?php if ($groupKey !== $currentGroupKey): ?>
                                            <?php $currentGroupKey = $groupKey; ?>
                                            <tr class="table-dark">
                                                <th colspan="6">
                                                    <?php if ($reference['similarity_group'] === null): ?>
                                                        Sem grupo — aproximação inferior a 70%
                                                    <?php else: ?>
                                                        Grupo <?= (int) $reference['similarity_group'] ?>
                                                        — <?= (int) $reference['similarity_group_size'] ?> referências similares
                                                    <?php endif; ?>
                                                </th>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input reference-checkbox" type="checkbox" name="references[]"
                                                    value="<?= (int) $reference['id_ca'] ?>"
                                                    aria-label="Selecionar referência <?= (int) $reference['id_ca'] ?>">
                                            </td>
                                            <td><?= (int) $reference['id_ca'] ?></td>
                                            <td><?= esc($reference['ca_text'] ?? '') ?></td>
                                            <td><?= esc($reference['ca_year'] ?? '') ?></td>
                                            <td><?= esc($reference['ca_doi'] ?? '') ?></td>
                                            <td class="text-nowrap">
                                                <strong><?= number_format((float) ($reference['approximation'] ?? 0), 1, ',', '.') ?>%</strong>
                                                <?php if (!empty($reference['closest_reference_id'])): ?>
                                                    <small class="d-block text-muted">com #<?= (int) $reference['closest_reference_id'] ?></small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary">Agrupar</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <h2 class="h5 mb-3">Confirmar agrupamento</h2>
            <form method="post" action="<?= site_url('labs/cited/cluter/group') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="q" value="<?= esc($query) ?>">
                <?php foreach ($selectedReferences as $reference): ?>
                    <input type="hidden" name="references[]" value="<?= (int) $reference['id_ca'] ?>">
                <?php endforeach; ?>

                <section class="mb-4">
                    <h3 class="h6">Referências semelhantes em cited_normalize</h3>
                    <?php if (empty($normalizedCandidates)): ?>
                        <p class="text-muted">Nenhum registro normalizado com DOI igual ou texto semelhante foi encontrado.</p>
                    <?php else: ?>
                        <?php foreach ($normalizedCandidates as $candidate): ?>
                            <div class="form-check border rounded p-3 ps-5 mb-2">
                                <input class="form-check-input border border-dark border-2" type="radio" name="normalized_id"
                                    id="normalized-<?= (int) $candidate['id_ca'] ?>"
                                    value="<?= (int) $candidate['id_ca'] ?>" data-doi="<?= esc($candidate['ca_doi_normalized'] ?? '', 'attr') ?>">
                                <label class="form-check-label" for="normalized-<?= (int) $candidate['id_ca'] ?>">
                                    <strong>#<?= (int) $candidate['id_ca'] ?> — <?= esc($candidate['match_reason']) ?></strong><br>
                                    <?= esc($candidate['ca_text'] ?? '') ?><br>
                                    <small class="text-muted">DOI: <?= esc($candidate['ca_doi'] ?? 'não informado') ?></small>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="form-check mt-2">
                        <input class="form-check-input border border-dark border-2" type="radio" name="normalized_id" id="create-normalized"
                            value="0" <?= empty($normalizedCandidates) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="create-normalized">Criar um novo registro normalizado</label>
                    </div>
                </section>

                <section class="mb-4">
                    <h3 class="h6">Referência padrão</h3>
                    <p class="text-muted">Escolha o texto que será usado caso um novo registro normalizado precise ser criado.</p>
                    <div id="doi-check-result" class="alert d-none" role="status"></div>
                    <?php foreach ($selectedReferences as $reference): ?>
                        <div class="form-check border rounded p-3 ps-5 mb-2">
                            <input class="form-check-input border border-dark border-2" type="radio" name="standard_id"
                                id="standard-<?= (int) $reference['id_ca'] ?>"
                                value="<?= (int) $reference['id_ca'] ?>" data-doi="<?= esc($reference['ca_doi_normalized'] ?? '', 'attr') ?>">
                            <label class="form-check-label" for="standard-<?= (int) $reference['id_ca'] ?>">
                                <strong>#<?= (int) $reference['id_ca'] ?></strong> — <?= esc($reference['ca_text'] ?? '') ?><br>
                                <small class="text-muted">DOI: <?= esc($reference['ca_doi'] ?? 'não informado') ?></small>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </section>

                <button type="submit" class="btn btn-primary">Confirmar agrupamento</button>
                <a href="<?= site_url('labs/cited/cluter') . '?q=' . rawurlencode($query) ?>" class="btn btn-outline-secondary">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('select-all-references');
    if (button) {
        const checkboxes = Array.from(document.querySelectorAll('.reference-checkbox'));
        const updateButton = function () {
            const allSelected = checkboxes.length > 0 && checkboxes.every(checkbox => checkbox.checked);
            button.textContent = allSelected ? 'Desmarcar todos' : 'Selecionar todos';
        };

        button.addEventListener('click', function () {
            const selectAll = !checkboxes.every(checkbox => checkbox.checked);
            checkboxes.forEach(checkbox => checkbox.checked = selectAll);
            updateButton();
        });

        checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateButton));
    }

    const standardRadios = Array.from(document.querySelectorAll('input[name="standard_id"]'));
    const normalizedRadios = Array.from(document.querySelectorAll('input[name="normalized_id"][data-doi]'));
    const createNormalized = document.getElementById('create-normalized');
    const doiResult = document.getElementById('doi-check-result');

    standardRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (!doiResult || !createNormalized) return;

            const doi = radio.dataset.doi || '';
            const existing = normalizedRadios.find(candidate => candidate.dataset.doi === doi && doi !== '');
            doiResult.className = 'alert';

            if (existing) {
                existing.checked = true;
                doiResult.classList.add('alert-success');
                doiResult.textContent = 'O DOI ' + doi + ' já está cadastrado em cited_normalize (#' + existing.value + '). Esse registro foi selecionado automaticamente.';
            } else {
                createNormalized.checked = true;
                doiResult.classList.add(doi === '' ? 'alert-warning' : 'alert-info');
                doiResult.textContent = doi === ''
                    ? 'A referência padrão selecionada não possui DOI. Será criado um novo registro normalizado.'
                    : 'O DOI ' + doi + ' ainda não está cadastrado. Será criado um novo registro normalizado.';
            }
        });
    });
});
</script>