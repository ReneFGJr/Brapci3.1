<?= $this->extend('bookmarks/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <h2 class="mb-4">
        <i class="bi bi-bookmark-plus"></i> Novo Site em:
        <span class="text-primary"><?= esc($folder['f_title']) ?></span>
    </h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('bookmarks/site/save') ?>" method="post" class="card shadow-sm p-4 border-0">
        <input type="hidden" name="folder_id" value="<?= esc($folder['id_f']) ?>">

        <div class="mb-3">
            <label for="title" class="form-label fw-bold">Título do Site</label>
            <input type="text" class="form-control" id="title" name="title" required placeholder="Ex: ChatGPT">
        </div>

        <div class="mb-3">
            <label for="url" class="form-label fw-bold">URL</label>
            <input type="url" class="form-control" id="url" name="url" required placeholder="https://example.com">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Descrição</label>
            <textarea class="form-control" id="description" name="description" rows="3"
                placeholder="Descrição opcional do site..."></textarea>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Salvar
            </button>
            <a href="<?= base_url('bookmarks/folders/view/' . $folder['id_f']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>