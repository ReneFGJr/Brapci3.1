<div class="content">
    <table class="table align-middle mb-0 full">

        <thead class="table-light">
            <tr>
                <th style="width: 30%">Projeto</th>
                <th style="width: 35%">Descrição</th>
                <th>Status</th>
                <th style="width: 25%" class="text-end">Ações</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($projects as $p): ?>
                <tr class="<?= ($current == $p['id']) ? 'table-primary' : '' ?>">

                    <!-- Nome -->
                    <td>
                        <strong><?= esc($p['project_name']) ?></strong>

                        <?php if ($current == $p['id']): ?>
                            <span class="badge bg-primary ms-2">
                                Ativo
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Descrição -->
                    <td class="small text-muted">
                        <?= esc($p['description'] ?? 'Sem descrição') ?>
                    </td>

                    <!-- Status -->
                    <td>
                        <span class="badge bg-secondary">
                            <?= esc($p['status']) ?>
                        </span>
                    </td>

                    <!-- Ações -->
                    <td class="text-end">

                        <div class="d-flex justify-content-end gap-2">

                            <!-- Usar projeto -->
                            <form method="post"
                                action="<?= base_url('labs/projects/set') ?>">

                                <?= csrf_field() ?>

                                <input type="hidden"
                                    name="project_id"
                                    value="<?= $p['id'] ?>">

                                <?php if ($current == $p['id']): ?>
                                    <button class="btn btn-sm btn-outline-primary" disabled>
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </button>
                                <?php endif; ?>

                            </form>
                        </div>

                    </td>

                </tr>
            <?php endforeach; ?>

        </tbody>

    </table>

</div>