<?php
/*
@category API
@package Brapci
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $URL/api/brapci/services
@example $URL/api/brapci/search?q=TERM&di=1972&df=2023
@example $URL/api/brapci/news #Novidades da Brapci
*/

namespace App\Models\Api\Endpoint;

use App\Models\Base\Metadata;
use CodeIgniter\Model;

class Brapci extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'finds';
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

    function index($d1, $d2, $d3)
    {

        header('Access-Control-Allow-Origin: *');
        if ((get("test") == '') and (get("header") == '')) {
            header("Content-Type: application/json");
        }

        $RSP = [];
        $RSP['status'] = '200';

        switch ($d1) {
            case 'getText':
                $Download = new \App\Models\Base\Download();
                $RSP = $Download->getText($d2);
                echo json_encode($RSP);
                exit;
                break;
            case 'timeline':
                $Sources = new \App\Models\Base\Sources();
                $RSP = $Sources->timeline($d2);
                echo json_encode($RSP);
                exit;
                break;
            case 'export':
                $Basket = new \App\Models\ElasticSearch\Index();
                $Basket->export($d2);
                exit;
                break;
            case 'setCookie':
                $dd['status'] = '200';
                if (isset($_SERVER['HTTP_COOKIE'])) {
                    $dd['cookie'] = md5($_SERVER['HTTP_COOKIE']);
                } else {
                    $dd['cookie'] = md5(date("YmdHis"));
                }
                echo json_encode($dd);
                exit;
                break;
            case 'news':
                $News = new \App\Models\Base\News();
                $RSP = $News->news($d2, $d3);
                echo json_encode($RSP);
                exit;
                break;
            case 'indexs':
                $RSP = $this->indexs($d2, $d3);
                break;
            case 'page':
                $WP = new \App\Models\WP\Index();
                $RSP = $WP->api($d2);
                break;
            case 'basket':
                $RSP = $this->basket(get("row") . get("q"));
                break;
            case 'book':
                $Book = new \App\Models\Base\Book();
                $RSP = $Book->vitrine(get("row") . get("q"));
                break;
            case 'data':
                $RSP['result'] = $this->getData($d2);
                break;
            case 'get':
                $RSP['result'] = $this->get($d2, $d3);
                break;
            case 'issue':
                $RSP = $this->issue($d2, $d3);
                break;
            case 'issueV2':
                $RSP = $this->issueV2($d2, $d3);
                break;
            case 'oai':
                $RSP = $this->oai($d2, $d3);
                break;
            case 'rdf':
                $RSP = $this->rdf($d2, $d3);
                break;
            case 'resume':
                $RSP = $this->resume();
                break;
            case 'search':
                switch ($d2) {
                    case 'a1':
                        echo "Search Advanced";
                        exit;
                        break;
                    case 'v2':
                        $RSP['strategy'] = array_merge($_POST, $_GET);
                        $RSP['result'] = $this->search();
                        exit;
                        break;
                    default:
                        $RSP['strategy'] = array_merge($_POST, $_GET);
                        $RSP['result'] = $this->search();
                        break;
                }
                break;
            case 'source':
                $RSP['source'] = $this->source($d2, $d3);
                break;
            case 'upload':
                $this->upload();
                break;
            default:
                $RSP = $this->services($RSP);
                $RSP['verb'] = $d1;
                break;
        }
        echo json_encode($RSP);
        exit;
    }

    /******************************** indexs */
    function indexs($t, $l = 'A', $lang = '')
    {
        if ($l == '') {
            $l = 'A';
        }
        $RDF = new \App\Models\RDF2\RDF();
        $RSP = [];
        switch ($t) {
            case 'subject':
                $RSP['data'] = $RDF->index_list('Subject', $l, $lang);
                break;
            case 'author':
                $RSP['data'] = $RDF->index_list('Person', $l, '');
                break;
            case 'bodycompany':
                $RSP['data'] = $RDF->index_list('BodyCompany', $l);
                break;
            default:
                $RSP['status'] = 404;
                $RSP['message'] = 'Index ' . $t . ' not found';
        }
        return $RSP;
    }
    /******************************* UPLOAD */
    function upload()
    {
        $RSP = [];
        $RSP['action'] = "POST";

        $RSP['post'] = $_POST;
        $RSP['files'] = $_FILES;
        $RSP['message'] = 'Problema no envio';

        $TechinalProceessing = new \App\Models\Books\TechinalProceessing();
        if (isset($_FILES['file'])) {
            $tmp = $_FILES['file']['tmp_name'];
            $file = $_FILES['file']['name'];
            $RSP = $TechinalProceessing->upload($file, $tmp);
            $RSP['message'] = 'Sucesso';
            //$RSP['fileID'] = 1;
        } else {
            $RSP['status'] = '500';
            $RSP['message'] = 'Erro na leitura do arquivos enviado';
            $RSP['fileId'] = 0;
        }
        return $RSP;
    }

    function rdf($d1, $d2, $d3 = '', $d4 = '', $d5 = '')
    {
        $RSP = [];
        switch ($d1) {
            case 'get':
                $RDFClass = new \App\Models\RDF2\RDFclass();
                $RSP = $RDFClass->get($d2);
                break;
            default:
                $RSP = $this->getAll($d2, $d3, $d4, $d5);
        }
        return $RSP;
    }


    /************* Default */
    function getAll()
    {
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFproperty = new \App\Models\RDF2\RDFproperty();

        $RSP = [];
        $RSP['200'] = 'Success';

        $RSP['time'] = date("Y-m-dTH:i:s");
        $Classes = $RDFclass->getClasses();
        $Property = $RDFproperty->getProperties();
        $RSP['Classes'] = $Classes;
        $RSP['Properties'] = $Property;
        return $RSP;
    }

    function resume()
    {
        $Journal = new \App\Models\Base\Sources();
        $cp = 'jnl_collection, jnl_historic';
        $dt = $Journal
            ->select($cp . ', count(*) as total')
            ->where('jnl_collection <> ""')
            ->groupBy($cp)
            ->findAll();
        $total = 0;
        $RSP = [];
        $RSP['Revistas'] = 0;
        $RSP['Revistas Estrangeiras'] = 0;
        $RSP['Revistas Históricas'] = 0;
        $RSP['Eventos Científicos'] = 0;
        $RSP['Livros'] = 0;
        $RSP['Capitulos de livros'] = 0;
        foreach ($dt as $id => $line) {
            if ($line['jnl_collection'] == 'JA') {
                $RSP['Revistas'] = $RSP['Revistas'] + $line['total'];
                if ($line['jnl_historic'] == 1) {
                    $RSP['Revistas Históricas'] = $RSP['Revistas Históricas'] + $line['total'];
                }
            }
            if ($line['jnl_collection'] == 'JE') {
                $RSP['Revistas'] = $RSP['Revistas'] + $line['total'];
                $RSP['Revistas Estrangeiras'] = $RSP['Revistas Estrangeiras'] + $line['total'];
            }
            if ($line['jnl_collection'] == 'EV') {
                $RSP['Eventos Científicos'] = $RSP['Eventos Científicos'] + $line['total'];
            }
        }
        foreach ($RSP as $id => $total) {
            if ($total == 0) {
                unset($RSP[$id]);
            }
        }
        $dd = [];
        $dd['publications'] = $RSP;
        return $dd;
    }

    function oai($verb, $issue)
    {
        $OAI = new \App\Models\Oaipmh\Index();
        $RSP = $OAI->api($verb, $issue);
        $RSP['verb'] = $verb;
        return $RSP;
    }

    function issue($issue)
    {
        $Source = new \App\Models\Base\Sources();
        $Issues = new \App\Models\Base\Issues();
        $IssuesWorks = new \App\Models\Base\IssuesWorks();

        $dt = $Issues->where('is_source_issue', $issue)->first();

        if ($dt == null) {
            echo "Vazio";
            echo '<a href="' . PATH . '/api/rdf/in/' . $issue . '">IN</a>';
            exit;
        }

        $dd = [];

        $dd['ID'] = $dt['is_source_issue'];
        $dd['source']['id_jnl'] = $dt['is_source'];
        $dd['place'] = $dt['is_place'];
        $dd['nr'] = $dt['is_vol_roman'];
        $dd['place'] = $dt['is_place'];
        $dd['works'] = $dt['is_works'];
        $dd['year'] = $dt['is_year'];
        $dd['id_jnl'] = $dt['is_source'];

        $dj = $Source->where('id_jnl', $dt['is_source'])->first();
        $dd['source']['name'] = $dj['jnl_name'];
        $dd['source']['rdf'] = $dj['jnl_frbr'];
        $dd['jnl_frbr'] = $dj['jnl_frbr'];
        $dd['acron'] = $dj['jnl_name_abrev'];

        $dt = $IssuesWorks
            ->join('brapci_elastic.dataset', 'ID = siw_work_rdf')
            ->where('siw_issue', $issue)
            ->orderBy('TITLE')
            ->findAll();

        $dw = [];
        $au = [];
        $wk = [];
        foreach ($dt as $id => $line) {
            $dq = [];
            $dq['ID'] = $line['siw_work_rdf'];
            array_push($wk, $line['siw_work_rdf']);
            $dq['LEGEND'] = $line['TITLE'];
            $dq['AUTHORS'] = $line['AUTHORS'];
            $dq['PDF'] = $line['PDF'];
            $dq['SESSION'] = $line['SESSION'];
            $dq['USE'] = $line['use'];
            $aut = troca($dq['AUTHORS'], '; ', ';');
            $aut = explode(';', $aut);
            foreach ($aut as $ida => $nome) {
                if (!isset($au[$nome])) {
                    $au[$nome] = ['name' => $nome, 'ID' => 0, 'total' => 1];
                } else {
                    $au[$nome]['total'] = $au[$nome]['total'] + 1;
                }
            }
            array_push($dw, $dq);
        }
        /******** Authors */
        ksort($au);
        $nm = [];
        foreach ($au as $name => $line) {
            array_push($nm, $line);
        }
        $dd['worksTotal'] = count($dt);
        $dd['works'] = $dw;
        $dd['worksID'] = $wk;
        $dd['authors'] = $nm;
        $dd['authorsTotal'] = count($au);
        return $dd;
    }

    function issuev2($issue)
    {
        $Source = new \App\Models\Base\Sources();
        $Issues = new \App\Models\Base\Issues();
        $IssuesWorks = new \App\Models\Base\IssuesWorks();

        $dt = $Issues->where('is_source_issue', $issue)->first();

        if ($dt == null) {
            echo "Vazio";
            echo '<a href="' . PATH . '/api/rdf/in/' . $issue . '">IN</a>';
            exit;
        }

        $dd = [];

        $dd['ID'] = $dt['is_source_issue'];
        $dd['source']['id_jnl'] = $dt['is_source'];
        $dd['place'] = $dt['is_place'];
        $dd['nr'] = $dt['is_vol_roman'];
        $dd['place'] = $dt['is_place'];
        $dd['works'] = $dt['is_works'];
        $dd['year'] = $dt['is_year'];
        $dd['id_jnl'] = $dt['is_source'];

        $dj = $Source->where('id_jnl', $dt['is_source'])->first();
        $dd['source']['name'] = $dj['jnl_name'];
        $dd['source']['rdf'] = $dj['jnl_frbr'];
        $dd['jnl_frbr'] = $dj['jnl_frbr'];
        $dd['acron'] = $dj['jnl_name_abrev'];

        $dt = $IssuesWorks
            ->join('brapci_elastic.dataset', 'ID = siw_work_rdf')
            ->where('siw_issue', $issue)
            ->orderBy('SESSION, TITLE')
            ->findAll();

        $dw = [];
        $au = [];
        $wk = [];
        $IDs = -1;
        $SESSIONx = '';
        foreach ($dt as $id => $line) {
            $SESSION = $line['SESSION'];
            if ($SESSION == '') { $SESSION = 'no_section'; }
            if ($SESSION != $SESSIONx)
                {
                    $IDs++;
                    $dw[$IDs]['name'] = $SESSION;
                    $dw[$IDs]['data'] = [];
                    $SESSIONx = $SESSION;
                }
            $dq = [];
            $dq['ID'] = $line['siw_work_rdf'];
            array_push($wk, $line['siw_work_rdf']);
            $dq['LEGEND'] = $line['TITLE'];
            $dq['AUTHORS'] = $line['AUTHORS'];
            $dq['PDF'] = $line['PDF'];
            $dq['SESSION'] = $line['SESSION'];
            $dq['USE'] = $line['use'];
            $aut = troca($dq['AUTHORS'], '; ', ';');
            $aut = explode(';', $aut);
            foreach ($aut as $ida => $nome) {
                if (!isset($au[$nome])) {
                    $au[$nome] = ['name' => $nome, 'ID' => 0, 'total' => 1];
                } else {
                    $au[$nome]['total'] = $au[$nome]['total'] + 1;
                }
            }

            array_push($dw[$IDs]['data'], $dq);
        }

        /******** Authors */
        ksort($au);
        $nm = [];
        foreach ($au as $name => $line) {
            array_push($nm, $line);
        }
        $dd['worksTotal'] = count($dt);
        $dd['works'] = $dw;
        $dd['worksID'] = $wk;
        $dd['authors'] = $nm;
        $dd['authorsTotal'] = count($au);
        return $dd;
    }

    function getSource($d1)
    {
        $Source = new \App\Models\Base\Sources();
        $dt = $Source->find($d1);

        $Cover = new \App\Models\Base\Cover();
        $dt['cover'] = $Cover->cover($dt['id_jnl']);

        $Issues = new \App\Models\Base\Issues();
        $dt['issue'] = $Issues->issuesRow($dt['id_jnl']);
        return $dt;
    }

    function source($d1, $d2)
    {
        if (sonumero($d1) == $d1) {
            return $this->getSource($d1);
        }
        $cp = 'id_jnl, jnl_name, jnl_name_abrev, jnl_issn, jnl_eissn, jnl_ano_inicio, jnl_ano_final';
        $cp .= ', jnl_active, jnl_historic, jnl_frbr, jnl_url, jnl_collection';
        $Source = new \App\Models\Base\Sources();
        if ($d1 == 'EV') {
            $d1 = 'proceddings';
        }
        if ($d1 == 'J') {
            $d1 = 'journal';
        }
        if ($d1 == 'R') {
            $d1 = 'journal';
        }
        if ($d1 == 'E') {
            $d1 = 'proceddings';
        }

        switch ($d1) {
            case 'proceddings':
                $dt = $Source->select($cp)
                    ->where('jnl_collection', 'EV')
                    ->orderBy('jnl_name')
                    ->findAll();
                break;
            case 'journal':
                $dt = $Source->select($cp)
                    ->where('jnl_collection', 'JA')
                    ->OrWhere('jnl_collection', 'JE')
                    ->orderBy('jnl_name')
                    ->findAll();
                break;
            default:
                $dt = $Source->select($cp)->orderBy('jnl_name')->findAll();
                break;
        }
        $Cover = new \App\Models\Base\Cover();

        foreach ($dt as $id => $data) {
            $dt[$id]['cover'] = $Cover->cover($data['id_jnl']);
        }
        echo json_encode($dt);
        exit;
    }

    function getData($id)
        {
            $RDF = new \App\Models\RDF2\RDF();
            $RDFclass = new \App\Models\RDF2\RDFclass();
            $RDFliteral = new \App\Models\RDF2\RDFliteral();
            $RDFdata = new \App\Models\RDF2\RDFdata();
            $dt = $RDFdata->where('id_d',$id)->first();

            $dt1 = $RDF->le($dt['d_r1']);
            $RSP = [];
            $RSP['concept'] = $dt1['concept']['n_name'] . ' (' . $dt1['concept']['c_class'] . ')';
            if ($dt['d_r2'] != 0)
                {
                    $dt2 = $RDF->le($dt['d_r2']);
                    $RSP['resource'] = $dt2['concept']['n_name'] . ' (' . $dt2['concept']['c_class'] . ')';
                } else {
                    if ($dt['d_literal'] > 0)
                    {
                        $dt2 = $RDFliteral->where('id_n', $dt['d_literal'])->first();
                        $RSP['resource'] = $dt2['n_name'] . ' (Literal)';
                    } else {
                        $RSP['resource'] = '##VAZIO##'. ' (Literal)';
                    }
                }

            $prop = $RDFclass->where('id_c',$dt['d_p'])->first();

            $RSP['Property'] = $prop['c_class'];


            return $RSP;
        }

    function get($v, $id = 0)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $Views = new \App\Models\Functions\Views();
        $Downloads = new \App\Models\Functions\Download();
        $Likes = new \App\Models\Functions\Likes();
        $RDFmetadata = new \App\Models\RDF2\RDFmetadata();

        /* Monitoramento de Visualizações */
        $Views->register($id);

        /* Le Registro do RDF */
        $dt = $RDF->le($id);

        /*********************************** Importar dados */
        if ($dt['data'] == []) {
            $RDFtools = new \App\Models\RDF2\RDFtoolsImport();
            $RDFtools->importRDF($id);
            $dt = $RDF->le($id);
        }
        switch ($v) {
            case 'v1':
                $RSP = $RDFmetadata->metadata($id);
                break;
            case 'v2':
                $RSP = $RDFmetadata->metadata($id);
                break;
            default:
                $RSP = $RDFmetadata->metadata($id);
                break;
        }

        if (!isset($dt['concept']['c_class'])) {
            $RSP['message'] = 'Register canceled';
            $RSP['status'] = '404';
        } else {

            $RSP['Class'] = $dt['concept']['c_class'];
            $RSP['Views'] = $Views->views($id);
            $RSP['Download'] = $Downloads->views($id);
            $RSP['Likes'] = $Likes->views($id);

            /************************************************ worksID */
            switch($RSP['Class'])
                {
                    case 'Issue':
                    $RSP['worksID'] = $RDFmetadata->worksID;
                    break;
                }


            /************************************************* ABNT */
            $ABNT = new \App\Models\Metadata\Abnt();
            $VANVOUVER = new \App\Models\Metadata\Vancouver();
            $APA = new \App\Models\Metadata\Apa();

            $RSP['cited']['abnt'] = $ABNT->show($RSP, substr($RSP['Class'], 0, 1));
            $RSP['cited']['vancouver'] = $VANVOUVER->show($RSP, substr($RSP['Class'], 0, 1));
            $RSP['cited']['apa'] = $APA->show($RSP, substr($RSP['Class'], 0, 1));
        }
        echo json_encode($RSP);
        exit;
    }

    function services($RSP)
    {
        $srv = [];
        $srv['livros'] = ['name' => 'Livros', 'link' => 'books', 'icone' => 'icone', 'issue' => 'issue'];
        $RSP['services'] = $srv;
        return $RSP;
    }

    function search()
    {
        $term = get("q");
        if ($term == '')
            {
                $term = get("term");
            }
        if ($term != '') {
            $Elastic = new \App\Models\ElasticSearch\Search();
            return $Elastic->searchFull($term);
        } else {
            return [];
        }
    }

    function basket($row)
    {
        $RSP['row'] = $row;
        $l = explode(',', $row);
        $Elastic = new \App\Models\ElasticSearch\Search();
        $dt = $Elastic->recoverList($l, 'abnt');
        $RSP['ABNT'] = $dt;
        return $RSP;
    }
}
