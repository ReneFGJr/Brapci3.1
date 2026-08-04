<div class="content">
    <?php $authorData = $author ?? []; ?>

    <?= view('BrapciLabs/widget/projects/header', ['project' => $project ?? null]); ?>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-pencil-square me-1"></i>
                Editar autor
            </h4>
            <small class="text-muted">Atualize os dados cadastrais do autor</small>
        </div>

        <a href="<?= base_url('labs/project/authors') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="post" action="<?= base_url('labs/authority/save/' . ($authorData['id'] ?? 0)) ?>" class="row g-3">
                <?= csrf_field() ?>

                <div class="col-12">
                    <label class="form-label" for="nome">Nome</label>
                    <input
                        type="text"
                        class="form-control"
                        id="nome"
                        name="nome"
                        value="<?= esc($authorData['nome'] ?? '') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="lattes_id">Lattes ID</label>
                    <input
                        type="text"
                        class="form-control"
                        id="lattes_id"
                        name="lattes_id"
                        value="<?= esc($authorData['lattes_id'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="brapci_id">BRAPCI ID</label>
                    <input
                        type="text"
                        class="form-control"
                        id="brapci_id"
                        name="brapci_id"
                        value="<?= esc($authorData['brapci_id'] ?? '') ?>">
                    <small class="text-muted">Apenas numeros sao considerados.</small>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="<?= base_url('labs/project/authors') ?>" class="btn btn-light border">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
