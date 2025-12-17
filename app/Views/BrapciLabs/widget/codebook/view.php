<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <!-- Cabeçalho do Projeto (widget informativo) -->
    <?= view('BrapciLabs/widgets/project_header', ['project' => $project ?? null]); ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">CodeBook do Projeto</h4>

        <!-- botão futuro -->
        <!--
        <a href="<?= base_url('labs/codebook/new') ?>" class="btn btn-sm btn-primary">
            Nova anotação
        </a>
        -->
    </div>

    <?php if (empty($codebooks)): ?>

        <div class="alert alert-info">
            Nenhuma anotação registrada para este projeto.
        </div>

    <?php else: ?>

        <div class="row">

            <?php foreach ($codebooks as $cb): ?>
                <div class="col-xl-4 col-lg-6 col-md-12 mb-4">

                    <div class="card h-100 shadow-sm">

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">
                                <?= esc($cb['title']) ?>
                            </h5>

                            <p class="card-text text-muted small">
                                <?= character_limiter(strip_tags($cb['content']), 180) ?>
                            </p>

                            <?php if (!empty($cb['tags'])): ?>
                                <div class="mb-2">
                                    <?php foreach (json_decode($cb['tags'], true) as $tag): ?>
                                        <span class="badge bg-light text-dark border me-1">
                                            <?= esc($tag) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-auto small text-muted">
                                Criado em:
                                <?= date('d/m/Y', strtotime($cb['created_at'])) ?>
                            </div>

                        </div>

                        <!-- área de ações (futuro) -->
                        <!--
                        <div class="card-footer bg-white text-end">
                            <a href="<?= base_url('labs/codebook/view/' . $cb['id']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                Ver
                            </a>
                        </div>
                        -->

                    </div>

                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>