<style>
    .console-box {
        background: #0d1117;
        color: #c9d1d9;
        font-family: "JetBrains Mono", Consolas, monospace;
        font-size: 0.9rem;
        border-radius: 6px;
        padding: 15px;
        min-height: 260px;
        overflow-y: auto;
    }

    .console-header {
        color: #58a6ff;
        margin-bottom: 10px;
    }

    .console-prompt {
        color: #7ee787;
    }

    .console-output {
        white-space: pre-wrap;
    }
</style>

<div class="content">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Console de Execução IA</h3>
        <span class="badge bg-dark">
            <?= esc($tool['ai_name']) ?> · v<?= esc($tool['ai_version']) ?>
        </span>
    </div>

    <!-- Descrição -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <p class="mb-1">
                <?= esc($tool['ai_description']) ?>
            </p>
            <small class="text-muted">
                Última atualização: <?= date('d/m/Y', strtotime($tool['ai_update'])) ?>
            </small>
        </div>
    </div>

    <div class="row">

        <!-- Painel de Comando -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <i class="bi bi-terminal"></i> Comando Base
                </div>
                <div class="card-body">
                    <textarea class="form-control font-monospace"
                        rows="6"
                        readonly><?= esc($tool['ai_command']) ?></textarea>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-sliders"></i> Parâmetros
                </div>
                <div class="card-body">
                    <textarea id="params"
                        class="form-control font-monospace"
                        rows="6"
                        placeholder="Informe parâmetros adicionais (JSON, flags, prompt...)"><?= esc($tool['ai_parameters']) ?></textarea>

                    <button id="runBtn" class="btn btn-success w-100 mt-3">
                        <i class="bi bi-play-circle"></i> Executar
                    </button>
                </div>
            </div>
        </div>

        <!-- Console -->
        <div class="col-md-8">
            <!---- Botão de limpar console ---->
            <button class="btn btn-sm btn-outline-success"
                onclick="clearConsole()"
                title="Limpar saída do console">
                <i class="bi bi-trash"></i> Limpar console
            </button>

            <div class="card shadow-sm mt-3">
                <div class="card-header d-flex justify-content-between">
                    <span>
                        <i class="bi bi-cpu"></i> Console
                    </span>
                    <button class="btn btn-sm btn-outline-light"
                        onclick="clearConsole()">
                        Limpar
                    </button>
                </div>

                <div class="card-body p-0">
                    <div id="console" class="console-box">
                        <div class="console-header">
                            BrapciLabs AI Console
                        </div>
                        <div class="console-output">
                            Aguardando execução...
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    const consoleBox = document.getElementById('console');

    function logConsole(text, type = 'normal') {
        const line = document.createElement('div');
        line.classList.add('console-output');

        if (type === 'cmd') {
            line.innerHTML = `<span class="console-prompt">$</span> ${text}`;
        } else {
            line.textContent = text;
        }

        consoleBox.appendChild(line);
        consoleBox.scrollTop = consoleBox.scrollHeight;
    }

    function clearConsole() {
        consoleBox.innerHTML = '';
        logConsole('Console limpo.');
    }

    document.getElementById('runBtn').addEventListener('click', () => {
        const params = document.getElementById('params').value;

        logConsole('Executando ferramenta...', 'cmd');
        logConsole('Parâmetros recebidos:');
        logConsole(params);

        // Simulação (trocar por AJAX / SSE / WebSocket)
        setTimeout(() => {
            logConsole('Processando...');
        }, 800);

        setTimeout(() => {
            logConsole('Execução finalizada com sucesso.');
        }, 1800);
    });
</script>