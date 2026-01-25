<div class="content">
    <!-- Cabeçalho do Projeto -->
    <?= view('BrapciLabs/widget/projects/header', ['project' => $project ?? null]); ?>

    <!-- ===============================
         AÇÕES GERAIS
    ================================ -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

        <div>
            <h4 class="mb-1">
                <i class="bi bi-person-badge me-1"></i>
                Autor selecionado
            </h4>
            <small class="text-muted">
                Visualização dos dados cadastrais do autor
            </small>
        </div>

        <a href="<?= base_url('labs/'); ?>"
            class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- ===============================
         DETALHES DO AUTOR
    ================================ -->
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h5 class="card-title mb-1">
                <i class="bi bi-info-circle me-1 text-primary"></i>
                Detalhes do Autor
            </h5>

            <div class="row g-3">

                <!-- Nome -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Nome</small>
                        <strong><?= esc($author['nome']) ?></strong>
                    </div>
                </div>

                <!-- ORCID -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">ORCID</small>
                        <?php if (!empty($author['orcid'])): ?>
                            <a href="https://orcid.org/<?= esc($author['orcid']) ?>"
                                target="_blank"
                                class="text-decoration-none">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <?= esc($author['orcid']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Não informado</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- BRAPCI ID -->
                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">BRAPCI ID</small>
                        <code><a href="https://brapci.inf.br/v/<?= esc($author['brapci_id']) ?>" target="_blank" class="link"><?= esc($author['brapci_id']) ?></a></code>
                        <!-- Atualizar dados BRAPCI -->
                        <a href="<?= base_url('labs/authority/update/Brapci/' . $author['id']); ?>"
                            class=""
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Atualizar dados a partir da BRAPCI">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>

                <!-- Lattes ID -->
                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Lattes ID</small>
                        <code><a href="https://lattes.cnpq.br/<?= esc($author['lattes_id']) ?>" target="_blank" class="link"><?= esc($author['lattes_id']) ?></a></code>
                    </div>
                </div>

                <!-- E-mail -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">E-mail</small>
                        <?php if (!empty($author['email'])): ?>
                            <a href="mailto:<?= esc($author['email']) ?>"
                                class="text-decoration-none">
                                <i class="bi bi-envelope"></i>
                                <?= esc($author['email']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Não informado</span>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <?php
    if (!empty($data['brapci'])) {
        echo view('BrapciLabs/widget/authors/brapci', ['brapci' => $data['brapci']]);
    }
    ?>
</div>
