<div class="p-2 mb-4 pdf_book" onclick="download('<?= $pdf; ?>',800,800);" ;>

    <div style="display: inline-block">
        <?= bsicone("pdf", 64); ?><br />
    </div>
    <div style="display: inline-block">
        <?= lang('brapci.download_full_text'); ?>
        <br/>
        <?= lang('brapci.download'); ?>
    </div>
</div>