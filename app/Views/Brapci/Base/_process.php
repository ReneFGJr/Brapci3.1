<?php
$vars = array(
    'title',  'authors',
    'idioma', 'year',
    'cover', 'isbn', 'editora_local', 'editora',
    'pages', 'subject', 'url',
    'CatAncib', 'CDD', 'CDU',
    'files', 'summary',
    'license',
    'issue', 'links'
);
if (!isset($Keywords))
    {
        $Keywords = array();
    }
$authors = '';
$authorsLN = '';
foreach ($Authors as $id => $name) {
    if (strpos($name, ';')) {
        $name = explode(';', $name);
    } else {
        $name[0] = $name;
        $name[1] = 0;
    }
    if ($name[1] > 0) {
        $authors .= anchor(PATH . '/' . $name[1], $name[0], 'class=""') . '<br>';
        $authorsLN .= anchor(PATH . '/' . $name[1], $name[0], 'class=""') . '; ';
    } else {
        $authors .= $name . '<br>';
        $authorsLN .= anchor(PATH . '/' . $name[1], $name[0], 'class=""') . '; ';
    }
    $authorsLN .= '.';
    $authorsLN = troca($authorsLN, ';.', '.');
}

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

if (isset($Chapter)) {
    //pre($Chapter);
    $vars = array(
        'title', 'idioma', 'authors',
        'AuthorsLN', 'idioma', 'year',
        'cover',
        'url', 'keywords',
        'files', 'summary', 'authorsChapet',
        'DOIChapet', 'abstract', 'license',
        'issue', 'links', 'pagi', 'pagf'
    );
    foreach ($vars as $v) {
        if (!isset($Chapter[$v])) {
            $Chapter[$v] = '';
        }
    }
}
