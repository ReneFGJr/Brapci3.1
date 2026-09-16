<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Processamento de referências — status <?= esc($status) ?></h1>
        <a href="<?= site_url('labs/cited_process') ?>" class="btn btn-outline-primary">Voltar</a>
    </div>
    <div class="card card-dashboard p-4">
        <p>Processamento síncrono de até 10 DOIs por lote. Total a processar no início deste lote: <strong><?= number_format((int) ($totalProcessar ?? 0), 0, ',', '.') ?></strong> DOI(s).</p>
        <?php if ($mensagem !== ''): ?>
            <p class="mb-0"><?= esc($mensagem) ?></p>
        <?php endif; ?>
        <?php foreach ($resultados as $resultado): ?>
            <div class="alert <?= $resultado['sucesso'] ? 'alert-success' : 'alert-danger' ?>">
                <strong><?= esc($resultado['doi']) ?></strong>
                <span class="badge bg-secondary">Status <?= $resultado['status'] === null ? 'não salvo' : esc($resultado['status']) ?></span>
                <div><?= esc($resultado['mensagem']) ?></div>
            </div>
        <?php endforeach; ?>
        <?php if ($resultados): ?>
            <p class="mb-0">Lote concluído: <?= count($resultados) ?> DOI(s).</p>
        <?php endif; ?>
    </div>
<?php if (in_array($status, [0, 2], true)): ?>
    <?php if ($recarregar ?? false): ?>
        <p class="mt-3" role="status">Restam <?= (int) $pendentes ?> DOI(s) pendentes. O próximo lote começa em 3 segundos.</p>
        <script>
            window.setTimeout(function () {
                window.location.reload();
            }, 3000);
        </script>
    <?php elseif (($pendentes ?? 0) === 0): ?>
        <p class="mt-3" role="status">Processamento finalizado: nenhum DOI com status <?= esc($status) ?>.</p>
    <?php endif; ?>
<?php endif; ?>
</main>
