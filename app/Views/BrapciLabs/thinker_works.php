<?php $id = (int) $pensador['id']; ?>
<main class="content">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Trabalhos vinculados — <?= esc($pensador['nome']) ?> <span class="badge bg-secondary"><?= count($works) ?></span></h1>
        <a href="<?= site_url('labs/thinkers/' . $id) ?>" class="btn btn-outline-secondary">Voltar ao pensador</a>
    </div>
    <?php if ($works === []): ?>
        <div class="alert alert-info">Nenhum trabalho vinculado a este pensador.</div>
    <?php else: ?>
        <?php foreach ($works as $work): ?>
            <article class="card card-dashboard p-4 mb-3" style="overflow-wrap: anywhere;">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <strong>#<?= (int) $work['id_ca'] ?></strong>
                    <a href="<?= site_url('labs/cite_work/' . (int) $work['id_ca']) ?>" class="btn btn-outline-primary btn-sm">Cited by <?= $work['cited_by'] === null ? '—' : (int) $work['cited_by'] ?></a>
                </div>
                <p><?= esc($work['ca_text'] ?: 'Referência sem texto cadastrado.') ?></p>
                <p class="mb-0"><strong>Autores:</strong> <?= esc($work['ca_authors'] ?? '') ?><br>
                    <strong>Ano:</strong> <?= esc($work['ca_year'] ?? '') ?><br>
                    <strong>DOI:</strong> <?= esc($work['ca_doi'] ?? '') ?></p>
            </article>
        <?php endforeach ?>
    <?php endif ?>
</main>
