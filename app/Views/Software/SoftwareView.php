    <div class="container mt-5">
        <h1 class="mb-4">Detalhes do Software</h1>

        <?php if (! empty($software)): ?>
            <div class="card">
                <div class="card-header">
                    <strong><?= esc($software['s_name']) ?></strong>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> <?= esc($software['id_s']) ?></p>
                    <p><strong>Descrição:</strong><br>
                        <?= nl2br(esc($software['s_description'])) ?></p>
                    <p><strong>URL:</strong>
                        <?php if (! empty($software['s_url'])): ?>
                            <a href="<?= esc($software['s_url']) ?>" target="_blank">
                                <?= esc($software['s_url']) ?>
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </p>
                    <p><strong>Versão:</strong> <?= esc($software['s_version']) ?></p>
                    <p><strong>Criado em:</strong>
                        <?= esc(date('d/m/Y H:i', strtotime($software['created_at']))) ?></p>
                </div>
                <div class="card-footer text-end">
                    <a href="<?= site_url('guide/software/edit/' . $software['id_s']) ?>"
                        class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil-square"></i> Editar
                    </a>
                    <form action="<?= site_url('guide/software/delete/' . $software['id_s']) ?>"
                        method="post" class="d-inline"
                        onsubmit="return confirm('Deseja realmente excluir este software?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash-fill"></i> Excluir
                        </button>
                    </form>
                    <a href="<?= site_url('guide/software/list') ?>"
                        class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Voltar
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Software não encontrado.
                <a href="<?= site_url('guide/software/SoftwareList') ?>" class="alert-link">Voltar a lista</a>.
            </div>
        <?php endif; ?>
    </div>
