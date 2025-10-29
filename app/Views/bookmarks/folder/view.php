<?= $this->extend('bookmarks/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-folder-fill text-warning"></i>
                <?= esc($folder['f_title'] ?? 'Pasta sem nome') ?>
            </h2>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar"></i>
                Criada em: <?= esc($folder['created_at'] ?? '-') ?>
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="<?= base_url('bookmarks/site/new/' . $folder['id_f']) ?>" class="btn btn-primary">
                <i class="bi bi-bookmark-plus"></i> Novo Site
            </a>
            <a href="<?= base_url('bookmarks/folder') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Mensagens de feedback -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Lista de favoritos -->
    <?php if (isset($bookmarks) && count($bookmarks) > 0): ?>
        <div class="row g-4 mt-3">
            <?php foreach ($bookmarks as $b): ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm position-relative bookmark-card">
                        <!-- Ícones de ação -->
                        <div class="position-absolute top-0 end-0 m-2 d-flex gap-2">
                            <a href="<?= base_url('bookmarks/link/' . $b['id']) ?>"
                                target="_blank"
                                class="text-success" title="Abrir link">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <a href="<?= base_url('bookmarks/site/delete/' . $b['id']) ?>"
                                class="text-danger"
                                onclick="return confirm('Deseja realmente remover este site?');"
                                title="Excluir site">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>

                        <!-- Conteúdo -->
                        <div class="card-body text-center p-3">
                            <a href="<?= base_url('bookmarks/link/' . $b['id']) ?>" target="_blank">
                                <img src="https://www.google.com/s2/favicons?sz=64&domain_url=<?= esc(parse_url($b['url'], PHP_URL_HOST)) ?>"
                                    alt="favicon" class="rounded mb-2 shadow-sm" width="48" height="48">
                            </a>

                            <h6 class="card-title text-truncate mb-1" title="<?= esc($b['title']) ?>">
                                <?= esc($b['title']) ?>
                            </h6>
                            <p class="small text-muted text-truncate mb-0">
                                <?= esc(parse_url($b['url'], PHP_URL_HOST)) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4 text-center py-3">
            <i class="bi bi-info-circle"></i> Nenhum favorito encontrado nesta pasta.
        </div>
    <?php endif; ?>
</div>

<style>
    .bookmark-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .bookmark-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
</style>

<?= $this->endSection() ?>