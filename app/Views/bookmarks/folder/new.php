<?= $this->extend('bookmarks/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-folder-plus"></i> Nova Pasta
        </h2>
        <a href="<?= base_url('bookmarks/folder') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('bookmarks/folder/save') ?>" method="post" class="card shadow-sm p-4 border-0">
        <div class="mb-3">
            <label for="f_title" class="form-label fw-bold">Nome da Pasta</label>
            <input type="text" name="f_title" id="f_title" class="form-control" required placeholder="Ex: Artigos favoritos">
        </div>

        <div class="mb-3">
            <label for="f_description" class="form-label fw-bold">Descrição</label>
            <textarea name="f_description" id="f_description" rows="3" class="form-control"
                placeholder="Descrição opcional da pasta..."></textarea>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Criar Pasta
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>