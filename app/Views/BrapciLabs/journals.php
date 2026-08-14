<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Revistas</h1>
            <p class="text-muted mb-0">
                <?= esc($journalsCount) ?> revistas distintas no projeto atual.
            </p>
        </div>

        <a href="<?= base_url('labs') ?>" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <section class="card card-dashboard p-4">
        <?php if ($journals === []): ?>
            <div class="alert alert-info mb-0">
                Nenhuma revista foi encontrada nos trabalhos deste projeto.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Revista</th>
                            <th class="text-end">Trabalhos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($journals as $journal): ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url('labs/journals/' . $journal['journal_id']) ?>"
                                        class="link">
                                        <?= esc($journal['journal']) ?>
                                    </a>
                                </td>
                                <td class="text-end"><?= esc($journal['works_count']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
