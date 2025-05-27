    <style>
        .step-card {
            min-height: 300px;
        }

        pre.code-block {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: .25rem;
        }
    </style>

    <div class="container py-5">
        <h2 class="mb-4">Passos para: <?= esc($softwareName) ?>
            <a href="<?php echo site_url('guide/software/steps_create/'.$id); ?>" class="btn btn-success float-end">
                +
            </a>
        </h2>

        <?php if (empty($steps)): ?>
            <div class="alert alert-warning">Nenhum passo encontrado para este software.</div>
        <?php else: ?>
            <div id="stepViewer" class="card step-card">
                <div class="card-header">
                    <strong>Passo <span id="stepNumber">1</span> de <?= count($steps) ?></strong>
                </div>
                <div class="card-body">
                    <pre id="user" style="color: red;"></pre>
                    <h5 id="stepTitle"></h5>
                    <p class="small" id="stepDescription"></p>

                    <div id="stepCodeSection" style="display:none;">
                        <h6>Código de Exemplo</h6>
                        <pre class="code-block" id="stepCode"></pre>
                    </div>

                    <div id="stepAnswerSection" style="display:none;">
                        <h6>Resposta Esperada</h6>
                        <pre class="code-block" id="stepAnswer"></p>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button id="btnPrev" class="btn btn-secondary" disabled>&laquo; Anterior</button>
                    <button id="btnNext" class="btn btn-primary">Próximo &raquo;</button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        // passos vindos do controller:
        const steps = <?= json_encode(array_values($steps), JSON_HEX_TAG) ?>;
        let current = 0;

        function renderStep(idx) {
            const s = steps[idx];
            document.getElementById('stepNumber').innerText = idx + 1;
            document.getElementById('stepTitle').innerText = s.st_description;
            document.getElementById('stepDescription').innerText = s.st_description;

            // user
            if (s.st_user) {
                document.getElementById('user').style.display = 'block';
                document.getElementById('user').innerText = 'Usuário: '+s.st_user;
            } else {
                document.getElementById('user').style.display = 'none';
            }

            // resposta
            if (s.st_answer) {
                document.getElementById('stepAnswerSection').style.display = 'block';
                document.getElementById('stepAnswer').innerText = s.st_answer;
            } else {
                document.getElementById('stepAnswerSection').style.display = 'none';
            }

            // código
            if (s.st_code) {
                document.getElementById('stepCodeSection').style.display = 'block';
                document.getElementById('stepCode').innerText = s.st_code;
            } else {
                document.getElementById('stepCodeSection').style.display = 'none';
            }

            // botões
            document.getElementById('btnPrev').disabled = (idx === 0);
            document.getElementById('btnNext').innerText = (idx === steps.length - 1) ? 'Finalizar' : 'Próximo »';
        }

        document.getElementById('btnPrev')?.addEventListener('click', () => {
            if (current > 0) {
                current--;
                renderStep(current);
            }
        });

        document.getElementById('btnNext')?.addEventListener('click', () => {
            if (current < steps.length - 1) {
                current++;
                renderStep(current);
            } else {
                // ao final, volta à lista de passos ou outra página
                window.location.href = '<?= site_url('guide/operational-system') ?>';
            }
        });

        // inicializa
        if (steps.length) renderStep(0);
    </script>