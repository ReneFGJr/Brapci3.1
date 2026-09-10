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

    function method_a1($limit=5000)
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
        return $this->method_v4query(get("term"), $this->field());
    }

    function method_v4query($method, $field)
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
        $method = str_replace(["\u{201C}", "\u{201D}"], '"', $method);
        $term = strtolower(ascii(trim($method)));

        // Preserve quoted phrases; normalize only standalone boolean operators.
        $term = preg_replace_callback(
            '/"(?:\\\\.|[^"\\\\])*"(*SKIP)(*F)|(?<![\\w-])(AND|OR|NOT)(?![\\w-])/i',
            static function ($match) {
                return strtoupper($match[0]);
            },
            $term
        );

        // Let Elasticsearch parse the complete expression, including nested groups.
        $query['query']['bool'] = [];
        if ($term !== '') {
            $query['query']['bool']['must'][] = [
                'query_string' => [
                    'default_field' => $this->normalizeField($field),
                    'query' => $term,
                    'default_operator' => 'AND',
                ],
            ];
        }

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

    function method_v4OR($method)
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
            $strategy = $this->make_search($method);

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

    function method_v4AND($method)
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
        $strategy = $this->make_search($method);

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
        $term = $this->normalizeField(get("field"));
        if ($term === '') {
            $term = $this->normalizeField(get("fields"));
        }

        return $term;
    }

    private function normalizeField($field)
    {
        switch (strtoupper(trim((string) $field))) {
            case 'AU':
            case 'AUTHOR':
            case 'AUTHORS':
                return 'authors';
            case 'AB':
            case 'ABSTRACT':
                return 'abstract';
            case 'KW':
            case 'KEYWORD':
            case 'KEYWORDS':
                return 'keyword';
            case 'TI':
            case 'TITLE':
                return 'title';
            default:
                return 'full';
        }
    }
}
