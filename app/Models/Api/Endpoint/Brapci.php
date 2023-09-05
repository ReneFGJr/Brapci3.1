<?php
/*
@category API
@package Brapci
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $URL/api/brapci/services
*/

namespace App\Models\Api\Endpoint;

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
            case 'resume':
                $RSP = $this->resume();
                break;
            case 'oai':
                $RSP = $this->oai($d2, $d3);
                break;
            case 'issue':
                $RSP = $this->issue($d2, $d3);
                break;
            case 'source':
                $RSP['source'] = $this->source($d2, $d3);
                break;
            case 'get':
                $RSP['result'] = $this->get($d2, $d3);
                break;
            case 'search':
                $RSP['strategy'] = array_merge($_POST, $_GET);
                $RSP['result'] = $this->search();
                break;
            default:
                $RSP = $this->services($RSP);
                $RSP['verb'] = $d1;
                break;
        }
        echo json_encode($RSP);
        exit;
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
                if ($line['jnl_historic']==1)
                    {
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
        foreach($RSP as $id=>$total)
            {
                if ($total == 0)
                    {
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
        $RSP['issue'] = $issue;
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
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);

        $RSP = [];
        $RSP['id'] = $id;
        $RSP['class'] = $dt['concept']['c_class'];
        $RSP['title'] = '';
        $RSP['creator_author'] = [];
        $RSP['description'] = '';
        $RSP['resource_pdf'] = '';
        $RSP['resource_url'] = '';
        $RSP['section'] = [];
        $RSP['subject'] = [];
        $RSP['cover'] = '';

        $ISSUE = new \App\Models\Base\Issues();


        $pg_ini = '';
        $pg_end = '';

        $data = [];

        //$RSP['class'] = $dt['concept']['id_cc'];

        foreach ($dt['data'] as $idx => $desc) {
            $class = trim($desc['c_class']);
            $vlr1 = $desc['n_name'];
            $vlr2 = $desc['n_name2'];

            $lk1 = $desc['d_r1'];
            $lk2 = $desc['d_r2'];

            $lang = $desc['n_lang'].$desc['n_lang2'];

            $lang = troca($desc['n_lang'] . $desc['n_lang2'], '-', '_');
            $vlr = trim($vlr1 . $vlr2);

            if ($lk2 == 0) {
                $lk2 = $lk1;
            }

            $data[msg('rdf.'.$class.'.'.$lang)] = $vlr;

            switch ($class) {
                case 'hasIssueOf':
                    $RSP['issue'] = $ISSUE->getIssue($desc['d_r1']);
                    break;
                case 'hasAbstract':
                    $RSP['description'] = $vlr;
                    break;
                case 'hasTitle':
                    $RSP['title'] = $vlr;
                    break;
                case 'hasUrl':
                    $RSP['resource_url'] = $vlr;
                    break;
                case 'hasFileStorage':
                    $RSP['resource_pdf'] = PATH . '/download/' . $id;
                    break;
                case 'hasPageStart':
                    $pg_ini = $vlr;
                    break;
                case 'hasPageEnd':
                    $pg_end = $vlr;
                    break;
                case 'publisher':
                    $RSP['publisher'] = $vlr;
                    break;
                case 'isPubishIn':
                    $journal = new \App\Models\Base\Sources();
                    $dtj = $journal->where('jnl_frbr', $lk2)->first();
                    $RSP['publisher'] = $vlr;
                    $RSP['cover'] = URL . '/_repository/cover/cover_issue_' . strzero($dtj['id_jnl'], 4) . '.jpg';
                    break;
                case 'hasAuthor':
                    $nome = nbr_author($vlr, 7);
                    array_push($RSP['creator_author'], ['name' => $nome, 'id' => $lk2]);
                    break;
                case 'hasSectionOf':
                    $nome = nbr_title($vlr);
                    array_push($RSP['section'], ['name' => $nome, 'id' => $lk2]);
                    break;
                case 'hasSubject':
                    $nome = nbr_title($vlr);
                    array_push($RSP['subject'], ['name' => $nome, 'id' => $lk2]);
                    break;
                default:
                    //echo '===>'.$class.'=='.$vlr.'<br>';
            }
        }

        if (($pg_ini . $pg_end) != '') {
            $pags = '';
            if ($pg_ini != '') {
                $pags .= $pg_ini;
            }
            if ($pg_end != '') {
                $pags .= '-' . $pg_end;
            }

            $RSP['pagination'] = $pags;
        }

        $RSP['data'] = $data;

        /************************************************* CITE */
        $dtn['title'] = $RSP['title'];
        $dtn['Authors'] = [];
        foreach($RSP['creator_author'] as $id=>$auth)
            {
                array_push($dtn['Authors'],$auth['name']);
            }
        $dtn['Journal'] = $RSP['publisher'];

        $dtn['issue']['Issue_nr'] = $RSP['issue']['nr'];
        $dtn['issue']['issue_vol'] = $RSP['issue']['vol'];
        $dtn['issue']['year'] = $RSP['issue']['year'];

        /************************************************* ABNT */
        $ABNT = new \App\Models\Metadata\Abnt();
        $VANVOUVER = new \App\Models\Metadata\Vancouver();
        $APA = new \App\Models\Metadata\Apa();
        $RSP['cited']['abnt'] = $ABNT->show($dtn,substr($RSP['class'],0,1));
        $RSP['cited']['vancouver'] = $VANVOUVER->show($dtn, substr($RSP['class'], 0, 1));
        $RSP['cited']['apa'] = $APA->show($dtn, substr($RSP['class'], 0, 1));
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
}
