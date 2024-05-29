<?php
$vc = [];
for ($r = 1920; $r < date("Y") + 10; $r++) {
    $vc[' ' . $r . ' '] = '{date:"' . $r . '"}';
    $vc['(' . $r . ')'] = '({date:"' . $r . '"})';
    $vc[' ' . $r . ')'] = ' {date:"' . $r . '"})';
    $vc['(' . $r . ','] = ' ({date:"' . $r . '"},';
    $vc[' ' . $r . ','] = ' {date:"' . $r . '"},';
    $vc[' ' . $r . ';'] = ' {date:"' . $r . '"};';
    $vc[' ' . $r . '.'] = ' {date:"' . $r . '"}.';
    $vc[' ' . $r . chr(13)] = ' {date:"' . $r . '"}' . chr(13);
}


$mes = ['jan', 'fev', 'mar', 'abr', 'maio', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez', 'mai'];
$mesN = [
    'jan' => 'janeiro',
    'fev' => 'fevereiro',
    'mar' => 'março',
    'abr' => 'abril',
    'maio' => 'maio',
    'mai' => 'maio',
    'jun' => 'junho',
    'jul' => 'julho',
    'ago' => 'agosto',
    'set' => 'setembro',
    'out' => 'outubro',
    'nov' => 'novembro',
    'dez' => 'dezembro'
];
foreach ($mes as $m) {
    $vc[' ' . $m . '.'] = ' {date:"' . $mesN[$m] . '"} ';
    $vc[' ' . $m . ' '] = ' {date:"' . $mesN[$m] . '"} ';
    $vc['/' . $m . '.'] = ' {date:"' . $mesN[$m] . '"} ';
}

foreach ($mesN as $m=>$N) {
    $vc[' ' . $N . '.'] = ' {date:"' . $m . '"} ';
    $vc[' ' . $N . ' '] = ' {date:"' . $m . '"} ';
    $vc['/' . $N . '.'] = ' {date:"' . $m . '"} ';
}

foreach ($mesN as $m => $N) {
    $N = ucfirst($N);
    $vc[' ' . $N . '.'] = ' {date:"' . $m . '"} ';
    $vc[' ' . $N . ' '] = ' {date:"' . $m . '"} ';
    $vc['/' . $N . '.'] = ' {date:"' . $m . '"} ';
}
