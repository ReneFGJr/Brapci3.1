<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Referências</h1>
            <p class="text-muted mb-0">Quantidade de DOIs por status em toda a base de referências.</p>
        </div>
        <a href="<?= site_url('labs') ?>" class="btn btn-outline-primary">Voltar</a>
    </div>

    <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $color): ?>
        <?php if ($message = session()->getFlashdata($key)): ?>
            <div class="alert alert-<?= esc($color) ?>"><?= esc($message) ?></div>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <form method="post" action="<?= site_url('labs/cited_process/importar_dois') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-primary">Importar DOIs das referências</button>
        </form>
        <a href="<?= site_url('labs/import') ?>" class="btn btn-primary">Inportar Referencias</a>
    </div>
    <section class="card card-dashboard p-4">
        <h2 class="h5 mb-3">Status das referências</h2>
        <?php if (empty($statuses)): ?>
            <p class="text-muted mb-0">Nenhum DOI cadastrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Status</th>
                            <th scope="col">Descrição</th>
                            <th scope="col" class="text-end">Quantidade</th><th scope="col">Processar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statuses as $status): ?>
                            <tr>
                                <td><?= esc($status['doi_status']) ?></td>
                                <td><?= esc($statusLabels[(int) $status['doi_status']] ?? 'Status não mapeado') ?></td>
                                <td class="text-end"><?= number_format((int) $status['total'], 0, ',', '.') ?></td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="<?= site_url('labs/cited_process/' . (int) $status['doi_status']) ?>">Processar</a>
                                    <?php if ((int) $status['doi_status'] === 10): ?>
                                        <a href="<?= site_url('labs/view/10') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye" aria-hidden="true"></i> Visualizar</a>
                                    <?php endif; ?>
                                    <?php if (in_array((int) $status['doi_status'], [2, 3], true)): ?>
                                        <form method="post" action="<?= site_url('labs/cited_process/reprocessar/' . (int) $status['doi_status']) ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-warning btn-sm">Reprocessar</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="row" colspan="2">Total de DOIs</th>
                            <td class="text-end fw-bold"><?= number_format(array_sum(array_column($statuses, 'total')), 0, ',', '.') ?></td><td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
