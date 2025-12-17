<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Selecionar Projeto de Pesquisa</h3>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($projects)): ?>

        <div class="alert alert-warning">
            Nenhum projeto disponível.
        </div>

    <?php else: ?>

        <div class="row">

            <?php foreach ($projects as $p): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">

                    <div class="card h-100 shadow-sm
                        <?= ($current == $p['id']) ? 'border-primary' : '' ?>">

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">
                                <?= esc($p['project_name']) ?>
                            </h5>

                            <p class="card-text small text-muted">
                                <?= esc($p['description'] ?? 'Sem descrição') ?>
                            </p>

                            <div class="mb-2">
                                <span class="badge bg-secondary">
                                    <?= esc($p['status']) ?>
                                </span>
                            </div>

                            <form method="post"
                                action="<?= base_url('labs/projects/set') ?>"
                                class="mt-auto">

                                <?= csrf_field() ?>

                                <input type="hidden"
                                    name="project_id"
                                    value="<?= $p['id'] ?>">

                                <?php if ($current == $p['id']): ?>
                                    <button class="btn btn-outline-primary w-100" disabled>
                                        Projeto Ativo
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-primary w-100">
                                        Usar este projeto
                                    </button>
                                <?php endif; ?>

                            </form>

                        </div>
                    </div>

                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>