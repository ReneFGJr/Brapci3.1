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
        $RSP = [];
        $RSP['status'] = '200';
        switch ($d1) {
            case 'basket':
                $RSP = $this->basket(get("row") . get("q"));
                break;
            case 'book':
                $Book = new \App\Models\Base\Book();
                $RSP = $Book->vitrine(get("row") . get("q"));
                break;
            case 'get':
                $RSP['result'] = $this->get($d2, $d3);
                break;
            case 'issue':
                $RSP = $this->issue($d2, $d3);
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
                $RSP['strategy'] = array_merge($_POST, $_GET);
                $RSP['result'] = $this->search();
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
            $RSP['fileID'] = 1;
        } else {
            $RSP['status'] = '500';
            $RSP['message'] = 'Erro na leitura do arquivos enviado';
            $RSP['fileId'] = 0;
        }
        echo json_encode($RSP);
        exit;
    }

    function rdf($d1, $d2 ,$d3='',$d4='',$d5='')
    {
        $RSP = [];
        switch ($d1) {
            case 'get':
                $RDFClass = new \App\Models\RDF2\RDFclass();
                $RSP = $RDFClass->get($d2);
                break;
            default:
                $RSP = $this->getAll($d2,$d3,$d4,$d5);
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
        $Issues = new \App\Models\Base\Issues();
        $IssuesWorks = new \App\Models\Base\IssuesWorks();

        $dt = $Issues->find($issue);

        $RSP = $this->getSource($dt['is_source']);
        $RSP['issue'] = $dt;

        $ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
        $RSP['oai'] = $ListIdentifiers->summary($dt['is_source'], $issue);
        $RSP['works'] = $IssuesWorks->getWorks($dt['id_is']);

        return $RSP;
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

    function get($v, $id = 0)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFmetadata = new \App\Models\RDF2\RDFmetadata();
        $dt = $RDF->le($id);

        $RSP = $RDFmetadata->metadata($id);
        $RSP['Class'] = $dt['concept']['c_class'];

        /************************************************* ABNT */
        $ABNT = new \App\Models\Metadata\Abnt();
        $VANVOUVER = new \App\Models\Metadata\Vancouver();
        $APA = new \App\Models\Metadata\Apa();
        $RSP['cited']['abnt'] = $ABNT->show($dd, substr($RSP['class'], 0, 1));
        $RSP['cited']['vancouver'] = $VANVOUVER->show($dd, substr($RSP['class'], 0, 1));
        $RSP['cited']['apa'] = $APA->show($dd, substr($RSP['class'], 0, 1));
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
        $dt = $Elastic->recoverList($l);

        $ARTI = [];
        $EVEN = [];
        $BOOK = [];
        $CAPT = [];

        foreach ($dt as $id => $line) {
            $ABNT = new \App\Models\Metadata\Abnt();
            $type = $line['type'];
            $ln = [];
            $ln['title'] = $line['ldl_title'];
            $ln['Authors'] = explode(';', $line['ldl_authors']);
            $ln['jnl_name'] = $line['ldl_legend'];
            $ln['is_year'] = $line['year'];
            $ln['legend'] = $line['ldl_legend'];
            $ln['ID'] = $line['article_id'];

            switch ($type) {
                case 'Proceeding':
                    $ref = $ABNT->show($ln, 'E');
                    array_push($ARTI, $ref);
                    break;
                case 'Article':
                    $ref = $ABNT->show($ln, 'A');
                    array_push($ARTI, $ref);
                    break;
                default:
                    $ref = $ABNT->show($ln, 'A');
                    array_push($ARTI, $ref);
                    break;
            }
        }

        sort($ARTI);

        $RSP['ABNT']['Article'] = $ARTI;
        $RSP['work'] = $dt;
        return $RSP;
    }
}
