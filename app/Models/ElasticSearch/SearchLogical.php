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

    function method_v1()
    {
        $start = round('0' . get('start'));
        $offset = round('0' . get('offset'));

        $term = get("term");
        $dt['post'] = $_POST;

        /******************** Sources */
        $data['_source'] = array("article_id", "id_jnl", "type", "title", "abstract", "subject", "year", "legend", "full");

        /* Strategy */
        $strategy['query']['match']['full'] = 'biblioteca';

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
        $query = [
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
        $SOURCES = troca(get("collection"), ',', ' ');
        if ($SOURCES != 'JA JE EV BK') {
            $filter = [];
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
        array_push($query['query']['bool']['must'], $range);

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
