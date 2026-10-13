<main class="content">
    <h1 class="h3">Editar obra #<?= (int) $work['id_ca'] ?></h1>
    <p><?= esc($pensador['nome']) ?></p>
    <p class="text-muted">Esta edição atualiza a referência compartilhada com outros pensadores vinculados à mesma obra.</p>
    <?php if ($error): ?><div class="alert alert-danger"><?= esc($error) ?></div><?php endif ?>
    <form method="post" class="card card-dashboard p-4">
        <?= csrf_field() ?>
        <?php foreach (['ca_text' => 'Referência', 'ca_authors' => 'Autores', 'ca_year' => 'Ano', 'ca_doi' => 'DOI'] as $field => $label): ?>
            <label for="<?= $field ?>" class="form-label"><?= $label ?></label>
            <?php if ($field === 'ca_text'): ?>
                <textarea id="<?= $field ?>" name="<?= $field ?>" class="form-control mb-3" rows="5" required><?= esc($work[$field] ?? '') ?></textarea>
            <?php else: ?>
                <input id="<?= $field ?>" name="<?= $field ?>" class="form-control mb-3" value="<?= esc($work[$field] ?? '', 'attr') ?>">
            <?php endif ?>
        <?php endforeach ?>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="submit">Salvar obra</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('labs/thinkers/' . (int) $pensador['id']) ?>">Cancelar</a>
        </div>
    </form>
</main>
