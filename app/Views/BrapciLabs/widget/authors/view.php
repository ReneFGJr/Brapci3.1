<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <!-- Cabeçalho do Projeto -->
    <?= view('BrapciLabs/widget/projects/header', ['project' => $project ?? null]); ?>

    <!-- ===============================
         AÇÕES GERAIS
    ================================ -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

        <h4 class="mb-0">Autores do Projeto</h4>

        <div class="btn-group">

            <!-- Voltar ao Projeto -->
            <a href="<?= base_url('labs/'); ?>"
                class="btn btn-outline-secondary btn-sm ms-2">
                ⬅️ Voltar
            </a>

            <!-- Remover duplicações -->
            <a href="<?= base_url('labs/project/authors/deduplicate/' . $project['id']) ?>"
                class="btn btn-outline-warning btn-sm"
                onclick="return confirm('Deseja remover autores duplicados deste projeto?')">
                🧹 Remover duplicações
            </a>

            <!-- Checar IDs -->
            <a href="<?= base_url('labs/project/authors/check-ids/' . $project['id']) ?>"
                class="btn btn-outline-info btn-sm">
                🔍 Checar ID dos pesquisadores
            </a>

        </div>
    </div>

    <!-- ===============================
         FILTRO POR NOME
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

                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100">
                        🔎 Buscar
                    </button>
                </div>

                <div class="col-md-3">
                    <a href="<?= current_url() ?>"
                        class="btn btn-outline-secondary w-100">
                        ✖ Limpar filtro
                    </a>
                </div>

            </form>

        </div>
    </div>

    <!-- ===============================
         LISTA DE AUTORES
    ================================ -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">

            <h5 class="card-title mb-3">
                Autores vinculados
                <span class="text-muted small">
                    (<?= esc($total ?? count($authors)) ?> registros)
                </span>
            </h5>

            <?php if (empty($authors)): ?>

                <div class="alert alert-info">
                    Nenhum autor encontrado.
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Lattes</th>
                                <th>BRAPCI ID</th>
                                <th class="text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($authors as $a): ?>
                                <tr>

                                    <td><?= esc($a['nome']) ?></td>

                                    <td>
                                        <?php if (!empty($a['lattes_id'])): ?>
                                            <a href="https://lattes.cnpq.br/<?= esc($a['lattes_id']) ?>"
                                                target="_blank">
                                                <?= esc($a['lattes_id']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= $a['brapci_id'] ?: '<span class="text-muted">—</span>' ?>
                                    </td>

                                    <td class="text-end">
                                        <form method="post"
                                            action="<?= base_url('labs/project/authors/delete/' . $a['id']) ?>"
                                            onsubmit="return confirm('Remover este autor do projeto?')"
                                            class="d-inline">

                                            <?= csrf_field() ?>

                                            <button class="btn btn-sm btn-outline-danger">
                                                🗑️ Excluir
                                            </button>
                                        </form>
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
                    <?= $pager->links('default', 'bootstrap_full') ?>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <!-- ===============================
         INSERIR AUTORES (LOTE)
    ================================ -->
    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="card-title mb-2">Inserir autores em lote</h5>

            <p class="text-muted small">
                Um autor por linha, no formato:<br>
                <code>Nome | lattes_id | brapci_id</code>
            </p>

            <form method="post" action="<?= base_url('labs/project/authors/import') ?>">

                <?= csrf_field() ?>

                <div class="mb-3">
                    <textarea name="authors"
                        rows="6"
                        class="form-control"
                        placeholder="Nome | lattes_id | brapci_id"></textarea>
                </div>

                <button class="btn btn-primary">
                    ➕ Inserir autores
                </button>

            </form>

        </div>
    </div>

</div>