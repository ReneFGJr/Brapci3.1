<?php
$vars = array('title', 'year','isbn', 'editora_local', 'editora','pages', 'subject', 'url', 'CatAncib', 'CDD', 'CDU','files', 'summary');
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
