<div class="p-2 mb-4"
    style="border: 1px solid #F00; text-align: center; background-color: #830706; color: #fff; border-radius: 10px; cursor: pointer;"
    onclick="download('<?= $pdf; ?>',800,800);" ;>
    <?= lang('brapci.download_full_text'); ?>
    <br>
    <?= bsicone("pdf", 64); ?><br />
    <?= lang('brapci.download'); ?>
</div>