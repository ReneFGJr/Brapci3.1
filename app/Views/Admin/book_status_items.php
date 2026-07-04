<?php

$tableUsed = $tableUsed ?? '';
$status = $status ?? '';
$statusLabel = $statusLabel ?? '';
$items = $items ?? [];
$backLink = PATH . 'admin/book';

if (!function_exists('book_item_url')) {
    function book_item_url($identifiers): string
    {
        if (!is_string($identifiers) || trim($identifiers) == '') {
            return '';
        }

        $urls = [];
        $parsed = json_decode($identifiers, true);
        if (is_array($parsed)) {
            foreach ($parsed as $value) {
                if (is_string($value) && str_starts_with(trim($value), 'http')) {
                    $urls[] = trim($value);
                }
            }
        } elseif (str_starts_with(trim($identifiers), 'http')) {
            $urls[] = trim($identifiers);
        }

        foreach ($urls as $url) {
            if (strpos($url, '/catalog/book/') !== false) {
                return $url;
            }
        }

        return $urls[0] ?? '';
    }
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Itens do Status <?= esc((string)$status) ?></h2>
        <a href="<?= esc($backLink) ?>" class="btn btn-outline-secondary btn-sm">Voltar ao resumo</a>
    </div>

    <p class="text-muted mb-1">Descricao: <?= esc($statusLabel) ?></p>
    <p class="text-muted mb-4">Tabela: <?= esc($tableUsed) ?> | Registros: <?= esc((string)count($items)) ?></p>

    <?php if (count($items) == 0): ?>
        <div class="card">
            <div class="card-body text-center text-muted py-5">Nenhum item encontrado para este status.</div>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <?php foreach ($items as $item): ?>
            <?php
            $identifier = (string)($item['identifier'] ?? '');
            $title = (string)($item['title'] ?? '');
            $datestamp = (string)($item['datestamp'] ?? '');
            $doi = (string)($item['DOI'] ?? '');
            $coverage = (string)($item['coverage'] ?? '');
            $identifiers = (string)($item['identifiers'] ?? '');
            $doiLink = $doi != '' ? 'https://doi.org/' . $doi : '';
            $itemLink = book_item_url($identifiers);
            $detailToken = bin2hex($identifier);
            $detailLink = PATH . 'admin/book/detail/' . $detailToken;
            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <?php if ($coverage != ''): ?>
                        <img src="<?= esc($coverage) ?>" alt="Capa" class="card-img-top" style="height: 320px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="height: 320px;">Sem capa</div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= esc($title) ?></h5>
                        <p class="card-text small text-muted mb-2"><?= esc($identifier) ?></p>
                        <p class="card-text small text-muted mb-3">Datestamp: <?= esc($datestamp) ?></p>

                        <div class="mt-auto d-flex gap-2 flex-wrap">
                            <a href="<?= esc($detailLink) ?>" class="btn btn-dark btn-sm">Detalhes do registro</a>
                            <?php if ($itemLink != ''): ?>
                                <a href="<?= esc($itemLink) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm">Visualizar item</a>
                            <?php endif; ?>
                            <?php if ($doi != ''): ?>
                                <a href="<?= esc($doiLink) ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">DOI</a>
                            <?php endif; ?>
                            <?php if ($coverage != ''): ?>
                                <a href="<?= esc($coverage) ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">Capa</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
