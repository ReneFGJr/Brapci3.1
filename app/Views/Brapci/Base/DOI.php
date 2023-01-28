<a href="<?= $DOI; ?>" target="_blank">
    <div class="p-2 mb-4 pdf_book">
        <table style="width: 100%">
            <tr>
                <td width="32px">
                    <img src="<?=URL. '/img/icons/academicons/doi.svg';?>" height="32px">
                </td>
                <td>
                    <?= lang('brapci.go_to_homepage'); ?>
                    <br>
                    <?= troca($DOI, 'https://doi.org/', ''); ?>
                </td>
            </tr>
        </table>
    </div>
</a>