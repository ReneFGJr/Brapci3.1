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

    function method_a1()
        {
            $qr = get("q");

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
            $query['size'] = 1000;
            $query['from'] = 0;

            $q = [];
            $q['query_string'] = [];
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
            $filter['query_string'] = ['default_field' => 'collection', 'query' => $SOURCES, 'default_operator' => 'OR'];
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
