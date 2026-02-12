<?php

/**
 * View: Participantes do Projeto
 * Espera:
 *  - $collaborators (array)
 *  - $project_id
 */
?>
<div class="content">
    <div class="container my-5">

        <div class="card shadow-lg border-0">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">
                    <i class="bi bi-people-fill"></i> Participantes do Projeto
                </h4>
            </div>

            <div class="card-body">

                <!-- LISTA DE COLABORADORES -->
                <?php if (!empty($collaborators)) : ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Instituição</th>
                                    <th>Perfil</th>
                                    <th>Último Acesso</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($collaborators as $col) : ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($col['us_nome']) ?></strong>
                                        </td>
                                        <td>
                                            <?= esc($col['us_institution'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?= esc($col['role_name']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= esc($col['us_lastaccess']) ?>
                                        </td>
                                        <td class="text-end">
                                            <form method="post"
                                                action="<?= site_url('labs/project/collaborators/delete/' . $col['id_rpc']) ?>"
                                                onsubmit="return confirm('Deseja realmente excluir este participante?');">

                                                <?= csrf_field() ?>

                                                <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="alert alert-warning">
                        Nenhum participante vinculado ao projeto.
                    </div>
                <?php endif; ?>

            </div>
        </div>


        <!-- FORMULÁRIO DE BUSCA E INCLUSÃO -->
        <div class="card shadow mt-4 border-0">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus"></i> Adicionar Novo Participante
                </h5>
            </div>

            <div class="card-body">

                <!-- BUSCA -->
                <form method="get" action="<?= site_url('labs/project/collaborators/search') ?>" class="row g-3">

                    <input type="hidden" name="project_id" value="<?= $project_id ?>">

                    <div class="col-md-8">
                        <label class="form-label">Buscar por Nome ou E-mail</label>
                        <input type="text"
                            name="q"
                            class="form-control"
                            placeholder="Digite o nome ou e-mail do usuário"
                            required>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-search"></i> Pesquisar
                        </button>
                    </div>

                </form>

                <!-- RESULTADOS DA BUSCA -->
                <?php if (!empty($search_results)) : ?>
                    <hr>

                    <h6>Resultados da busca:</h6>

                    <div class="list-group">
                        <?php foreach ($search_results as $user) : ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">

                                <div>
                                    <strong><?= esc($user['us_nome']) ?></strong> (<?= esc($user['us_institution']) ?>)
                                    -
                                    <small class="text-muted"><?= esc($user['us_email']) ?></small>
                                    <sup>(ID:<?= esc($user['id_us']) ?>)</sup>
                                </div>

                                <form method="post"
                                    action="<?= site_url('labs/project/collaborators/add') ?>">

                                    <?= csrf_field() ?>

                                    <input type="hidden" name="project_id" value="<?= $project_id ?>">
                                    <input type="hidden" name="user_id" value="<?= $user['id_us'] ?>">

                                    <button type="submit"
                                        class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle"></i> Adicionar
                                    </button>
                                </form>

                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>

            </div>
        </div>

    </div>
</div>