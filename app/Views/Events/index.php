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
                            <?php if ($e['ev_image']):
                            if (substr($e['ev_image'], 0, 4) == 'http'):
                                echo '<img src="' . esc($e['ev_image']) . '" width="80">';
                            else:
                                echo '<img src="' . base_url('uploads/events/' . $e['ev_image']) . '" width="80">';
                            endif; ?>
                        </td>
                        <td><?= esc($e['ev_name']) ?></td>
                        <td><?= esc($e['ev_place']) ?></td>
                        <td>
                            <nobr><?= $e['ev_data_start'] ?></nobr>
                        </td>
                        <td class="text-nowrap">
                            <a href="<?= site_url('events/edit/' . $e['id_ev']) ?>"
                                class="btn btn-sm btn-outline-primary"
                                title="Editar evento"
                                aria-label="Editar evento">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <a href="<?= site_url('events/delete/' . $e['id_ev']) ?>"
                                class="btn btn-sm btn-outline-danger"
                                title="Excluir evento"
                                aria-label="Excluir evento"
                                onclick="return confirm('Confirma exclusão?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>

                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</div>