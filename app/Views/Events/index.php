<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1>Lista de Eventos</h1>
            <a href="<?= site_url('events/create') ?>" class="btn btn-primary">Novo Evento</a>

            <table class="table table-bordered mt-3">
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Local</th>
                    <th>Início</th>
                    <th>Ações</th>
                </tr>

                <?php foreach ($events as $e): ?>
                    <tr>
                        <td>
                            <?php if ($e['ev_image']): ?>
                                <img src="<?= base_url('uploads/events/' . $e['ev_image']) ?>" width="80">
                            <?php endif; ?>
                        </td>
                        <td><?= esc($e['ev_name']) ?></td>
                        <td><?= esc($e['ev_place']) ?></td>
                        <td><?= $e['ev_data_start'] ?></td>
                        <td>
                            <a href="<?= site_url('events/edit/' . $e['id_ev']) ?>">Editar</a> |
                            <a href="<?= site_url('events/delete/' . $e['id_ev']) ?>"
                                onclick="return confirm('Confirma exclusão?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</div>