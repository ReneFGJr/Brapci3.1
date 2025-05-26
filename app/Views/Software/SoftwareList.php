    <div class="container mt-5">
        <h1 class="mb-4">Softwares Cadastrados</h1>

        <!-- Botão para adicionar novo registro -->
        <a href="<?= site_url('guide/software/create') ?>" class="btn btn-success mb-3">
            <i class="bi bi-plus-circle"></i> Novo Software
        </a>

        <?php if (! empty($softwares) && is_array($softwares)): ?>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Versão</th>
                        <th>Criado em</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($softwares as $soft): ?>
                        <tr>
                            <td><?= esc($soft['id_s']) ?></td>
                            <td><?= esc($soft['s_name']) ?></td>
                            <td><?= esc($soft['s_description']) ?></td>
                            <td><?= esc($soft['s_version']) ?></td>
                            <td><?= esc(date('d/m/Y H:i', strtotime($soft['created_at']))) ?></td>
                            <td class="text-center">
                                <!-- Editar -->
                                <a href="<?= site_url('guide/software/view/' . $soft['id_s']) ?>"
                                    class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Editar -->
                                <a href="<?= site_url('guide/software/edit/' . $soft['id_s']) ?>"
                                    class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Excluir -->
                                <form action="<?= site_url('guide/software/delete/' . $soft['id_s']) ?>"
                                    method="post"
                                    class="d-inline"
                                    onsubmit="return confirm('Confirma a exclusão deste software?');">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        title="Excluir">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Nenhum software cadastrado.</div>
        <?php endif; ?>
    </div>