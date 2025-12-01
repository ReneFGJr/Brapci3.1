<div class="container mt-4" style="max-width: 950px;">

    <h3 class="mb-3">
        <i class="bi bi-eye"></i> Visualização do Evento
    </h3>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong><?= esc($event['e_name']) ?></strong>
        </div>

        <div class="card-body">

            <h5 class="mb-3 text-secondary">Informações Gerais</h5>

            <table class="table table-bordered">
                <tr>
                    <th style="width: 25%;">ID</th>
                    <td><?= esc($event['id_e']) ?></td>
                </tr>

                <tr>
                    <th>Nome do Certificado</th>
                    <td><?= esc($event['e_name']) ?></td>
                </tr>

                <tr>
                    <th>Evento (Código)</th>
                    <td><?= esc($event['e_event']) ?></td>
                </tr>

                <tr>
                    <th>Total Inscritos</th>
                    <td>
                    <?= esc($inscritos['total']) ?> 
                    / 
                    <?= esc($certificados['total']) ?></td>
                </tr>                

                <tr>
                    <th>Palavras-chave</th>
                    <td><?= esc($event['e_keywords']) ?></td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <?php if ($event['e_status'] == 1): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inativo</span>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <th>Cidade</th>
                    <td><?= esc($event['e_cidade']) ?></td>
                </tr>

                <tr>
                    <th>Data do Registro</th>
                    <td><?= esc($event['e_data']) ?></td>
                </tr>

                <tr>
                    <th>Data Inicial</th>
                    <td><?= esc($event['e_data_i']) ?></td>
                </tr>

                <tr>
                    <th>Data Final</th>
                    <td><?= esc($event['e_data_f']) ?></td>
                </tr>
            </table>

            <hr>

            <h5 class="mb-3 text-secondary">Texto do Certificado</h5>
            <div class="p-3 border bg-light" style="white-space: pre-wrap;">
                <?= esc($event['e_texto']) ?>
            </div>

            <hr>

            <h5 class="mb-3 text-secondary">Assinaturas</h5>

            <table class="table table-striped">
                <tr>
                    <th>Nome Assinatura 1</th>
                    <td><?= esc($event['e_ass_none_1']) ?></td>
                </tr>
                <tr>
                    <th>Cargo 1</th>
                    <td><?= esc($event['e_ass_cargo_1']) ?></td>
                </tr>

                <tr>
                    <th>Nome Assinatura 2</th>
                    <td><?= esc($event['e_ass_none_2']) ?></td>
                </tr>
                <tr>
                    <th>Cargo 2</th>
                    <td><?= esc($event['e_ass_cargo_2']) ?></td>
                </tr>

                <tr>
                    <th>Nome Assinatura 3</th>
                    <td><?= esc($event['e_ass_none_3']) ?></td>
                </tr>
                <tr>
                    <th>Cargo 3</th>
                    <td><?= esc($event['e_ass_cargo_3']) ?></td>
                </tr>

                <tr>
                    <th>Imagem Assinatura</th>
                    <td><?= esc($event['e_ass_img']) ?></td>
                </tr>
            </table>

            <hr>

            <h5 class="mb-3 text-secondary">Layout e Template</h5>

            <table class="table table-bordered">
                <tr>
                    <th style="width: 25%;">Background</th>
                    <td><?= esc($event['e_background']) ?></td>
                </tr>

                <tr>
                    <th>Template</th>
                    <td><?= esc($event['e_templat']) ?></td>
                </tr>

                <tr>
                    <th>Localização Extra</th>
                    <td><?= esc($event['e_location']) ?></td>
                </tr>
            </table>

        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="<?= base_url('event/events') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>

            <a href="<?= base_url('event/event/register/' . $event['id_e']) ?>" 
               class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Inscrever Participantes
            </a>            

            <a href="<?= base_url('event/event/edit/' . $event['id_e']) ?>" 
               class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Editar
            </a>
        </div>

    </div>

</div>
