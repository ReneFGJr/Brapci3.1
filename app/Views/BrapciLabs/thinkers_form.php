<main class="content">
    <h1 class="h3 mb-4"><?= esc($title) ?></h1>
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <section class="card card-dashboard p-4">
        <form method="post" action="<?= site_url(isset($pensador['id']) ? 'labs/thinkers/' . (int) $pensador['id'] . '/update' : 'labs/thinkers') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do pensador</label>
                <input type="text" id="nome" name="nome" class="form-control" maxlength="255" required value="<?= esc(old('nome', $pensador['nome'] ?? '', false), 'attr') ?>">
            </div>
            <div class="mb-3">
                <label for="nome_citacao" class="form-label">Nome de citação</label>
                <input type="text" id="nome_citacao" name="nome_citacao" class="form-control" maxlength="255" required value="<?= esc(old('nome_citacao', $pensador['nome_citacao'] ?? '', false), 'attr') ?>">
            </div>
            <div class="mb-3">
                <label for="link_wikipedia" class="form-label">Link da Wikipédia (opcional)</label>
                <input type="url" id="link_wikipedia" name="link_wikipedia" class="form-control" maxlength="2048" value="<?= esc(old('link_wikipedia', $pensador['link_wikipedia'] ?? '', false), 'attr') ?>">
            </div>
            <div class="mb-3">
                <label for="link_wikidata" class="form-label">Link da Wikidata (opcional)</label>
                <input type="url" id="link_wikidata" name="link_wikidata" class="form-control" maxlength="2048" value="<?= esc(old('link_wikidata', $pensador['link_wikidata'] ?? '', false), 'attr') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Salvar pensador</button>
            <a href="<?= site_url('labs/thinkers') ?>" class="btn btn-outline-secondary">Cancelar</a>
        </form>
    </section>
</main>
