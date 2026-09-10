<?php
// Run with: php tests/regression/SearchLogicalV4Test.php
namespace CodeIgniter {
    class Model {}
}
namespace App\Models\ElasticSearch {
    function get($key) { return $GLOBALS['searchInput'][$key] ?? ''; }
    function ascii($value) { return iconv('UTF-8', 'ASCII//TRANSLIT', $value); }
    function troca($value, $from, $to) { return str_replace($from, $to, $value); }
}
namespace {
    require __DIR__ . '/../../app/Models/ElasticSearch/SearchLogical.php';
    function check($expected, $actual) {
        if ($expected !== $actual) {
            throw new \RuntimeException(var_export([$expected, $actual], true));
        }
    }
    $logic = new \App\Models\ElasticSearch\SearchLogical();
    $fields = [
        'TI' => 'title', 'Title' => 'title',
        'AB' => 'abstract', 'Abstract' => 'abstract',
        'KW' => 'keyword', 'Keywords' => 'keyword',
        'AU' => 'authors', 'Author' => 'authors',
        '' => 'full', 'All fields' => 'full',
    ];
    $expressions = [
        '(alpha or beta) and (gamma or delta)' => '(alpha OR beta) AND (gamma OR delta)',
        'alpha OR (beta AND (gamma OR delta))' => 'alpha OR (beta AND (gamma OR delta))',
        'alpha and not beta' => 'alpha AND NOT beta',
        'not(alpha or beta)' => 'NOT(alpha OR beta)',
        '"war and peace" OR "and"' => '"war and peace" OR "and"',
        'alpha OR beta' => 'alpha OR beta',
        'alpha AND beta' => 'alpha AND beta',
        'alpha beta' => 'alpha beta',
        '"alpha \"and\" beta" or gamma' => '"alpha \"and\" beta" OR gamma',
    ];
    foreach ($fields as $input => $field) {
        foreach ($expressions as $expression => $expected) {
            $GLOBALS['searchInput'] = [
                'field' => $input, 'term' => $expression,
                'start' => '20', 'offset' => '5',
                'journal' => '12', 'collection' => 'JA,EV',
                'year_start' => '2000', 'year_end' => '2025',
            ];
            $query = $logic->method_v4();
            check(['default_field' => $field, 'query' => $expected, 'default_operator' => 'AND'],
                $query['query']['bool']['must'][0]['query_string']);
            check(20, $query['from']);
            check(5, $query['size']);
            check('12', $query['query']['bool']['filter'][0]['query_string']['query']);
            check('JA EV', $query['query']['bool']['filter'][1]['query_string']['query']);
            check(['gte' => 2000, 'lte' => 2025], $query['query']['bool']['filter'][2]['range']['year']);
        }
    }
    $GLOBALS['searchInput'] = [];
    $query = $logic->method_v4query('alpha OR beta', 'KW');
    check('keyword', $query['query']['bool']['must'][0]['query_string']['default_field']);
    check(10, $query['size']);
    check(false, isset($logic->method_v4()['query']['bool']['must']));
    echo "OK: 90 expression/field combinations, filters, pagination and empty input.\n";
}
