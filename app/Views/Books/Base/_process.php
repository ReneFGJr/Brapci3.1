<?php
$vars = array(
        'title', 'idiomaChapet','authors',
        'titleChapet', 'idioma','year',
        'cover','isbn', 'editora_local', 'editora',
        'pages', 'subject', 'url',
        'CatAncib', 'CDD', 'CDU', 'URL',
        'files', 'summary', 'authorsChapet',
        'DOIChapet', 'abstractChapt', 'license');

foreach ($vars as $v) {
    if (!isset($$v)) {
        $$v = '';
    }
}
/**************** ISBN */
if (is_array($isbn)) {
    $isbn_v = '';
    $isbna = array();
    foreach ($isbn as $v) {
        if (!isset($isbna[$v])) {
            if ($isbn_v != '') {
                $isbn_v .= '<br>';
            }
            $isbn_v .= $v;
            $isbna[$v] = 1;
        }
    }
    $isbn = $isbn_v;
}
