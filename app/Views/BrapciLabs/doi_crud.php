<main class="content">
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-4">
        <h1 class="h3">DOIs — status <?= esc($status) ?></h1>
        <div>
            <a class="btn btn-primary" href="<?= site_url('labs/view/' . $status) ?>?new=1">Novo registro</a>
            <a class="btn btn-outline-primary" href="<?= site_url('labs/cited_process') ?>">Voltar</a>
        </div>
    </div>
    <section class="card card-dashboard p-3 mb-4">
        <h2 class="h5">Resumo por origem</h2>
        <p class="text-muted">Totais do status <?= esc($status) ?><?= $q !== '' ? ', considerando a busca atual' : '' ?>.</p>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th scope="col">Origem (doi_ref)</th><th scope="col">ID da origem</th><th scope="col" class="text-end">Quantidade</th></tr></thead>
                <tbody>
                    <?php foreach ($sourceStats as $source): ?>
                        <tr>
                            <td><?= esc($source['doi_ref'] ?? 'Não informado') ?></td>
                            <td><?= esc($source['id_source'] ?? 'Sem correspondência') ?></td>
                            <td class="text-end"><?= number_format((int) $source['total'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$sourceStats): ?><tr><td colspan="3">Nenhum registro encontrado.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $color): ?>
        <?php if ($message = session()->getFlashdata($key)): ?>
            <div class="alert alert-<?= esc($color) ?>"><?= esc($message) ?></div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($record !== null): ?>
        <section class="card card-dashboard p-4 mb-4">
            <h2 class="h5"><?= $record['id_doi'] ? 'Editar registro #' . esc($record['id_doi']) : 'Novo registro' ?></h2>
            <form method="post" action="<?= site_url('labs/view/' . $status . '/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id_doi" value="<?= esc($record['id_doi']) ?>">
                <label for="doi_ID" class="form-label">DOI</label>
                <input id="doi_ID" name="doi_ID" class="form-control mb-3" maxlength="100" required value="<?= esc(old('doi_ID', $record['doi_ID'], false)) ?>">
                <label for="doi_status" class="form-label">Status</label>
                <input id="doi_status" name="doi_status" type="number" min="0" max="2147483647" required class="form-control mb-3" value="<?= esc(old('doi_status', $record['doi_status'], false)) ?>">
                <p>Criado em: <?= esc($record['doi_created_at'] ?: 'Preenchido ao cadastrar') ?></p>
                <label for="doi_content" class="form-label">Conteúdo JSON</label>
                <textarea id="doi_content" name="doi_content" class="form-control font-monospace mb-3" rows="18"><?= esc(old('doi_content', $record['doi_content'], false)) ?></textarea>
                <button class="btn btn-primary" type="submit">Salvar</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('labs/view/' . $status) ?>">Cancelar</a>
            </form>
        </section>
    <?php endif; ?>
    <section class="card card-dashboard p-4">
        <form method="get" action="<?= site_url('labs/view/' . $status) ?>" class="d-flex gap-2 mb-3">
            <label for="q" class="visually-hidden">Buscar DOI ou conteúdo</label>
            <input id="q" name="q" class="form-control" value="<?= esc($q) ?>" placeholder="Buscar DOI ou conteúdo JSON">
            <button class="btn btn-primary" type="submit">Buscar</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('labs/view/' . $status) ?>">Limpar</a>
        </form>
        <p><?= (int) $pager->getTotal() ?> registro(s) encontrado(s).</p>
        <?php if (!$rows): ?><p>Nenhum registro encontrado.</p><?php endif; ?>
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle text-nowrap">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">DOI</th>
                        <th scope="col">Status</th>
                        <th scope="col">Criado em</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= esc($row['id_doi']) ?></td>
                            <td><?= esc($row['doi_ID']) ?></td>
                            <td><?= esc($row['doi_status']) ?></td>
                            <td><?= esc($row['doi_created_at']) ?></td>
                            <td>
                                <a class="btn btn-outline-primary btn-sm" href="<?= site_url('labs/view/' . $status) ?>?edit=<?= (int) $row['id_doi'] ?>">Editar</a>
                                <form method="post" class="d-inline" action="<?= site_url('labs/view/' . $status . '/delete/' . $row['id_doi']) ?>" onsubmit="return confirm('Excluir este registro de DOI?');">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger btn-sm" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?= $pager->links() ?>
    </section>
</main>
