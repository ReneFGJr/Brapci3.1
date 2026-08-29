<main class="pt-5">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Revistas OJS</h1>
                <p class="text-muted mb-0">Revistas gerenciadas pela integração com a API do OJS.</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="<?= base_url('ojs/') ?>">
                    <i class="bi bi-arrow-left"></i> Voltar ao OJS
                </a>
                <a class="btn btn-primary" href="<?= base_url('ojs/journals/new') ?>">
                    <i class="bi bi-plus-lg"></i> Nova revista
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Revista</th>
                            <th>APIKEY</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($journals === []): ?>
                            <tr><td colspan="4" class="text-center text-muted py-5">Nenhuma revista cadastrada.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($journals as $journal): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($journal['name']) ?></strong>
                                    <?php if (!empty($journal['acronym'])): ?>
                                        <span class="text-muted">(<?= esc($journal['acronym']) ?>)</span>
                                    <?php endif; ?>
                                    <?php if (!empty($journal['is_default'])): ?>
                                        <span class="badge bg-primary ms-1">Padrão</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="text-muted" title="A chave não é exibida">••••••••</span></td>
                                <td>
                                    <span class="badge <?= !empty($journal['active']) ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= !empty($journal['active']) ? 'Ativa' : 'Inativa' ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <?php $isSelected = $selectedJournalId === (int) $journal['id']; ?>
                                    <form class="d-inline" method="post" action="<?= base_url('ojs/journals/select/' . $journal['id']) ?>">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm <?= $isSelected ? 'btn-success' : 'btn-outline-success' ?>" type="submit"
                                            title="<?= $isSelected ? 'Revista selecionada' : 'Selecionar revista' ?>"
                                            aria-label="<?= $isSelected ? 'Revista selecionada' : 'Selecionar revista' ?>"
                                            <?= empty($journal['active']) || $isSelected ? 'disabled' : '' ?>>
                                            <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('ojs/journals/view/' . $journal['id']) ?>" title="Ver revista" aria-label="Ver revista">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= base_url('ojs/journals/edit/' . $journal['id']) ?>" title="Editar revista" aria-label="Editar revista">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>
                                    <form class="d-inline" method="post" action="<?= base_url('ojs/journals/delete/' . $journal['id']) ?>" onsubmit="return confirm('Excluir esta revista?');">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Excluir revista" aria-label="Excluir revista">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>