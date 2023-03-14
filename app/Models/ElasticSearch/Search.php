<?php

namespace App\Models\ElasticSearch;

use CodeIgniter\Model;

class Search extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'searches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function search($q = '',$type='')
    {
        $start = round('0' . get('start'));
        $offset = round('0' . get('offset'));

        if ($start <= 0) {
            $start = 0;
        }
        if ($offset < 1) {
            $offset = 10;
        }

        $API = new \App\Models\ElasticSearch\API();

        $qs = trim(get("q"));
        if ($qs == '') {
            $qs = trim(get("qs"));
        }

        /*************** SOURCE **********************************************************/
        $method = "POST";

        /***************************************************************** MULTI MATCH **/
        $data = array();
        $data['query']['bool']['must'] = array();


        /************************* TYPE */
        $type_multi_match = array(
            'best_fields', 'most_fields', 'cross_fields',
            'phrase', 'phrase_prefix', 'phrase_prefix', 'bool_prefix'
        );
        $query['multi_match']['type'] = $type_multi_match[0];
        $query['multi_match']['operator'] = 'and';

        /********************************************** QUERY */
        $query['multi_match']['query'] = ascii($qs);

        $query = [];
        $query['must']['match_phrase']['title'] = ascii($qs);

        $strategy = $query;

        /******************** Fields */
        $flds = round('0' . get("field"));

        switch ($flds) {
            case 1:
                $fields = array("title");
                break;
            case 2:
                $fields = array("abstract");
                break;
            case 3:
                $fields = array("subject");
                break;
            default:
                $fields = array("title^10", "abstract", "subject^5", "authors");
                break;
        }

        //$query['multi_match']['fields'] = $fields;

        $range['range']['year']['gte'] = (int)trim(get("di"));
        $range['range']['year']['lte'] = (int)trim(get("df"));
        $range['range']['year']['boost'] = 2.0;

        //array_push($data['query']['bool']['must'], $query);
        //array_push($data['query']['bool']['must'], $range);


        /******************** Sources */
        $data['_source'] = array("article_id", "id_jnl", "title", "abstract", "subject", "year");

        /******************** Limites */
        $data['size'] = $offset;
        $data['from'] = $start;
        $data['query']['bool'] = $strategy;

        $sx =  $q;

        /************************** */
        $type = trim(COLLECTION);
        $type = mb_strtolower($type);
        $type = troca($type, '/', '');

        switch ($type) {
            case 'books':
                $url = 'brapci3.1/_search';
                $filter['match']['collection'] = 'BK';
                break;
            case 'benancib':
                $url = 'brapci3.1/_search';
                $filter['match']['id_jnl'] = [75];
                break;
            default:
                $url = 'brp2/_search';
                //$filter['terms']['id_jnl'] = [75];
                break;
        }

        /********************************************************************** FILTER  */
        /* FILTER ******************************************* Only one */
        if (isset($filter['terms']['id_jnl'])) {
            $data['query']['bool']['filter'] = array();
            array_push($data['query']['bool']['filter'], $filter);
        }
        if (isset($filter['terms']['collection'])) {
            $data['query']['bool']['filter'] = array();
            array_push($data['query']['bool']['filter'], $filter);
        }

        //pre($data,false);

        $dt = $API->call($url, $method, $data);

        /* Mostra resultados ****************************************************/

        $rsp = array();
        $rsp['query'] = $qs;

        $total = 0;
        if (isset($dt['hits'])) {
            $rsp['total'] = $dt['hits']['total']['value'];
            $rsp['start'] = $start + 1;
            $rsp['offset'] = $offset;
            $rsp['works'] = array();
            $hits = $dt['hits']['hits'];
            for ($r = 0; $r < count($hits); $r++) {
                $line = $hits[$r];
                if (isset($line['_source']['article_id'])) {
                    array_push($rsp['works'], array(
                        'id' => $line['_source']['article_id'],
                        'score' => $line['_score']
                    ));
                }
            }
        }
        return $rsp;
    }
}
