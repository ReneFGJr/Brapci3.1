<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-people"></i> Lista de Participantes (n= <?= count($pessoas); ?>)</h3>

        <a href="<?= base_url('g3vent/import') ?>" class="btn btn-success">
            <i class="bi bi-upload"></i> Importar Nomes
        </a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th style="width: 40%;">Nome</th>
                <th style="width: 40%;">E-mail</th>
                <th style="width: 20%;">Ação</th>
            </tr>
        </thead>

        <tbody>
            <?php if (isset($pessoas) && count($pessoas) > 0): ?>
                <?php foreach ($pessoas as $p): ?>
                    <tr>
                        <td><?= esc($p['n_nome']) ?></td>
                        <td><?= esc($p['n_email']) ?></td>
                        <td>
                            <a href="<?= base_url('events/edit/' . $p['id_n']) ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
