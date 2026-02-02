<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>AI Tools – Funções Disponíveis</h3>
        <span class="badge bg-dark">BrapciLabs · IA</span>
    </div>

    <!-- Introdução -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <p class="mb-2">
                Este painel apresenta as ferramentas de Inteligência Artificial
                cadastradas no sistema, incluindo seus comandos, parâmetros
                e versões disponíveis.
            </p>
            <p class="text-muted mb-0">
                As funções abaixo são carregadas dinamicamente a partir do
                <code>AiToolsModel</code>.
            </p>
        </div>
    </div>

    <!-- Lista de Ferramentas -->
    <?php if (empty($tools)): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            Nenhuma ferramenta de IA cadastrada no momento.
        </div>
    <?php else: ?>

        <div class="row">

            <?php foreach ($tools as $tool): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">

                        <div class="card-body">
                            <h5 class="card-title d-flex justify-content-between align-items-center">
                                <?= esc($tool['ai_name']) ?>
                                <span class="badge bg-secondary">
                                    v<?= esc($tool['ai_version']) ?>
                                </span>
                            </h5>

                            <p class="text-muted small mb-2">
                                Atualizado em <?= date('d/m/Y', strtotime($tool['ai_update'])) ?>
                            </p>

                            <p class="card-text">
                                <?= esc($tool['ai_description']) ?>
                            </p>

                            <hr>

                            <p class="mb-1">
                                <strong>Parâmetros:</strong>
                            </p>
                            <pre class="bg-light p-2 small rounded">
<?= esc($tool['ai_parameters']) ?>
                            </pre>

                            <p class="mb-1">
                                <strong>Comando:</strong>
                            </p>
                            <pre class="bg-dark text-light p-2 small rounded">
<?= esc($tool['ai_command']) ?>
                            </pre>
                        </div>

                        <div class="card-footer bg-white text-end">
                            <a href="<?= site_url('labs/ai/view/' . $tool['id_ai']) ?>"
                                class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i> Detalhes
                            </a>

                            <a href="<?= site_url('labs/ai/run/' . $tool['id_ai']) ?>"
                                class="btn btn-outline-success btn-sm">
                                <i class="bi bi-play-circle"></i> Executar
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <!-- Bloco informativo -->
    <div class="alert alert-info mt-4">
        <i class="bi bi-lightbulb"></i>
        <strong>Dica:</strong> você pode versionar comandos e parâmetros
        de cada ferramenta de IA sem alterar o código, apenas atualizando
        os registros na tabela <code>ai_tools</code>.
    </div>

</div>

<?= view('BrapciLabs/layout/footer'); ?>