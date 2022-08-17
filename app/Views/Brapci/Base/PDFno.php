<div class="p-2 mb-4"
    style="border: 1px solid #00F; text-align: center; background-color: #8786F3; color: #fff; border-radius: 10px; cursor: pointer;"
    onclick="download('<?= $URL; ?>',800,800);" ;>
    <?= lang('brapci.go_to_homepage'); ?>
    <br>
    <?= bsicone("html", 64); ?><br />
    <?= lang('brapci.homepage'); ?>
</div>
<?php
$Socials = new \App\Models\Socials();
if ($Socials->getAccess("#ADM"))
    {
        echo '<div style="width: 100%" class="text-right">';
        echo onclick(PATH.'/popup/upload/?id='. $id_cc,600,300)."Upload".'</span>';
        echo '</div>';
    }
?>