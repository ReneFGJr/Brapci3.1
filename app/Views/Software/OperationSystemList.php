    <div class="container mt-5">
        <h1 class="mb-4">Sistemas Operacionais Cadastrados</h1>

        <!-- Botão para adicionar novo OS -->
        <a href="<?= site_url('guide/operational-system/create') ?>" class="btn btn-success mb-3">
            <i class="bi bi-plus-circle"></i> Novo SO
        </a>

        <?php if (! empty($oses) && is_array($oses)): ?>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>URL</th>
                        <th>Versão</th>
                        <th>Criado em</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($oses as $os): ?>
                        <tr>
                            <td><?= esc($os['id_os']) ?></td>
                            <td><?= esc($os['os_name']) ?></td>
                            <td><?= esc($os['os_description']) ?></td>
                            <td>
                                <?php if (! empty($os['os_url'])): ?>
                                    <a href="<?= esc($os['os_url']) ?>" target="_blank">
                                        <?= esc($os['os_url']) ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($os['os_version']) ?></td>
                            <td><?= esc(date('d/m/Y H:i', strtotime($os['created_at']))) ?></td>
                            <td class="text-center">
                                <!-- Editar -->
                                <a href="<?= site_url('guide/operational-system/edit/' . $os['id_os']) ?>"
                                    class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Excluir -->
                                <form action="<?= site_url('guide/operational-system/delete/' . $os['id_os']) ?>"
                                    method="post"
                                    class="d-inline"
                                    onsubmit="return confirm('Confirma a exclusão deste SO?');">
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
            <div class="alert alert-info">Nenhum Sistema Operacional cadastrado.</div>
        <?php endif; ?>

    </div>
