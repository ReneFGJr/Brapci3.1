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
                display: flex;
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
                padding: 0.75rem;
                text-align: left;
                font-weight: 600;
                color: #495057;
                font-size: 0.9rem;
                border-right: 1px solid #dee2e6;
            }

            .citations-table th:last-child {
                border-right: none;
            }

            .citations-table tbody tr {
                border-bottom: 1px solid #dee2e6;
            }

            .citations-table tbody tr:hover {
                background-color: #f8f9fa;
            }

            .citations-table td {
                padding: 0.75rem;
                vertical-align: top;
            }

            .citation-id {
                width: 10%;
                font-weight: 600;
                color: #667eea;
                font-size: 0.85rem;
                border-right: 1px solid #dee2e6;
                word-break: break-word;
            }

            .citation-text {
                width: 60%;
                line-height: 1.6;
                color: #212529;
                font-size: 0.95rem;
                border-right: 1px solid #dee2e6;
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
                                    $citationId = $citation[0] ?? '-';
                                    $citingAuthors = $citation[1] ?? [];
                                    $citationText = $citation[2] ?? 'Sem descrição';
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
                                    <td style="font-size: 0.9rem; color: #6c757d;">
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