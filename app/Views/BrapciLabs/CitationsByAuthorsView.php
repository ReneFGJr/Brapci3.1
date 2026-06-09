<?php
/**
 * Citações por Autores - View
 * Exibe citações agrupadas por autor em formato de tabela
 */

if (empty($citations)) {
    echo '<div class="alert alert-info">Nenhuma citação encontrada.</div>';
    return;
}
?>

<div class="citations-container">
    <style>
        .citations-container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .author-section {
            margin-bottom: 2rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .author-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .author-header::before {
            content: "👤 ";
            margin-right: 0.5rem;
        }

        .citations-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .citations-table thead {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .citations-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid #dee2e6;
        }

        .citations-table th:last-child {
            border-right: none;
        }

        .citations-table tbody tr {
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.2s ease;
        }

        .citations-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .citations-table tbody tr:last-child {
            border-bottom: none;
        }

        .citations-table td {
            padding: 1rem;
            vertical-align: top;
        }

        .citation-id {
            width: 8%;
            font-weight: 600;
            color: #667eea;
            font-size: 0.9rem;
            border-right: 1px solid #dee2e6;
        }

        .citation-text {
            width: 92%;
            line-height: 1.6;
            color: #212529;
            font-size: 0.95rem;
        }

        .citation-count {
            display: inline-block;
            background-color: #667eea;
            color: white;
            border-radius: 50%;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
            background-color: #f8f9fa;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>

    <!-- Estatísticas -->
    <div class="summary-stats">
        <div class="stat-card">
            <div class="stat-value"><?php echo count($citations); ?></div>
            <div class="stat-label">Autores Encontrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                <?php 
                $totalCitations = 0;
                foreach ($citations as $authorCitations) {
                    $totalCitations += count($authorCitations);
                }
                echo $totalCitations;
                ?>
            </div>
            <div class="stat-label">Total de Citações</div>
        </div>
    </div>

    <!-- Citações por Autor -->
    <?php foreach ($citations as $author => $authorCitations): ?>
        <?php if (!empty($authorCitations)): ?>
            <div class="author-section">
                <div class="author-header">
                    <?php echo ucfirst(str_replace('_', ' ', $author)); ?>
                    <span class="citation-count"><?php echo count($authorCitations); ?></span>
                </div>

                <table class="citations-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">ID</th>
                            <th style="width: 92%;">Citação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($authorCitations as $index => $citation): ?>
                            <tr>
                                <td class="citation-id">
                                    <?php echo isset($citation[0]) ? htmlspecialchars($citation[0]) : '-'; ?>
                                </td>
                                <td class="citation-text">
                                    <?php echo isset($citation[1]) ? htmlspecialchars($citation[1]) : 'Sem descrição'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
