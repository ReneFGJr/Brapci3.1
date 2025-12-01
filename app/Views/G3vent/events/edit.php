<div class="container mt-4" style="max-width: 900px;">

    <h3><i class="bi bi-pencil-square"></i> Editar Certificado</h3>
    <hr>

    <form action="<?= base_url('event/event/update/' . $event['id_e']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Nome do Certificado</label>
            <input type="text" class="form-control" name="e_name" 
                   value="<?= esc($event['e_name']) ?>" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Data Inicial</label>
                <input type="date" class="form-control" name="e_data_i"
                       value="<?= esc($event['e_data_i']) ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Data Final</label>
                <input type="date" class="form-control" name="e_data_f"
                       value="<?= esc($event['e_data_f']) ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Texto do Certificado</label>
            <textarea class="form-control" name="e_texto" rows="5"><?= esc($event['e_texto']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Palavras-chave</label>
            <textarea class="form-control" name="e_keywords" rows="3"><?= esc($event['e_keywords']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Cidade</label>
            <input type="text" class="form-control" name="e_cidade"
                   value="<?= esc($event['e_cidade']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="e_status">
                <option value="0" <?= $event['e_status']==0?'selected':'' ?>>Inativo</option>
                <option value="1" <?= $event['e_status']==1?'selected':'' ?>>Ativo</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Assinatura (Imagem)</label>
            <input type="text" class="form-control" name="e_ass_img"
                   value="<?= esc($event['e_ass_img']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Background</label>
            <input type="text" class="form-control" name="e_background"
                   value="<?= esc($event['e_background']) ?>">
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= base_url('events') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-check2-circle"></i> Salvar Alterações
            </button>
        </div>

    </form>

</div>
