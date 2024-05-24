<?php
$vc = [];

$vc[chr(13) . 'RESUMO' . chr(13)] = chr(13) . '{RESUMO}' . chr(13);
$vc[chr(13) . 'ABSTRACT' . chr(13)] = chr(13) . '{RESUMO}' . chr(13);
$vc['Palavras–chave:'] = '{keywords}';

$vc[chr(13) . 'Referências' . chr(13)] = chr(13) . '{REFERENCIAS}' . chr(13);
$vc[chr(13) . '1 Introdução' . chr(13)] = chr(13) . '{INTRODUCAO}' . chr(13);
$vc[chr(13) . 'Introdução' . chr(13)] = chr(13) . '{INTRODUCAO}' . chr(13);

$vc[chr(13) . '2 Referencial teórico' . chr(13)] = chr(13) . '{REFERENCIAL_TEORICO}' . chr(13);
$vc[chr(13) . 'Referencial teórico' . chr(13)] = chr(13) . '{REFERENCIAL_TEORICO}' . chr(13);

$vc[chr(13) . '3 Metodologia' . chr(13)] = chr(13) . '{METODOLOGIA}' . chr(13);

$vc['levantamento bibliográfico'] = '{method:"Levantamento Bibliográfico"}';
?>