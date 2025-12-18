<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<div class="content">

    <?= view('BrapciLabs/widget/projects/header', ['project' => $project ?? null]); ?>

    <div class="card shadow-sm">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <?= isset($codebook) ? 'Editar Anotação' : 'Nova Anotação' ?>
                </h4>

                <a href="<?= base_url('labs/project/codebook') ?>"
                    class="btn btn-outline-secondary btn-sm">
                    ← Voltar
                </a>
            </div>

            <form method="post"
                action="<?= isset($codebook)
                            ? base_url('labs/project/codebook/update/' . $codebook['id'])
                            : base_url('labs/project/codebook/create') ?>">

                <?= csrf_field() ?>

                <!-- TÍTULO -->
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text"
                        name="title"
                        class="form-control"
                        required
                        value="<?= esc($codebook['title'] ?? '') ?>">
                </div>

                <!-- TAGS -->
                <div class="mb-3">
                    <label class="form-label">Tags (separadas por vírgula)</label>
                    <input type="text"
                        name="tags"
                        class="form-control"
                        value="<?= isset($codebook['tags'])
                                    ? esc(implode(', ', json_decode($codebook['tags'], true)))
                                    : '' ?>">
                </div>

                <!-- CONTEÚDO -->
                <div class="mb-3">
                    <label class="form-label">Conteúdo</label>
                    <textarea name="content"
                        rows="10"
                        class="form-control"
                        required><?= esc($codebook['content'] ?? '') ?></textarea>
                </div>

                <button class="btn btn-primary">
                    <?= isset($codebook) ? 'Salvar Alterações' : 'Criar Anotação' ?>
                </button>

            </form>

        </div>

    </div>

</div>