<?php
$title = '';
$authors = '';
$isbn = '';
$subject = '';
$lang = '';
$idioma = '';
$pag = '';
$editora = '';
$lugar = '';
$data = '';
$cover = '/img/books/no_cover.png';
$id_cc = 0;
for ($r = 0; $r < count($book); $r++) {
    $line = $book[$r];
    $class = $line['c_class'];

    switch ($class) {
        case 'hasLanguageExpression':
            $idioma .= $line['n_name2'] . ' ';;
            break;
        case 'isPublisher':
            $editora .= $line['n_name2'] . ' ';;
            break;
        case 'dateOfPublication':
            $data .= $line['n_name2'] . ' ';;
            break;
        case 'hasCover':
            $COVER = new \App\Models\Base\Cover();
            $cover = $COVER->tumb($line['d_r2'], $line['n_name2']);
            break;
        case 'hasTitle':
            $id_cc = $line['d_r1'];
            if ($title != '') {
                $title .= '<br>';
            }
            $title .= trim($line['n_name']);
            break;
        case 'hasSubject':
            if ($subject != '') {
                $subject .= '; ';
            }
            $subject .= trim($line['n_name2']);
            break;
        case 'hasAuthor':
            if ($authors != '') {
                $authors .= '; ';
            }
            $authors .= trim($line['n_name2']);
            break;
        case 'hasISBN':
            if ($isbn != '') {
                $isbn .= '<br>';
            }
            $isbn .= trim($line['n_name']);
            break;
        case 'hasPage':
            $pag .= $line['n_name'];
            break;
        case 'hasClassificationCDD':
            break;
        default:
            //echo '<br>==>' . $class . '==>' . $line['n_name2'];
            break;
    }
}
if ($id_cc > 0)
{
    $link = '<a href="' . PATH . COLLECTION . '/v/' . $id_cc . '">';
    $linka = '</a>';
} else {
    $link = '<a href="#" onclick="alert(\'ERRO DE ACESSO\');">';
    $linka = '</a>';
}

echo '<br>'.$cover;
