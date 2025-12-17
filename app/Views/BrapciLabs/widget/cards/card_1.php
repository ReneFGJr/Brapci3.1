        <?php
        $mask = 'col-md-'.$size;
        ?>
        <div class="<?= $mask ?>">
            <div class="card card-dashboard p-3">
                <h6>Revenue</h6>
                <h4>$2400.50</h4>
                <div class="fake-bars mt-3">
                    <?php for($i=0;$i<10;$i++): ?>
                        <span class="<?= rand(0,1) ? 'active':'' ?>" style="height:<?= rand(20,60) ?>px"></span>
                    <?php endfor ?>
                </div>
            </div>
        </div>