<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Nova Questão</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
</head>
<body class="p-4">
  <div class="container">
    <h2>Cadastrar Questão</h2>

    <?php if (isset($validation)): ?>
      <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
    <?php endif ?>

    <?= form_open($url); ?>

      <div class="mb-3">
        <label for="id_q" class="form-label">ID da Questão</label>
        <input
          type="hidden"
          name="id_q"
          id="id_q"
          value="<?= set_value('id_q') ?>"
          class="form-control <?= isset($validation) && $validation->hasError('id_q') ? 'is-invalid' : '' ?>"
        >
        <div class="invalid-feedback">
          <?php if (isset($validation)) { echo $validation->getError('id_q');} ?>
        </div>
      </div>

      <div class="mb-3">
        <label for="q_statement" class="form-label">Enunciado</label>
        <textarea
          name="q_statement"
          id="q_statement"
          rows="3"
          class="form-control <?= isset($validation) && $validation->hasError('q_statement') ? 'is-invalid' : '' ?>"
        ><?= set_value('q_statement') ?></textarea>
        <div class="invalid-feedback">
          <?php if (isset($validation)) { echo $validation->getError('q_statement'); } ?>
        </div>
      </div>

      <div class="mb-3">
        <label for="q_ask" class="form-label">Possibilidades</label>
        <textarea
          name="q_ask"
          id="q_ask"
          rows="6"
          class="form-control <?= isset($validation) && $validation->hasError('q_ask') ? 'is-invalid' : '' ?>"
        ><?= set_value('q_ask') ?></textarea>
        <div class="invalid-feedback">
          <?php if (isset($validation)) { echo $validation->getError('q_ask'); } ?>
        </div>
      </div>

      <div class="mb-3">
        <label for="q_comentary" class="form-label">Resposta</label>
        <textarea
          name="q_comentary"
          id="q_comentary"
          rows="2"
          class="form-control <?= isset($validation) && $validation->hasError('q_comentary') ? 'is-invalid' : '' ?>"
        ><?= set_value('q_comentary') ?></textarea>
        <div class="invalid-feedback">
          <?php if (isset($validation)) { echo $validation->getError('q_comentary'); } ?>
        </div>
      </div>

      <div class="mb-3">
        <label for="q_group" class="form-label">Grupo</label>
        <input
          type="text"
          name="q_group"
          id="q_group"
          value="<?= set_value('q_group') ?>"
          class="form-control <?= isset($validation) && $validation->hasError('q_group') ? 'is-invalid' : '' ?>"
        >
        <div class="invalid-feedback">
          <?php if (isset($validation)) { echo $validation->getError('q_group'); } ?>
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Salvar</button>
    <?= form_close() ?>
  </div>
</body>
</html>
