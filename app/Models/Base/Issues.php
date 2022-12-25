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

    function index($act, $id)
    {
        $sx = '';
        switch ($act) {
            case 'listidentifiers':
                $jissue = get("id");
                if ($jissue != '')
                    {
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
                if ($id > 0)
                {
                    $sx .= bsc($this->issue($id), 12);
                    $sx .= $this->harvesting($id);
                } else {
                    $sx .= bsmessage('ERRO: 580 - id not found',3);
                }
                break;
            default:
                $id = get("id");
                $sx .= bsc($this->issue($id), 12);
                $sx .= '<hr>';
                $sx .= bsc($this->issue_section_works($id), 12);
                break;
        }
        return $sx;
    }

    function issues($id=0)
        {
            $Sources = new \App\Models\Base\Sources();
            $ds = $Sources->find($id);

            $id = round($id);
            $dt = $this->where("is_source",$id)
                ->orderBy('is_year','DESC')
                ->orderBy('is_vol','DESC')
                ->orderBy('is_issue','DESC')
                ->findAll();

            $sx = '';
            $sx .= $Sources->journal_header($ds);
            $sx .= '<hr>';

            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $edit = bsicone('edit');
                    $sa = '';
                    $sa .= bsc($line['is_year'],2);
                    $sa .= bsc($line['is_vol'],2);
                    $sa .= bsc($line['is_place'],2);
                    $sa .= bsc($line['is_thema'],6);
                    $sa .= bsc($edit);
                    $sx .= bs($sa);
                }

            $sx .= $this->btn_new_issue($id);
            return $sx;
        }

    function btn_new_issue($id)
        {
            $sx = '';
            $sx .= '<a href="'.base_url(PATH.COLLECTION.'/issues/edit/0?jid='.$id).'" class="btn btn-primary">';
            $sx .= msg('new_issue');
            $sx .= '</a>';
            return $sx;
        }
    function listidentifiers($id)
        {
            $OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
            $dt = $OAI_ListIdentifiers
                ->where('li_issue',$id)
                ->orderBy('li_setSpec, li_identifier','DESC')
                ->findAll();

            $sx = h('OAI - ListIdentifiers',3);
            $sx .= '<p>'.$this->getlastquery().'</p>';
            $sx .= '<p>Total de '.count($dt).' registers.</p>';
            $sx .= '<ul>';
            $xsetSpec = '';
            $tot = 0;
            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $setSpec = $line['li_setSpec'];
                    if ($setSpec != $xsetSpec)
                        {
                            if ($tot > 0)
                                {
                                    $sx .= '<p>Total '.$tot.'</p>';
                                }
                            $sx .= h($setSpec,4) . cr();
                            $xsetSpec = $setSpec;
                            $tot = 0;
                        }
                    $sx .= '<li>'.$OAI_ListIdentifiers->row($line). '</li>'.cr();
                    $tot = $tot + 1;
                }
            if ($tot > 0) {
                $sx .= '<p>Total ' . $tot . '</p>';
            }
            $sx .= '</ul>';
            $sx = bs(bsc($sx,12));
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
        if ($Social->getAccess("#ADM"))
            {
            $sx .= '<div class="container"><div class="row">';
            $link = '<a href="'.PATH.COLLECTION.'/issues/'.$id.'" class="btn btn-primary">'.bsicone('plus').'</a>';
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
                $img = 'img/issue/issue_'.strzero($line['is_source'],5).'.png';
            }

            $link = PATH . COLLECTION . '/issue?id=' . $line['id_is'];
            if (!file_exists($img)) {
                $img = $default_img;
            }

            $sx .= '
                    <div class="card  m-3" style="width: 18rem; cursor: pointer;" onclick="location.href = \'' . $link . '\';">
                    <img src="' . URL.'/'.$img . '" class="card-img-top" alt="...">
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

    function issue($id)
    {
        $dt = $this
            ->join('source_source', 'is_source = id_jnl')
            ->where('id_is', round($id))
            ->findAll();
        $sx = '';

        if (count($dt) == 0)
            {
                return "ERRO";
            } else {
                $dt = $dt[0];
            }

        if ($dt['is_source_issue'] == 0)
            {
                $sx .= 'Criar ISSUE RDF';
                $this->RDFIssue($dt);
                exit;
            }

        $sx .= $this->header_issue($dt);

        return $sx;
    }

    function RDFIssue($dt)
        {
            $RDF = new \App\Models\Rdf\RDF();
            $class = 'IssueProceeding';
            $prefLabel = trim($dt['is_vol_roman']).' '.trim($dt['jnl_name']).', '.trim($dt['is_year']);
            $id_issue = $RDF->concept($prefLabel,$class);

            /************************************************************* Vincula a fontes principal */
            $RDF->propriety($dt['jnl_frbr'], 'hasIssueProceeding',$id_issue);

            /***** Data */
             if (strlen(trim($dt['is_year'])) > 0) {
                $id_date = $RDF->concept(trim($dt['is_year']), 'Date');
                $RDF->propriety($id_issue, 'hasDateTime', $id_date);
             }

            /***** Place */
            if (strlen(trim($dt['is_place'])) > 0)
                {
                    $id_place = $RDF->concept($dt['is_place'], 'Place');
                    $RDF->propriety($id_issue, 'hasPlace', $id_place);
                }

            $dd['is_source_rdf'] = $dt['jnl_frbr'];
            $dd['is_source_issue'] = $id_issue;
            $this->set($dd)->where('id_is',$dt['id_is'])->update();

            return "";
        }

    function issue_section_works($id)
    {
        /* Recupera ID RDF */
        $dt = $this->find($id);
        if ($dt == '')
            {
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
        $file = strzero($dt['id_jnl'],4).'-'.strzero($dt['is_source_issue'],6).'.name';

        if (file_exists($dir.$file))
            {
                $sx = file_get_contents($dir.$file);
                return $sx;
            }

        $tools = '';

        /************************************* HARVESTING */
        $Socials = new \App\Models\Socials();
        if ($Socials->getAccess("#CAR#ADM")) {
            $tools = '';
            $OAI = new \App\Models\Oaipmh\Index();
            if (trim($dt['is_url_oai']) != '') {
                $tot = $OAI->to_harvesting(0,$dt['id_is']);
                if ($tot > 0)
                    {
                        $class = 'class = "blink" ';
                        $tools .= anchor(PATH . '/' . COLLECTION . '/oai/' . $dt['id_is']. '/getrecords', bsicone('harvesting', 32), 'title="Harvesing (' . $tot . ')" ' . $class);
                    } else {
                        $class = '';
                        $tools .= anchor(PATH . '/' . COLLECTION . '/issue/harvesting/?id=' . $dt['id_is'], bsicone('harvesting', 32), 'title="Harvesing"'.$class);
                    }
                $tools .= '<span class="p-2"></span>';
                $tools .= anchor(PATH . '/' . COLLECTION . '/issue/listidentifiers/?id=' . $dt['id_is'], bsicone('gear', 32), 'title="Check"');
                $tools .= '<span class="p-2"></span>';
            }
            $tools .= anchor(PATH . '/' . COLLECTION . '/issue/?id=' . $dt['id_is'] . '&reindex=1', bsicone('reload', 32), 'title="Reindex"');
            $tools .= '<span class="p-2"></span>';
            $tools .= anchor(PATH . '/' . COLLECTION . '/issue/edit/' . $dt['id_is'] . '', bsicone('edit', 32),'title="Edit"');
        }

        /************************************ Mount Header */
        $sx = '';
        $vol = $dt['is_vol'];
        $roman = trim($dt['is_vol_roman']);
        if (strlen($roman) > 0) {
            $vol .= ' (' . $roman . ')';
        }
        $link = '<a href="' . PATH . COLLECTION . '/source/'.$dt['id_jnl'].'" target="_new">';
        $linka = '</a>';
        $sx .= bsc($link.h($dt['jnl_name'].$linka, 3), 12);
        $sx .= bsc($vol, 1);
        $sx .= bsc($dt['is_year'], 1);
        $sx .= bsc($dt['is_place'], 2);
        $sx .= bsc($dt['is_thema'], 6);
        $sx .= bsc($tools, 2);

        $img1 = 'img/headers/journals/image_'.strzero($dt['is_source'],4).'.png';
        $img2 = 'img/headers/issue/image_' . strzero($dt['id_is'], 6) . '.png';

        if (!file_exists($img1)) {
            $img1 = 'img/headers/journals/image_' . strzero(0, 6) . '.png';
        }

        if (!file_exists($img2))
            {
                $img2 = 'img/headers/issue/image_' . strzero(0, 6) . '.png';
            }
        $sx .= bsc('', 4);
        $sx .= bsc('<img src="'.URL.'/'. $img2 . '" class="text-end" style="max-height: 80px;">', 5,'text-end');
        $sx .= bsc('<img src="' . URL . '/' . $img1 . '" class="img-fluid" style="width: 100%">', 3);

        /**************************** */
        $sx = bs($sx);
        $id_issue_rdf = $dt['is_source_issue'];

        //file_put_contents($dir.$file,$sx);

        $IssuesWorks = new \App\Models\Base\IssuesWorks();
        $sx .= $IssuesWorks->check($dt);
        return $sx;
    }
}
