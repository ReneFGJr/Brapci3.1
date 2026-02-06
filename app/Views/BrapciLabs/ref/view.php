<div class="content">
    <?php
    /**
     * View: Detalhes do Artigo
     * Espera um array $data com os metadados do artigo
     */
    ?>

    <div class="container my-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <!-- Título -->
                <div class="container">
                    <div class="row">
                        <div class="col-md-10">
                            <h4 class="card-title mb-2">
                            <?= esc($data['title']) ?>
                            </h4>
                        </div>
                        <div class="col-md-2 text-end">
                            <?= view('BrapciLabs/ref/view_brapci'); ?>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Autores -->
                <p class="text-muted mb-1">
                    <strong>Autores:</strong>
                    <?= esc($data['authors']) ?>
                </p>

                <!-- Periódico -->
                <p class="mb-1">
                    <strong>Periódico:</strong>
                    <?= esc($data['journal']) ?>
                </p>

                <!-- Ano / Tipo -->
                <p class="mb-3">
                    <strong>Ano:</strong> <?= esc($data['year']) ?> |
                    <strong>Tipo RIS:</strong> <?= esc($data['ris_type']) ?>
                </p>

                <!-- DOI / URL -->
                <p class="mb-3">
                    <?php if (!empty($data['doi'])): ?>
                        <strong>DOI:</strong>
                        <a href="https://doi.org/<?= esc($data['doi']) ?>" target="_blank">
                            <?= esc($data['doi']) ?>
                        </a>
                    <?php else: ?>
                        <strong>URL:</strong>
                        <a href="<?= esc($data['url']) ?>" target="_blank">
                            <?= esc($data['url']) ?>
                        </a>
                    <?php endif; ?>
                </p>

                <hr>

                <!-- Resumo -->
                <h6 class="text-primary">Resumo</h6>
                <p style="text-align: justify;">
                    <?= esc($data['abstract']) ?>
                </p>

                <hr>

                <!-- Palavras-chave -->
                <h6 class="text-primary">Palavras-chave</h6>

                <?php
                $keywords = array_filter(array_map('trim', explode(';', $data['keywords'])));
                ?>

                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($keywords as $kw): ?>
                        <span class="badge bg-secondary">
                            <?= esc($kw) ?>
                        </span>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- Rodapé -->
            <div class="card-footer text-muted small d-flex justify-content-between">
                <span>
                    ID interno: <?= esc($data['id']) ?>
                </span>
                <span>
                    Criado em: <?= date('d/m/Y H:i', strtotime($data['created_at'])) ?>
                </span>
            </div>
        </div>
    </div>

    <div>
        <?php echo view('BrapciLabs/ref/view_cited'); ?>
    </div>
</div>