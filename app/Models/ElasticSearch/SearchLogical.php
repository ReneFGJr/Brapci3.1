<?php

namespace App\Models\ElasticSearch;

use CodeIgniter\Model;

class SearchLogical extends Model
{
    protected $table            = 'searchlogicals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function make_search($term)
    {
        $query = [];
        $field = $this->field(); // Define o campo padrão para a busca

        // Normaliza o termo de entrada
        $term = strtolower(ascii($term));
        $term = troca($term, ' or ', ' OR ');
        $term = troca($term, ' and ', ' AND ');
        $term = troca($term, '(', ' ( ');
        $term = troca($term, ')', ' ) ');
        $termO = troca($term, '"', ' " ');
        $term = $this->separarPalavrasComAspas($termO); // Divide os termos mantendo trechos entre aspas

        $boo = 'must'; // Operador booleano padrão
        $pOR = strpos($termO, ' OR ');
        $pAND = strpos($termO, ' AND ');

        if ($pAND < $pOR)
            {
                $boo = 'should';
            }
            // Itera pelos termos e constrói a consulta
        foreach ($term as $t) {
            $t = trim($t);

            if ($t === '') {
                continue; // Ignora termos vazios
            }

            switch ($t) {
                case 'AND':
                    $boo = 'must';
                    break;

                case 'OR':
                    $boo = 'should';
                    break;

                default:
                    // Adiciona o termo à consulta

                        $qr = [
                            'query_string' => [
                                'default_field' => $field,
                                'query' => '"'.$t.'"',
                                //'default_operator' => 'AND',
                            ],
                        ];
                        if (!isset($query[$boo])) {
                        $query[$boo] = []; }
                        array_push($query[$boo],$qr);
                    break;
            }
        }
        return $query;
    }


    function separarPalavrasComAspas($texto)
    {
        $palavras = explode(' ', $texto);
        $resultado = [];
        $agrupando = false;
        $fraseAtual = '';

        foreach ($palavras as $palavra) {
            $palavra = trim($palavra);

            if ($palavra === '') {
                continue;
            }

            if (strpos($palavra, '"') !== false) {
                if ($agrupando) {
                    // Fechando uma frase
                    $fraseAtual .= ' ' . str_replace('"', '', $palavra);
                    $resultado[] = $fraseAtual;
                    $fraseAtual = '';
                    $agrupando = false;
                } else {
                    // Iniciando uma nova frase
                    $agrupando = true;
                    $fraseAtual = str_replace('"', '', $palavra);
                }
            } elseif ($agrupando) {
                // Continuando uma frase agrupada
                $fraseAtual .= ' ' . $palavra;
            } else {
                // Adicionando palavras fora de agrupamento
                $resultado[] = $palavra;
            }
        }

        // Adiciona a última frase se necessário
        if ($agrupando && $fraseAtual !== '') {
            $resultado[] = $fraseAtual;
        }

        return $resultado;
    }

    function method_a1($limit=1000)
        {
            $qr = get("q");
            $qr = ascii($qr);
            $qr = strtolower($qr);

            $qr = troca($qr, ' and ', ' AND ');
            $qr = troca($qr, ' or ', ' OR ');
            $qr = troca($qr, ' not ', ' NOT ');
            for ($r = 0;$r < 32;$r++)
                {
                    $qr = troca($qr, chr($r), ' ');
                }

            $query = [];
            $query['query'] = [];
            $query['query']['bool'] = [];
            $query['query']['bool']['must'] = [];
            $query['size'] = $limit;
            if ($limit < 0) {
                $limit = 100000;
            }
            $query['from'] = 0;


            $q = [];
            $q['query_string'] = [];
            $q['query_string']['default_field'] = 'full';
            $q['query_string']['query'] = $qr;

            array_push($query['query']['bool']['must'],$q);
            return $query;
        }

