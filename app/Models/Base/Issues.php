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
        'id_is', 'is_source_rdf', 'is_source_issue',
        'is_year', 'is_issue', 'is_vol', 'is_vol_roman',
        'is_nr', 'is_place', 'is_thema',
        'is_cover', 'is_url_oai', 'is_works'
    ];

    protected $typeFields    = [
        'hidden', 'string', 'string',
        'string', 'string', 'string', 'string',
        'string', 'string', 'string',
        'string', 'string', 'string'
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

    function edit($id)
        {
            $this->id = $id;
            $this->path = URL.'/'.COLLECTION.'/issue/';
            $this->path_back = URL.'/'.COLLECTION.'/issue/?id='.$id;
            $sx = form($this);
            $sx .= '=edit=>'.$this->id;
            $sx = bs(bsc($sx,12));
            return $sx;
        }

    function show_list_cards($id,$default_img='/img/issue/issue_enancib_ancib.png')
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
                    <span class="position-absolute top-0 start-0" style="padding: 0px; margin: 0px; font-size: 350%; color: #666;"><b>'.$line['is_vol_roman'].'</b></span>
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

    function issue($id)
    {
        $dt = $this
            ->join('source_source', 'is_source = id_jnl')
            ->where('id_is', round($id))
            ->findAll();

        $sx = '';
        $sx .= $this->header_issue($dt[0]);
        //$sx .= $IssuesWorks->check($dt[0]);
        return $sx;
    }

    function issue_section_works($id)
    {
        /* Recupera ID RDF */
        $dt = $this->find($id);
        $id_rdf = $dt['is_source_issue'];

        if (get("reindex") != '')
        {
            $IssuesWorks = new \App\Models\Base\IssuesWorks();
            $IssuesWorks->check($dt);
        }


        /* Recupera works */
        $IssuesWorks = new \App\Models\Base\IssuesWorks();
        $sx = $IssuesWorks->show_issue_works($id_rdf);
        return $sx;
    }

    function header_issue($dt)
    {
        $tools = anchor(URL.'/'.COLLECTION.'/issue/?id='.$dt['id_is'].'&reindex=1',bsicone('reload',32));
        $tools .= '<span class="p-2"></span>';
        $tools .= anchor(URL.'/'.COLLECTION.'/issue/edit/'.$dt['id_is'].'',bsicone('edit',32));
        $sx = '';
        $vol = $dt['is_vol'];
        $roman = trim($dt['is_vol_roman']);
        if (strlen($roman) > 0)
            {
                $vol .= ' ('.$roman.')';
            }
        $sx .= bsc(h($dt['jnl_name'], 3), 12);
        $sx .= bsc($vol, 1);
        $sx .= bsc($dt['is_year'], 1);
        $sx .= bsc($dt['is_place'], 2);
        $sx .= bsc($dt['is_thema'], 7);
        $sx .= bsc($tools, 1);

        /**************************** */
        $sx = bs($sx);
        $id_issue_rdf = $dt['is_source_issue'];
        return $sx;
    }
}
