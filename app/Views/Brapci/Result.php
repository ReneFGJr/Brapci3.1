<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            <?= lang('brapci.search_stategy'); ?>
            <?php
            echo '<br><tt>Query: '.get("q"). '</tt>';
            $di = $_SESSION['search']['di'];
            $df = $_SESSION['search']['df'];
            $ord = $_SESSION['search']['ord'];
            $field = $_SESSION['search']['field'];
            echo '<br>'.$di.'-'.$df;
            ?>
        </div>
    </div>
</div>