    function method_v1()
    {
        $start = round('0' . get('start'));
        $offset = round('0' . get('offset'));

        $dt['post'] = $_POST;

        /******************** Sources */
        $data['_source'] = array("article_id", "id_jnl", "type", "title", "abstract", "subject", "year", "legend", "full");

        /******************** Limites */
        if ($offset == 0) {
            $offset = 10;
        }
        $dt['size'] = $offset;
        $dt['from'] = $start;
        //$dt['query']['bool'] = $strategy;

        $Term = get("term");
        $Term = troca($Term, ' and ', ' AND ');
        $Term = troca($Term, ' and ', ' AND ');
        $Term = strtolower(ascii($Term));

        $field = $this->field();
        $query = [];
        $query['query']['bool'] = $this->make_search(get("term"));
        $query['from'] = $start; // Define o deslocamento
        $query['size'] = $offset;  // Quantidade de documentos retornados

        /******* Journal */
        $Journal = trim(troca(get("journal"), ',', ' '));
        if (($Journal != 'JA JE EV BK') and ($Journal != '')) {
            $filter = [];
            if (!isset($query['query']['bool']['must'])) {
                $query['query']['bool']['must'] = [];
            }
            $filter['query_string'] = ['default_field' => 'journal', 'query' => $Journal, 'default_operator' => 'AND'];
            array_push($query['query']['bool']['must'], $filter);
        }


        /******* Collection */
        $SOURCES = trim(troca(get("collection"), ',', ' '));
        if (($SOURCES != 'JA JE EV BK') and ($SOURCES != '')){
            $filter = [];
            if (!isset($query['query']['bool']['must'])) {
                $query['query']['bool']['must'] = [];
            }
            //$filter['query_string'] = ['default_field' => 'collection', 'query' => $SOURCES, 'default_operator' => 'OR'];
            $filter['query_string'] = ['default_field' => 'collection', 'query' => $SOURCES];
            array_push($query['query']['bool']['must'], $filter);
        }

        /******* Range */
        $di = ((int)trim(get("year_start")) - 1);
        $df = ((int)trim(get("year_end")) + 1);
        if ($di < 0) {
            $di = 1950;
        }
        if ($df == 1) {
            $df = date("Y") + 1;
        }
        $range = [];
        $range['range']['year'] = ['gt' => $di, 'lt' => $df];
        if (!isset($query['query']['bool']['must']))
            {
                $query['query']['bool']['must'] = [];
            }
        array_push($query['query']['bool']['must'], $range);
        //echo json_encode($query);
        //pre($query);
        if (get("test") != "") {
            pre($query);
        }

        return $query;
    }

    function method_v4()
    {
        $method = get("term");
        $method = troca($method, ' and ', ' AND ');
        $method = troca($method, ' not ', ' NOT ');
        $method = troca($method, ' or ', ' OR ');


        $OR = (strpos($method, ' OR ') !== false);
        $AND = (strpos($method, ' AND ') !== false);

        if ($OR and $AND === false) {
            $query = $this->method_v4OR();
        } elseif ($AND and $OR === false) {
            $query = $this->method_v4AND();
        } elseif ($OR and $AND) {
            $query = $this->method_v4query();
        } else {
            echo "OPS ========= fullsearch sem operador booleano. Default para AND";
            exit;
            $query = $this->method_v4AND(); // Default to OR if no operator is found
        }
        return $query;
    }

