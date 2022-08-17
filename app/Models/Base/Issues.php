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

        'is_nr',
        'is_place',
        'is_thema',

        'is_source_rdf',
        'is_cover',
        'is_url_oai',

        'is_works',
        'is_source_issue'
    ];

    protected $typeFields    = [
        'hidden', 'sql:id_jnl:jnl_name:source_source order by jnl_name*', 'year*',
        'hidden', 'string', 'string',
        '[1-199]', 'string', 'text',
        'hidden', 'hidden', 'string',
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

    function show_list_cards($id, $default_img = URL . '/img/issue/issue_enancib_ancib.png')
    {
        $dt = $this
            ->where('is_source', $id)
            ->orderBy('is_year desc, is_nr desc')
            ->findAll();

        $sx = '<div class="container"><div class="row">';
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $sa = '';
            //$sa .= bsc($line['is_year'],2);
            //$sa .= bsc($line['is_vol'],2);
            //$sa .= bsc($line['is_place'],2);
            //$sa .= bsc($line['is_thema'],6);
            //$sx .= bs($sa);
            $img = $line['is_card'];
            $link = PATH . COLLECTION . '/issue?id=' . $line['id_is'];
            if (!file_exists($img)) {
                $img = $default_img;
            }

            $sx .= '
                    <div class="card  m-3" style="width: 18rem; cursor: pointer;" onclick="location.href = \'' . $link . '\';">
                    <img src="' . $img . '" class="card-img-top" alt="...">
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
        $sx .= anchor(URL . COLLECTION . '/issue/edit/' . $idj . '?jid=' . $idj, lang('brapci.new_issue'));
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
        $sx .= $this->header_issue($dt[0]);
        //$sx .= $IssuesWorks->check($dt[0]);
        return $sx;
    }

    function RDFIssue($dt)
        {
            pre($dt);
        }

    function issue_section_works($id)
    {
        /* Recupera ID RDF */
        $dt = $this->find($id);
        $id_rdf = $dt['is_source_issue'];

        if (get("reindex") != '') {
            $IssuesWorks = new \App\Models\Base\IssuesWorks();
            pre($dt);
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
        $tools = '';
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
                        $tools .= anchor(PATH . '/' . COLLECTION . '/issue/harvesting/?id=' . $dt['id_is'], bsicone('harvesting', 32), 'title="Harvesing"'.$class);
                    }
                $tools .= '<span class="p-2"></span>';
            }
            $tools .= anchor(PATH . '/' . COLLECTION . '/issue/?id=' . $dt['id_is'] . '&reindex=1', bsicone('reload', 32), 'title="Reindex"');
            $tools .= '<span class="p-2"></span>';
            $tools .= anchor(PATH . '/' . COLLECTION . '/issue/edit/' . $dt['id_is'] . '', bsicone('edit', 32),'title="Edit"');
        }
        $sx = '';
        $vol = $dt['is_vol'];
        $roman = trim($dt['is_vol_roman']);
        if (strlen($roman) > 0) {
            $vol .= ' (' . $roman . ')';
        }
        $sx .= bsc(h($dt['jnl_name'], 3), 12);
        $sx .= bsc($vol, 1);
        $sx .= bsc($dt['is_year'], 1);
        $sx .= bsc($dt['is_place'], 2);
        $sx .= bsc($dt['is_thema'], 6);
        $sx .= bsc($tools, 2);

        /**************************** */
        $sx = bs($sx);
        $id_issue_rdf = $dt['is_source_issue'];
        return $sx;
    }
}
