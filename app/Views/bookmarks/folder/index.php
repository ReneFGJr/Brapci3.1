<?= $this->extend('bookmarks/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-folder2-open"></i> Minhas Pastas</h2>
        <a href="<?= base_url('bookmarks/folders/new') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Pasta
        </a>
    </div>

    <?php if (isset($folders) && count($folders) > 0): ?>
        <div class="row g-4">
            <?php foreach ($folders as $f): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <a href="<?= base_url('bookmarks/folders/view/' . $f['id_f']) ?>">
                                    <i class="bi bi-folder-fill text-warning" style="font-size:3rem;"></i>
                                </a>
                            </div>
                            <h5 class="card-title text-truncate mb-2"><?= esc($f['f_title']) ?></h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">
            <i class="bi bi-info-circle"></i> Nenhuma pasta cadastrada ainda.
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>