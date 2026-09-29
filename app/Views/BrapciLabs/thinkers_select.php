<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Referências de <?= esc($pensador['nome']) ?></h1>
        <a href="<?= site_url('labs/thinkers/' . (int) $pensador['id']) ?>" class="btn btn-outline-secondary">Voltar</a>
    </div>
    <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $style): ?>
        <?php if ($message = session()->getFlashdata($key)): ?>
            <div class="alert alert-<?= $style ?>" role="alert"><?= esc($message) ?></div>
        <?php endif; ?>
    <?php endforeach; ?>
    <p>Busca por sobrenome: <strong><?= esc($surname) ?></strong>. <?= count($references) ?> referência(s) ainda não vinculada(s) a este pensador.</p>
    <p class="text-muted">Confira as referências antes de vincular: o sobrenome pode corresponder a outros autores.</p>
    <section class="card card-dashboard p-4">
        <?php if ($references === []): ?>
            <div class="alert alert-info mb-0">Nenhuma referência disponível para vincular.</div>
        <?php else: ?>
            <form method="post" action="<?= site_url('labs/thinkers/' . (int) $pensador['id'] . '/select') ?>">
                <?= csrf_field() ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Selecionar</th><th>ID</th><th>Referência</th><th>Autores</th><th>Ano</th><th>DOI</th></tr></thead>
                        <tbody>
                            <?php foreach ($references as $reference): ?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input" name="references[]" value="<?= esc($reference['id_ca'], 'attr') ?>" aria-label="Selecionar referência <?= esc($reference['id_ca'], 'attr') ?>"></td>
                                    <td><?= esc($reference['id_ca']) ?></td>
                                    <td><?= esc($reference['ca_text']) ?></td>
                                    <td><?= esc($reference['ca_authors']) ?></td>
                                    <td><?= esc($reference['ca_year']) ?></td>
                                    <td><?= esc($reference['ca_doi']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary">Vincular selecionadas</button>
            </form>
        <?php endif; ?>
    </section>
</main>
