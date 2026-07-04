<div class="container py-5" style="max-width: 760px;">
    <h2 class="mb-3">Notepad</h2>
    <p class="text-muted">Crie uma pagina publica e compartilhe o link para editar colaborativamente.</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= esc((string) session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('notepad') ?>" class="card p-4 shadow-sm">
        <?= csrf_field() ?>
        <label for="slug" class="form-label">Nome da pagina</label>
        <div class="input-group input-group-lg">
            <span class="input-group-text"><?= esc(base_url('notepad')) ?>/</span>
            <input type="text" id="slug" name="slug" class="form-control" placeholder="exemplo-minha-nota" required maxlength="120">
            <button class="btn btn-primary" type="submit">Abrir</button>
        </div>
        <div class="form-text mt-2">Use apenas letras, numeros, hifen e underscore.</div>
    </form>
</div>
