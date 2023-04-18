<?php

namespace App\Models\Guide\Course;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'guide';
    protected $table            = 'curso';
    protected $primaryKey       = 'id_c';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function index($d1,$d2)
        {
            $sx = ''.$d1;
            switch($d1)
                {
                    case 'content':
                        $CONT = new \App\Models\Guide\Course\Content();
                        $sx .= $CONT->edit($d2);
                        break;
                    case 'module':
                        $MOD = new \App\Models\Guide\Course\Module();
                        $sx .= $MOD->view($d2);
                        break;
                    case 'viewer':
                        $TILHAS = new \App\Models\Guide\Course\Trilha();
                        $sx.= $TILHAS->view($d2);
                        break;
                    default:
                        $TILHAS = new \App\Models\Guide\Course\Trilha();
                        $sx .= $TILHAS->list();
                        break;
                }
            return $sx;
        }

        function le($id)
            {
                $dt = $this->where('id_c',$id)->first();
                return $dt;
            }

}
