<?php $edit = isset($api); ?>

<div class="container mt-4">
    <h2><?= $edit ? 'Editar API' : 'Nova API' ?></h2>

    <form method="post"
        action="<?= $edit
                    ? base_url('labs/api-library/update/' . $api['id'])
                    : base_url('labs/api-library/store') ?>">

        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control"
                value="<?= $api['nome'] ?? '' ?>" required>
        </div>

        <div class="mb-3">
            <label>Endpoint</label>
            <input type="text" name="endpoint" class="form-control"
                value="<?= $api['endpoint'] ?? '' ?>" required>
        </div>

        <div class="mb-3">
            <label>Método</label>
            <select name="metodo" class="form-select">
                <?php foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $m): ?>
                    <option value="<?= $m ?>"
                        <?= (($api['metodo'] ?? 'GET') == $m) ? 'selected' : '' ?>>
                        <?= $m ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Parâmetros</label>
            <textarea name="parametros" class="form-control" rows="4"><?= $api['parametros'] ?? '' ?></textarea>
        </div>

        <div class="mb-3">
            <label>Headers</label>
            <textarea name="headers" class="form-control" rows="3"><?= $api['headers'] ?? '' ?></textarea>
        </div>

        <div class="mb-3">
            <label>Descrição / Uso</label>
            <textarea name="descricao" class="form-control" rows="4"><?= $api['descricao'] ?? '' ?></textarea>
        </div>

        <button class="btn btn-primary">Salvar</button>
        <a href="<?= base_url('api-library') ?>" class="btn btn-secondary">Voltar</a>
    </form>
</div>