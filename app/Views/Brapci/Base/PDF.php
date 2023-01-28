<div class="p-2 mb-4 pdf_book" onclick="download('<?= $pdf; ?>',800,800);" ;>
    <table style="width: 100%">
        <tr>
            <td width="32px"><?= bsicone("pdf", 32); ?></td>
            <td>
                <?= lang('brapci.download_full_text'); ?>
                <br />
                <?= lang('brapci.download'); ?>
            </td>
        </tr>
    </table>
</div>