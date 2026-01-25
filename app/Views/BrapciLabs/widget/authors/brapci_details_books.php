<?php
$works = [];
$brapci = (array)$brapci;
if (isset($brapci['works'])) {
    $works = (array)$brapci['works'];
    if (isset($works['Book'])) {
        $works = (array)$works['Book'];
        echo '<ul>';
        foreach ($works as $key => $work) {
            $works[$key] = (array)$work;
            echo '<li>' . ($work) . '</li>';
        }
        echo '</ul>';
    } else {
        echo 'Nenhum livro encontrado para este autor na BRAPCI.';
    }
}
