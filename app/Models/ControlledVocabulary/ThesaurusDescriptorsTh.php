<?php

namespace App\Models\ControlledVocabulary;

use CodeIgniter\Model;

class ThesaurusDescriptorsTh extends Model
{
    protected $DBGroup          = 'vc';
    protected $table            = 'thesaurus_descriptors_th';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_tdt', 'tdt_th', 'tdt_term',
        'tdt_lang', 'tdt_url', 'tdt_type',
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


    function resume($th)
        {
            $dt = $this
                ->select('count(*) as total, tdt_lang, tdt_type')
                ->where('tdt_th',$th)
                ->groupBy('tdt_lang, tdt_type')
                ->orderBy('tdt_type, tdt_lang')
                ->findAll();
            $type = ['A'=>lang('brapci.altLabel'), 'P' => lang('brapci.prefLabel'), 'H' => lang('brapcihiddenLabel')];
            $rst = [];
            foreach($dt as $id=>$line)
                {
                    $t = $line['tdt_type'];
                    $lang = $line['tdt_lang'];
                    $sc = '<li>' . $type[$t].'-'.$lang.' ('.$line['total'] . ')</li>' . cr();
                    $rst[$t][$lang] = $sc;
                }
            $sx = '<table class="table full">';
            $sx .= '<tr>';
            foreach($rst as $lang=>$ul)
                {
                    $sx .= '<td><ul>';
                    foreach($ul as $idx=>$li)
                        {
                            $sx .= $li;
                        }
                    $sx .= '</ul></td>';

                }
            $sx .= '</tr>';
            $sx .= '</table>';
            return $sx;
        }

    function register($th, $idt, $lang, $type, $concp)
        {
            $dt = $this
                ->where('tdt_term',$idt)
                ->where('tdt_lang',$lang)
                ->where('tdt_th',$th)
                ->first();

            if ($dt == '')
                {
                    $dt['tdt_th'] = $th;
                    $dt['tdt_term'] = $idt;
                    $dt['tdt_lang'] = $lang;
                    $dt['tdt_url'] = $concp;
                    $dt['tdt_type'] = $type;
                    $this->set($dt)->insert();
                }
        }
}
