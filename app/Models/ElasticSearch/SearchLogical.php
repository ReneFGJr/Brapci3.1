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
        $boo = 'must'; // Operador booleano padrão
        $order = 0; // Para rastrear a ordem dos termos

        // Normaliza o termo de entrada
        $term = troca($term, ' or ', ' OR ');
        $term = troca($term, ' and ', ' AND ');
        $term = troca($term, '(', ' ( ');
        $term = troca($term, ')', ' ) ');
        $term = troca($term, '"', ' " ');
        $term = $this->separarPalavrasComAspas($term); // Divide os termos mantendo trechos entre aspas

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
                                'query' => $t,
                                'default_operator' => 'AND',
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
        $query2 = [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'query_string' => [
                                'default_field' => $field,  // Campo(s) para buscar
                                'query' => $Term, // Termo de busca
                                'default_operator' => 'AND', // Operador padrão (opcional)
                            ]
                        ]
                    ]
                ]
            ],
            'from' => $start, // Define o deslocamento
            'size' => $offset,  // Quantidade de documentos retornados
        ];
        /******* Collection */

        $SOURCES = trim(troca(get("collection"), ',', ' '));
        if (($SOURCES != 'JA JE EV BK') or ($SOURCES == '')){
            $filter = [];
            $filter['must']['query_string'] = ['default_field' => 'collection', 'query' => $SOURCES, 'default_operator' => 'OR'];
            //array_push($query['query']['bool'], $filter);
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
        $range['must']['range']['year'] = ['gt' => $di, 'lt' => $df];
        //array_push($query['query']['bool'], $range);
        echo json_encode($query);
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
