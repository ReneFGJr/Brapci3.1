<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Selecionar Repositório OAI</h3>
        <span class="badge bg-secondary">Repositórios cadastrados</span>
    </div>

    <!-- Instrução -->
    <div class="alert alert-info">
        Selecione um repositório para iniciar a coleta via protocolo
        <strong>OAI-PMH</strong>.
    </div>

    <!-- Lista de Repositórios -->
    <div class="row">

        <?php foreach ($repositories as $rp): ?>

            <?php
                // Status visual
                $statusClass = 'secondary';
                $statusLabel = 'Indefinido';

                if ($rp['rp_status'] == 1) {
                    $statusClass = 'success';
                    $statusLabel = 'Ativo';
                } elseif ($rp['rp_status'] >= 400) {
                    $statusClass = 'danger';
                    $statusLabel = 'Erro';
                }
            ?>

            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card h-100 shadow-sm">

                    <div class="card-body d-flex flex-column">

                        <!-- Nome -->
                        <h5 class="card-title">
                            <?= esc($rp['rp_name']) ?>
                        </h5>

                        <!-- Instituição -->
                        <?php if (!empty($rp['rp_instituicao'])): ?>
                            <p class="mb-1">
                                <i class="bi bi-building"></i>
                                <?= esc($rp['rp_instituicao']) ?>
                            </p>
                        <?php endif; ?>

                        <!-- Plataforma -->
                        <?php if (!empty($rp['rp_plataforma'])): ?>
                            <p class="mb-1">
                                <i class="bi bi-cpu"></i>
                                <?= esc($rp['rp_plataforma']) ?>
                                <?= esc($rp['rp_versao']) ?>
                            </p>
                        <?php endif; ?>

                        <!-- URL -->
                        <p class="mb-1">
                            <i class="bi bi-link-45deg"></i>
                            <a href="<?= esc($rp['rp_url']) ?>" target="_blank">
                                Site do repositório
                            </a>
                        </p>

                        <!-- URL OAI -->
                        <?php if (!empty($rp['rp_url_oai'])): ?>
                            <p class="mb-2 text-success">
                                <i class="bi bi-cloud-check"></i>
                                OAI disponível
                            </p>
                        <?php else: ?>
                            <p class="mb-2 text-muted">
                                <i class="bi bi-cloud-slash"></i>
                                OAI não informado
                            </p>
                        <?php endif; ?>

                        <!-- Status -->
                        <span class="badge bg-<?= $statusClass ?> mb-3">
                            <?= $statusLabel ?>
                        </span>

                        <!-- Ação -->
                        <div class="mt-auto">
                            <a href="<?= site_url('oai/select/' . $rp['id_rp']) ?>"
                               class="btn btn-outline-primary w-100"
                               <?= empty($rp['rp_url_oai']) ? 'disabled' : '' ?>>
                                <i class="bi bi-check-circle"></i>
                                Selecionar
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        <?php endforeach; ?>

    </div>

</div>

<?= view('BrapciLabs/layout/footer'); ?>
