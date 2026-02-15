<div class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>
            <i class="bi bi-list-check"></i>
            Lista de Convites
        </h3>

        <a href="<?= base_url('invite/create') ?>"
            class="btn btn-success">
            <i class="bi bi-plus-circle"></i>
            Novo Convite
        </a>
    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Revista</th>
                            <th>Contato</th>
                            <th>Idioma</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (!empty($invites)): ?>
                            <?php foreach ($invites as $row): ?>

                                <?php
                                $statusColor = 'secondary';

                                switch ($row['iv_status']) {
                                    case 1:
                                        $statusColor = 'primary';
                                        break;
                                    case 2:
                                        $statusColor = 'warning';
                                        break;
                                    case 3:
                                        $statusColor = 'info';
                                        break;
                                    case 4:
                                        $statusColor = 'dark';
                                        break;
                                    case 5:
                                        $statusColor = 'success';
                                        break;
                                    case 6:
                                        $statusColor = 'success';
                                        break;
                                    case 9:
                                        $statusColor = 'danger';
                                        break;
                                }
                                ?>

                                <tr>
                                    <td><?= $row['id_iv'] ?></td>

                                    <td>
                                        <strong><?= esc($row['iv_journal']) ?></strong><br>
                                        <small class="text-muted">
                                            <a href="<?= esc($row['iv_url']) ?>" target="_blank">
                                                <?= esc($row['iv_url']) ?>
                                            </a>
                                        </small>
                                    </td>

                                    <td>
                                        <strong><?= esc($row['iv_contact_name']) ?></strong><br>
                                        <small><?= esc($row['iv_contact']) ?></small>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?= strtoupper($row['iv_language']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-<?= $statusColor ?>">
                                            <?= esc($status[$row['iv_status']]) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <small>
                                            <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                        </small>
                                    </td>

                                    <td class="text-end">

                                        <a href="<?= base_url('admin/source/Invitation/view/' . $row['id_iv']) ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>

                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        Nenhum convite encontrado.
                                    </div>
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </div>

    </div>

</div>