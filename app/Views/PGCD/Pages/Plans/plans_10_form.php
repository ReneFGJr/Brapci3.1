<div class="container">
    <div class="row">
        <div class="col-8 mb-4">
            <label class="form_label"><?= lang('dgdp.plan_form'); ?></label>
                <select name="pgdc_form" class="form-control">
                <option value=""><?=lang('pgcd.select_templat');?></option>
                <?php
                /******************************************************** TITLE */
                $forms = new \App\Models\PGCD\PlansForm();
                $dt = $forms->findAll();
                for ($r=0;$r < count($dt);$r++)
                    {
                        $selected = '';
                        $line = $dt[$r];
                        if ($line['id_pf'] == get("pgdc_form")) { $selected = 'selected'; }
                        echo '<option value="'.$line['id_pf'].'" '.$selected.'>';
                        echo $line['pf_acronic'] .' - '.$line['pf_name'];
                        echo '</option>';
                    }
                ?>
                </select>
        </div>
        <div class="col-4 info mb-4">
            <?=lang('dgdp.plan_title_info');?>
        </div>
    </div>
</div>
<?= form_input(['class' => "form-control", 'name' => 'pgdc']); ?>