<?php
/************************************* Header */
if (!isset($this->collaboration)) {
    $this->collaboration = 0;
?>
<div class="container mt-3">
    <div class="row">
        <div class="col-8"><h4 class="pgcd_h"><?=lang('pgcd.'.trim($plf_field));?></h4></div>
    </div>
    <div class="row">
        <div class="col-8">
            <?php
            $PlansCollaborations = new \App\Models\PGCD\PlansCollaborations();
            echo $PlansCollaborations->list($id_p);

            $data['id_p'] = $id_p;
            echo view("PGCD/Forms/collaborations_authors_invitation",$data);
            ?>
        </div>
    </div>    
</div>
<?php


}
/********************************* End Header */
?>
