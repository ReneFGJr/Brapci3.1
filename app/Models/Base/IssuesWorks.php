<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class IssuesWorks extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_issue_work';
    protected $primaryKey       = 'id_siw';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_siw', 'siw_journal', 'siw_issue',
        'siw_section', 'siw_work_rdf', 'update_at',
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

    function exclude($id)
        {
            $this->where('siw_work_rdf',$id)->delete();
            return true;
        }

    function issueWorks($id_rdf)
        {
        $dt = $this
            ->where('siw_issue', $id_rdf)
            ->orderBy('siw_order, siw_pag_ini')
            ->findAll();
        return $dt;
        }

    function show_issue_works($id_rdf)
    {
        $Work = new \App\Models\Base\Work();
        $Keywords = new \App\Models\Base\Keywords();
        $Authors = new \App\Models\Base\Authors();
        $Sections = new \App\Models\Base\Sections();
        $Indexshow = new \App\Models\Base\IndexShow();

        $dt = $this->issueWorks($id_rdf);
        $sx = h(count($dt).' '.lang('brapci.works'),4,'text-end');

        /******************************* */
        /* Index */
        $auth = array();
        $keys = array();
        $sect = array();

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $sx .= '<p>' . $Work->show_reference($line['siw_work_rdf']) . '</p>';

            $keys = $Keywords->index_keys($keys, $line['siw_work_rdf']);
            $auth = $Authors->index_auths($auth, $line['siw_work_rdf']);
            $sect = $Sections->index_sections($sect, $line['siw_work_rdf']);
        }

        $key_index = '';
        $key_index .= $Indexshow->show_index($auth, 'authors');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($sect,'sections');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($keys,'keyword');
        $sx = bs(
            bsc($key_index, 4, 'text_indexes') .
                bsc($sx, 8)
        );
        return $sx;
    }

    function saving($da)
    {
        $dt = $this->where('siw_work_rdf', $da['siw_work_rdf'])->findAll();
        if (count($dt) == 0) {
            echo "O";
            $id = $this->insert($da);
            return 1;
        }
        return 0;
    }

    function check($dd)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $idr = $dd['is_source_issue'];

        $dt = $RDF->le_data($idr);
        $dt = $dt['data'];

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $class = trim($line['c_class']);
            if ($class == 'hasIssueProceedingOf') {
                $da = array();
                $da['siw_work_rdf'] = $line['d_r2'];
                $da['siw_journal'] = $dd['is_source'];
                $da['siw_journal_rdf'] = $dd['is_source_rdf'];
                $da['siw_section'] = 0;
                $da['siw_issue'] = $idr;
                $this->saving($da);
            }
            if ($class == 'hasIssueOf') {
                $da = array();
                $da['siw_work_rdf'] = $line['d_r2'];
                $da['siw_journal'] = $dd['is_source'];
                $da['siw_journal_rdf'] = $dd['is_source_rdf'];
                $da['siw_section'] = 0;
                $da['siw_issue'] = $idr;
                $this->saving($da);
            }
        }
    }
}