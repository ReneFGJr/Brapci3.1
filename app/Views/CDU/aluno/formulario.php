<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Identificação do Aluno</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >
</head>
<body class="p-4">
    <div class="container">
        <h2>Informe seu número de crachá</h2>

        <!-- Mostrar erros de validação, se houver -->
        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif ?>

        <?= form_open($url) ?>

            <div class="mb-3">
                <label for="cracha" class="form-label">Crachá (8 dígitos)</label>
                <input
                  type="text"
                  name="cracha"
                  id="cracha"
                  value="<?= set_value('cracha') ?>"
                  class="form-control <?= isset($validation) && $validation->hasError('cracha') ? 'is-invalid' : '' ?>"
                  maxlength="8"
                  placeholder="Ex: 12345678"
                >
                <?php if (isset($validation) && $validation->hasError('cracha')): ?>
                    <div class="invalid-feedback">
                        <?= $validation->getError('cracha') ?>
                    </div>
                <?php endif ?>
            </div>

            <button type="submit" class="btn btn-primary">Enviar</button>

        <?= form_close() ?>
    </div>
</body>
</html>
