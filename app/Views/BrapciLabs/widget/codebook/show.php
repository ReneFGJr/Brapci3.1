<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <?= view('BrapciLabs/widget/projects/header', ['project' => $project ?? null]); ?>

    <?php if (empty($codebook)): ?>

        <div class="alert alert-warning">
            Anotação não encontrada.
        </div>

        <a href="<?= base_url('labs/project/codebook') ?>" class="btn btn-secondary">
            ← Voltar
        </a>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="card-body">

                <!-- TOPO -->
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h4 class="mb-0">
                        <?= esc($codebook['title']) ?>
                    </h4>

                    <div class="btn-group">

                        <a href="<?= base_url('labs/project/codebook') ?>"
                            class="btn btn-outline-secondary btn-sm ms-2">
                            ← Voltar
                        </a>

                        <a href="<?= base_url('labs/project/codebook/edit/' . $codebook['id']) ?>"
                            class="btn btn-outline-primary btn-sm ms-2">
                            ✏️ Editar
                        </a>

                        <form action="<?= base_url('labs/project/codebook/delete/' . $codebook['id']) ?>"
                            method="post"
                            onsubmit="return confirm('Deseja realmente excluir esta anotação?')">

                            <?= csrf_field() ?>

                            <button class="btn btn-outline-danger btn-sm ms-2">
                                🗑️ Excluir
                            </button>
                        </form>

                    </div>
                </div>

                <div class="mb-3 text-muted small">
                    Criado em <?= date('d/m/Y H:i', strtotime($codebook['created_at'])) ?>
                </div>

                <?php if (!empty($codebook['tags'])): ?>
                    <div class="mb-3">
                        <?php foreach (json_decode($codebook['tags'], true) as $tag): ?>
                            <span class="badge bg-light text-dark border me-1">
                                <?= esc($tag) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <hr>

                <div class="codebook-content">
                    <?= nl2br($codebook['content']) ?>
                </div>

            </div>

        </div>

    <?php endif; ?>

</div>