<?php
require("_process.php");
?>
<div class="container">
    <div class="row">
        <div class="col-12 col-lg-3">
            <ul>
                <li id="id"><a class="pointer" onclick="identify();" ?>Identify</a></li>
                <li id="lr"><a class="pointer" onclick="identify();" ?>ListRecords</a></li>
                <li id="ls"><a class="pointer" onclick="identify();" ?>ListSets</a></li>
                <li id="ld"><a class="pointer" onclick="identify();" ?>ListMetadataFormats</a></li>
                <li id="li"><a class="pointer" onclick="identify();" ?>ListIdentifiers</a></li>
            </ul>
        </div>
        <div class="col-12 col-lg-9">
            <?= form_label(lang('brapci.url')); ?>

            <div class="input-group mb-3">
                <input type="text" name="url" id="url" value="<?= $url; ?>" class="form-control border border-primary" placeholder="Link do OAI" aria-label="https://site.com.br" aria-describedby="button-addon2">
                <button onclick="fixed();" class="btn btn-primary" type="button" id="button-addon2"><?= lang("brapci.fixed"); ?></button>
            </div>
            <?= form_label(lang('brapci.query')); ?>
            <div class="input-group mb-3">
                <div style="width: 80%;" class="cmd form-control border border-secondary mt-2" id="query"><tt>&nbsp;</tt></div>
                <div style="width: 20%;" class="cmd form-control border border-secondary mt-2" id="verb"><tt>&nbsp;</tt></div>
            </div>

            <!------------------------------------------ Identify ---->
            <div id="identify_info" class="full" style="display: none;">
                <h4>Identify</h4>
                <p>Recupera informações sobre o repositório.</p>
                <p><button id="identify_execute" onclick="identify_execute();" class="btn btn-outline-primary"><?= lang("brapci.execute"); ?></button></p>
            </div>

            <!----------------------------------------- Result -------->
            <div id="result" class="full border form-control border-secondary"><tt>Result</tt></div>
        </div>
    </div>
</div>

<div class="position-static">
    <div class="position-absolute bottom-0 start-0 ps-3 pb-1 border pe-3 border-secondary">
        Status:<span id="status" class="fw-bold"><span class="text-success"> OK</span></span>
    </div>
</div>

<script>
    function identify_execute() {
        $url = $("#url").val();
        if ($url == "") {
            alert("URL é obrigatória");
        } else {
            $url = $url + "?verb=Identify";
            $("#query").html("<tt>" + $url + "</tt>");

            $html = '<iframe style="width: 100%; height: 400px" src="'+$url+'"></iframe>';

            $("#result").html($html);
}
    }

    function identify() {
        $('#lr, #ls, #ld, #li').removeClass('fw-bolder');
        $('#id').addClass('fw-bolder');

        $("#verb").html('Identify');

        $("#identify_info").show('slow');
        /*
        $("#identify_info").show('slow');
        $("#identify_info").show('slow');
        $("#identify_info").show('slow');
        $("#identify_info").show('slow');
        */
    }

    function fixed() {
        $url = $("#url").val();
        alert($url);
    }
</script>