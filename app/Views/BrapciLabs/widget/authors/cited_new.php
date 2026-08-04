<?= view('BrapciLabs/layout/header'); ?>

<div class="container my-4" style="max-width: 980px;">

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                <div>
                    <h4 class="mb-0">Nova referencia</h4>
                    <div class="text-muted small">ca_rdf: <?= esc($rdf ?? '') ?></div>
                </div>

                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.close();">
                    Fechar
                </button>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert <?= !empty($saved) ? 'alert-success' : 'alert-warning' ?>">
                    <?= esc($message) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= esc($actionUrl ?? '') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">ca_text</label>
                    <textarea name="ca_text" class="form-control" rows="8" placeholder="Digite a nova referencia"></textarea>
                    <div class="form-text">Se houver quebra de linha (Enter), sera criada uma referencia por linha.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.close();">Cancelar</button>
                </div>
            </form>

        </div>
    </div>

</div>

<?= view('BrapciLabs/layout/footer'); ?>
