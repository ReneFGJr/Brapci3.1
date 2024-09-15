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
                    case 'view':
                        $id = $d2;
                        $sx .= $this->viewid($d2);
                        break;
                    default:
                        $sx .= $this->tableview();
                        break;
                }

            $sx = bs($sx);


            return $sx;
        }

        function viewid($id)
            {
                $ThesaurusDescriptors = new \App\Models\ControlledVocabulary\ThesaurusDescriptors();
                $dt = $this
                    ->join('thesa_literal', 'id_l = c_term')
                    ->where('id_c',$id)
                    ->first();
                $sx = '';
                $sx .= bsc(h($dt['l_term']));

                $sx .= bsc('',5).bsc($ThesaurusDescriptors->view($id),7);
                $sx = bs($sx);
                return $sx;
            }

        function form()
            {
                $sx = form_open();
                $sx .= form_input('q','form-contro full');
                $sx .= form_close();
                return $sx;
            }

        function tableview()
            {
                $term = 'Biblioteca';
                $sx = '';
                $sx .= $this->form();

                $dt = $this
                    ->select('id_c, l_term, l_lang')
                    ->join('thesa_literal', 'id_l = c_term')
                    ->where('l_lang','pt')
                    ->like('l_term',$term)
                    ->orderBy('l_term')
                    ->findAll(1000);
                $sx .= tableview2($dt,'admin/vc/');
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
