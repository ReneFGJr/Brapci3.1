<div class="content">
    <?php if (empty($citationsByAuthors) || !is_array($citationsByAuthors)): ?>
        <div class="alert alert-info">
            Nenhuma citação encontrada para os autores deste projeto.
        </div>
    <?php else: ?>
        <h5>Citações por Autor (<?= count($citationsByAuthors); ?> autores)</h5>

        <style>
            .author-section {
                margin-bottom: 2rem;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .author-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 1rem;
                font-weight: 600;
                font-size: 1rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                display: flex !important;
                justify-content: space-between;
                align-items: center;
            }

            .citation-count {
                background-color: rgba(255, 255, 255, 0.25);
                color: white;
                border-radius: 50%;
                width: 2rem;
                height: 2rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: 600;
                flex-shrink: 0;
            }

            .citations-table {
                width: 100% !important;
                border-collapse: collapse;
                background: white;
                display: table;
            }

            .citations-table thead {
                background-color: #f8f9fa;
                border-bottom: 2px solid #dee2e6;
                display: table-header-group;
            }

            .citations-table th {
                padding: 0.75rem;
                text-align: left;
                font-weight: 600;
                color: #495057;
                font-size: 0.9rem;
                border-right: 1px solid #dee2e6;
                display: table-cell;
            }

            .citations-table th:last-child {
                border-right: none;
            }

            .citations-table tbody {
                display: table-row-group;
            }

            .citations-table tbody tr {
                border-bottom: 1px solid #dee2e6;
                display: table-row;
            }

            .citations-table tbody tr:hover {
                background-color: #f8f9fa;
            }

            .citations-table td {
                padding: 0.75rem;
                vertical-align: top;
                display: table-cell;
                border-right: 1px solid #dee2e6;
            }

            .citations-table td:last-child {
                border-right: none;
            }

            .citation-id {
                width: 10% !important;
                font-weight: 600;
                color: #667eea;
                font-size: 0.85rem;
                word-break: break-word;
                min-width: 60px;
            }

            .citation-text {
                width: 60% !important;
                line-height: 1.6;
                color: #212529;
                font-size: 0.95rem;
                min-width: 200px;
            }

            .citation-authors {
                width: 30% !important;
                font-size: 0.9rem;
                color: #6c757d;
                min-width: 120px;
            }
        </style>

        <?php foreach ($citationsByAuthors as $citedAuthor => $authorCitations): ?>
            <?php if (is_array($authorCitations) && !empty($authorCitations)): ?>
                <div class="author-section">
                    <div class="author-header">
                        <span><?= htmlspecialchars(ucwords($citedAuthor)); ?></span>
                        <span class="citation-count"><?= count($authorCitations); ?></span>
                    </div>

                    <table class="citations-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 60%;">Citação</th>
                                <th style="width: 30%;">Citado por</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($authorCitations as $citation): ?>
                                <?php
                                    $citationId = $citation['rdf'] ?? '-';
                                    $citingAuthors = $citation['authors'] ?? [];
                                    $citationText = $citation['text'] ?? 'Sem descrição';
                                    $citingAuthorsList = implode(', ', array_keys((array)$citingAuthors));
                                ?>
                                <tr>
                                    <td class="citation-id">
                                        <nobr>
                                            <?= htmlspecialchars($citationId); ?>
                                        </nobr>
                                    </td>
                                    <td class="citation-text">
                                        <?= htmlspecialchars($citationText); ?>
                                    </td>
                                    <td class="citation-authors">
                                        <small><?= htmlspecialchars($citingAuthorsList ?: 'N/A'); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>