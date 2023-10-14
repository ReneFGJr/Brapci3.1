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

        'is_issue',
        'is_vol',
        'is_vol_roman',

        'is_card',

        'is_nr',
        'is_place',
        'is_thema',

        'is_source_rdf',
        'is_cover',
        'is_url_oai',

        'is_works',
        'is_source_issue',
        'is_oai_update',
        'is_oai_token',
    ];

    protected $typeFields    = [
        'hidden', 'sql:id_jnl:jnl_name:source_source order by jnl_name*', 'year*',
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
        $RDF = new \App\Models\Rdf\RDF();
        $RDFdata = new \App\Models\Rdf\RDFData();
        $RDFconcept = new \App\Models\Rdf\RDFConcept();
        $class = 'Issue';
        $idc = $RDF->getClass($class, false);

        $cp = 'id_cc, cc_class, cc_use, id_is, is_year, id_jnl';
        //$cp = '*';
        $dtd = $RDFconcept
            ->select($cp)
            ->join('brapci.source_issue', 'is_issue = id_cc', 'LEFT')
            ->where('cc_class', $idc)
            ->where('id_is is null')
            ->groupby($cp)
            ->findAll();

        /************************ */
        $sx .= '<ol>';
        for ($rz = 0; $rz < count($dtd); $rz++) {
            $line = $dtd[$rz];
            $idissue = $line['id_cc'];
            $dt = $this->getIssue($idissue);

            $da['is_source'] = $dt['id_jnl'];
            $da['is_source_rdf'] = 0;
            $da['is_source_issue'] = '';
            $da['is_year'] = $dt['year'];
            $da['is_issue'] = $line['id_cc'];
            $da['is_source_issue'] = $line['id_cc'];
            $da['is_vol'] = $dt['vol'];
            $da['is_vol_roman'] = '';
            $da['is_nr'] = $dt['vol'];
            $da['is_place'] = '';
            $da['is_edition'] = '';
            $da['is_cover'] = 0;
            $da['is_card'] = 0;
            $da['is_url_oai'] = 0;
            $da['is_oai_token'] = '';
            $da['is_oai_update'] = date("Y-m-d H:i:s");

            $di = $this->where('is_issue', $line['id_cc'])->first();
            if ($di == '') {
                $this->set($da)->insert();
                $sx .= '<li>' . $line['id_cc'] . '=>' . $da['is_year'] . $da['is_vol'] . $da['is_nr'] . ' Insered</li>';
            }
        }
        $sx .= '</ol>';
        return $sx;
    }

    function register_issue($da)
    {
        if (isset($da['ISSUE']))
            {
                $Source = new \App\Models\Base\Sources();
                $dj = $Source->where('id_jnl',$da['JOURNAL'])->first();
                if ($dj == '')
                    {
                        echo "<hr>ERRO DE JOURNAL #20231014<hr>";
                        pre($da);
                    }
                $da['is_source'] = $da['JOURNAL'];
                $da['is_source_rdf'] = $dj['jnl_frbr'];
                $da['is_source_issue'] = $da['ISSUE'];
                $da['is_year']  = $da['year'];
                $da['is_issue'] = $da['ISSUE'];
                $da['is_vol'] = $da['vol'];
                $da['is_vol_roman']  = '';
                $da['is_nr'] = $da['nr'];
                $da['is_place'] = '';
                $da['is_edition'] = '';
                $da['is_thema'] = '';
                $da['is_cover'] = '';
                $da['is_card'] = '';
                $da['is_url_oai'] = '';
                $da['is_oai_token'] = '';
                $da['is_works'] = 0;
                $da['is_card'] = '';
            }

        $da['is_oai_update'] = date("Y-m-d H:i:s");
        $dt = $this->where('is_issue', $da['is_source_issue'])->first();
        if ($dt == '') {
            $this->set($da)->insert();
        } else {
            echo "<br>ISSUE UPDATE?";
        }
    }

    function v($dt)
    {
        $sx = '';
        $RDF = new \App\Models\Rdf\RDF();
        $RDFdata = new \App\Models\Rdf\RDFData();
        $sx .= bs(bsc(h('Class: ' . $dt['concept']['c_class'], 4), 12));

        $sx .= $RDFdata->view_data($dt);
        return $sx;
    }

    function getIssue($id_issue)
    {

        /************************************** GET ISSUE */
        $RDF = new \App\Models\Rdf\RDF();
        $Source = new \App\Models\Base\Sources();

        $dt = $RDF->le($id_issue);
        $RSP['year'] = '';
        $RSP['nr'] = '';
        $RSP['vol'] = '';
        $RSP['ISSUE'] = $id_issue;
        $RSP['JOURNAL'] = -1;
        $RSP['JOURNAL_RDF'] = -1;
        $pref = '';
        $works = [];
        $worksJ = [];

        foreach ($dt['data'] as $id => $line) {
            $class = trim($line['c_class']);
            $vlr1 = $line['n_name'];
            $vlr2 = $line['n_name2'];
            switch ($class) {
                case 'prefLabel':
                    $pref = $line['n_name'];
                    break;
                case 'hasIssueOf':
                    array_push($works, $line['d_r1']);
                    array_push($worksJ, $line['d_r2']);
                    break;
                case 'hasIssue':
                    $RSP['is_source_rdf'] = $line['d_r2'];
                    $ds = $Source->where('jnl_frbr', $line['d_r2'])->first();
                    if ($ds != '') {
                        $RSP['id_jnl'] = $ds['id_jnl'];
                    }
                    break;
                case 'dateOfPublication':
                    $RSP['year'] = $vlr2;
                    break;
                case 'hasPublicationNumber':
                    $RSP['nr'] = $vlr2;
                    break;
                case 'hasPublicationVolume':
                    $RSP['vol'] = $vlr2;
                    break;
                default:
                    //echo $class . ': ' . $vlr1 . ' | ' . $vlr2 . '<br>';
                    break;
            }
        }
        if ($RSP['year'] == '') {
            $RSP['year'] = 9996;
        }

        /************************************** RECUPERA JOURNAL */
        if ($RSP['JOURNAL']==-1) {
            $dar = $RDF->le($works[0]);
            $jnl = $RDF->extract($dar, 'isPubishIn');

            if (isset($jnl[0])) {
                $dj = $RDF->le($jnl[0]);
                if (isset($dj['concept'])) {
                    if ($dj['concept']['c_class'] == 'Journal') {
                        $jnl = $dj['concept']['id_cc'];
                        $dj = $Source->where('jnl_frbr',$jnl)->first();
                        $RSP['JOURNAL'] = $dj['id_jnl'];
                        $RSP['JOURNAL_RDF'] = $jnl;
                    } else {
                        echo "=======================";
                        pre($dar);
                        if (count($jnl) > 0) {
                            $Source = new \App\Models\Base\Sources();
                            $idj = $jnl[0][1];
                            $ln = $Source->where('jnl_frbr', $idj)->first();
                        }
                    }
                }
            } else {
                $jn = $dar['concept']['n_name'];
                if (substr($jn,0,9) == 'ISSUE:JNL')
                    {
                        $jn1 = substr($jn,0,9);
                        $jn2 = substr($jn, 10, 5);
                        $jn3 = substr($jn, 15, 1);

                        if ($jn3 == '-')
                            {
                                $RDF = new \App\Models\Rdf\RDF();
                                $Class = 'isPubishIn';
                                $prop = $RDF->getClass($Class);

                                $Source = new \App\Models\Base\Sources();
                                $dj = $Source->where('id_jnl',round($jn2))->first();
                                $RSP['JOURNAL'] = $dj['id_jnl'];
                                $RSP['JOURNAL_RDF'] = $dj['jnl_frbr'];

                                foreach($worksJ as $id=>$art)
                                    {
                                        $RDF->propriety($RSP['JOURNAL_RDF'], $prop, $art);
                                    }
                            } else {
                                $RSP['id_jnl'] = 9990;
                            }
                    }
            }
        }

/********************************************************************* */
        $dt = [];
        $da['is_source'] = $RSP['JOURNAL'];
        $da['is_source_rdf'] = $RSP['JOURNAL_RDF'];
        $da['is_year'] = $RSP['year'];
        $da['is_issue'] = $id;
        $da['is_source_issue'] = $id_issue;
        $da['is_vol'] = $RSP['vol'];
        $da['is_vol_roman'] = '';
        $da['is_nr'] = $RSP['nr'];
        $da['is_place'] = '';
        $da['is_edition'] = '';
        $da['is_cover'] = 0;
        $da['is_card'] = 0;
        $da['is_url_oai'] = 0;
        $da['is_oai_token'] = '';
        $da['is_oai_update'] = date("Y-m-d H:i:s");

        $ds = $this->where('is_source_issue', $id_issue)->first();
        if ($ds == '') {
            echo "<hr>*NOVO*<hr>";
            $this->register_issue($da);
        } else {

        }
        //echo "=================OKKK===============<hr>";
        //pre($RSP);
        return $RSP;
    }

    function checkData()
        {
            $RDFdata = new \App\Models\Rdf\RDFData();
            $RDFdata->check_issue();
        }


    function check($jnl, $auto = false)
    {
        $Source = new \App\Models\Base\Sources();
        if ($jnl == 0) {
            $dj = $Source->orderBy('jnl_frbr')->first();
            $jnl = $dj['jnl_frbr'];
        } else {
            $dj = $Source->where('jnl_frbr', $jnl)->first();
        }
        $Source = new \App\Models\Base\Sources();
        $dj = $Source->where('jnl_frbr', $jnl)->first();

        $sx = $Source->journal_header($dj);

        $Issues = new \App\Models\Base\Issues();
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($jnl);

        $data = $dt['data'];

        $sx .= '<ul>';
        foreach ($data as $id => $line) {
            $class = $line['c_class'];

            switch ($class) {
                case 'hasIssue':
                    $RDF->c($line['d_r1']);
                    $dir = $RDF->directory($line['d_r1']);
                    $file = $dir . 'issue.json';
                    if (file_exists($file)) {
                        $json = file_get_contents($file);
                        $json = (array)json_decode($json);
                        /************* Year */
                        if (isset($json['vol'])) {
                            $year = (array)$json['year'];
                            $year = $year['name'];
                        } else {
                            $year = 0;
                        }

                        /************* VOL */
                        if (isset($json['vol'])) {
                            $vol = (array)$json['vol'];
                            $vol = $vol['name'];
                        } else {
                            $vol = '';
                        }

                        /************* NR */
                        if (isset($json['nr'])) {
                            $nr = (array)$json['nr'];
                            $nr = $nr['name'];
                        } else {
                            $nr = '';
                        }

                        $da['is_source'] = $dj['id_jnl'];
                        $da['is_source_rdf'] = $dj['jnl_frbr'];
                        $da['is_source_issue'] = '';
                        $da['is_year'] = $year;
                        $da['is_issue'] = $line['d_r1'];
                        $da['is_vol'] = trim(troca($vol, 'v.', ''));
                        $da['is_vol_roman'] = '';
                        $da['is_nr'] = trim(troca($nr, 'n.', ''));
                        $da['is_place'] = '';
                        $da['is_edition'] = '';
                        $da['is_cover'] = 0;
                        $da['is_card'] = 0;
                        $da['is_url_oai'] = 0;
                        $da['is_oai_token'] = '';
                        $da['is_oai_update'] = $dj['jnl_oai_last_harvesting'];

                        $dr = $this
                            ->where('is_source_rdf', $dj['jnl_frbr'])
                            ->where('is_issue', $line['d_r1'])
                            ->findAll();

                        $sx .= '<li>';
                        $sx .= 'v. ' . $da['is_vol'] . ', n. ' . $da['is_nr'] . ', ' . $da['is_year'];
                        if (count($dr) == 0) {
                            $this->set($da)->insert();
                            $sx .= ' - inserted';
                        } else {
                            $sx .= ' - updated';
                        }
                        $sx .= '</li>';
                    } else {
                        $sx .= bsmessage("ERRO " . $file, 3);
                    }


                    break;
            }
        }
        $sx .= '</ul>';
        $sx .= "Checked";
        $sx = bs(bsc($sx));

        if ($auto != '') {
            $dj = $Source
                ->where('jnl_frbr > ' . $jnl)
                ->orderBy('jnl_frbr')
                ->first();

            if ($dj != '') {
                $sx .= metarefresh(PATH . '/journals/check/' . $dj['jnl_frbr'] . '/auto', 2);
            } else {
                $sx .= 'FIM do processo';
            }
        }

        return $sx;
    }

    function le($id)
    {
        $dt = $this
            ->join('source_source', 'id_jnl = is_source')
            ->where('is_source_issue', $id)
            ->findAll();
        if (count($dt) > 0) {
            return $dt[0];
        }
        return (array());
    }

    function issuesRow($id = 0)
    {
        $Sources = new \App\Models\Base\Sources();
        $ds = $Sources->find($id);

        $id = round($id);
        $dt = $this->where("is_source", $id)
            ->orderBy('is_year', 'DESC')
            ->orderBy('is_vol', 'DESC')
            ->orderBy('is_issue', 'DESC')
            ->findAll();
        return $dt;
    }

    function issues($id = 0)
    {
        $Sources = new \App\Models\Base\Sources();
        $ds = $Sources->find($id);

        $id = round($id);
        $dt = $this->where("is_source", $id)
            ->orderBy('is_year', 'DESC')
            ->orderBy('is_vol', 'DESC')
            ->orderBy('is_issue', 'DESC')
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

    function btn_new_issue($id)
    {
        $sx = '';
        $sx .= '<a href="' . base_url(PATH . COLLECTION . '/issues/edit/0?jid=' . $id) . '" class="btn btn-primary">';
        $sx .= msg('new_issue');
        $sx .= '</a>';
        return $sx;
    }
    function listidentifiers($id)
    {
        $OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
        $dt = $OAI_ListIdentifiers
            ->where('li_issue', $id)
            ->orderBy('li_setSpec, li_identifier', 'DESC')
            ->findAll();

        $sx = h('OAI - ListIdentifiers', 3);
        $sx .= '<p>' . $this->getlastquery() . '</p>';
        $sx .= '<p>Total de ' . count($dt) . ' registers.</p>';
        $sx .= '<ul>';
        $xsetSpec = '';
        $tot = 0;
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $setSpec = $line['li_setSpec'];
            if ($setSpec != $xsetSpec) {
                if ($tot > 0) {
                    $sx .= '<p>Total ' . $tot . '</p>';
                }
                $sx .= h($setSpec, 4) . cr();
                $xsetSpec = $setSpec;
                $tot = 0;
            }
            $sx .= '<li>' . $OAI_ListIdentifiers->row($line) . '</li>' . cr();
            $tot = $tot + 1;
        }
        if ($tot > 0) {
            $sx .= '<p>Total ' . $tot . '</p>';
        }
        $sx .= '</ul>';
        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    function harvesting($id)
    {
        $OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();

        $dt = $this->find($id);
        $sx = '';
        $sx .= $OAI_ListIdentifiers->harvesting_issue($dt);
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
            ->orderBy('is_year desc, is_nr desc')
            ->findAll();

        $sx .= '<div class="container"><div class="row">';
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $sa = '';
            //$sa .= bsc($line['is_year'],2);
            //$sa .= bsc($line['is_vol'],2);
            //$sa .= bsc($line['is_place'],2);
            //$sa .= bsc($line['is_thema'],6);
            //$sx .= bs($sa);

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

    function PainelAdmin($idj)
    {
        $sx = '';
        $sx .= anchor(URL . COLLECTION . '/issue/edit/' . $idj . '?jid=' . $idj, bsicone('plus'));
        return $sx;
    }

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

    function RDFIssue($dt)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $class = 'IssueProceeding';
        $prefLabel = trim($dt['is_vol_roman']) . ' ' . trim($dt['jnl_name']) . ', ' . trim($dt['is_year']);
        $id_issue = $RDF->concept($prefLabel, $class);

        /************************************************************* Vincula a fontes principal */
        $RDF->propriety($dt['jnl_frbr'], 'hasIssue', $id_issue);

        /***** Data */
        if (strlen(trim($dt['is_year'])) > 0) {
            $id_date = $RDF->concept(trim($dt['is_year']), 'Date');
            $RDF->propriety($id_issue, 'hasDateTime', $id_date);
        }

        /***** Place */
        if (strlen(trim($dt['is_place'])) > 0) {
            $id_place = $RDF->concept($dt['is_place'], 'Place');
            $RDF->propriety($id_issue, 'hasPlace', $id_place);
        }

        $dd['is_source_rdf'] = $dt['jnl_frbr'];
        $dd['is_source_issue'] = $id_issue;
        $dd['is_issue'] = $dt['id_is'];
        $this->set($dd)->where('id_is', $dt['id_is'])->update();

        return "";
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
}
