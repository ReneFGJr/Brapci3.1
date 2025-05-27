<div class="container">

    <form action="<?= ($form_action) ?>" method="post" class="row g-3">
        <?= csrf_field() ?>
        <input type="hidden" name="st_software" value="<?= esc($st_software ?? '') ?>">
        <?php if (isset($step['id_st'])): ?>
            <input type="hidden" name="id_st" value="<?= esc($step['id_st']) ?>">
        <?php endif ?>

        <div class="col-md-2">
            <label for="st_order" class="form-label">Order</label>
            <input type="number" class="form-control" style="border: 1px solid #000;" id="st_order" name="st_order" value="<?= set_value('st_order', $step['st_order'] ?? '') ?>" required>
        </div>

        <div class="col-md-12">
            <label for="st_description" class="form-label">Description</label>
            <textarea rows=10 type="text" class="form-control" style="border: 1px solid #000;" id="st_description" name="st_description" value="<?= set_value('st_description', $step['st_description'] ?? '') ?>" required>
        </textarea>
        </div>

        <div class="col-md-12">
            <label for="st_answer" class="form-label">Answer</label>
            <textarea rows=10 class="form-control" style="border: 1px solid #000;" id="st_answer" name="st_answer" value="<?= set_value('st_answer', $step['st_answer'] ?? '') ?>">
            </textarea>
        </div>

        <div class="col-md-12">
            <label for="st_code" class="form-label">Code</label>
            <textarea rows=10 class="form-control" style="border: 1px solid #000;" id="st_code" name="st_code" rows="2"><?= set_value('st_code', $step['st_code'] ?? '') ?></textarea>
        </div>

        <div class="col-md-12">
            <label for="st_so" class="form-label">OS</label>
            <input type="text" class="form-control" style="border: 1px solid #000;" id="st_so" name="st_so" value="<?= set_value('st_so', $step['st_so'] ?? '') ?>">
        </div>

        <div class="col-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary"><?= isset($step['id_st']) ? 'Update Step' : 'Add Step' ?></button>
        </div>
    </form>
</div>