<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h2><?= $title; ?></h2>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif ?>

        <?= form_open($url) ?>

        <div class="mb-3">
            <label for="mensagem" class="form-label"><?= $description; ?></label>
            <?php if (isset($sample) && $sample !== '') : ?>
                <div><tt><?= esc($sample) ?></tt></div>
            <?php endif ?>
            <textarea
                name="mensagem"
                id="mensagem"
                rows="5"
                class="form-control <?= isset($validation) && $validation->hasError('mensagem') ? 'is-invalid' : '' ?>"
                placeholder="Digite aqui sua mensagem..."><?= set_value('mensagem') ?></textarea>
            <?php if (isset($validation) && $validation->hasError('mensagem')): ?>
                <div class="invalid-feedback">
                    <?= $validation->getError('mensagem') ?>
                </div>
            <?php endif ?>
        </div>

        <button type="submit" class="btn btn-primary">Enviar</button>

        <?= form_close() ?>
    </div>
</body>

</html>