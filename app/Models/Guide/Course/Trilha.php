<?php

namespace App\Models\Guide\Course;

use CodeIgniter\Model;

class Trilha extends Model
{
    protected $DBGroup          = 'guide';
    protected $table            = 'trilha';
    protected $primaryKey       = 'id_tr';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_tr', 'tr_trilha', 'tr_ativo'
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

    function view($id)
        {
            $dt = $this->le($id);
            $sx = '';
            $sx .= bsc(h($dt['tr_trilha'], 1), 10);
            $sx .= bsc($dt['created_at'], 2);

            $Module = new \App\Models\Guide\Course\Module();
            $sx .= $Module->summary($id);
            return bs($sx);
        }

    function le($id)
        {
            $dt = $this->where('id_tr',$id)->first();


            return $dt;
        }

    function list()
        {
            $dt = $this
                ->where('tr_ativo',1)
                ->findAll();
            $sx = '';
            foreach($dt as $id=>$line)
                {
                    $link = '<a href="'.PATH. '/guide/course/viewer/'.$line['id_tr'].'">';
                    $linka = '</a>';
                    $sx .= bsc($link.h($line['tr_trilha'].$linka,3),12);
                }
            return bs($sx);
        }
}
