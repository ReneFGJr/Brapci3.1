<?php
$brapci = (array) ($brapci ?? []);
$works = (array) ($brapci['works'] ?? []);

$getCount = function ($items): int {
    if ($items === null || $items === '') {
        return 0;
    }

    if (!is_array($items)) {
        return 1;
    }

    return count($items);
};

$counts = [
    'Article' => $getCount($works['Article'] ?? []),
    'Book' => $getCount($works['Book'] ?? []),
    'Proceeding' => $getCount($works['Proceeding'] ?? []),
];

$totalKnown = array_sum($counts);

$otherCount = 0;
foreach ($works as $type => $items) {
    if (!array_key_exists((string) $type, $counts)) {
        $otherCount += $getCount($items);
    }
}

$total = $totalKnown + $otherCount;
?>

<div class="p-3">
    <h5 class="mb-3">Resumo BRAPCI</h5>

    <?php if ($total > 0): ?>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="border rounded p-2 text-center">
                    <div class="small text-muted">Total</div>
                    <div class="h4 mb-0"><?= esc((string) $total) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border rounded p-2 text-center">
                    <div class="small text-muted">Artigos</div>
                    <div class="h4 mb-0"><?= esc((string) $counts['Article']) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border rounded p-2 text-center">
                    <div class="small text-muted">Livros</div>
                    <div class="h4 mb-0"><?= esc((string) $counts['Book']) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border rounded p-2 text-center">
                    <div class="small text-muted">Eventos</div>
                    <div class="h4 mb-0"><?= esc((string) $counts['Proceeding']) ?></div>
                </div>
            </div>
        </div>

        <?php if ($otherCount > 0): ?>
            <div class="alert alert-secondary mt-3 mb-0 py-2">
                Outros tipos de producao: <strong><?= esc((string) $otherCount) ?></strong>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-light border mb-0">
            Nenhum registro encontrado para este autor na BRAPCI.
        </div>
    <?php endif; ?>
</div>
