<?php
$status = $invite['iv_status'];
?>

<div class="container my-4">

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-gear"></i>
                Ações do Convite
            </h5>
        </div>

        <div class="card-body">

            <?php switch ($status) {
                //<!-- 1 - Convite Enviado -->
                case 1: ?>
                    <div class="alert alert-primary">
                        O convite aguarda autorização para ser enviado ao editor e aguarda resposta.
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= base_url('/admin/source/Invitation/resend/' . $invite['id_iv']) ?>"
                            class="btn btn-outline-primary">
                            <i class="bi bi-envelope"></i> Reenviar Convite
                        </a>

                        <a href="<?= base_url('/admin/source/Invitation/update-status/' . $invite['id_iv'] . '/2') ?>"
                            class="btn btn-warning">
                            <i class="bi bi-hourglass"></i> Marcar como "Em análise"
                        </a>

                        <a href="<?= base_url('/admin/source/Invitation/update-status/' . $invite['id_iv'] . '/9') ?>"
                            class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Registrar Recusa
                        </a>
                    </div>
                    <!-- 2 - Em análise -->
                <?php
                    break;
                case '2': ?>
                    <div class="alert alert-warning">
                        O editor está analisando o convite.
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= base_url('/admin/source/Invitation/update-status/' . $invite['id_iv'] . '/5') ?>"
                            class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Convite Aceito
                        </a>

                        <a href="<?= base_url('/admin/source/Invitation/update-status/' . $invite['id_iv'] . '/9') ?>"
                            class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Convite Recusado
                        </a>
                    </div>
                    <!-- 5 - Aceito -->
                <?php
                    break;
                case '5': ?>
                    <div class="alert alert-success">
                        O convite foi aceito. Instruções devem ser enviadas ao editor.
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= base_url('/admin/source/Invitation/send-instructions/' . $invite['id_iv']) ?>"
                            class="btn btn-info">
                            <i class="bi bi-send"></i> Enviar Instruções
                        </a>

                        <a href="<?= base_url('/admin/source/Invitation/update-status/' . $invite['id_iv'] . '/3') ?>"
                            class="btn btn-primary">
                            <i class="bi bi-arrow-right-circle"></i> Marcar Instruções Enviadas
                        </a>
                    </div>
                    <!-- 3 - Instruções enviadas -->
                <?php
                    break;
                case '3': ?>
                    <div class="alert alert-info">
                        As instruções foram enviadas ao editor.
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= base_url('/admin/source/Invitation/update-status/' . $invite['id_iv'] . '/4') ?>"
                            class="btn btn-dark">
                            <i class="bi bi-search"></i> Iniciar Checagem
                        </a>
                    </div>
                    <!-- 4 - Checagem -->
                <?php
                    break;
                case '4': ?>
                    <div class="alert alert-dark">
                        O processo de checagem da indexação está em andamento.
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= base_url('/admin/source/Invitation/update-status/' . $invite['id_iv'] . '/6') ?>"
                            class="btn btn-success">
                            <i class="bi bi-award"></i> Confirmar Indexação
                        </a>
                    </div>
                    <!-- 6 - Indexado -->
                <?php
                    break;
                case '6': ?>
                    <div class="alert alert-success">
                        A revista está oficialmente indexada na Brapci.
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= base_url('/admin/source/Invitation/certificate/' . $invite['id_iv']) ?>"
                            class="btn btn-outline-success">
                            <i class="bi bi-file-earmark-pdf"></i> Gerar Certificado
                        </a>
                    </div>
                    <!-- 9 - Recusado -->
                <?php
                    break;
                case 9: ?>
                    <div class="alert alert-danger">
                        O convite foi recusado pelo editor.
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= base_url('/admin/source/Invitation/resend/' . $invite['id_iv']) ?>"
                            class="btn btn-outline-primary">
                            <i class="bi bi-arrow-repeat"></i> Reenviar Convite
                        </a>
                    </div>
                    <?php break; ?>


                    <!-- Default -->
                <?php
                default: ?>
                    <div class="alert alert-secondary">
                        Status não reconhecido.
                    </div>
            <?php } ?>

        </div>

    </div>

</div>