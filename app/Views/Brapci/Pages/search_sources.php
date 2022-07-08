<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><?= lang('brapci.sources'); ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <span><?= lang('brapci.sources_all'); ?></span>:
            <span><?= lang('brapci.sources_all_ja'); ?></span> |
            <span><?= lang('brapci.sources_all_je'); ?></span> |
            <span><?= lang('brapci.sources_all_ev'); ?></span>
        </div>
        <?php
        $Source = new \App\Models\Base\Sources();
        echo $Source->search_source();
        ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Select</button>
    </div>
</div>
</div>