<?php

namespace App\Models\ScientificCommunication;

use CodeIgniter\Model;

class PeerReview extends Model
{
    protected $DBGroup          = 'pgcd';
    protected $table            = 'scientific_opinion';
    protected $primaryKey       = 'id_op';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_op', 'op_name',
        'op_title', 'op_instituicao',
        'op_curso', 'op_type', 'op_date',
        'op_hora', 'op_local', 'op_membros',
         'updated_at'
    ];

    protected $typeFields    = [
        'hidden', 'string*',
        'text*', 'string*',
        'string', 'string', 'string',
        'string', 'string', 'text',
         'up'
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

    function index($d1,$d2,$d3,$d4)
        {
            $sx = '';
            switch($d1)
                {
                    case 'viewid':
                        $sx .= $this->view($d2,$d3);
                        break;
                    case 'edit':
                        $sx .= $this->edit($d2);
                        break;
                    case 'ajax_field_save':
                        $PeerReviewContent = new \App\Models\ScientificCommunication\PeerReviewContent();
                        $PeerReviewContent->ajax_save($d2, $d3);
                        exit;
                        break;
                    case 'ajax_field':
                        $PeerReviewContent = new \App\Models\ScientificCommunication\PeerReviewContent();
                        $PeerReviewContent->edit($d2,$d3);
                        exit;
                        break;
                    case 'delete':
                        $sx .= $this->delete($d2);
                        break;
                    default:
                        $sx .= $this->list();
                        break;
                }
            return $sx;
        }

        function header($dt)
            {
                $sx = '';
                $inst = $dt['op_instituicao'];
                $inst .= '. '.$dt['op_curso'];
                $membros = $dt['op_membros'];
                $menbros = troca($membros,chr(10),'; ');
                $sx .= h($dt['op_title'],3);
                $sx .= h($dt['op_name'],4);
                $sx .= h($inst, 5);
                $sx .= h('<i>'.$membros. '</i>', 5);
                return $sx;
            }

        function view($id)
            {
                $PeerReviewContent = new \App\Models\ScientificCommunication\PeerReviewContent();
                $sx = '';
                $dt = $this->find($id);
                $sx .= bsc($this->header($dt),12);

                $sx .= bsc($PeerReviewContent->view($id));

                $sx = bs($sx);
                return $sx;
            }

        function edit($id)
            {
                $this->path = PATH . COLLECTION . '/opinion';
                $this->path_back = PATH . COLLECTION . '/opinion';
                $this->id = round('0'.$id);
                $sx = form($this);
                $sx = bs(bsc($sx, 12));
                return $sx;
            }

        function list()
            {
                $this->path = PATH . COLLECTION . '/opinion';
                $this->path_back = PATH . COLLECTION . '/opinion';
                $sx = tableview($this);
                $sx = bs(bsc($sx,12));
                return $sx;
            }
}
