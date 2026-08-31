<?php
$isEdit = !empty($journal['id']);
$value = static function (string $field, string $default = '') use ($journal): string {
    $old = old($field);
    return esc($old !== null ? $old : ($journal[$field] ?? $default));
};
?>
<main class="pt-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0"><?= $isEdit ? 'Editar revista OJS' : 'Nova revista OJS' ?></h1>
                    <a class="btn btn-outline-secondary" href="<?= base_url('ojs/journals') ?>">Voltar</a>
                </div>

                <?php if ($errors !== []): ?>
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="card shadow-sm" method="post" action="<?= $isEdit ? base_url('ojs/journals/' . $journal['id']) : base_url('ojs/journals') ?>">
                    <?= csrf_field() ?>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" for="name">Nome</label>
                                <input class="form-control" id="name" name="name" maxlength="200" required value="<?= $value('name') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="acronym">Sigla</label>
                                <input class="form-control" id="acronym" name="acronym" maxlength="30" value="<?= $value('acronym') ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="base_url">URL base do OJS</label>
                                <input class="form-control" type="url" id="base_url" name="base_url" maxlength="255" placeholder="https://editora.exemplo.br" required value="<?= $value('base_url') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="context_path">Caminho da revista</label>
                                <input class="form-control" id="context_path" name="context_path" maxlength="100" placeholder="revista" required value="<?= $value('context_path') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="api_key">APIKEY</label>
                                <input class="form-control" type="password" id="api_key" name="api_key" autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>
                                <div class="form-text">
                                    <?= $isEdit ? 'Deixe em branco para manter a chave atual.' : 'A chave será armazenada no banco e não será exibida após o cadastro.' ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="api_key_editor">APIKEY do Editor</label>
                                <input class="form-control" type="password" id="api_key_editor" name="api_key_editor" autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>
                                <div class="form-text">
                                    <?= $isEdit ? 'Deixe em branco para manter a chave editorial atual.' : 'Use a chave de um usuário OJS com papel de Editor ou Gerente da Revista.' ?>
                                </div>
                            </div>
                            <div class="col-12 d-flex gap-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="active" value="0">
                                    <input class="form-check-input" type="checkbox" id="active" name="active" value="1" <?= $value('active', '1') === '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="active">Ativa</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_default" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" <?= $value('is_default', '0') === '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_default">Revista padrão</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white p-4 text-end">
                        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Salvar alterações' : 'Cadastrar revista' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>