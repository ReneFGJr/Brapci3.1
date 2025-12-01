
<div class="container mt-4" style="max-width: 720px;">

    <h3><i class="bi bi-upload"></i> Importar Usuários</h3>
    <p class="text-muted">Cole a lista de nomes e e-mails abaixo. Um por linha.</p>

    <form action="<?= base_url('event/event/register/' . $event['id_e']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label fw-bold">Lista de Usuários (para inscrição)</label>
            <textarea 
                name="lista" 
                class="form-control" 
                rows="12" 
                placeholder="Exemplo:
maria@example.com
joao@dominio.com
ana@gmail.com"
                required><?= get("lista") ?></textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= base_url('g3vent') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Importar
            </button>
        </div>

    </form>

</div>

