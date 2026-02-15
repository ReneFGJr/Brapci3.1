<div class="container my-4">

    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="fw-light">
                <i class="bi bi-envelope-paper"></i>
                Gerenciamento de Convites para Indexação
            </h1>
        </div>

        <div class="col-md-4 text-end">
            <a href="<?= base_url('admin/source/Invitation/create') ?>"
                class="btn btn-success btn-lg shadow-sm">
                <i class="bi bi-plus-circle"></i>
                Enviar Convite
            </a>
        </div>
    </div>


    <div class="row g-4">

        <?php foreach ($resume as $id => $dt): ?>

            <div class="col-lg-4 col-md-6 col-sm-12">

                <div class="card shadow-sm border-0 h-100">

                    <div class="card-body text-center">

                        <h5 class="card-title mb-3">
                            <?= esc($dt['description']) ?>
                        </h5>

                        <div class="display-4 fw-bold text-primary mb-3">
                            <?= esc($dt['total']) ?>
                        </div>

                        <span class="badge bg-secondary mb-3 px-3 py-2">
                            Status: <?= esc($dt['status']) ?>
                        </span>

                        <div class="d-grid">
                            <a href="<?= base_url('admin/source/Invitation/status/' . $dt['status']) ?>"
                                class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver Itens
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>