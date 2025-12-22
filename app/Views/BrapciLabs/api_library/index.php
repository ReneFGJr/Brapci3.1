<div class="container mt-4">
    <h2>Biblioteca de APIs</h2>

    <a href="<?= base_url('labs/api-library/create') ?>" class="btn btn-success mb-3">
        + Nova API
    </a>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Método</th>
                <th>Endpoint</th>
                <th>Status</th>
                <th width="180">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($apis as $api): ?>
                <tr>
                    <td><?= esc($api['nome']) ?></td>
                    <td><span class="badge bg-info"><?= $api['metodo'] ?></span></td>
                    <td><code><?= esc($api['endpoint']) ?></code></td>
                    <td><?= $api['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                    <td>
                        <a href="<?= base_url('labs/api-library/show/' . $api['id']) ?>" class="btn btn-sm btn-primary">Ver</a>
                        <a href="<?= base_url('labs/api-library/edit/' . $api['id']) ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="<?= base_url('labs/api-library/delete/' . $api['id']) ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Confirmar exclusão?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>