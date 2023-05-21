<?php
if (isset($_SESSION['oai_url']))
    {
        $url = $_SESSION['oai_url'];
    } else {
        $url = 'https://seer.ufrgs.br/EmQuestao/oai';
    }
?>