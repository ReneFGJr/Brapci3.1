<div class="container mt-4">
    <div class="row">
        <div class="col-12">

            <form method="post" enctype="multipart/form-data"
                action="<?= isset($event) ? site_url('events/update/' . $event['id_ev']) : site_url('events/store') ?>">

                <label>Nome</label>
                <input type="text" name="ev_name" class="form-control"
                    value="<?= $event['ev_name'] ?? '' ?>">

                <label>Local</label>
                <input type="text" name="ev_place" class="form-control"
                    value="<?= $event['ev_place'] ?? '' ?>">

                <label>Data início</label>
                <input type="date" name="ev_data_start" class="form-control"
                    value="<?= $event['ev_data_start'] ?? '' ?>">

                <label>Data fim</label>
                <input type="date" name="ev_data_end" class="form-control"
                    value="<?= $event['ev_data_end'] ?? '' ?>">

                <label>URL</label>
                <input type="url" name="ev_url" class="form-control"
                    value="<?= $event['ev_url'] ?? '' ?>">

                <label>Descrição</label>
                <textarea name="ev_description" class="form-control"><?= $event['ev_description'] ?? '' ?></textarea>

                <label>Imagem</label>
                <input type="file" name="ev_image" class="form-control">

                <?php if (!empty($event['ev_image'])): ?>
                    <img src="<?= base_url('uploads/events/' . $event['ev_image']) ?>" width="120">
                <?php endif; ?>

                <br>
                <button class="btn btn-success">Salvar</button>
            </form>
        </div>
    </div>
</div>