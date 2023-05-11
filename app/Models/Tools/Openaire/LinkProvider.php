<?php

namespace App\Models\Tools\Openaire;

use CodeIgniter\Model;

class LinkProvider extends Model
{
    protected $DBGroup          = 'openaire';
    protected $table            = 'openaire_linkproviders';
    protected $primaryKey       = 'id_lk';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_lk', 'lk_doi', 'lk_status', 'lk_method', 'lk_result'
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

    function resume($id)
        {
            $dt = $this
                ->select('count(*) as total, lk_status')
                ->join('openaire_linkproviders_prj', 'id_lk = olp_doi', 'LEFT')
                ->where('olp_prj', $id)
                ->groupBy('lk_status')
                ->findAll();

            $sx = '<ul>';
            foreach($dt as $id=>$line)
                {
                    $sx .= '<li>'.lang('brapci.status_'.$line['lk_status']).' ('.$line['total'].')</li>';
                }
            $sx .= '</ul>';
            return $sx;
        }

    function register($doi,$prj)
        {
            $sx = '';
            $dt = $this
                ->join('openaire_linkproviders_prj', 'id_lk = olp_doi','LEFT')
                ->where('lk_doi',$doi)
                ->first();

            if ($dt=='')
                {
                    $dt['lk_doi'] = $doi;
                    $idd = $this->set($dt)->insert();
                    $sx .= lang('brapci.insered');
                } else {
                    $idd = $dt['id_lk'];
                    $sx .= lang('brapci.already_registered');
                }

            if ($dt['id_olp'] != '')
                {
                } else {
                    $Prop = new \App\Models\Tools\Openaire\LinkProviderPrj();
                    $dx['olp_doi'] = $idd;
                    $dx['olp_prj'] = $prj;
                    $dp = $Prop->set($dx)->insert();
                    $sx .= '+';
                }

            return $sx;

        }
}
