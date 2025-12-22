<div class="container mt-4">

    <a href="<?= base_url('labs/api-library') ?>" class="btn btn-secondary mb-3">
        ← Voltar
    </a>

    <h2><?= esc($api['nome']) ?></h2>

    <div class="card mb-3">
        <div class="card-body">

            <p>
                <strong>Método:</strong>
                <span class="badge bg-primary"><?= esc($api['metodo']) ?></span>
            </p>

            <p>
                <strong>Endpoint:</strong><br>
                <code><?= esc($api['endpoint']) ?></code>
            </p>

            <?php if (!empty($api['descricao'])): ?>
                <hr>
                <p><strong>Descrição</strong></p>
                <p><?= nl2br(esc($api['descricao'])) ?></p>
            <?php endif; ?>

        </div>
    </div>

    <?php
    $campos = [
        'parametros'        => 'Parâmetros',
        'headers'           => 'Headers',
        'exemplo_request'   => 'Exemplo de Request',
        'exemplo_response'  => 'Exemplo de Response'
    ];
    ?>

    <?php foreach ($campos as $campo => $titulo): ?>
        <?php if (!empty($api[$campo])): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <strong><?= $titulo ?></strong>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><?= esc($api[$campo]) ?></pre>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

</div>