<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Pensadores</h1>
        <a href="<?= site_url('labs/thinkers/new') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo pensador</a>
    </div>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <section class="card card-dashboard p-4">
        <form method="get" action="<?= site_url('labs/thinkers') ?>" class="mb-4">
            <label for="thinker-search" class="form-label">Buscar pensador</label>
            <div class="d-flex flex-wrap gap-2">
                <input type="search" id="thinker-search" name="q" class="form-control flex-grow-1" style="width: auto; min-width: 0;"
                    value="<?= esc($query, 'attr') ?>" placeholder="Nome ou nome de citação">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search" aria-hidden="true"></i> Buscar</button>
                <?php if ($query !== ''): ?>
                    <a href="<?= site_url('labs/thinkers') ?>" class="btn btn-outline-secondary">Limpar</a>
                <?php endif ?>
            </div>
        </form>
        <?php if ($pensadores === []): ?>
            <div class="alert alert-info mb-0"><?= $query !== '' ? 'Nenhum pensador encontrado para esta busca.' : 'Nenhum pensador cadastrado.' ?></div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nome do pensador</th>
                            <th>Nome de citação</th>
                            <th>BRAPCI</th>
                            <th>Wikipédia</th>
                            <th>Wikidata</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pensadores as $pensador): ?>
                            <tr>
                                <td><?= esc($pensador['nome']) ?></td>
                                <td><?= esc($pensador['nome_citacao']) ?></td>
                                <td>
                                    <?php if ((int) ($pensador['brapci_id'] ?? 0) > 0): ?>
                                        <a href="https://brapci.inf.br/v/<?= (int) $pensador['brapci_id'] ?>" target="_blank" rel="noopener noreferrer">Consultar</a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <?php foreach (['link_wikipedia', 'link_wikidata'] as $campo): ?>
                                    <td>
                                        <?php if (filter_var($pensador[$campo], FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($pensador[$campo], PHP_URL_SCHEME)), ['http', 'https'], true)): ?>
                                            <a href="<?= esc($pensador[$campo], 'attr') ?>" target="_blank" rel="noopener noreferrer">Consultar</a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?= site_url('labs/thinkers/' . (int) $pensador['id']) ?>" class="btn btn-outline-secondary btn-sm" title="Visualizar" aria-label="Visualizar pensador"><i class="bi bi-eye" aria-hidden="true"></i></a>
                                        <a href="<?= site_url('labs/thinkers/' . (int) $pensador['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm" title="Editar" aria-label="Editar pensador"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                        <form method="post" action="<?= site_url('labs/thinkers/' . (int) $pensador['id'] . '/delete') ?>" class="m-0" onsubmit="return confirm('Deseja excluir este pensador?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir" aria-label="Excluir pensador"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= $pager->only(['q'])->links() ?>
        <?php endif; ?>
    </section>
</main>
