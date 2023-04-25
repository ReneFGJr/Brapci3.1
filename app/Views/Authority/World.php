<style>
    body {
        background: url('<?= URL . '/img/logo/world.svg'; ?>') no-repeat center center fixed;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-4 p-2">
            <?= $genere; ?>
        </div>
        <div class="col-8">
            <?= $search; ?>
            <?= $search_result; ?>
        </div>
    </div>
</div>