<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Importar referências</h1>
            <p class="text-muted mb-0">Informe uma referência por linha.</p>
        </div>
        <a href="<?= site_url('labs/cited_process') ?>" class="btn btn-outline-primary">Voltar</a>
    </div>

    <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $color): ?>
        <?php if ($message = session()->getFlashdata($key)): ?>
            <div class="alert alert-<?= esc($color) ?>"><?= esc($message) ?></div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (! empty($success)): ?>
        <div class="alert alert-success"><?= esc($success) ?></div>
    <?php endif; ?>

    <section class="card card-dashboard p-4">
        <form method="post" action="<?= site_url('labs/import') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="referencias" class="form-label">Referências</label>
                <textarea class="form-control" id="referencias" name="referencias" rows="15"
                    required><?= esc(old('referencias')) ?></textarea>
                <div class="form-text">Linhas vazias e referências já importadas serão ignoradas.</div>
            </div>
            <button type="submit" class="btn btn-primary">Importar referências</button>
        </form>
    </section>

    <?php if (! empty($result['similares'])): ?>
        <section class="card card-dashboard p-4 mt-4">
            <h2 class="h5 mb-3">Referências não importadas por similaridade</h2>
            <?php foreach ($result['similares'] as $similar): ?>
                <article class="border rounded p-3 mb-3">
                    <div class="mb-3">
                        <strong>Referência anterior</strong>
                        <div><?= esc($similar['anterior']) ?></div>
                    </div>
                    <div>
                        <strong>Referência atual</strong>
                        <span class="badge bg-warning text-dark ms-2">
                            <?= number_format($similar['percentual'], 2, ',', '.') ?>%
                        </span>
                        <div><?= esc($similar['atual']) ?></div>
                        <?php if (! empty($similar['doi_completado'])): ?>
                            <div class="text-success mt-2">
                                DOI preenchido na referência anterior: <?= esc($similar['doi_completado']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>
