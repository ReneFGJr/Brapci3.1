<?php

namespace App\Models\ControlledVocabulary;

use CodeIgniter\Model;

class Thesaurus extends Model
{
    protected $DBGroup          = 'vc';
    protected $table            = 'thesa_concept';
    protected $primaryKey       = 'id_c';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_c',
        'c_thesa',
        'c_group',
        'c_term',
        'c_property',
        'c_brapci',
        'c_update'
    ];
    protected $TYPEdFields    = [
        'hidden', 'string*', 'string*',
        'SN', 'text', 'hidden'
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

    var $path = PATH.'/admin/vc';
    var $path_back = PATH . '/admin/vc';
    var $id = 0;

    function index($d1,$d2,$d3,$d4)
        {
            $sx = '';
            $sx .= h(lang('brapci.vocabulary_controlled'));
            switch($d1)
                {
                    case 'import':
                        $sx .= $this->import($d2);
                        break;
                    case 'viewid':
                        $sx .= $this->viewid($d2);
                        break;
                    default:
                        $sx .= $this->tableview();
                        break;
                }

            $sx = bs($sx);


            return $sx;
        }

        function tableview()
            {
                $dt = $this
                    ->select('id_c, l_term, l_lang')
                    ->join('thesa_literal', 'id_l = c_term')
                    ->findAll(1000);
                $sx = tableview2($dt);
                return $sx;
            }

        function import($id)
            {
                $sx = '';
                $dt = $this->find($id);
                switch($dt['th_type'])
                    {
                        case 'THESA':
                            $VC = new \App\Models\ControlledVocabulary\Thesa();
                            $sx .= $VC->import($id);
                        break;

                        default:
                            $sx = bsmessage('Importação deste tipo de tesauro não implementado',3);
                    }
                return $sx;
            }


}
