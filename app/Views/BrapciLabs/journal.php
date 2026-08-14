<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= esc($journal['journal']) ?></h1>
            <p class="text-muted mb-0">
                <?= esc($journal['works_count']) ?> trabalhos no projeto atual.
            </p>
        </div>

        <a href="<?= base_url('labs/journals/') ?>" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Voltar para revistas
        </a>
    </div>

    <section class="card card-dashboard p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autores</th>
                        <th>Ano</th>
                        <th>DOI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($journal['works'] as $work): ?>
                        <tr>
                            <td>
                                <?php if (! empty($work['url'])): ?>
                                    <a href="<?= esc($work['url'], 'attr') ?>" target="_blank" rel="noopener noreferrer">
                                        <?= esc($work['title'] ?: 'Sem título') ?>
                                    </a>
                                <?php else: ?>
                                    <?= esc($work['title'] ?: 'Sem título') ?>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($work['authors'] ?: '—') ?></td>
                            <td><?= esc($work['year'] ?: '—') ?></td>
                            <td>
                                <?php if (! empty($work['doi'])): ?>
                                    <a href="https://doi.org/<?= esc(ltrim($work['doi'], '/'), 'attr') ?>"
                                        target="_blank" rel="noopener noreferrer">
                                        <?= esc($work['doi']) ?>
                                    </a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
