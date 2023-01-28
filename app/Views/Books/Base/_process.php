<?php
$vars = array('title', 'pages', 'subject', 'url', 'CatAncib', 'CDD', 'CDU');
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
