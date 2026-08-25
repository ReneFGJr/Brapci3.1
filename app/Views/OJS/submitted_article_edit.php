<?php
$value = static function (string $field) use ($article): string {
    $oldValue = old($field);
    return esc($oldValue !== null ? $oldValue : ($article[$field] ?? ''), 'attr');
};
?>
<main class="bg-light pt-5">
    <div class="container-fluid px-3 px-lg-5 py-5">
        <div class="card shadow-lg p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h2 mb-1">Editar artigo submetido</h1>
                    <p class="text-muted mb-0">ID local <?= esc($article['idR']) ?> · Submissão OJS <?= esc($article['journal_submit_id']) ?></p>
                </div>
                <a class="btn btn-outline-secondary" href="<?= base_url('ojs/articles_submied/view/' . $article['idR']) ?>">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
            </div>

            <?= view('OJS/partials/selected_journal', ['journal' => $journal]) ?>

            <?php if ($errors !== []): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('ojs/submit/edit/' . $article['idR']) ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="Title">Título</label>
                        <textarea class="form-control" id="Title" name="Title" rows="3" required><?= esc(old('Title') ?? $article['Title'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="Authors">Autores</label>
                        <textarea class="form-control" id="Authors" name="Authors" rows="3"><?= esc(old('Authors') ?? $article['Authors'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="Affiliation">Afiliação</label>
                        <input class="form-control" id="Affiliation" name="Affiliation" value="<?= $value('Affiliation') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="Year">Ano</label>
                        <input class="form-control" type="number" id="Year" name="Year" min="1000" max="9999" value="<?= $value('Year') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="Vol">Volume/Seção</label>
                        <input class="form-control" id="Vol" name="Vol" value="<?= $value('Vol') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="Num">Número</label>
                        <input class="form-control" id="Num" name="Num" value="<?= $value('Num') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="PagINI">Página inicial</label>
                        <input class="form-control" id="PagINI" name="PagINI" value="<?= $value('PagINI') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="PagEND">Página final</label>
                        <input class="form-control" id="PagEND" name="PagEND" value="<?= $value('PagEND') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="Keywords">Palavras-chave</label>
                        <textarea class="form-control" id="Keywords" name="Keywords" rows="2"><?= esc(old('Keywords') ?? $article['Keywords'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-success btn-lg" type="submit"><i class="bi bi-check-lg me-1"></i>Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
</main>