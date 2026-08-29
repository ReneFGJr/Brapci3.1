<div class="card border-primary mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-check me-2"></i>Revista selecionada</span>
        <a class="btn btn-sm btn-light" href="<?= base_url('ojs/journals') ?>">Alterar</a>
    </div>
    <div class="card-body">
        <?php if ($journal !== null): ?>
            <div class="row g-3">
                <div class="col-md-5"><strong>Nome:</strong> <?= esc($journal['name']) ?></div>
                <div class="col-md-2"><strong>Sigla:</strong> <?= esc($journal['acronym'] ?: '—') ?></div>
                <div class="col-md-5"><strong>OJS:</strong> <?= esc($journal['base_url']) ?>/index.php/<?= esc($journal['context_path']) ?></div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mb-0">
                Nenhuma revista ativa selecionada. <a class="alert-link" href="<?= base_url('ojs/journals') ?>">Selecionar revista</a>.
            </div>
        <?php endif; ?>
    </div>
</div>