    function method_v4query()
    {
        /************************************************************
         * Paginação
         ************************************************************/
        $start  = (int) get('start');
        $offset = (int) get('offset');

        if ($offset <= 0) {
            $offset = 10;
        }

        /************************************************************
         * Query básica
         ************************************************************/
        $query = [];

        $query['from'] = $start;
        $query['size'] = $offset;

        /************************************************************
         * Recupera estratégia
         ************************************************************/
        $term = trim(get("term"));

        // Normaliza operadores booleanos
        $term = preg_replace('/\s+AND\s+/i', ' AND ', $term);
        $term = preg_replace('/\s+OR\s+/i',  ' OR ',  $term);

        /************************************************************
         * Remove parênteses externos, quando existirem
         *
         * Exemplo:
         *
         * ("A" OR "B") AND ("C" OR "D")
         *
         * será dividido inicialmente em:
         *
         * ("A" OR "B")
         * ("C" OR "D")
         ************************************************************/

        $groups = preg_split(
            '/\s+AND\s+/i',
            $term
        );

        /************************************************************
         * MUST principal
         *
         * Cada grupo separado por AND será obrigatório.
         ************************************************************/
        $must = [];

        foreach ($groups as $group) {

            $group = trim($group);

            /******************************************************
             * Remove parênteses externos
             ******************************************************/
            if (
                substr($group, 0, 1) == '(' &&
                substr($group, -1) == ')'
            ) {
                $group = substr($group, 1, -1);
            }

            $group = trim($group);

            /******************************************************
             * Verifica se dentro do grupo existe OR
             ******************************************************/
            $terms = preg_split(
                '/\s+OR\s+/i',
                $group
            );

            /******************************************************
             * Mais de um termo = grupo OR
             ******************************************************/
            if (count($terms) > 1) {

                $should = [];

                foreach ($terms as $searchTerm) {

                    $searchTerm = trim($searchTerm);

                    if ($searchTerm == '') {
                        continue;
                    }

                    /************************************************
                     * Normalização utilizada pela BRAPCI
                     ************************************************/
                    $searchTerm = strtolower(
                        ascii($searchTerm)
                    );

                    $should[] = [
                        'query_string' => [
                            'default_field' => 'full',
                            'query'         => $searchTerm
                        ]
                    ];
                }

                if (count($should) > 0) {

                    $must[] = [
                        'bool' => [
                            'should' => $should,
                            'minimum_should_match' => 1
                        ]
                    ];
                }
            } else {

                /**************************************************
                 * Não existe OR dentro do grupo.
                 *
                 * Portanto é uma condição obrigatória.
                 **************************************************/
                $searchTerm = trim($group);

                if ($searchTerm != '') {

                    $searchTerm = strtolower(
                        ascii($searchTerm)
                    );

                    $must[] = [
                        'query_string' => [
                            'default_field' => 'full',
                            'query'         => $searchTerm
                        ]
                    ];
                }
            }
        }

        /************************************************************
         * Monta BOOL principal
         ************************************************************/
        $query['query']['bool'] = [];

        if (count($must) > 0) {
            $query['query']['bool']['must'] = $must;
        }

        /************************************************************
         * FILTER
         ************************************************************/
        $query['query']['bool']['filter'] = [];

        /************************************************************
         * Journal
         ************************************************************/
        $Journal = trim(
            troca(
                get("journal"),
                ',',
                ' '
            )
        );

        if (
            ($Journal != 'JA JE EV BK') &&
            ($Journal != '')
        ) {

            $query['query']['bool']['filter'][] = [
                'query_string' => [
                    'default_field'    => 'journal',
                    'query'            => $Journal,
                    'default_operator' => 'AND'
                ]
            ];
        }

        /************************************************************
         * Collection
         ************************************************************/
        $SOURCES = trim(
            troca(
                get("collection"),
                ',',
                ' '
            )
        );

        if (
            ($SOURCES != 'JA JE EV BK') &&
            ($SOURCES != '')
        ) {

            $query['query']['bool']['filter'][] = [
                'query_string' => [
                    'default_field'    => 'collection',
                    'query'            => $SOURCES,
                    'default_operator' => 'OR'
                ]
            ];
        }

        /************************************************************
         * Intervalo de anos
         ************************************************************/
        $year_start = (int) trim(get("year_start"));
        $year_end   = (int) trim(get("year_end"));

        if ($year_start <= 0) {
            $year_start = 1951;
        }

        if ($year_end <= 0) {
            $year_end = (int) date("Y");
        }

        $query['query']['bool']['filter'][] = [
            'range' => [
                'year' => [
                    'gte' => $year_start,
                    'lte' => $year_end
                ]
            ]
        ];

        /************************************************************
         * Remove FILTER vazio
         ************************************************************/
        if (empty($query['query']['bool']['filter'])) {
            unset($query['query']['bool']['filter']);
        }

        /************************************************************
         * Debug
         ************************************************************/
        if (get("test") != "") {
            pre($query);
        }

        return $query;
    }

    function method_v4OR()
        {
            /************************************************************
             * Paginação
             ************************************************************/
            $start  = (int) get('start');
            $offset = (int) get('offset');

            if ($offset <= 0) {
                $offset = 10;
            }

            /************************************************************
             * Query básica
             ************************************************************/
            $query = [];

            $query['from'] = $start;
            $query['size'] = $offset;

            /************************************************************
             * Estratégia de busca
             *
             * Exemplo:
             * "Indexação automática" OR "Indexação manual"
             ************************************************************/
            $strategy = $this->make_search(get("term"));

            /*
     * make_search() retorna o conteúdo do bool.
     */
            $query['query']['bool'] = $strategy;

            /*
     * IMPORTANTE:
     * Se existir SHOULD, pelo menos uma condição deve ser atendida.
     */
            if (
                isset($query['query']['bool']['should']) &&
                count($query['query']['bool']['should']) > 0
            ) {
                $query['query']['bool']['minimum_should_match'] = 1;
            }

            /************************************************************
             * Cria FILTER
             *
             * Journal, Collection e Year não precisam participar
             * do cálculo de relevância (_score).
             ************************************************************/
            if (!isset($query['query']['bool']['filter'])) {
                $query['query']['bool']['filter'] = [];
            }

            /************************************************************
             * Journal
             ************************************************************/
            $Journal = trim(
                troca(
                    get("journal"),
                    ',',
                    ' '
                )
            );

            if (
                ($Journal != 'JA JE EV BK') &&
                ($Journal != '')
            ) {

                $filter = [
                    'query_string' => [
                        'default_field'    => 'journal',
                        'query'            => $Journal,
                        'default_operator' => 'AND'
                    ]
                ];

                $query['query']['bool']['filter'][] = $filter;
            }

            /************************************************************
             * Collection
             ************************************************************/
            $SOURCES = trim(
                troca(
                    get("collection"),
                    ',',
                    ' '
                )
            );

            if (
                ($SOURCES != 'JA JE EV BK') &&
                ($SOURCES != '')
            ) {

                $filter = [
                    'query_string' => [
                        'default_field' => 'collection',
                        'query'         => $SOURCES,
                        'default_operator' => 'OR'
                    ]
                ];

                $query['query']['bool']['filter'][] = $filter;
            }

            /************************************************************
             * Intervalo de anos
             ************************************************************/
            $year_start = (int) trim(get("year_start"));
            $year_end   = (int) trim(get("year_end"));

            if ($year_start <= 0) {
                $year_start = 1951;
            }

            if ($year_end <= 0) {
                $year_end = (int) date("Y");
            }

            $range = [
                'range' => [
                    'year' => [
                        'gte' => $year_start,
                        'lte' => $year_end
                    ]
                ]
            ];

            $query['query']['bool']['filter'][] = $range;

            /************************************************************
             * Remove FILTER vazio
             ************************************************************/
            if (empty($query['query']['bool']['filter'])) {
                unset($query['query']['bool']['filter']);
            }

            return $query;
        }

