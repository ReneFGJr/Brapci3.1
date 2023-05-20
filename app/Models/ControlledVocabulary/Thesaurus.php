<?php

namespace App\Models\ControlledVocabulary;

use CodeIgniter\Model;

class Thesaurus extends Model
{
    protected $DBGroup          = 'vc';
    protected $table            = 'thesaurus';
    protected $primaryKey       = 'id_th';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_th', 'th_name', 'th_url',
        'th_status', 'th_about', 'updated_at'
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
                        $sx .= tableview($this);
                        break;
                }

            $sx = bs($sx);


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

        function btn_import($id)
            {
                $sx = '<a href="'.$this->path.'/import/'.$id.'" class="btn btn-outline-primary">';
                $sx .= bsicone('import');
                $sx .= ' ';
                $sx .= lang('brapci.import');
                $sx .= '</a>';
                return $sx;
            }

        function header($dt)
            {
                $link = anchor($dt['th_url'],bsicone('url',32),['class'=>'']);
                $sx = '';
                $sx .= h($dt['th_name'].$link,2);

                return bsc($sx);
            }

        function viewid($id)
            {
                $ThesaurusDescriptorsTh = new \App\Models\ControlledVocabulary\ThesaurusDescriptorsTh();
                $dt = $this->find($id);
                $sx = $this->header($dt);
                $sx .= bsc($this->btn_import($id),12);

                $sx .= bsc($ThesaurusDescriptorsTh->resume($id));

                $sx = bs($sx);
                return $sx;
            }
}
