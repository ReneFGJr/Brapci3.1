<div class="container">
    <div class="row">
        <div class="col-12">
            <table class="table pgcd_table">
                <tr class="pgcd_table_th">
                    <th class="pgcd_table_th" width="5%"><?=lang('pgcd.p_nr');?></th>
                    <th class="pgcd_table_th" width="70%"><?=lang('pgcd.p_title');?></th>                    
                    <th class="pgcd_table_th" width="10%"><?=lang('pgcd.p_version');?></th>
                    <th class="pgcd_table_th" width="10%"><?=lang('pgcd.updated_at');?></th>
                    <th class="pgcd_table_th" width="10%"><?=lang('pgcd.p_status');?></th>
                    <th class="pgcd_table_th" colspan=4><?=lang('pgcd.action');?></th>
                </tr>
                <?php
                for ($r = 0; $r < count($plans); $r++) {
                    $line = $plans[$r];
                    $status = lang('pgcd.plans_status_'.$line['p_status']);
                    /* PDF ****************************************************/
                    $pdf = '';
                    $edit = '';
                    $trash = '';
                    $linka = '</a>';
                    switch($line['p_status'])
                    {
                        case 0:
                            /* Edit ***************************************************/
                            $link = '<a href="/pgcd/plans/edit/'.$line['id_p'].'" class="pgcd_edit_plans">';
                            $edit = $link.bsicone('edit',16).$linka;
                            /* Tash ***************************************************/
                            $link = '<a href="/pgcd/plans/trash/'.$line['id_p'].'" class="pgcd_trash_plans">';
                            $trash = $link.bsicone('trash',16).$linka;; 
                            break;

                        case 1:
                            $pdf = bsicone('pdf',16);
                            break;
                    }
                $link = '<a href="/pgcd/plans/view/'.$line['id_p'].'" class="pgcd_view_plans">';
                ?>
                    <tr>
                        <td><?= strzero($line['p_nr'],6).'/'.$line['p_year']; ?></td>
                        <td><?= $link.$line['p_title'].$linka; ?></td>
                        <td><?= $line['p_version']; ?></td>
                        <td><?= $line['updated_at']; ?></td>
                        <td><?= $status ?></td>
                        <td width="1%"><?= $edit; bsicone('edit',16); ?></td>
                        <td width="1%"><?= $trash; ?></td>
                        <td width="1%"><?= $pdf; ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>