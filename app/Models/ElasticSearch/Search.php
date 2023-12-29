<?php

namespace App\Models\ElasticSearch;

use CodeIgniter\Model;

class Search extends Model
{
    protected $DBGroup          = 'elastic';
    protected $table            = 'dataset';
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

    function searchFull($q = '', $type = '')
    {
        $Search = new \App\Models\ElasticSearch\Search();
        $Cover = new \App\Models\Base\Cover();

        $dt = $this->search($q, $type);

        $cp = 'ID, id_jnl, jnl_name as JOURNAL, ISSUE,
                        SESSION, LEGEND, TITLE, AUTHORS, COVER as cover';
        $cp = 'ID, ISSUE,
                        SESSION, LEGEND, TITLE, AUTHORS, COVER as cover';


        if (!isset($dt['works']))
            {
                $dt['status'] = '500';
                $dt['messagem'] = 'Erro de acesso ao servidor de busca';
                echo (json_encode($dt));
                exit;
            }

        /* Retorno */
        $n = 0;
        $Search->select($cp);
        //$Search->join('brapci.source_source','JOURNAL = id_jnl','RIGHT');
        foreach ($dt['works'] as $id => $line) {
            $ida = $line['id'];
            if ($n == 0)
                {
                    $Search->where('ID', $ida);
                } else {
                    $Search->Orwhere('ID', $ida);
                }
        }
        $ds = $Search->findAll();
        pre($dt,false);
        pre($ds);
        $dsr = [];
        foreach($ds as $id=>$line)
            {
                pre($line);
                $dsr[$idr] = 1;
            }

        foreach ($dt['works'] as $id => $line) {
            $dt['works'][$id]['data'] = $ds[$id];
        }

        if (!isset($dt['works'])) {
            $dt['works'] = [];
        }
        echo (json_encode($dt));
        exit;
    }

    function recoverList($ids)
    {
        $cp = 'ID as article_id, json, type, YEAR as year';
        $this->select($cp);
        $this->where('ID', $ids[0]);
        for ($r = 1; $r < count($ids); $r++) {
            $this->Orwhere('ID', $ids[$r]);
        }
        $dt = $this->findAll();

        $dr = [];
        foreach ($dt as $id => $line) {
            $js = (array)json_decode($line['json']);
            $ds = [];
            $ds['article_id'] = $line['article_id'];
            $ds['year'] = $line['year'];
            $ds['ldl_title'] = $line['article_id'];
            $ds['ldl_authors'] = $line['article_id'];
            array_push($dr, $ds);
        }
        return $dr;
    }

    function search($q = '', $type = '')
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
        $data = [];

        $strategy = [];
        //$strategy['must']['term']['full'] = ascii($qs);
        //$strategy['must'][0]['match']['full'] = ascii($qs);
        $strategy['must'][0]['match_phrase']['full'] = ascii($qs);

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


        /******************** Sources */
        $data['_source'] = array("article_id", "id_jnl", "type", "title", "abstract", "subject", "year", "legend", "full");

        /******************** Limites */
        $data['size'] = $offset;
        $data['from'] = $start;
        $data['query']['bool'] = $strategy;

        $sx =  '';

        /************************** */
        if ($type == '') {
            $type = trim(COLLECTION);
            $type = mb_strtolower($type);
            $type = troca($type, '/', '');
        }

        switch ($type) {
            case 'autoridade':
                $url = 'brapci3.3/_search';
                $data['query']['bool']['must'][1]['match']['collection'] = 'AC';
                break;
            case 'person':
                $url = 'brapci3.3/_search';
                $data['query']['bool']['must'][1]['match']['collection'] = 'AU';
                break;
            case 'books':
                $url = 'brapci3.3/_search';
                $data['query']['bool']['must'][1]['match']['collection'] = 'BK';
                break;
            case 'benancib':
                $url = 'brapci3.3/_search';
                $data['query']['bool']['must'][1]['match']['id_jnl'] = 75;
                break;
            default:
                //$url = 'brp2/_search';
                $url = 'brapci3.3/_search';
                //$filter['match']['collection'] = 'AR';
                break;
        }

        /********************************************************************** FILTER  */
        /* FILTER ******************************************* Only one */
        /* Journals */
        //$data['query']['bool']['must'][1]['match']['id_jnl'] = '75 1 2 3';


        /* RANGE ******************************************* Only one */
        $di = ((int)trim(get("di")) - 1);
        $df = ((int)trim(get("df")) + 1);
        if ($di < 0) {
            $di = 1899;
        }
        if ($df == 1) {
            $df = date("Y") + 1;
        }
        $data['query']['bool']['filter']['range']['year']['gte'] = $di;
        $data['query']['bool']['filter']['range']['year']['lte'] = $df;
        $data['query']['bool']['filter']['range']['year']['boost'] = 2.0;

        $dt = $API->call($url, $method, $data);

        /* Mostra resultados ****************************************************/
        $rsp = array();
        $rsp['data'] = $data;
        $rsp['data_post'] = $_POST;
        $rsp['data_get'] = $_GET;
        $rsp['url'] = $url;

        if (isset($dt['error'])) {
            $sx = $rsp['error'] = bsmessage(
                h("ERRO") .
                    '<p>' . $dt['error']['root_cause'][0]['type'] . '</p>' .
                    '<p>' . $dt['error']['root_cause'][0]['reason'] . '</p>'
            );
        }

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
                if (isset($line['_id'])) {
                    array_push($rsp['works'], array(
                        'id' => $line['_id'],
                        'score' => $line['_score'],
                        'type' => $line['_source']['type'],
                        //'jnl' => $line['_source']['id_jnl'],
                        'year' => $line['_source']['year'],
                    ));
                }
            }
        }

        return $rsp;
    }
}
