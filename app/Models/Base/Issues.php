<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Issues extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_issue';
    protected $primaryKey       = 'id_is';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_is',
        'is_source',
        'is_year',
        'is_visible',

        'is_vol',
        'is_vol_roman',

        'is_card',

        'is_nr',
        'is_place',
        'is_thema',

        'is_cover',
        'is_url_oai',

        'is_works',
        'is_source_issue',
        'xx_is_issue',
        'is_oai_update',
        'is_oai_token',
    ];

    protected $typeFields    = [
        'hidden', 'sql:id_jnl:jnl_name:source_source order by jnl_name*', 'year*',
        'sn',
        'hidden', 'string', 'string',
        'string',
        '[1-199]', 'string', 'text',
        'string*', 'hidden', 'string',
        'hidden', 'string*', 'hidden',
        'hidden', 'hidden'
    ];

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
    var $path = '';
    var $path_back = '';
    var $id = 0;

    function index($act, $id)
    {
        $sx = '';
        switch ($act) {
            case 'listidentifiers':
                $jissue = get("id");
                if ($jissue != '') {
                    $sx .= $this->listidentifiers($jissue);
                } else {
                    $sx .= bsmessage('Error - No issue selected');
                }
                break;
            case 'edit':
                $jid = get("jid");
                if ($jid != 0) {
                    $_POST['is_source'] = $jid;
                }
                $sx = $this->edit($id);
                break;
            case 'harvesting':
                $id = get("id");
                if ($id > 0) {
                    $sx .= bsc($this->issue($id), 12);
                    $sx .= $this->harvesting($id);
                } else {
                    $sx .= bsmessage('ERRO: 580 - id not found', 3);
                }
                break;
            default:
                $id = get("id");
                $sx .= bsc($this->issue($id, true), 12);
                $sx .= '<hr>';
                $sx .= bsc($this->issue_section_works($id), 12);
                break;
        }
        return $sx;
    }

    function check_issues()
    {
        $sx = '';
        /************************************************* IssueProceeding */
        $RDFConcept = new \App\Models\Rdf\RDFConcept();
        $RDFData = new \App\Models\Rdf\RDFData();
        $dt = $RDFConcept->countClass('IssueProceeding');
        if ($dt['total'] > 0)
            {
                $RDFConcept->changeClass('IssueProceeding', 'Issue');
                $sx .= bsmessage('Trocado '.$dt['total'].' conceitos',3);
            } else {
                $sx .= bsmessage('Nenhuma classe identificada',1);
            }

        /************************************************* hasIssueProceeding */
        $ge = ['hasIssueOf', 'hasIssueProceeding', 'hasIssueProceedingOf'];
        foreach($ge as $prop)
            {
                $dt = $RDFData->countProp($prop);
                if ($dt['total'] > 0) {
                    $RDFData->changeProp($prop, 'hasIssue');
                    $sx .= bsmessage('Trocado ' . $dt['total'] . ' conceitos', 3);
                } else {
                    $sx .= bsmessage('Nenhuma propriedade <b>'.$prop. '</b> identificada', 1);
                }
            }

        /************************************************* Criar os Issue */
        $sx .= $this->checkIssues();

        return $sx;
    }

    function checkIssues()
        {
            $sx = '';
            $RDFConcept = new \App\Models\Rdf\RDFConcept();
            $RDF = new \App\Models\Rdf\RDF();
            $Class = 'Issue';
            $c1 = $RDF->getClass($Class);

            $dt = $RDFConcept
                ->select('id_cc, is_source_issue')
                ->join('source_issue', 'is_source_issue = id_cc','LEFT')
                ->where('cc_class',$c1)
                ->where('is_source_issue is NULL')
                ->findAll(10);

            foreach($dt as $id=>$line)
                {
                    $sx .= '<li>Processando '.$line['id_cc'].'</li>';
                    $RSP = $this->getIssue($line['id_cc']);
                }

            if (count($dt) == 0)
                {
                    $sx .= bsmessage("Nenhum Issue Encontrado para cadastro",1);
                } else {
                    $sx .= metarefresh('',2);
                }


            return $sx;
        }

        function getIssue($id)
            {
                $sx = '';
                $RDF = new \App\Models\Rdf\RDF();
                $dt = $RDF->le($id);
                if ($dt['concept']['c_class'] == 'Issue')
                    {
                        $da = $this->getDadosIssue($dt);
                        $da['is_source_issue'] = $id;
                        $this->register($da);
                        $sx .= bsmessage("REGISTRADO ISSUE - $id");
                    } else {
                        /************************** ERRO DE ISSUE */
                        $sx .= bsmessage("ERRO DE CLASSO DE ISSUE - $id");
                    }
                return $sx;
            }

        function register($dt)
            {
                $this->check($dt);
                $dt = $this
                    ->set($dt)
                    ->where('is_source_issue',$dt['is_source_issue'])
                    ->orwhere('xx_is_issue', $dt['is_source_issue'])
                    ->first();
                if ($dt == '')
                    {
                        $this->set($dt)->insert();
                    } else {
                        $this
                            ->set($dt)
                            ->where('id_is', $dt['id_is'])
                            ->update();
                    }
                return "";
            }

        function check($dt)
            {
                $ck = ['is_year', 'is_source_issue', 'is_source'];
                foreach($ck as $fld)
                    {
                        if (!isset($dt[$fld])) {
                            echo "ERRO - '$fld' não informado<br>";
                            pre($dt);
                            exit;
                        }

                    }
                return true;
            }

        function getDadosIssue($dt)
            {
                $RDF = new \App\Models\Rdf\RDF();
                $Metadata = new \App\Models\Base\Metadata();
                $prop = $dt['data'];
                $RSP = [];
                $w = [];
                foreach($prop as $id=>$line)
                    {
                        $prop = trim($line['c_class']);
                        $dd1 = $line['d_r1'];
                        $dd2 = $line['d_r2'];
                        $vv1 = $line['n_name'];
                        $vv2 = $line['n_name2'];
                        $lg1 = $line['n_lang'];
                        $lg2 = $line['n_lang2'];
                        //echo '<br>'.$prop . ' '.$dd1.' '.$dd2.' '.$vv1.' '. $vv2;
                        switch($prop)
                            {
                                case 'hasIssue':
                                    array_push($w,$dd2);
                                    array_push($w, $dd1);
                                    break;
                                case 'hasPlace':
                                    $RSP['is_place'] = $vv2;
                                    break;
                                case 'hasPublicationNumber':
                                    $RSP['is_nr'] = $vv2;
                                    break;
                                case 'hasPublicationVolume':
                                    $RSP['is_vol'] = $vv2;
                                    break;
                                case 'dateOfPublication':
                                    $RSP['is_year'] = $vv2;
                                    break;
                                case 'hasDateTime':
                                    $RSP['is_year'] = $vv2;
                                    break;
                            }
                    }

                    if ((!isset($RSP['is_source'])) and (count($w) > 0))
                        {
                            foreach($w as $idx=>$work)
                                {
                                    $da = $RDF->le($work);
                                    $dt = $Metadata->metadata($da);
                                    if (isset($dt['id_jnl']))
                                        {
                                            $RSP['is_source'] = $dt['id_jnl'];
                                            break;
                                        }
                                }
                        }
                return $RSP;
            }

    function issuesRow($id = 0)
    {
        $Sources = new \App\Models\Base\Sources();
        $ds = $Sources->find($id);

        $id = round($id);
        $dt = $this
            ->where("is_source", $id)
            ->where('is_visible',1)
            ->orderBy('is_year', 'DESC')
            ->orderBy('is_vol', 'DESC')
            ->orderBy('is_source_issue', 'DESC')
            ->findAll();
        return $dt;
    }

    /* Legado */

    function issue($id, $tool = false)
    {
        $dt = $this
            ->join('source_source', 'is_source = id_jnl')
            ->where('id_is', round($id))
            ->findAll();
        $sx = '';

        if (count($dt) == 0) {
            return "ERRO";
        } else {
            $dt = $dt[0];
        }

        if ($dt['is_source_issue'] == 0) {
            $sx .= '/************************************* Buscando RDF Issue */<br>';
            $sx .= 'Criar ISSUE RDF';
            $this->RDFIssue($dt);
            exit;
        }

        $sx .= $this->header_issue($dt);
        if ($tool) {
            $sx .= $this->tools($dt);
        }
        return $sx;
    }

    function issues($id = 0)
    {
        $Sources = new \App\Models\Base\Sources();
        $ds = $Sources->find($id);

        $id = round($id);
        $dt = $this->where("is_source", $id)
            ->orderBy('is_year', 'DESC')
            ->orderBy('is_vol', 'DESC')
            ->orderBy('is_source_issue', 'DESC')
            ->findAll();

        $sx = '';
        $sx .= $Sources->journal_header($ds);

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];

            $link = PATH . COLLECTION . 'issue/edit/' . $line['id_is'];
            $edit = '<a href="' . $link . '">';
            $edit = bsicone('edit');
            $edit .= '</a>';
            $sa = '';
            $sa .= bsc($line['is_year'], 2);
            $sa .= bsc($line['is_vol'], 1);
            $sa .= bsc($line['is_place'], 2);
            $sa .= bsc($line['is_thema'], 5);
            $sa .= bsc($edit);
            $sx .= bs($sa);
        }

        $sx .= $this->btn_new_issue($id);
        return $sx;
    }

    function header_issue($dt)
    {
        $dir = '.tmp';
        dircheck($dir);
        $dir = '.tmp/issues/';
        dircheck($dir);
        $file = strzero($dt['id_jnl'], 4) . '-' . strzero($dt['is_source_issue'], 6) . '.name';
        if (file_exists($dir . $file) and (get("reindex") == '')) {
            $sx = file_get_contents($dir . $file);
            //return $sx;
        } else {
            /************************************ Mount Header */
            $sx = '';
            $vol = $dt['is_vol'];
            $roman = trim($dt['is_vol_roman']);

            $dt['roman'] = '';
            if (strlen($roman) > 0) {
                $vol .= ' (' . $roman . ')';
                $dt['roman'] = $roman;
            }
            $link = '<a href="' . PATH . COLLECTION . '/source/' . $dt['id_jnl'] . '" target="_new">';
            $linka = '</a>';

            $dt['volume'] = $vol;

            $img1 = 'img/headers/journals/image_' . strzero($dt['is_source'], 6) . '.png';
            $img2 = 'img/headers/issue/image_' . strzero($dt['id_is'], 6) . '.png';

            if (!file_exists($img1)) {
                $img1 = 'img/headers/journals/image_' . strzero(0, 6) . '.png';
            }
            if (!file_exists($img2)) {
                $img2 = 'img/headers/issue/image_' . strzero(0, 6) . '.png';
            }
            $dt['img1'] = $img1;
            $dt['img2'] = $img2;

            /******************** IMAGES */
            if (!file_exists($img1)) {
                $img1 = 'img/headers/journals/image_' . strzero(0, 6) . '.png';
            }

            if (!file_exists($img2)) {
                $img2 = 'img/headers/issue/image_' . strzero(0, 6) . '.png';
            }

            $sx .= view('Brapci/Base/header_proceedings.php', $dt);

            /**************************** */
            $sx = bs($sx);
            $id_issue_rdf = $dt['is_source_issue'];

            file_put_contents($dir . $file, $sx);
        }
        $tools = '';

        /************************************* HARVESTING */
        if (get("reindex") != '') {
            $IssuesWorks = new \App\Models\Base\IssuesWorks();
            $sx .= $IssuesWorks->check($dt);
        }
        return $sx;
    }

    function tools($dt)
    {
        $Socials = new \App\Models\Socials();
        $sx = '';
        if ($Socials->getAccess("#CAR#ADM#EVE")) {
            $tools = '';
            $OAI = new \App\Models\Oaipmh\Index();
            if (trim($dt['is_url_oai']) != '') {
                $tot = $OAI->to_harvesting(0, $dt['id_is']);
                if ($tot > 0) {
                    $class = 'class = "blink" ';
                    $tools .= anchor(PATH . '/' . COLLECTION . '/oai/' . $dt['id_is'] . '/getrecords', bsicone('circle-1', 32), 'title="Harvesing (' . $tot . ')" ' . $class);
                } else {
                    $class = '';
                    $tools .= anchor(PATH . '/' . COLLECTION . '/issue/harvesting/?id=' . $dt['id_is'], bsicone('harvesting', 32), 'title="Processing ' . $tot . ' registers"' . $class);
                }

                $tools .= '<span class="p-2"></span>';
                $tools .= anchor(PATH . '/' . COLLECTION . '/issue/listidentifiers/?id=' . $dt['id_is'], bsicone('gear', 32), 'title="Check"');
                $tools .= '<span class="p-2"></span>';
            }
            $tools .= anchor(PATH . '/' . COLLECTION . '/issue/?id=' . $dt['id_is'] . '&reindex=1', bsicone('reload', 32), 'title="Reindex"');
            $tools .= '<span class="p-2"></span>';
            $tools .= anchor(PATH . '/' . COLLECTION . '/issue/edit/' . $dt['id_is'] . '', bsicone('edit', 32), 'title="Edit"');
            $tools .= '<span class="p-2"></span>';

            $oai_tools = bs(
                bsc($OAI->logo(), 2) .
                    bsc($OAI->resume(0, $dt['id_is']), 10)
            );
            $dt['tools'] = bs(bsc($tools, 12)) . $oai_tools;
            $sx .= view('Brapci/Base/header_proceedings_tools.php', $dt);
        }
        return $sx;
    }

    function issue_section_works($id)
    {
        /* Recupera ID RDF */
        $dt = $this->find($id);
        if ($dt == '') {
            return "";
        }
        $id_rdf = $dt['is_source_issue'];

        if (get("reindex") != '') {
            $IssuesWorks = new \App\Models\Base\IssuesWorks();
            $IssuesWorks->check($dt);
        }

        /* Selected */
        $Work = new \App\Models\Base\Work();
        $sels = $Work->WorkSelected();
        $sx = '';
        $sx .= bs(bsc('<div id="result" class="border border-secondary rounded" style="padding: 0px 5px; background-color: #EEE;">' . $sels . '</div>', 12));

        /* Recupera works */
        $IssuesWorks = new \App\Models\Base\IssuesWorks();
        $sx .= $IssuesWorks->show_issue_works($id_rdf);
        return $sx;
    }

    function PainelAdmin($idj)
    {
        $sx = '';
        $sx .= anchor(URL . COLLECTION . '/issue/edit/' . $idj . '?jid=' . $idj, bsicone('plus'));
        return $sx;
    }

    function show_list_cards($id, $default_img = URL . 'img/issue/issue_00000.png')
    {
        $sx = '';
        $Social = new \App\Models\Socials();
        if ($Social->getAccess("#ADM")) {
            $sx .= '<div class="container"><div class="row">';
            $link = '<a href="' . PATH . COLLECTION . '/issues/' . $id . '" class="btn btn-primary">' . bsicone('plus') . '</a>';
            $sx .= bsc($link);
            $sx .= '</div></div>';
        }
        $dt = $this
            ->where('is_source', $id)
            ->where('is_visible', 1)
            ->orderBy('is_year desc, is_nr desc')
            ->findAll();

        $sx .= '<div class="container"><div class="row">';
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $img = $line['is_card'];
            if (strlen($img) == 0) {
                $img = 'img/issue/issue_' . strzero($line['is_source'], 5) . '.png';
            }

            $link = PATH . COLLECTION . '/issue?id=' . $line['id_is'];
            if (!file_exists($img)) {
                $img = $default_img;
            }

            $sx .= '
                    <div class="card  m-3" style="width: 18rem; cursor: pointer;" onclick="location.href = \'' . $link . '\';">
                    <img src="' . URL . '/' . $img . '" class="card-img-top" alt="...">
                    <span class="position-absolute top-0 start-0" style="padding: 0px; margin: 0px; font-size: 350%; color: #666;"><b>' . $line['is_vol_roman'] . '</b></span>
                    <div class="card-body">
                        <h5 class="card-title">' . $line['is_year'] . ' - ' . $line['is_place'] . '</h5>
                        <!---
                        <p class="card-text">' . $line['is_thema'] . '</p>
                        --->
                    </div>
                    </div>
                    ';
        }
        $sx .= '</div></div>';
        return $sx;
    }

    function edit($id)
    {
        $this->id = $id;
        $this->path = URL . '/' . COLLECTION . '/issue/';
        if ($id > 0) {
            $this->path_back = URL . '/' . COLLECTION . '/issue/?id=' . $id;
        } else {
            $this->path_back = URL . '/' . COLLECTION . '/source/' . get("is_source");
        }

        $sx = form($this);
        $sx = bs(bsc($sx, 12));
        return $sx;
    }

}
