<!-- Conteúdo -->
<div class="content">
    <div class="container mt-4">
        <h4 class="mb-3">
            <i class="bi bi-upload"></i> Importação de arquivo RIS
        </h4>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">

                <form action="<?= site_url('labs/importRIS') ?>"
                    method="post"
                    enctype="multipart/form-data">

                    <?= csrf_field() ?>

                    <input type="hidden" name="project_id" value="<?= esc($project_id) ?>">

                    <div class="mb-3">
                        <label for="ris_file" class="form-label">
                            Selecione o arquivo RIS
                        </label>

                        <input
                            type="file"
                            name="ris_file"
                            id="ris_file"
                            class="form-control"
                            accept=".ris"
                            required>

                        <div class="form-text">
                            Apenas arquivos no formato <strong>.RIS</strong>.
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-cloud-upload"></i> Importar
                        </button>

                        <a href="<?= site_url('brapcilab') ?>" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>