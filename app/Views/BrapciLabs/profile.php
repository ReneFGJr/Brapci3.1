<?php
/**
 * View: Perfil do Usuário
 * Variável esperada: $user (array)
 */
?>

<div class="container py-4">

    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center">
            <div class="me-3">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                     style="width:72px;height:72px;font-size:28px;">
                    <?= strtoupper(substr($user['us_nome'], 0, 1)) ?>
                </div>
            </div>
            <div>
                <h3 class="mb-0"><?= esc($user['us_nome']) ?></h3>
                <small class="text-muted">
                    <?= esc($user['us_email']) ?>
                </small>
                <div class="mt-1">
                    <?php if ($user['us_ativo']): ?>
                        <span class="badge bg-success">Ativo</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inativo</span>
                    <?php endif; ?>

                    <?php if ($user['us_apikey_active']): ?>
                        <span class="badge bg-info text-dark">API Key ativa</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Dados principais -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light fw-bold">
                    <i class="bi bi-person"></i> Informações do Usuário
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8"><?= $user['id_us'] ?></dd>

                        <dt class="col-sm-4">Login</dt>
                        <dd class="col-sm-8"><?= esc($user['us_login']) ?></dd>

                        <dt class="col-sm-4">Instituição</dt>
                        <dd class="col-sm-8"><?= esc($user['us_institution'] ?: '—') ?></dd>

                        <dt class="col-sm-4">Afiliado</dt>
                        <dd class="col-sm-8"><?= esc($user['us_affiliation'] ?: '—') ?></dd>

                        <dt class="col-sm-4">Cidade</dt>
                        <dd class="col-sm-8"><?= esc($user['us_cidade'] ?: '—') ?></dd>

                        <dt class="col-sm-4">País</dt>
                        <dd class="col-sm-8"><?= esc($user['us_pais'] ?: '—') ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light fw-bold">
                    <i class="bi bi-bar-chart"></i> Estatísticas
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="mb-0"><?= $user['us_revisoes'] ?></h4>
                            <small class="text-muted">Revisões</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0"><?= $user['us_colaboracoes'] ?></h4>
                            <small class="text-muted">Colaborações</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0"><?= $user['us_acessos'] ?></h4>
                            <small class="text-muted">Acessos</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-4">
                            <h5><?= $user['us_pesquisa'] ?></h5>
                            <small class="text-muted">Pesquisas</small>
                        </div>
                        <div class="col-4">
                            <h5><?= $user['us_erros'] ?></h5>
                            <small class="text-muted">Erros</small>
                        </div>
                        <div class="col-4">
                            <h5><?= $user['us_outros'] ?></h5>
                            <small class="text-muted">Outros</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Key -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-key"></i> API Key</span>
                    <?php if ($user['us_apikey_active']): ?>
                        <span class="badge bg-success">Ativa</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Inativa</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="password"
                               class="form-control"
                               id="apikey"
                               value="<?= esc($user['us_apikey']) ?>"
                               readonly>
                        <button class="btn btn-outline-secondary"
                                type="button"
                                onclick="toggleApiKey()">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-primary"
                                type="button"
                                onclick="navigator.clipboard.writeText('<?= esc($user['us_apikey']) ?>')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Mantenha sua chave em sigilo. Ela concede acesso à API.
                    </small>
                </div>
            </div>
        </div>

        <!-- Rodapé -->
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body d-flex justify-content-between">
                    <small>
                        Criado em:
                        <strong><?= date('d/m/Y H:i', strtotime($user['us_created'])) ?></strong>
                    </small>
                    <small>
                        Último acesso:
                        <strong>
                            <?= $user['us_lastaccess']
                                ? date('d/m/Y H:i', strtotime($user['us_lastaccess']))
                                : '—' ?>
                        </strong>
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function toggleApiKey() {
    const input = document.getElementById('apikey');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
