<div class="p-2 mb-4 pdf_book" onclick="download('<?= $pdf; ?>',800,800);" ;>
    <table style="width: 100%">
        <tr>
            <td width="32px"><?= bsicone("html", 32); ?></td>
            <td>
                <?= lang('brapci.go_to_homepage'); ?>
                <br>
                <?= bsicone("html", 64); ?><br />
                <?= lang('brapci.homepage'); ?>
            </td>
        </tr>
    </table>
</div>

<?php
$Socials = new \App\Models\Socials();
if ($Socials->getAccess("#ADM")) {
    echo '<div style="width: 100%" class="text-right">';
    echo onclick(PATH . '/popup/upload/?id=' . $id_cc, 600, 300) . "Upload" . '</span>';
    echo '</div>';
}
?>