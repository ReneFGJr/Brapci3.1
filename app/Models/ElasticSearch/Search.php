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

    function search($q='')
        {
            $start = round(get('start'));
            $offset = round(get('offset'));

            if ($start <= 0) { $start = 0; }
            if ($offset < 1) { $offset = 10; }

            $API = new \App\Models\ElasticSearch\API();

            $qs = get("search");
 
            /*************** SOURCE **********************************************************/
            $method = "POST";

            /***************************************************************** MULTI MATCH **/
            $data = array();
            $data['query']['bool']['must'] = array();
            
            $query['multi_match']['query'] = $qs;
            $query['multi_match']['fields'] = array("title","abstract","subject","year"); 

            $range['range']['year']['gte'] = 2021;
            $range['range']['year']['lte'] = 2021;

            array_push($data['query']['bool']['must'],$query);
            array_push($data['query']['bool']['must'],$range);

            /******************** Sources */
            $data['_source'] = array("article_id","id_jnl","title","abstract","subject","year");

            /******************** Limites */
            $data['size'] = $offset;
            $data['from'] = $start;
            
            $sx =  $q;
            $url = 'brp2/_search';

            //echo '<pre>';
            //print_r($data);
            $dt = $API->call($url,$method,$data);

            $rsp = array();
            $total = 0;
            if (isset($dt['hits']))
                {
                    $rsp['total'] = $dt['hits']['total']['value'];
                    $rsp['start'] = $start+1;
                    $rsp['offset'] = $offset;
                    $rsp['works'] = array();
                    $hits = $dt['hits']['hits'];
                    for ($r=0;$r < count($hits);$r++)
                        {
                            $line = $hits[$r];
                            array_push($rsp['works'],array(
                                'id'=>$line['_source']['article_id'],
                                'score'=>$line['_score']
                                ));
                        }
                }

            return $rsp;            
        }
}