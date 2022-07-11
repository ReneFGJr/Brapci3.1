<div class="container">
    <div class="row">
        <div class="col-8 mb-4">
            <label class="form_label"><?= lang('dgdp.plan_title'); ?></label>
                <?php
                /******************************************************** TITLE */
                $options = array(
                    'name' => 'pgdc_title',
                    'rows' => '4',
                    'class'=>'form-control',
                    'width' => '100%',
                    'value' => get("pgdc_title")
                );
                echo form_textarea($options);
                ?>
        </div>
        <div class="col-4 info mb-4">
            <?=lang('dgdp.plan_title_info');?>
        </div>
    </div>
</div>
<?= form_input(['class' => "form-control", 'name' => 'pgdc']); ?>