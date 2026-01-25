<?php
$works = [];
$brapci = (array)$brapci;
if (isset($brapci['works'])) {
    $works = (array)$brapci['works'];
    if (isset($works['Article'])) {
        $works = (array)$works['Article'];
        echo '<ul>';
        foreach ($works as $key => $work) {
            $works[$key] = (array)$work;
            echo '<li>'.($work). '</li>';
        }
        echo '</ul>';
    } else {
        echo 'Nenhum artigo encontrado para este autor na BRAPCI.';
    }
}
?>
