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

    function analysis($id)
    {
        $dt = $this
            ->join('openaire_linkproviders_prj', 'id_lk = olp_doi')
            ->where('olp_prj', $id)
            ->where('lk_status', 1)
            ->findAll();
        $sx = '';
        foreach ($dt as $id => $line) {
            $js = $line['lk_result'];
            $js = json_decode($js);
            if ($js == '') {
                //echo h("ERRO");
            } else {
                pre($js);
                foreach ($js as $ids => $line2) {
                    $pubData = $line2->publicationDate;
                    $source = $line2->source;
                    $target = $line2->target;
                    $target = $this->recover_target($target);

                    $sx .= $line['lk_doi'] . ';';
                    $sx .= $target['doi'].';';
                    $sx .= $target['Type'] . ';';
                    $sx .= $target['SType'] . ';';
                    $sx .= '<br>';
                }
            }
        }
        return $sx;
    }

    function recover_target($t)
        {
            $doi = $t->identifiers[0]->identifier; //.' ('. $t->identifiers[0]->identifier.')';
            $objectType = $t->objectType;
            $objectSubType = $t->objectSubType;
            $dt['doi'] = $doi;
            $dt['Type'] = $objectType;
            $dt['SType'] = $objectType;
            return $dt;
        }

    function resume($id)
    {
        $link = [];
        $link[1] = PATH . '/tools/openaire/result';
        $dt = $this
            ->select('count(*) as total, lk_status')
            ->join('openaire_linkproviders_prj', 'id_lk = olp_doi', 'LEFT')
            ->where('olp_prj', $id)
            ->groupBy('lk_status')
            ->findAll();
        $sx = h('tools.openaire', 4);
        $sx .= '<ul>';
        foreach ($dt as $id => $line) {
            $lk = '';
            $lka = '';
            $idk = $line['lk_status'];
            if (isset($link[$idk])) {
                $lk = '<a href="' . $link[$idk] . '">';
                $lka = '</a>';
            }
            $sx .= '<li>' . $lk . lang('brapci.status_' . $line['lk_status']) . $lka . ' (' . $line['total'] . ')</li>';
        }
        $sx .= '</ul>';
        return $sx;
    }

    function register($doi, $prj)
    {
        $sx = '';
        $dt = $this
            ->join('openaire_linkproviders_prj', 'id_lk = olp_doi', 'LEFT')
            ->where('lk_doi', $doi)
            ->first();

        if ($dt == '') {
            $dt['lk_doi'] = $doi;
            $idd = $this->set($dt)->insert();
            $sx .= lang('brapci.insered');
        } else {
            $idd = $dt['id_lk'];
            $sx .= lang('brapci.already_registered');
        }

        if ($dt['id_olp'] != '') {
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
