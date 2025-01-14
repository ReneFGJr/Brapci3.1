<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Encargos extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'encargos';
    protected $primaryKey       = 'id_e';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_e', 'e_semestre', 'e_disciplina',
        'e_docente', 'e_credito',
        'e_dia',
        'e_horario',
        'e_turma'

    ];

    protected $typeFields    = [
        'hidden',
        'qr:id_sem:sem_descricao:(select id_sem,sem_descricao from dci.semestre where sem_ativo = 1) as semestre ',
        'qr:id_di:di_disciplina:(select id_di, concat(\' Etapa \', di_etapa,\' - \', di_codigo ,\' - \',di_disciplina) as di_disciplina from dci.disciplinas order by di_codigo) as disciplinas',
        'qr:id_dc:dc_nome:dci.docentes',
        'qr:cr_valor:cr_nome:dci.creditos',
        'qr:hd_dia:hd_dia_name:dci.horario_dia',
        'qr:id_hora:hora_inicio:dci.horario_hora',
        'st'

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

    var $path = PATH.'/dci/encargos';
    var $path_back = PATH . '/dci/';
    var $id = 0;

    function indications($dt,$sem)
        {
            $Disciplinas = new \App\Models\Dci\Disciplinas();
            $lst = $Disciplinas->candidatas($dt,$sem);

            return $lst;
        }

    function remove($pro, $sem, $disc)
    {

        $dt = $this
            ->where('e_docente', $pro)
            ->where('e_semestre', $sem)
            ->where('e_disciplina', $disc)
            ->delete();
    }
    function register($pro, $sem, $disc,$turm)
        {

            $dt = $this
                ->where('e_docente',$pro)
                ->where('e_semestre', $sem)
                ->where('e_disciplina', $disc)
                ->where('e_turma', $turm)
                ->findAll();

            if (count($dt) == 0)
                {

                    $Disciplinas = new \App\Models\Dci\Disciplinas();
                    $dc = $Disciplinas->find($disc);

                    $data['e_credito'] = $dc['di_crd'];
                    $data['e_semestre'] = $sem;
                    $data['e_disciplina'] = $disc;
                    $data['e_docente'] = $pro;
                    $data['e_turma'] = $turm;
                    $this->set($data)->insert();
                }
        }

    function edit($id)
        {
            $sx = '';
            if ($id == '') { $id = 0; }
            $this->id = $id;
            $sx .= form($this);
            $sx = bs(bsc($sx));
            return $sx;
        }

    function view($id,$sem)
        {
            $sx = '';
            $dt = $this
                    ->where("e_docente",$id)
                    ->where("e_semestre",$sem)
                    ->findAll();

            if (count($dt) == 0)
                {
                    $sx .= 'Sem encargos definidos';
                } else {

                }
            $sx = bs(bsc($sx));
            return $sx;

        }
}
