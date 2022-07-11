<div class="container">
    <div class="row">
        <div class="col-12">
            <a href="<?= '/pgcd/plans/trash/'.$id_p.'?confirm=true';?>" class="btn btn-danger">
            <?=lang('pgcd.confirm_exclusion');?>
            </a>

            <a href="<?= '/pgcd/plans/view/'.$id_p;?>" class="btn btn-outline-warning">
            <?=lang('pgcd.return');?>
            </a>            
        </div>
    </div>
</div>