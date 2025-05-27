    <div class="container">
        <h2 class="mb-4"><?= esc($title) ?></h2>
        <?php if (isset($validation)): ?>
            <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
        <?php endif ?>
        <?= form_open($form_action, ['method' => $form_method, 'class' => 'row g-3']) ?>

        <div class="col-md-6">
            <label for="s_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="s_name" name="s_name" value="<?= set_value('s_name', $software['s_name'] ?? '') ?>" required>
        </div>

        <div class="col-md-6">
            <label for="s_version" class="form-label">Version</label>
            <input type="text" class="form-control" id="s_version" name="s_version" value="<?= set_value('s_version', $software['s_version'] ?? '') ?>">
        </div>

        <div class="col-12">
            <label for="s_description" class="form-label">Description</label>
            <textarea class="form-control" id="s_description" name="s_description" rows="3"><?= set_value('s_description', $software['s_description'] ?? '') ?></textarea>
        </div>

        <div class="col-8">
            <label for="s_url" class="form-label">URL</label>
            <input type="url" class="form-control" id="s_url" name="s_url" value="<?= set_value('s_url', $software['s_url'] ?? '') ?>">
        </div>

        <div class="col-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary"><?= esc($title) ?></button>
        </div>

        <?= form_close() ?>
    </div>
</body>

</html>

<?php
// File: app/Views/Software/SoftwareView.php
// Detailed view of a single software entry
?>
