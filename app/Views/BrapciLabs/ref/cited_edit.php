<div class="content">
    <div class="container my-4">
        <?php if (empty($work)): ?>
            <div class="alert alert-danger">Artigo não encontrado.</div>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h4 class="mb-1">Editar citações</h4>
                    <div class="text-muted"><?= esc($work['title'] ?? '') ?></div>
                </div>
                <a href="<?= base_url('labs/works/view/' . (int) $work_id) ?>"
                    class="btn btn-outline-secondary btn-sm"
                    title="Voltar ao artigo">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>

            <?= view('BrapciLabs/widget/authors/brapci_details_cited', ['data' => $data]) ?>
        <?php endif; ?>
    </div>
</div>