<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Docentes extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'docentes';
    protected $primaryKey       = 'id_d';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_d'
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

    function index($d1='',$d2='',$d3='',$d4='')
        {
            $sx = '';
            switch($d1)
                {
                    default:
                        $sx .= bs($this->list($d2));
                        break;
                }
            return $sx;
        }

    function list($curso=0)
        {
            $dt = $this
                ->join('curso','dc_curso = id_c')
                ->orderby('c_curso, dc_nome')
                ->findAll();

            $sx = '';
            $xcurso = '';
            $nr = 0;
            $nri = 0;
            foreach($dt as $id=>$line)
                {
                    $curso = $line['c_curso'];
                    $link = '<a href="'.PATH.'dci/docentes/view/'.$line['id_dc'].'">';
                    $linka = '</a>';
                    if ($curso != $xcurso)
                        {
                            $sx .= bsc(h($curso, 2), 12);
                            $xcurso = $curso;
                            $nr = 0;
                        }
                    $nr++;
                    $nri++;
                    $sx .= bsc($link.$nr.'. '.$line['dc_nome'].$linka, 8);
                    $sx .= bsc($line['c_curso'], 2);
                    $sx .= bsc($line['dc_status'], 2);
                }
            return $sx;
        }
}
