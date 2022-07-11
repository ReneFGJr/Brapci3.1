<?php
global $keys;


/************************************* Header */
if (!isset($this->form_keywords)) {
    $this->form_keywords = 0;
?>
<div class="container mt-3">
    <div class="row">
        <div class="col-8"><h4 class="pgcd_h"><?=lang('pgcd.'.$plf_field);?></h4></div>
    </div>
</div>
<?php
}
/********************************* End Header */
?>
<div class="container">
    <div class="row">
        <div class="col-8">
            <?= $pv_value; ?>
        </div>
    </div>
</div>