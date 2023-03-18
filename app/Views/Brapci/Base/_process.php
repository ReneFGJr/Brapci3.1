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
if (!isset($Keywords)) {
    $Keywords = array();
}

$Classification = '';

if (isset($CatAncibArray))
    {
        foreach($CatAncibArray as $name=>$id)
            {
                if ($Classification != '')
                    {
                        $Classification .= '<br/>';
                    }
                $Classification .= '<a href="'.PATH.'/books/v/'.$id.'">'.$name.'</a>';
            }

    }

$KeywordsLN = '';
foreach($Keywords as $name=>$id)
    {
        $lnt = explode(';',$name);
        if (count($lnt) == 2)
            {
                $KeywordsLN .= '<a href="'.PATH.'/v/'.$lnt[1].'">'.$lnt[0].'.</a> ';
            }
    }
$authors = '';
$authorsLN = '';
$authorsCP = '';
foreach ($Authors as $id => $name) {
    if (strpos($name, ';')) {
        $name = explode(';', $name);
    } else {
        $name[0] = $name;
        $name[1] = 0;
    }
    if ($name[1] > 0) {
        $authors .= anchor(PATH . '/v/' . $name[1], $name[0], 'class=""') . '<br>';
        $authorsLN .= anchor(PATH . '/v/' . $name[1], $name[0], 'class=""') . '; ';
    } else {
        $authors .= $name . '<br>';
        $authorsLN .= anchor(PATH . '/' . $name[1], $name[0], 'class=""') . '; ';
    }
    $authorsLN .= '.';
    $authorsLN = troca($authorsLN, ';.', '.');
}

if (isset($Chapter['authors'])) {
    $aux = $Chapter['authors'];
    $authorsCP = troca($aux, '$', ';');
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
