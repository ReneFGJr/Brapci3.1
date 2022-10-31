<div class="container mt-5">
    <div class="row">
        <div class="col-10" style="font-size: 1.8em;">
            <?= $ag_name; ?>
        </div>

        <div class="col-2" style="font-size: 1.8em;">
            <?php
            echo $ag_country;
            if (strlen($ag_state) != '') {
                echo '/' . $ag_state;
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-8" style="font-size: 1.8em;">
        </div>
            <div class="col-4 text-end">
                <?= $patents; ?>
            </div>
        </div>
    </div>