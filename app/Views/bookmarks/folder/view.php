<?= $this->extend('bookmarks/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-folder-fill text-warning"></i>
                <?= esc($folder['f_title'] ?? 'Pasta sem nome') ?>
            </h2>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar"></i> Criada em: <?= esc($folder['created_at'] ?? '-') ?>
            </p>
        </div>
        <div>
            <a href="<?= base_url('folders') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Lista de favoritos -->
    <?php if (isset($bookmarks) && count($bookmarks) > 0): ?>
        <div class="row g-4">
            <?php foreach ($bookmarks as $b): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="text-center mb-3">
                                <img src="https://www.google.com/s2/favicons?sz=64&domain_url=<?= esc(parse_url($b['url'], PHP_URL_HOST)) ?>"
                                    alt="favicon" class="rounded mb-2" width="48" height="48">
                                <h6 class="card-title text-truncate mb-2">
                                        <?= esc($b['title']) ?>
                                </h6>
                                <p class="text-muted small mb-2">
                                    <a href="<?=base_url('bookmarks/link/');?><?= esc($b['id']) ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Abrir link">
                                        <i class="bi bi-link-45deg"></i> <?= esc(parse_url($b['url'], PHP_URL_HOST)) ?>
                                    </a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">
            <i class="bi bi-info-circle"></i> Nenhum favorito encontrado nesta pasta.
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>