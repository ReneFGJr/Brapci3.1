<div class="container my-5">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow border-0">

                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope-plus"></i>
                        Enviar Convite para Indexação
                    </h4>
                </div>

                <div class="card-body">

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('admin/source/Invitation/store') ?>">

                        <!-- Revista -->
                        <div class="mb-3">
                            <label class="form-label">Nome da Revista</label>
                            <input type="text"
                                name="iv_journal"
                                class="form-control"
                                value="<?= old('iv_journal') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">URL da Revista</label>
                            <input type="url"
                                name="iv_url"
                                class="form-control"
                                value="<?= old('iv_url') ?>">
                        </div>

                        <hr>

                        <!-- Contato Principal -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome do Contato Principal</label>
                                <input type="text"
                                    name="iv_contact_name"
                                    class="form-control"
                                    value="<?= old('iv_contact_name') ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail do Contato Principal</label>
                                <input type="email"
                                    name="iv_contact"
                                    class="form-control"
                                    value="<?= old('iv_contact') ?>"
                                    required>
                            </div>
                        </div>

                        <!-- Contato Secundário -->
                        <div class="mb-3">
                            <label class="form-label">E-mail Secundário (Opcional)</label>
                            <input type="email"
                                name="iv_contact_2"
                                class="form-control"
                                value="<?= old('iv_contact_2') ?>">
                        </div>

                        <hr>

                        <!-- Idioma e Status -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Idioma</label>
                                <select name="iv_language" class="form-select">
                                    <option value="por">Português</option>
                                    <option value="eng">Inglês</option>
                                    <option value="spa">Espanhol</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="iv_status" class="form-select">
                                    <?php foreach ($statusList as $key => $label): ?>
                                        <option value="<?= $key ?>">
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('invite') ?>"
                                class="btn btn-secondary">
                                Cancelar
                            </a>

                            <button type="submit"
                                class="btn btn-success">
                                <i class="bi bi-send"></i>
                                Enviar Convite
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>