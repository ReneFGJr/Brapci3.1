<?php

$labels = $labels ?? [];
$summary = $summary ?? [];
$total = $total ?? 0;
$tableUsed = $tableUsed ?? '';

$orderedStatuses = array_keys($labels);
foreach (array_keys($summary) as $status) {
    if (!in_array($status, $orderedStatuses, true)) {
        $orderedStatuses[] = $status;
    }
}
?>

<div class="container py-4">
    <h2 class="mb-2">Resumo do Harvesting de Livros</h2>
    <p class="text-muted mb-4">Tabela: <?= esc($tableUsed) ?></p>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <div class="small text-muted">Total de registros</div>
                    <div class="display-6"><?= esc((string)$total) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Status da tabela book_harvesting
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Status</th>
                            <th>Descricao</th>
                            <th style="width: 180px;" class="text-end">Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderedStatuses as $status): ?>
                            <?php
                            $desc = $labels[(string)$status] ?? 'Status customizado';
                            $qtd = $summary[(string)$status] ?? 0;
                            $statusLink = PATH . 'admin/book/items/' . urlencode((string)$status);
                            ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= esc((string)$status) ?></span></td>
                                <td><?= esc($desc) ?></td>
                                <td class="text-end"><a href="<?= esc($statusLink) ?>"><?= esc((string)$qtd) ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
