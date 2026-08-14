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

        <div class="mt-3">
            <button
                class="btn btn-outline-primary"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#importacaoPorId"
                aria-expanded="false"
                aria-controls="importacaoPorId">
                <i class="bi bi-list-ol"></i> Importação por ID
            </button>
        </div>

        <div class="collapse mt-3" id="importacaoPorId">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Importar IDs da BRAPCI</h5>
                    <p class="text-muted">
                        Informe um ID por linha ou separe os IDs por vírgula, espaço ou ponto e vírgula.
                    </p>

                    <form action="<?= site_url('labs/importIDs') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="brapci_ids" class="form-label">IDs da BRAPCI</label>
                            <textarea
                                name="brapci_ids"
                                id="brapci_ids"
                                class="form-control"
                                rows="8"
                                placeholder="12345&#10;67890&#10;112233"
                                required></textarea>
                            <div class="form-text">Limite de 1.000 IDs por importação.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-cloud-download"></i> Importar IDs
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
