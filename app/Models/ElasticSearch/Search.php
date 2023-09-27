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

            foreach($dt['works'] as $id=>$line)
                {
                    $ida = $line['id'];
                    $ds = $Search
                            ->join('brapci.source_source', 'dataset.id_jnl = source_source.id_jnl')
                            ->where('article_id',$ida)
                            ->first();
                    if ($ds != '')
                        {
                            $ds['cover'] = $Cover->cover($ds['id_jnl']);
                        } else {
                            $ds['id'] = $ida;
                            echo $Search->getlastquery().'<br>';
                        }

                    $dt['works'][$id]['data'] = $ds;
                }
            echo (json_encode($dt));
            exit;
        }

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
        $data = [];

        $strategy = [];
        //$strategy['must']['term']['full'] = ascii($qs);
        $strategy['must'][0]['match']['full'] = ascii($qs);

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
        $data['_source'] = array("article_id", "id_jnl", "type", "title", "abstract", "subject", "year","full");

        /******************** Limites */
        $data['size'] = $offset;
        $data['from'] = $start;
        $data['query']['bool'] = $strategy;

        $sx =  '';

        /************************** */
        if ($type == '')
        {
            $type = trim(COLLECTION);
            $type = mb_strtolower($type);
            $type = troca($type, '/', '');
        }

        switch ($type) {
            case 'autoridade':
                $url = 'brapci3.1/_search';
                $data['query']['bool']['must'][1]['match']['collection'] = 'AC';
                break;
            case 'person':
                $url = 'brapci3.1/_search';
                $data['query']['bool']['must'][1]['match']['collection'] = 'AU';
                break;
            case 'books':
                $url = 'brapci3.1/_search';
                $data['query']['bool']['must'][1]['match']['collection'] = 'BK';
                break;
            case 'benancib':
                $url = 'brapci3.1/_search';
                $data['query']['bool']['must'][1]['match']['id_jnl'] = 75;
                break;
            default:
                //$url = 'brp2/_search';
                $url = 'brapci3.1/_search';
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
        if ($di < 0) { $di = 1899; }
        if ($df == 1) { $df = date("Y")+1; }
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


        if (isset($dt['error']))
            {
                $sx = $rsp['error'] = bsmessage(
                            h("ERRO").
                            '<p>'.$dt['error']['root_cause'][0]['type']. '</p>'.
                            '<p>'.$dt['error']['root_cause'][0]['reason']. '</p>'
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
                if (isset($line['_source']['article_id'])) {
                    if (!isset($line['_source']['type']))
                        {
                        $line['_source']['type'] = 'ARTICLE';
                        }

                    if (isset($line['_source']['id_jnl'])) {
                        $jnl = $line['_source']['id_jnl'];
                    } else {
                        $jnl = 0;
                    }

                    if (isset($line['_source']['year'])) {
                        $year = $line['_source']['year'];
                    } else {
                        $year = 0;
                    }

                    array_push($rsp['works'], array(
                        'id' => $line['_source']['article_id'],
                        'score' => $line['_score'],
                        'type'=> $line['_source']['type'],
                        'jnl'=>$jnl,
                        'year'=>$year
                    ));
                }
            }
        }

        return $rsp;
    }
}
