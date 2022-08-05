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

for ($r = 0; $r < count($book); $r++) {
    $line = $book[$r];
    $class = $line['c_class'];


    switch ($class) {
        case 'hasLanguageExpression':
            $idioma .= $line['n_name2'].' ';;
            break;
        case 'isPublisher':
            $editora .= $line['n_name2'] . ' ';;
            break;
        case 'hasCover':
            $cover = '<img src="' . $line['n_name2'] . '" class="img-fluid">';
            break;
        case 'hasTitle':
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
            echo '<br>==>' . $class . '==>' . $line['n_name2'];
            break;
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-8">
            <h2><?= $title; ?></h2>
            <h4><i><?= $authors; ?></i></h4>
            <p><?= $isbn; ?></p>
            <p><?=$idioma;?></p>
            <p>Editora: <?=$editora;?></p>
            <p>Palavras-chave: <?= $subject; ?></p>
        </div>
        <div class="col-4">
            <?= $cover; ?>
        </div>
    </div>
</div>