<div class="container my-4">

    <div class="card shadow border-0">

        <div class="card-body">

            <div class="row align-items-center">

                <!-- Coluna principal -->
                <div class="col-md-8">

                    <h3 class="mb-1">
                        <?= esc($invite['iv_journal']) ?>
                    </h3>

                    <p class="mb-2 text-muted">
                        <a href="<?= esc($invite['iv_url']) ?>" target="_blank">
                            <?= esc($invite['iv_url']) ?>
                        </a>
                    </p>

                    <div class="d-flex flex-wrap gap-3">

                        <div>
                            <strong>Contato:</strong><br>
                            <?= esc($invite['iv_contact_name']) ?><br>
                            <small><?= esc($invite['iv_contact']) ?></small>
                        </div>

                        <?php if (!empty($invite['iv_contact_2'])): ?>
                            <div>
                                <strong>Contato Secundário:</strong><br>
                                <small><?= esc($invite['iv_contact_2']) ?></small>
                            </div>
                        <?php endif; ?>

                        <div>
                            <strong>Idioma:</strong><br>
                            <span class="badge bg-light text-dark">
                                <?= strtoupper($invite['iv_language']) ?>
                            </span>
                        </div>

                    </div>

                </div>

                <!-- Coluna lateral -->
                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <?php
                    $statusColor = 'secondary';
                    switch ($invite['iv_status']) {
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

                    <div class="mb-2">
                        <span class="badge bg-<?= $statusColor ?> px-3 py-2 fs-6">
                            Status: <?= esc($status[$invite['iv_status']]) ?>
                        </span>
                    </div>

                    <small class="text-muted">
                        Criado em:<br>
                        <?= date('d/m/Y H:i', strtotime($invite['created_at'])) ?>
                    </small>

                </div>

            </div>

        </div>

    </div>

</div>