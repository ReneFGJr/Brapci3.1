<?php $memberNames = array_column($members, 'nome', 'id'); ?>
<main class="content">
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-4">
        <h1 class="h3"><?= esc($group['nome']) ?></h1>
        <a class="btn btn-outline-primary" href="<?= site_url('labs/thinkers') ?>">Buscar e adicionar pensadores</a>
    </div>
    <?php if ($message = session()->getFlashdata('success')): ?><div class="alert alert-success" role="alert"><?= esc($message) ?></div><?php endif ?>
    <h2 class="h4">Pensadores do grupo (<?= count($members) ?>)</h2>
    <div class="card card-dashboard p-3 mb-4">
        <?php if (!$members): ?><p class="mb-0">Nenhum pensador no grupo. Use a busca para adicionar membros.</p><?php endif ?>
        <?php foreach ($members as $member): ?>
            <div class="d-flex justify-content-between align-items-center gap-2 border-bottom py-2">
                <a href="<?= site_url('labs/thinkers/' . (int) $member['id']) ?>"><?= esc($member['nome']) ?></a>
                <form method="post" action="<?= site_url('labs/thinker-groups/' . (int) $group['id'] . '/remove/' . (int) $member['id']) ?>">
                    <?= csrf_field() ?><button type="submit" class="btn btn-outline-danger btn-sm">Retirar do grupo</button>
                </form>
            </div>
        <?php endforeach ?>
    </div>
    <h2 class="h4">Obras do grupo (<?= count($works) ?>)</h2>
    <p class="text-muted">Cada obra aparece uma vez, com os pensadores do grupo aos quais está vinculada.</p>
    <?php if (!$works): ?><div class="alert alert-info">Nenhuma obra vinculada aos pensadores deste grupo.</div><?php endif ?>
    <?php foreach ($works as $work): ?>
        <article class="card card-dashboard p-4 mb-3 text-break">
            <h3 class="h6"><?= esc($work['ca_text'] ?? '') ?></h3>
            <p><strong>Autores:</strong> <?= esc($work['ca_authors'] ?? '') ?><br>
                <strong>Ano:</strong> <?= (int) $work['ca_year'] ?: '—' ?>
                <?php if (!empty($work['ca_doi'])): ?><br><strong>DOI:</strong> <?= esc($work['ca_doi']) ?><?php endif ?>
            </p>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($work['thinkers'] as $thinkerId): ?>
                    <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('labs/thinkers/' . $thinkerId . '/works/' . (int) $work['id_ca']) ?>"><?= esc($memberNames[$thinkerId]) ?></a>
                <?php endforeach ?>
                <a class="btn btn-outline-primary btn-sm" href="<?= site_url('labs/cite_work/' . (int) $work['id_ca']) ?>">Trabalhos citantes</a>
            </div>
        </article>
    <?php endforeach ?>
</main>
