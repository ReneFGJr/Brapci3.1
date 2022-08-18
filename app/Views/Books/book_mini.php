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
$cover = 'img/books/no_cover.png';
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
            $cover = $line['n_name2'];
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
?>

<div class="container-a2">
    <ul class="caption-style-2">
        <li>
            <img src=" <?= $cover; ?>" class="img-fluidx" style="width: 100%;">
            <div class="caption">
                <div class="blur"></div>
                <div class="caption-text">
                    <b><?= $title; ?></b>
                    <br><br>
                    <?= $data; ?>
                </div>
            </div>
        </li>
    </ul>
</div>