    function method_v4AND()
    {
        /************************************************************
         * Paginação
         ************************************************************/
        $start  = (int) get('start');
        $offset = (int) get('offset');

        if ($offset <= 0) {
            $offset = 10;
        }

        /************************************************************
         * Query básica
         ************************************************************/
        $query = [];

        $query['from'] = $start;
        $query['size'] = $offset;

        /************************************************************
         * Estratégia de busca
         *
         * Exemplo:
         * "Indexação automática" AND "Inteligência artificial"
         ************************************************************/
        $strategy = $this->make_search(get("term"));

        /************************************************************
         * Converte SHOULD para MUST
         *
         * OR:
         * should => [
         *     termo A,
         *     termo B
         * ]
         *
         * AND:
         * must => [
         *     termo A,
         *     termo B
         * ]
         ************************************************************/
        if (isset($strategy['should'])) {

            if (!isset($strategy['must'])) {
                $strategy['must'] = [];
            }

            foreach ($strategy['should'] as $condition) {
                $strategy['must'][] = $condition;
            }

            unset($strategy['should']);
            unset($strategy['minimum_should_match']);
        }

        $query['query']['bool'] = $strategy;

        /************************************************************
         * Cria FILTER
         ************************************************************/
        if (!isset($query['query']['bool']['filter'])) {
            $query['query']['bool']['filter'] = [];
        }

        /************************************************************
         * Journal
         ************************************************************/
        $Journal = trim(
            troca(
                get("journal"),
                ',',
                ' '
            )
        );

        if (
            ($Journal != 'JA JE EV BK') &&
            ($Journal != '')
        ) {

            $filter = [
                'query_string' => [
                    'default_field'    => 'journal',
                    'query'            => $Journal,
                    'default_operator' => 'AND'
                ]
            ];

            $query['query']['bool']['filter'][] = $filter;
        }

        /************************************************************
         * Collection
         ************************************************************/
        $SOURCES = trim(
            troca(
                get("collection"),
                ',',
                ' '
            )
        );

        if (
            ($SOURCES != 'JA JE EV BK') &&
            ($SOURCES != '')
        ) {

            $filter = [
                'query_string' => [
                    'default_field'    => 'collection',
                    'query'            => $SOURCES,
                    'default_operator' => 'OR'
                ]
            ];

            $query['query']['bool']['filter'][] = $filter;
        }

        /************************************************************
         * Intervalo de anos
         ************************************************************/
        $year_start = (int) trim(get("year_start"));
        $year_end   = (int) trim(get("year_end"));

        if ($year_start <= 0) {
            $year_start = 1951;
        }

        if ($year_end <= 0) {
            $year_end = (int) date("Y");
        }

        $range = [
            'range' => [
                'year' => [
                    'gte' => $year_start,
                    'lte' => $year_end
                ]
            ]
        ];

        $query['query']['bool']['filter'][] = $range;

        /************************************************************
         * Remove FILTER vazio
         ************************************************************/
        if (empty($query['query']['bool']['filter'])) {
            unset($query['query']['bool']['filter']);
        }
        return $query;
    }

    function field()
    {
        $flds = get("field");
        switch ($flds) {
            case 'AU':
                $field = 'authors';
                break;
            case 'AB':
                $field = 'abstract';
                break;
            case 'KW':
                $field = 'keyword';
                break;
            case 'TI':
                $field = 'title';
                break;
            default:
                $field = 'full';
                break;
        }
        return $field;
    }
}
