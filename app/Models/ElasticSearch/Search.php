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
        $SearchDB = new \App\Models\Functions\Search();

        $dt = $this->search($q, $type);

        $cp = 'ID, id_jnl, jnl_name as JOURNAL, ISSUE, CLASS,
                        SESSION, LEGEND, TITLE, AUTHORS, COVER as cover';


        if (!isset($dt['works'])) {
            $dt['status'] = '500';
            $dt['messagem'] = 'Erro de acesso ao servidor de busca';
            echo (json_encode($dt));
            exit;
        }

        /***************** Grava convulta */
        $SearchDB->register($q, count($dt['works']),$type);

        /* Retorno */
        $n = 0;
        $Search->select($cp);
        $Search->join('brapci.source_source', 'JOURNAL = id_jnl', 'LEFT');
        foreach ($dt['works'] as $id => $line) {
            $ida = $line['id'];
            if ($n == 0) {
                $Search->where('ID', $ida);
            } else {
                $Search->Orwhere('ID', $ida);
            }
            $n++;
        }

        $ds = $Search->findAll();

        /********************* Organiza Array Por ID */
        $dsr = [];
        foreach ($ds as $id => $line) {
            $IDt = $line['ID'];
            $dsr[$IDt] = $line;
        }

        /******************** Completa recuperação com as ID */
        foreach ($dt['works'] as $idx => $line) {
            $idt = $line['id'];
            if (isset($dsr[$idt])) {
                $dt['works'][$idx]['data'] = $dsr[$idt];
            }
        }

        if (!isset($dt['works'])) {
            $dt['works'] = [];
        }
        echo (json_encode($dt));
        exit;
    }

    function recoverList($ids, $tp = "abnt")
    {
        $abnt = new \App\Models\Metadata\Abnt();

//        $ids = ['44753', '66834', '129525', '103527', '232822', '207369', '201576', '198429', '199459', '158229', '191669', '199474', '182143', '192377', '183471', '148646', '68600', '73005', '148533', '150006', '137762', '194448', '41471', '239382', '148937', '191894', '199529', '45298', '185071', '239429', '123402', '149044', '176510', '184011', '243169', '103816', '123091', '91338', '198347', '243463', '243332', '114738', '33017', '129496', '139913', '149765', '216239', '197946', '242864', '243289', '238061', '184002', '185084', '12794', '112498', '193505', '14734', '241180', '127650', '138080', '222457', '102369', '156962', '224989', '141371', '223909', '148648', '122077', '245795', '197375', '105324', '105548', '122900', '223838', '15931', '149212', '113706', '192816', '157125', '222541', '184972', '91689', '229171', '193331', '193747', '69208', '13487', '14586', '247906', '232841', '158672', '151632', '194198', '185077', '243487', '33878', '223822', '41859', '226185', '103551'];

        $cp = 'ID as article_id, json, CLASS as type, YEAR as year';
        $this->select($cp);
        $this->where('ID', $ids[0]);
        for ($r = 1; $r < count($ids); $r++) {
            $this->Orwhere('ID', $ids[$r]);
        }
        $dts = $this->findAll();

        $dr = [];
        $ARTI = [];
        $BOOK = [];
        $CHAP = [];
        $PROC = [];
        foreach ($dts as $id => $line) {

            $js = (array)json_decode($line['json']);
            switch($tp)
                {
                    default:
                    if (!isset($js['Authors']))
                        {
                            $js['Authors'] = [];
                        }
                    $ds =  $abnt->ref($js);
                }

            $Class  = $js['Class'];
            if ($Class == 'Article') {
                array_push($ARTI, $ds);
            }
            if ($Class == 'Proceeding') {
                array_push($PROC, $ds);
            }
            if ($Class == 'Book') {
                array_push($BOOK, $ds);
            }
            if ($Class == 'BookChapter') {
                array_push($CHAP, $ds);
            }
        }
        sort($ARTI);
        sort($PROC);
        sort($BOOK);
        sort($CHAP);
        $dr['Articles'] = $ARTI;
        $dr['Proceedings'] = $PROC;
        $dr['Books'] = $BOOK;
        $dr['BooksChapter'] = $CHAP;
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
        /************** Trata Termos */
        $aspas = false;
        $qp = '';
        $q = ascii($q);
        $q = mb_strtolower($q);

        /*********** REMOVE O AND */
        $q = trim(troca(' '.$q.' ',' and ',' '));

        for ($r=0;$r < strlen($q);$r++)
            {
                $c = substr($q,$r,1);
                if ($c == '"')
                    {
                        $aspas = !$aspas;
                    }
                else {
                    if (($c == ' ') and ($aspas == true))
                        {
                            $c = '_';
                        }
                    $qp .= $c;
                }
            }

        /******************** Fields */
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

        $wd = explode(' ',$qp);
        foreach($wd as $id=>$word)
            {
                $word = troca($word,'_',' ');
                //$strategy['must'][$id]['match_phrase']['full'] = ascii($word);
                $strategy['must'][$id]['match_phrase'][$field] = ascii($word);
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

        /*************************** */
        $rsp['words'] = $wd;


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

            /*************************************** Grava Consulta */
            $SearchREG = new \App\Models\Functions\Search();


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
