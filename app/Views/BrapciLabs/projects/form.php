<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><?= esc($title) ?></h3>

        <a href="<?= site_url('labs/projects') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <form method="post" action="<?= $action ?>" class="card shadow-sm">
        <?= csrf_field() ?>

        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">Nome do projeto</label>
                <input type="text"
                       name="project_name"
                       class="form-control"
                       required
                       value="<?= esc($project['project_name'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="description"
                          class="form-control"
                          rows="4"><?= esc($project['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="ativo" <?= (($project['status'] ?? '') == 'ativo') ? 'selected' : '' ?>>
                        Ativo
                    </option>
                    <option value="inativo" <?= (($project['status'] ?? '') == 'inativo') ? 'selected' : '' ?>>
                        Inativo
                    </option>
                </select>
            </div>

        </div>

        <div class="card-footer text-end">
            <button class="btn btn-success">
                <i class="bi bi-save"></i> Salvar
            </button>
        </div>

    </form>

</div>
