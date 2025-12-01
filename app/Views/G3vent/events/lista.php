<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-file-earmark-text"></i> Tipos de Certificados</h3>

        <a href="<?= base_url('events/new') ?>" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Novo Certificado
        </a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th style="width: 40%;">Nome</th>
                <th style="width: 20%;">Data</th>
                <th style="width: 20%;">Cidade</th>
                <th style="width: 20%;">Ação</th>
            </tr>
        </thead>

        <tbody>
            <?php if (isset($events) && count($events) > 0): ?>
                <?php foreach ($events as $e): ?>
                    <tr>
                        <td><?= esc($e['e_name']) ?></td>
                        <td><?= esc($e['e_data']) ?></td>
                        <td><?= esc($e['e_cidade']) ?></td>
                        <td>
                            <a href="<?= base_url('event/event/edit/' . $e['id_e']) ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>

                            <a href="<?= base_url('event/event/view/' . $e['id_e']) ?>" 
                               class="btn btn-success btn-sm">
                                <i class="bi bi-pencil-square"></i> Ver
                            </a>                            

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">Nenhum certificado encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
