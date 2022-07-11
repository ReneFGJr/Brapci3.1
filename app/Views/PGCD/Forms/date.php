<div class="container mt-3">
    <div class="row">
        <div class="col-6 pgcd_h"><h4 class=""><?=lang('pgcd.'.$plf_field);?></h4></div>

        <div class='col-sm-2 pgcd_h'>
            <input class="form-control text-center" data-provide="datepicker"  data-date-format="dd/mm/yyyy">
        </div>

        <div class='col-sm-4 info'>
            <?= lang('pgcd.input_tips_date');?>
        </div>
    </div>

</div>

<script>
    $('.datepicker').datepicker();
</script>