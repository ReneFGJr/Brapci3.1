<?php

/**
 * View: Lista de Artigos Citados
 * Espera: array $data_cited
 */
?>

<div class="container my-4">

    <h4 class="mb-3">
        Referências Citadas
        <span class="badge bg-secondary">
            <?= count($data_cited) ?>
        </span>
    </h4>

    <?php if (empty($data_cited)): ?>
        <div class="alert alert-warning">
            Nenhuma referência citada encontrada.
        </div>
    <?php else: ?>

        <?php
        $LabelCaption = '';
        ?>

        <div class="list-group shadow-sm">

            <?php foreach ($data_cited as $item): ?>

                <?php
                // Tipos documentais
                $tipos = [
                    0 => 'Livro',
                    1 => 'Artigo',
                    2 => 'Capítulo / Trabalho',
                    7 => 'Tese / Dissertação'
                ];

                $tipoLabel = $tipos[$item['ca_tipo']] ?? 'Outro';

                // Status
                switch ($item['ca_status']) {
                    case 0:
                        $statusLabel = 'Para processar';
                        $statusClass = 'secondary';
                        break;
                    case 1:
                        $statusLabel = 'Verificado';
                        $statusClass = 'success';
                        break;
                    case 2:
                        $statusLabel = 'Corrigido';
                        $statusClass = 'warning';
                        break;
                    default:
                        $statusLabel = 'Outros';
                        $statusClass = 'dark';
                }

                // Cabeçalho por tipo
                if ($LabelCaption !== $tipoLabel):
                    $LabelCaption = $tipoLabel;
                ?>
                    <div class="list-group-item list-group-item-secondary">
                        <strong><?= esc($LabelCaption) ?></strong>
                    </div>
                <?php endif; ?>

                <!-- Item -->
                <div class="list-group-item">

                    <div class="d-flex justify-content-between align-items-start gap-3">

                        <p class="mb-0"
                            style="line-height:1.2; font-size:0.85rem; text-align: left;">
                            <?= esc($item['ca_text']) ?>
                        </p>

                        <span class="badge bg-<?= $statusClass ?>">
                            <?= esc($statusLabel) ?>
                        </span>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>