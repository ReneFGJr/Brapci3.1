<div class="card card-dashboard p-3">
    <h6>Expenses</h6>
    <h4>$1850.20</h4>
    <div class="fake-bars mt-3">
        <?php for ($i = 0; $i < 10; $i++): ?>
            <span class="active" style="height:<?= rand(20, 60) ?>px"></span>
        <?php endfor ?>
    </div>
</div>