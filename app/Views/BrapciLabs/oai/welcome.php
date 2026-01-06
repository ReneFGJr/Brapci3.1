<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>OAI Harvest – Coletor OAI-PMH</h3>
        <span class="badge bg-primary">OAI-PMH 2.0</span>
    </div>

    <!-- Introdução -->
    <?php if ($repositoryID == 0): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <p class="mb-2">
                    Este módulo permite a coleta, análise e monitoramento de repositórios digitais
                    que disponibilizam metadados via protocolo <strong>OAI-PMH</strong>
                    (Open Archives Initiative – Protocol for Metadata Harvesting).
                </p>
                <p class="text-muted mb-0">
                    Utilize as opções abaixo para explorar os conjuntos (sets), identificar o repositório,
                    listar formatos de metadados e iniciar processos de colheita.
                </p>
            </div>
        </div>

        <!-- Cards de Ações -->
        <div class="row">
            <a href="<?= site_url('labs/oai/select') ?>" class="col-md-3 mb-4 text-decoration-none">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-folder-plus fs-1 text-primary"></i>
                        <h5 class="mt-3">Selecionar Repositório</h5>
                        <p class="text-muted">
                            Escolha um repositório OAI para iniciar a coleta.
                        </p>
                        <span class="btn btn-outline-primary btn-sm">
                            Selecionar
                        </span>
                    </div>
                </div>
            </a>
        </div>
    <?php else: ?>

        <!-- Cards de Ações -->
        <div class="row">

            <!-- Identify -->
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-info-circle fs-1 text-primary"></i>
                        <h5 class="mt-3">Identify</h5>
                        <p class="text-muted">
                            Informações gerais do repositório OAI.
                        </p>
                        <a href="<?= site_url('oai/identify') ?>" class="btn btn-outline-primary btn-sm">
                            Consultar
                        </a>
                    </div>
                </div>
            </div>

            <!-- ListSets -->
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-collection fs-1 text-success"></i>
                        <h5 class="mt-3">ListSets</h5>
                        <p class="text-muted">
                            Coleções e comunidades disponíveis.
                        </p>
                        <a href="<?= site_url('oai/sets') ?>" class="btn btn-outline-success btn-sm">
                            Ver coleções
                        </a>
                    </div>
                </div>
            </div>

            <!-- ListMetadataFormats -->
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-code-slash fs-1 text-warning"></i>
                        <h5 class="mt-3">Metadata Formats</h5>
                        <p class="text-muted">
                            Formatos de metadados suportados.
                        </p>
                        <a href="<?= site_url('oai/formats') ?>" class="btn btn-outline-warning btn-sm">
                            Listar formatos
                        </a>
                    </div>
                </div>
            </div>

            <!-- ListRecords -->
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-cloud-download fs-1 text-danger"></i>
                        <h5 class="mt-3">Harvest</h5>
                        <p class="text-muted">
                            Coleta de registros OAI-PMH.
                        </p>
                        <a href="<?= site_url('oai/records') ?>" class="btn btn-outline-danger btn-sm">
                            Iniciar coleta
                        </a>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

    <!-- Bloco informativo -->
    <div class="alert alert-info mt-4">
        <i class="bi bi-lightbulb"></i>
        <strong>Dica:</strong> repositórios grandes podem exigir
        <em>resumptionToken</em> para coleta paginada.
        O sistema está preparado para esse fluxo.
    </div>

</div>

<?= view('BrapciLabs/layout/footer'); ?>