<style>
    .citations-container {
        padding: 0;
    }

    .citations-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #667eea;
    }

    .citations-header h2 {
        margin: 0;
        color: #2c3e50;
        font-size: 1.5rem;
    }

    .search-box {
        display: flex;
        gap: 0.5rem;
    }

    .search-box input {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.9rem;
        min-width: 250px;
    }

    .search-box button {
        padding: 0.5rem 1rem;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
    }

    .search-box button:hover {
        background: #5568d3;
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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

    .author-section {
        margin-bottom: 2rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .author-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.25rem;
        font-weight: 600;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .citation-count {
        display: inline-flex;
        background-color: rgba(255,255,255,0.25);
        color: white;
        border-radius: 50%;
        width: 2.5rem;
        height: 2.5rem;
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
        transition: background-color 0.15s ease;
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
        width: 10%;
        font-weight: 600;
        color: #667eea;
        font-size: 0.85rem;
        border-right: 1px solid #dee2e6;
        word-break: break-word;
    }

    .citation-text {
        width: 90%;
        line-height: 1.6;
        color: #212529;
        font-size: 0.95rem;
    }

    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
        background: white;
        border: 2px dashed #dee2e6;
        border-radius: 0.375rem;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .citations-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .search-box {
            width: 100%;
            flex-direction: column;
        }

        .search-box input,
        .search-box button {
            width: 100%;
        }

        .citation-id {
            width: 15%;
            font-size: 0.8rem;
            padding: 0.75rem;
        }

        .citation-text {
            width: 85%;
            font-size: 0.9rem;
        }

        .citations-table td {
            padding: 0.75rem;
        }
    }
</style>

<div class="content">
    <div class="citations-container">

        <!-- Header com título e busca -->
        <div class="citations-header">
            <h2>📚 Citações por Autores</h2>
            <div class="search-box">
                <form method="GET" style="display: flex; gap: 0.5rem; flex: 1;">
                    <input type="text" name="q" placeholder="Buscar citações..."
                           value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                           style="flex: 1;">
                    <button type="submit">🔍 Buscar</button>
                </form>
            </div>
        </div>

        <?php
        // Calcula estatísticas
        $totalAuthors = count($argv ?? []);
        $totalCitations = 0;
        foreach ($argv ?? [] as $authorCitations) {
            if (is_array($authorCitations)) {
                $totalCitations += count($authorCitations);
            }
        }
        $avgCitations = $totalAuthors > 0 ? round($totalCitations / $totalAuthors, 2) : 0;
        ?>

        <?php if (empty($argv) || !is_array($argv)): ?>
            <!-- Se não há dados -->
            <div class="empty-state">
                <h3>Nenhuma citação encontrada</h3>
                <p>Tente ajustar os termos de busca</p>
            </div>
        <?php else: ?>
            <!-- Estatísticas -->
            <div class="summary-stats">
                <div class="stat-card">
                    <div class="stat-value"><?= $totalAuthors; ?></div>
                    <div class="stat-label">Autores Encontrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $totalCitations; ?></div>
                    <div class="stat-label">Total de Citações</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $avgCitations; ?></div>
                    <div class="stat-label">Média por Autor</div>
                </div>
            </div>

            <!-- Citações por Autor -->
            <?php foreach ($argv as $author => $authorCitations): ?>
                <?php if (is_array($authorCitations) && !empty($authorCitations)): ?>
                    <div class="author-section">
                        <div class="author-header">
                            <span><?= ucfirst(str_replace('_', ' ', htmlspecialchars($author))); ?></span>
                            <span class="citation-count"><?= count($authorCitations); ?></span>
                        </div>

                        <table class="citations-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">ID</th>
                                    <th style="width: 90%;">Citação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($authorCitations as $index => $citation): ?>
                                    <tr>
                                        <td class="citation-id">
                                            <?php
                                            $citationId = isset($citation[0]) ? htmlspecialchars($citation[0]) : (isset($citation['id']) ? htmlspecialchars($citation['id']) : '-');
                                            echo $citationId;
                                            ?>
                                        </td>
                                        <td class="citation-text">
                                            <?php
                                            $citationText = isset($citation[1]) ? htmlspecialchars($citation[1]) : (isset($citation['text']) ? htmlspecialchars($citation['text']) : 'Sem descrição');
                                            echo $citationText;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div style="text-align: center; margin-top: 3rem; padding-bottom: 2rem;">
                <p style="color: #6c757d; font-size: 0.9rem;">
                    Total de <strong><?= $totalCitations; ?></strong> citações encontradas em
                    <strong><?= $totalAuthors; ?></strong> autores
                </p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= view('BrapciLabs/layout/footer'); ?>
