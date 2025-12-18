<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <!-- Cabeçalho do Projeto -->
    <?= view('BrapciLabs/widget/projects/header', ['project' => $project ?? null]); ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Autores do Projeto</h4>
    </div>

    <!-- ===============================
         LISTA DE AUTORES
    ================================ -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">

            <h5 class="card-title mb-3">Autores vinculados</h5>

            <?php if (empty($authors)): ?>

                <div class="alert alert-info">
                    Nenhum autor vinculado a este projeto.
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
                                                target="_blank"
                                                class="link">
                                                <?= esc($a['lattes_id']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= esc($a['brapci_id']) ?></td>

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
                <code>Nome | lattes_id | brapci_id</code><br>
                Exemplo:<br>
                <code>René Faustino Gabriel Jr. | 1234567890123456 | 42</code>
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