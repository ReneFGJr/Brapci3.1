<?php
$vc = [];

$vc[chr(13) . 'RESUMO' . chr(13)] = chr(13) . '{RESUMO}' . chr(13);
$vc[chr(13) . 'Resumo' . chr(13)] = chr(13) . '{RESUMO}' . chr(13);

$vc[chr(13) . 'ABSTRACT' . chr(13)] = chr(13) . '{RESUMO}' . chr(13);
$vc[chr(13) . 'Abstract' . chr(13)] = chr(13) . '{RESUMO}' . chr(13);

$vc[chr(13) . 'RESUMEN' . chr(13)] = chr(13) . '{RESUMO}' . chr(13);
$vc[chr(13) . 'Resumo:'] = chr(13) . '{RESUMO}' . chr(13);
$vc[chr(13) . 'Abstract:'] = chr(13) . '{RESUMO}' . chr(13);



$vc['Palavras–chave:'] = '{keywords}';
$vc['Keywords:'] = '{keywords}';
$vc['Palavras-chave:'] = '{keywords}';
$vc['palavras-chave:'] = '{keywords}';


$vc[chr(13) . 'Referências' . chr(13)] = chr(13) . '{structure:"Referencia"}' . chr(13);
$vc[chr(13) . 'REFERÊNCIAS' . chr(13)] = chr(13) . '{structure:"Referencia"}' . chr(13);
$vc[chr(13) . 'REFERÊNCIAS BIBLIOGRÁFICAS' . chr(13)] = chr(13) . '{structure:"Referencia"}' . chr(13);

$vc[chr(13) . '1 Introdução' . chr(13)] = chr(13) . '{structure:"Introducao"}' . chr(13);
$vc[chr(13) . '1 INTRODUÇÃO' . chr(13)] = chr(13) . '{structure:"Introducao"}' . chr(13);

$vc[chr(13) . 'Introdução' . chr(13)] = chr(13) . '{structure:"Introducao"}' . chr(13);

$vc[chr(13) . '2 Referencial teórico' . chr(13)] = chr(13) . '{structure:"Referencial"}' . chr(13);
$vc[chr(13) . 'Referencial teórico' . chr(13)] = chr(13) . '{structure:"Referencial"}' . chr(13);

$vc[chr(13) . '3 Metodologia' . chr(13)] = chr(13) . '{structure:"Metodologia"}' . chr(13);
$vc[chr(13) . '2 METODOLOGIA' . chr(13)] = chr(13) . '{structure:"Metodologia"}' . chr(13);
$vc[chr(13) . '3 METODOLOGIA' . chr(13)] = chr(13) . '{structure:"Metodologia"}' . chr(13);
$vc[chr(13) . '4 METODOLOGIA' . chr(13)] = chr(13) . '{structure:"Metodologia"}' . chr(13);

$vc[chr(13) . '2 RESULTADOS' . chr(13)] = chr(13) . '{structure:"Resultados"}' . chr(13);
$vc[chr(13) . '3 RESULTADOS' . chr(13)] = chr(13) . '{structure:"Resultados"}' . chr(13);
$vc[chr(13) . '4 RESULTADOS' . chr(13)] = chr(13) . '{structure:"Resultados"}' . chr(13);
$vc[chr(13) . '5 RESULTADOS' . chr(13)] = chr(13) . '{structure:"Resultados"}' . chr(13);

$vc[chr(13) . '5 CONSIDERAÇÕES FINAIS' . chr(13)] = chr(13) . '{structure:"Considerações Finais"}' . chr(13);


$vc['levantamento bibliográfico'] = '{method:"Levantamento Bibliográfico"}';
?>