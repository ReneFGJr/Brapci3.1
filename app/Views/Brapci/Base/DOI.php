<a href="<?= $DOI; ?>" target="_blank">
    <div class="p-2 mb-4 pdf_book">
        <table style="width: 100%">
            <tr>
                <td width="32px">
                    <img src="<?= URL . '/img/icons/academicons/doi.svg'; ?>" height="32px">
                </td>
                <td>
                    <span style="font-size: 0.7em;"><?= troca($DOI, 'https://doi.org/', ''); ?></span>
                </td>
            </tr>
        </table>
    </div>
</a>