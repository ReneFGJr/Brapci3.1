<main class="content">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Referências agrupadas <span class="badge bg-secondary"><?= count($references) ?></span></h1>
        <a href="<?= site_url('labs/thinkers/' . (int) $pensador['id']) ?>" class="btn btn-outline-secondary">Voltar ao pensador</a>
    </div>
    <section class="card card-dashboard p-4 mb-4" style="overflow-wrap: anywhere;">
        <h2 class="h5">Obra normalizada #<?= (int) $reference['id_ca'] ?></h2>
        <p><?= esc($reference['ca_text'] ?? '') ?></p>
        <p class="mb-0"><strong>DOI:</strong> <?= esc($reference['ca_doi'] ?? '') ?></p>
    </section>
    <?php if ($references === []): ?>
        <div class="alert alert-info">Nenhuma referência vinculada a este ID normalizado.</div>
    <?php else: ?>
        <?php foreach ($references as $item): ?>
            <article class="card card-dashboard p-4 mb-3" style="overflow-wrap: anywhere;">
                <h2 class="h6">Referência #<?= (int) $item['id_ca'] ?></h2>
                <p><?= esc($item['ca_text'] ?? '') ?></p>
                <p><strong>Ano da referência:</strong> <?= esc($item['ca_year'] ?? '') ?><br>
                    <strong>Ano do trabalho:</strong> <?= esc($item['ca_year_origem'] ?? '') ?><br>
                    <strong>DOI:</strong> <?= esc($item['ca_doi'] ?? '') ?></p>
                <?php if ((int) $item['ca_rdf'] > 0): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="https://brapci.inf.br/v/<?= (int) $item['ca_rdf'] ?>" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener noreferrer">Ver trabalho #<?= (int) $item['ca_rdf'] ?> na BRAPCI</a>
                        <a href="<?= site_url('labs/cited/work/' . (int) $item['ca_rdf']) ?>" class="btn btn-outline-secondary btn-sm">Ver referências do trabalho</a>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Trabalho RDF não informado.</p>
                <?php endif ?>
            </article>
        <?php endforeach ?>
    <?php endif ?>
</main>
