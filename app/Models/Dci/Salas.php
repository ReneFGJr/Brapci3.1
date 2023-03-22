<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Salas extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'salas_aula';
    protected $primaryKey       = 'id_sala';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_sala', 'sala_nome','sala_predio','Sala_informatizada'
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

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5='')
    {
        $sx = '';

        $mn = [];
        $mn['Departamento'] = PATH . '/dci/';
        $mn['Ensalamento'] = PATH . '/dci/salas/';
        $sx .= breadcrumbs($mn);

        switch ($d1) {
            case 'mark':
                $sx .= $this->mark($d2,$d3,$d4,$d5);
                break;
            case 'view':
                $sx .= $this->viewid($d2,$d3,$d4);
                break;
            default:
                $sx .= bs($this->list($d2));
                break;
        }
        return $sx;
    }

    function mark($id,$d2,$d3,$d4)
        {
        $dt = $this
            ->find($id);

        $sx = bs(bsc($this->header($dt), 12));

        $Ensalamento = new \App\Models\Dci\Ensalamento();
        $sx .= $Ensalamento->mark($id,$d2,$d3,$d4);

        return $sx;
        }
    function viewid($id)
        {
            $sx = '';
            $dt = $this
                ->find($id);

            $sx .= bsc($this->header($dt),12);

            $Ensalamento = new \App\Models\Dci\Ensalamento();
            $sx .= $Ensalamento->viewid($id);


            $sx = bs($sx);

            return $sx;

        }

    function header($dt)
        {
            $sx = '';
            $sx .= h($dt['sala_predio'].' - '.$dt['sala_nome'],2);
            return $sx;
        }


    function list($curso = 0)
    {
        $sem = 1;
        $dt = $this
            ->orderby('sala_predio DESC, sala_nome')
            ->findAll();

        $sx = '';
        $xcurso = '';
        $nr = 0;
        $nri = 0;
        $xeta = 0;
        $salas = [];
        foreach ($dt as $id => $line) {
            $predio = $line['sala_predio'];
            $sala = $line['sala_nome'];
            $info = $line['sala_informatizada'];

            $link = '<a href="' . PATH . 'dci/salas/view/' . $line['id_sala'] . '">';
            $linka = '</a>';
            $nome = $link.$sala.$linka;
            if ($info) { $nome .= ' [C]'; }
            $salas[$predio][$sala] = $nome;
        }
        $col = round(12/count($salas));
        if ($col < 2) { $col = 2; }
        foreach($salas as $predio=>$sala)
            {
                $sa = '';
                foreach($sala as $s1=>$nome)
                    {
                        $sa .= $nome.'<br>';
                    }
                $sx .= bsc(h($predio,4).$sa,$col);
            }
        $sx = bs($sx);
        return $sx;
    }
}
