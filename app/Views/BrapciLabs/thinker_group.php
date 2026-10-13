<?php $id = (int) $pensador['id']; ?>
<main class="content">
    <h1 class="h3">Agrupar referências — <?= esc($pensador['nome']) ?></h1>
    <?php if ($message = session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc($message) ?></div><?php endif ?>
    <?php if ($review): ?>
        <div class="alert alert-warning">
            <strong>Confirme o resumo antes de gravar.</strong>
            <?= count($works) ?> obras serão unificadas no ID #<?= $targetId ?>.
            O texto, autores, ano e DOI desse ID serão mantidos. As citações e os vínculos com todos os pensadores
            serão transferidos para ele; os demais registros normalizados selecionados serão removidos.
        </div>
    <?php else: ?>
        <p>Selecione as obras duplicadas e marque o ID que deve ser mantido. A próxima etapa mostra o resumo antes de gravar.</p>
    <?php endif ?>
    <form method="post" action="<?= site_url('labs/thinkers/' . $id . '/group' . ($review ? '/confirm' : '')) ?>" class="card card-dashboard p-4">
        <?= csrf_field() ?>
        <?php if ($review): ?><input type="hidden" name="token" value="<?= esc($token, 'attr') ?>"><?php endif ?>
        <?php if (!$works): ?><p>Nenhuma obra vinculada.</p><?php endif ?>
        <?php foreach ($works as $work): ?>
            <section class="border rounded p-3 mb-3" style="overflow-wrap: anywhere;">
                <div class="d-flex flex-wrap gap-3 align-items-center mb-2">
                    <strong>#<?= (int) $work['id_ca'] ?><?= $review && (int) $work['id_ca'] === $targetId ? ' — ID mantido' : '' ?></strong>
                    <?php if (!$review): ?>
                        <label><input type="checkbox" name="references[]" value="<?= (int) $work['id_ca'] ?>"> Agrupar</label>
                        <label><input type="radio" name="target_id" value="<?= (int) $work['id_ca'] ?>" required> Manter este ID</label>
                    <?php endif ?>
                </div>
                <p><?= esc($work['ca_text'] ?? '') ?></p>
                <p class="mb-0"><strong>Autores:</strong> <?= esc($work['ca_authors'] ?? '') ?><br>
                    <strong>Ano:</strong> <?= esc($work['ca_year'] ?? '') ?> · <strong>DOI:</strong> <?= esc($work['ca_doi'] ?? '') ?><br>
                    <strong>Cited by:</strong> <?= $work['cited_by'] === null ? '—' : (int) $work['cited_by'] ?></p>
            </section>
        <?php endforeach ?>
        <div class="d-flex flex-wrap gap-2">
            <?php if ($works): ?><button type="submit" class="btn btn-primary"><?= $review ? 'Confirmar e gravar agrupamento' : 'Revisar agrupamento' ?></button><?php endif ?>
            <a class="btn btn-outline-secondary" href="<?= site_url('labs/thinkers/' . $id . ($review ? '/group' : '')) ?>">Cancelar</a>
        </div>
    </form>
</main>
