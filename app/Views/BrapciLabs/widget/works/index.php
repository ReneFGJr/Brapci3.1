<div class="content">

    <!-- Título -->
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-journal-text fs-4 me-2"></i>
        <h4 class="mb-0">Trabalhos do Projeto</h4>
    </div>

    <?php if (empty($works)): ?>
        <div class="alert alert-warning">
            Nenhum trabalho encontrado para este projeto.
        </div>
    <?php else: ?>

        <!-- ===============================
             FILTRO POR TÍTULO
        ================================ -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">

                <form method="get" class="row g-3 align-items-end">

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">
                            Buscar pelo título
                        </label>
                        <input type="text"
                            name="q"
                            value="<?= esc($q ?? '') ?>"
                            class="form-control"
                            placeholder="Digite parte do título do trabalho">
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>

                    <div class="col-md-3">
                        <a href="<?= current_url() ?>"
                            class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle"></i> Limpar
                        </a>
                    </div>

                </form>

            </div>
        </div>

        <!-- ===============================
             RESUMOS
        ================================ -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
            <a href="<?= base_url('labs/works/cloud_keys') ?>" title="Nuvem de Palavras" class="link"><i class="bi bi-cloud"></i></a>
            </div>
        </div>

        <!-- ===============================
             TABELA DE TRABALHOS
        ================================ -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Título / Autores</th>
                        <th style="width:90px">Ano</th>
                        <th>Periódico / Evento</th>
                        <th style="width:140px" class="text-center">Ações</th>
                    </tr>
                </thead>

                <tbody class="accordion" id="worksAccordion">

                    <?php foreach ($works as $i => $w): ?>
                        <!-- Linha principal -->
                        <tr>
                            <td><?= $i + 1 ?></td>

                            <td>
                                <strong><?= esc($w['title']) ?></strong><br>
                                <small class="text-muted">
                                    <?= esc($w['authors']) ?>
                                </small>
                            </td>

                            <td><?= esc($w['year']) ?></td>

                            <td><?= esc($w['journal']) ?></td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#work<?= $w['id'] ?>"
                                    title="Ver detalhes">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <a href="<?= base_url('labs/works/view/' . $w['id']) ?>"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="Acessar trabalho">
                                    <i class="bi bi-box"></i>
                                </a>

                                <?php if (!empty($w['url'])): ?>
                                    <a href="<?= esc($w['url']) ?>"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="Acessar trabalho">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- ===============================
                         DETALHES (ACCORDION)
                    ================================ -->
                        <tr class="accordion-collapse collapse"
                            id="work<?= $w['id'] ?>"
                            data-bs-parent="#worksAccordion">

                            <td colspan="5" class="bg-light">

                                <div class="card border-0">
                                    <div class="card-body">

                                        <!-- Metadados -->
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <strong>Ano:</strong> <?= esc($w['year']) ?>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Tipo:</strong> <?= esc($w['ris_type']) ?>
                                            </div>
                                            <div class="col-md-4 text-muted">
                                                ID: <?= $w['id'] ?>
                                            </div>
                                        </div>

                                        <!-- Resumo -->
                                        <?php if (!empty($w['abstract'])): ?>
                                            <div class="mb-3">
                                                <h6 class="fw-semibold">Resumo</h6>
                                                <p class="mb-0 small">
                                                    <?= esc($w['abstract']) ?>
                                                </p>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Palavras-chave -->
                                        <?php if (!empty($w['keywords'])): ?>
                                            <div>
                                                <h6 class="fw-semibold">Palavras-chave</h6>
                                                <?php foreach (explode(';', $w['keywords']) as $kw): ?>
                                                    <span class="badge bg-secondary me-1 mb-1">
                                                        <?= esc(trim($kw)) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>

                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    <?php endif; ?>

    <?php if (isset($pager)): ?>
        <div class="d-flex justify-content-center mt-4">
            <?= $pager->links('default', 'bootstrap_full') ?>
        </div>
    <?php endif; ?>


</div>