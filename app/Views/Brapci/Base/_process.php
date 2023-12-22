<?php

use function RectorPrefix20220609\dump_node;

$vars = array(
    'title',  'authors',
    'idioma', 'year',
    'cover', 'isbn', 'editora_local', 'editora',
    'pages', 'subject', 'url',
    'CatAncib', 'CDD', 'CDU',
    'files', 'summary',
    'license',
    'issue', 'links','abstract'
);

foreach ($vars as $v) {
    if (!isset($$v)) {
        $$v = '';
    }
}

if (!isset($Keywords)) {
    $Keywords = array();
}

$Classification = '';

if (isset($CatAncibArray)) {
    foreach ($CatAncibArray as $name => $id) {
        if ($Classification != '') {
            $Classification .= '<br/>';
        }
        $Classification .= '<a href="' . PATH . '/books/v/' . $id . '">' . $name . '</a>';
    }
}

$KeywordsLN = '';
foreach ($subject as $idx => $subjx) {
    $KeywordsLN .= '<a href="' . PATH . '/v/' . $subjx['ID'] . '">' . $subjx['name'] . '.</a> ';
}
$authors = '';
$authorsLN = '';
$authorsCP = '';

if ((isset($creator_author)) and ($creator_author != '')) {
    foreach ($creator_author as $id => $name) {
        $authors .= anchor(PATH . '/v/' . $name['ID'], $name['name'], 'class=""') . '<br>';
    }
    $authors = troca($authors, ';.', '.');
}

/************************************************************** ABSTRACT */
if (isset($data['hasAbstract']))
    {
    foreach ($data['hasAbstract'] as $lang => $name) {
        $name = key($name[0]);
        $abstract .= $name.'@'.(string)$lang.'<hr>';
    }
    }
