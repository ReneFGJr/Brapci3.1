<?php
$title = '';
$authors = '';
$isbn = '';

for ($r = 0; $r < count($book); $r++) {
    $line = $book[$r];
    $class = $line['c_class'];

    echo '<br>==>' . $class . '==>' . $line['n_name2'];
    switch ($class) {
        case 'hasCover':
            $cover = '<img src="' . $line['n_name2'] . '" class="img-fluid">';
            break;
        case 'hasTitle':
            if ($title != '') {
                $title .= '<br>';
            }
            $title .= trim($line['n_name']);
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
            break;
        case '':
            break;
        case '':
            break;
        case '':
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
        </div>
        <div class="col-4">
            <?= $cover; ?>
        </div>
    </div>
</div>