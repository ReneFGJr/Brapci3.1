<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <!-- Cabeçalho do Projeto -->
    <?= view('BrapciLabs/widget/projects/header', ['project' => $project ?? null]); ?>

    <!-- ===============================
         AÇÕES GERAIS
    ================================ -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

        <h4 class="mb-0">Citações por Autores</h4>

        <div class="btn-group">

            <!-- Voltar ao Projeto -->
            <a href="<?= base_url('labs/'); ?>"
                class="btn btn-outline-secondary btn-sm ms-2">
                ⬅️ Voltar
            </a>

            <!-- Exportar -->
            <button class="btn btn-outline-primary btn-sm ms-2" id="exportBtn">
                📥 Exportar
            </button>

        </div>
    </div>

    <!-- ===============================
         ESTATÍSTICAS GERAIS
    ================================ -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total de Autores</h6>
                    <h3 class="mb-0"><?= esc($totalAuthors ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total de Citações</h6>
                    <h3 class="mb-0"><?= esc($totalCitations ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Média de Citações/Autor</h6>
                    <h3 class="mb-0"><?= number_format($avgCitations ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Autor Mais Citado</h6>
                    <p class="mb-0 small"><?= esc($topAuthor ?? 'N/A') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- ===============================
         FILTRO E BUSCA
    ================================ -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">

            <form method="get" class="row g-2 align-items-end">

                <div class="col-md-6">
                    <label class="form-label small mb-1">Buscar autor pelo nome</label>
                    <input type="text"
                        name="q"
                        value="<?= esc($q ?? '') ?>"
                        class="form-control"
                        placeholder="Digite parte do nome do autor">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">
                        🔎 Buscar
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="<?= current_url() ?>"
                        class="btn btn-outline-secondary w-100">
                        ✖ Limpar
                    </a>
                </div>

            </form>

        </div>
    </div>

    <!-- ===============================
         LISTA DE CITAÇÕES POR AUTORES
    ================================ -->
    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="card-title mb-3">
                Citações agrupadas por autor
                <span class="text-muted small">
                    (<?= esc($total ?? 0) ?> registros)
                </span>
            </h5>

            <?php if (empty($citationsByAuthors)): ?>

                <div class="alert alert-info">
                    Nenhuma citação encontrada.
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Autor</th>
                                <th class="text-center">Qtd. Citações</th>
                                <th class="text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($citationsByAuthors as $author): ?>
                                <tr>

                                    <td>
                                        <strong><?= esc($author['author_name'] ?? 'N/A') ?></strong>
                                        <?php if (!empty($author['lattes_id'])): ?>
                                            <br>
                                            <small class="text-muted">
                                                <a href="https://lattes.cnpq.br/<?= esc($author['lattes_id']) ?>"
                                                    target="_blank" class="text-muted">
                                                    Lattes: <?= esc($author['lattes_id']) ?>
                                                </a>
                                            </small>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-primary"><?= esc($author['citation_count'] ?? 0) ?></span>
                                    </td>

                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-info"
                                            onclick="toggleDetails(<?= esc($author['author_id'] ?? 0) ?>)"
                                            title="Ver citações deste autor">
                                            👁️ Detalhes
                                        </button>
                                    </td>

                                </tr>

                                <!-- Detalhes (inicialmente ocultos) -->
                                <tr id="details-<?= esc($author['author_id'] ?? 0) ?>" class="details-row" style="display:none;">
                                    <td colspan="3">
                                        <div class="ms-3 mt-2 mb-2">
                                            <h6>Citações deste autor:</h6>
                                            <ul class="small">
                                                <?php if (!empty($author['citations']) && is_array($author['citations'])): ?>
                                                    <?php foreach ($author['citations'] as $citation): ?>
                                                        <li>
                                                            <small class="text-muted"><?= esc($citation) ?></small>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li><small class="text-muted">Nenhuma citação encontrada</small></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <!-- ===============================
                     PAGINAÇÃO
                ================================ -->
                <div class="mt-3">
                    <?php if (!empty($pager)): ?>
                        <?= $pager->links('default', 'bootstrap_full') ?>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script>
function toggleDetails(authorId) {
    const detailsRow = document.getElementById('details-' + authorId);
    if (detailsRow) {
        detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
    }
}

document.getElementById('exportBtn').addEventListener('click', function() {
    alert('Funcionalidade de exportação em desenvolvimento');
    // TODO: Implementar exportação para CSV/Excel
});
</script>

<?= view('BrapciLabs/layout/footer'); ?>
