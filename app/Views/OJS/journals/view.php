<main class="pt-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Detalhes da revista OJS</h1>
                    <div>
                        <a class="btn btn-outline-primary" href="<?= base_url('ojs/journals/edit/' . $journal['id']) ?>" title="Editar revista">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                            <span class="visually-hidden">Editar revista</span>
                        </a>
                        <a class="btn btn-outline-secondary" href="<?= base_url('ojs/journals') ?>">Voltar</a>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Nome</dt>
                            <dd class="col-sm-8"><?= esc($journal['name']) ?></dd>

                            <dt class="col-sm-4">Sigla</dt>
                            <dd class="col-sm-8"><?= esc($journal['acronym'] ?: '—') ?></dd>

                            <dt class="col-sm-4">URL base</dt>
                            <dd class="col-sm-8"><a href="<?= esc($journal['base_url'], 'attr') ?>" target="_blank" rel="noopener noreferrer"><?= esc($journal['base_url']) ?></a></dd>

                            <dt class="col-sm-4">Caminho da revista</dt>
                            <dd class="col-sm-8"><code><?= esc($journal['context_path']) ?></code></dd>

                            <dt class="col-sm-4">APIKEY</dt>
                            <dd class="col-sm-8"><span class="text-muted">••••••••</span></dd>
                            <dt class="col-sm-4">APIKEY do Editor</dt>
                            <dd class="col-sm-8"><span class="text-muted">••••••••</span></dd>


                            <dt class="col-sm-4">Revista padrão</dt>
                            <dd class="col-sm-8"><?= !empty($journal['is_default']) ? 'Sim' : 'Não' ?></dd>

                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                <span class="badge <?= !empty($journal['active']) ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= !empty($journal['active']) ? 'Ativa' : 'Inativa' ?>
                                </span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>