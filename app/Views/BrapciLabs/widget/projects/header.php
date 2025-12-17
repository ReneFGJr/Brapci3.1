<?php if (!empty($project)): ?>

    <div class="col-12 card mb-4 shadow-sm border-0">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-1">
                        <?= esc($project['project_name']) ?>
                    </h5>
                </div>

                <div class="text-end">
                    <span class="badge bg-<?= match ($project['status']) {
                                                'em_andamento' => 'success',
                                                'planejado'    => 'secondary',
                                                'concluido'    => 'primary',
                                                'suspenso'     => 'warning',
                                                'cancelado'    => 'danger',
                                                default        => 'dark'
                                            } ?>">
                        <?= esc($project['status']) ?>
                    </span>
                </div>

            </div>

            <hr class="my-2">

            <div class="row small text-muted">

                <div class="col-md-4">
                    <strong>ID do Projeto:</strong>
                    <?= esc($project['id']) ?>
                </div>

                <div class="col-md-4">
                    <strong>Criado em:</strong>
                    <?= date('d/m/Y', strtotime($project['created_at'])) ?>
                </div>

                <div class="col-md-4">
                    <strong>Atualizado em:</strong>
                    <?= $project['updated_at']
                        ? date('d/m/Y', strtotime($project['updated_at']))
                        : '-' ?>
                </div>

            </div>

        </div>
    </div>

<?php endif; ?>