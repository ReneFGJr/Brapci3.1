<?php

$tableUsed = $tableUsed ?? '';
$item = $item ?? [];
$backLink = PATH . 'admin/book';

$coverage = (string)($item['coverage'] ?? '');
$doi = (string)($item['DOI'] ?? '');
$doiLink = $doi != '' ? 'https://doi.org/' . $doi : '';
$id = (int)$item['id'] ?? 0;
$status = (string)($item['status'] ?? '');

?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Detalhes do Registro</h2>
        <a href="<?= esc($backLink) ?>" class="btn btn-outline-secondary btn-sm">Voltar ao resumo</a>
    </div>

    <p class="text-muted mb-4">Tabela: <?= esc($tableUsed) ?></p>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <?php if ($coverage != ''): ?>
                    <img src="<?= esc($coverage) ?>" alt="Capa" class="card-img-top" style="height: 420px; object-fit: cover;">
                <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="height: 420px;">Sem capa</div>
                <?php endif; ?>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <?php if ($doi != ''): ?>
                            <a href="<?= esc($doiLink) ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">Abrir DOI</a>
                        <?php endif; ?>
                        <?php if ($coverage != ''): ?>
                            <a href="<?= esc($coverage) ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">Abrir capa</a>
                        <?php endif; ?>
                        <?php if ($coverage != '' && $status === '2'): ?>
                            <a href="<?=base_url('admin/book/catalog')?>/<?=esc($id);?>" target="_blank" rel="noopener" class="btn btn-outline-danger btn-sm">Catalogar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">Campos do registro</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 240px;">Campo</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($item as $field => $value): ?>
                                    <tr>
                                        <td><strong><?= esc((string)$field) ?></strong></td>
                                        <td style="white-space: pre-wrap;"><?= esc((string)$value) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>