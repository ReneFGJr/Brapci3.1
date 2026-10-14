<?php
// Executar: php tests/regression/CitedClusterSearchTest.php
namespace CodeIgniter {
    class Model {}
}
namespace {
    require __DIR__ . '/../../app/Models/AI/Cited/Index.php';
    $cases = [
        ['saracevic Notion of Relevance in Information', ['saracevic', 'Notion', 'Relevance', 'Information']],
        ['Saracevic OF IN THE with Information', ['Saracevic', 'Information']],
        ['Informação da ciência para bibliotecas', ['Informação', 'ciência', 'bibliotecas']],
        ['of in the de da a !!!', []],
        ['  ', []],
        ['"Notion" of Relevance Relevance', ['Notion', 'Relevance']],
    ];
    foreach ($cases as [$query, $expected]) {
        if (\App\Models\AI\Cited\Index::clusteringSearchTerms($query) !== $expected) {
            throw new \RuntimeException('Termos incorretos para: ' . $query);
        }
    }
    echo "OK: stopwords, maiúsculas, português, pontuação e buscas vazias.\n";
}
