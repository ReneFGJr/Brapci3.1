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
        <?php if ($pensadores === []): ?>
            <div class="alert alert-info mb-0">Nenhum pensador cadastrado.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nome do pensador</th>
                            <th>Nome de citação</th>
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
            <?= $pager->links() ?>
        <?php endif; ?>
    </section>
</main>
