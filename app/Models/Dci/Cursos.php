<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Cursos extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'curso';
    protected $primaryKey       = 'id_c';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_c','c_curso', 'c_departamento', 'c_bg'];
    protected $tpeFields    = ['hidden','string','string','string', 'string'];

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

    var $path = PATH.'/dci/cursos';
    var $path_back = PATH . '/dci/cursos';

    function select()
        {
            $dt = $this->findAll();
            $Cursos = [];
            foreach($dt as $id=>$line)
                {
                    $Cursos[$line['id_c']] = $line['c_curso'];
                }
            return $Cursos;
        }

    function index($d1,$d2,$d3,$d4)
        {
            $sx = '';

            $mn = [];
            $mn['Departamento'] = PATH . '/dci/';
            $mn['Cursos'] = PATH . '/dci/cursos/';
            $sx .= breadcrumbs($mn);

            switch($d1)
                {
                    default:
                        $st = tableview($this);
                        $sx .= bs(bsc($st));
                }
            return $sx;
        }
}
