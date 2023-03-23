<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Disciplinas extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'disciplinas';
    protected $primaryKey       = 'id_di ';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_di', 'di_curso', 'di_disciplina',
        'di_codigo', 'di_etapa', 'di_tipo',
        'di_crd', 'di_ch', 'di_ext'
    ];

    protected $typeFields    = [
        'hidden', 'hidden', 'string',
        'string', 'int', 'hidden',
        'hidden', 'hidden', 'hidden'
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

    var $id = 0;
    var $path = '';
    var $path_back = '';

    function __construct()
    {
        $this->path = PATH.'/dci/salas/';
        $this->path_back = PATH . '/dci/salas/';
    }

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $sx = '';
        switch ($d1) {
            case 'edit':
                $sx .= $this->edit($d2);
                break;
            default:
                $sx .= bs($this->list($d2));
                break;
        }
        return $sx;
    }

    function register($pro,$sem,$disc,$turm)
        {
            $Encargos = new \App\Models\Dci\Encargos();
            $Encargos->register($pro,$sem,$disc,$turm);
        }

    function remove($pro, $sem, $disc)
    {
        $Encargos = new \App\Models\Dci\Encargos();
        $Encargos->remove($pro, $sem, $disc);
    }

    function edit($id)
        {
            $this->id = $id;
            $dt = $this->find($id);
            pre($dt);
            echo '===>'.$id;
            $sx = form($this);
            return $sx;
        }

    function candidatas($dt,$sem)
        {
        $curso_pref = $dt['dc_curso'];
        $id_doc = $dt['id_dc'];

        $op1 = get("opt1");
        $op2 = get("opt2");
        $act = get("action");
        $turm = get("turma");

        if ($act != '')
            {
                if ($act == 'Onerar >>')
                    {
                        $this->register($id_doc,$sem,$op1,$turm);
                    }

                if ($act == '<<< Desonerar') {
                    $this->remove($id_doc, $sem, $op2);
                }
            }

            $dt = $this
            //->select('id_di, di_curso, di_etapa,di_disciplina, di_codigo')
                ->join('curso', 'di_curso = id_c')
                ->join('encargos', '((e_semestre = '.$sem. ') and (e_disciplina = id_di))','LEFT')

                //->where('di_curso',$curso_pref)
                ->orderBy('c_curso, di_etapa')
                ->findAll();

            $opt1 = [];
            $opt2 = [];
            $xet = 0;
            $xcurso = '';
            foreach($dt as $id=>$line)
                {
                    $curso = $line['c_curso'];
                    $turmaN = $line['e_turma'];

                    $et = $line['di_etapa'];
                    if ($et != $xet)
                        {
                            $etapa = 'Etapa ' . $et;
                            $opt[$etapa] = [];
                            $xet = $et;
                        }
                    $codigo = $line['id_di'];
                    $name = $line['di_codigo'].' '.nbr_title($line['di_disciplina']);

                    $mult = $line['di_multi'];

                    if ($line['e_docente'] == $id_doc)
                        {
                            if ($mult == true)
                                {
                                    $opt1[$curso . '-' . $etapa][$codigo] = $name;
                                    $opt2[$curso . '-' . $etapa][$codigo.'.'. $turmaN] = $name . ' (' . $line['e_turma'] . ')';
                                } else {
                                    $opt2[$curso . '-' . $etapa][$codigo] = $name . ' (' . $line['e_turma'] . ')';
                                }
                        } else {
                            if ($line['e_docente'] > 0)
                                {

                                } else {
                                    $opt1[$curso.'-'.$etapa][$codigo] = $name;
                                }

                        }
                }

            /*************/
            $turma = [];
            $turma['U'] = 'Única';
            $turma['1'] = 'Turma 1';
            $turma['2'] = 'Turma 2';
            $turma['3'] = 'Turma 3';

            $sx = form_open(PATH.'dci/docentes/view/'.$id_doc);

            $sa = form_dropdown('opt1',$opt1, $op1,['size'=>20,'class'=>'full']);
            $sb = form_dropdown('opt2', $opt2, $op2, ['size' =>20, 'class' => 'full']);

            $act = '';
            $act .= form_label('Turma');
            $act .= form_dropdown('turma', $turma, $turm, ['size' => 1, 'class'=>'full']);
            $act .= '<br><br>';
            $act .= '<input type="submit" class="btn btn-secondary full" name="action" value="Onerar >>">';
            $act .= '<br><br>';
            $act .= '<input type="submit" class="btn btn-secondary full" name="action" value="<<< Desonerar">';

            $sx .= '<table><tr valign="top">
                            <td width="45%">' . $sa . '</td>
                            <td width="10%" class="p-3">'.$act.'</td></td>
                            <td width="45%">' . $sb . '</td>
                        </tr></table>';
            $sx .= form_close();

            return $sx;
        }

    function list($curso = 0)
    {
        $sem = 1;
        $dt = $this
            ->join('curso', 'di_curso = id_c')
            ->join('encargos', '((e_semestre = ' . $sem . ') and (e_disciplina = id_di))', 'LEFT')
            ->join('docentes','e_docente = id_dc', 'LEFT')
            ->orderby('c_curso, di_etapa, di_codigo, di_disciplina')
            ->findAll();

        $sx = '';
        $xcurso = '';
        $nr = 0;
        $nri = 0;
        $xeta = 0;
        foreach ($dt as $id => $line) {
            $curso = $line['c_curso'];
            $eta = $line['di_etapa'];
            if ($curso != $xcurso) {
                $sx .= bsc(h($curso, 2), 12);
                $xcurso = $curso;
                $nr = 0;
            }

            if ($xeta != $eta)
                {
                $sx .= bsc(h('Etapa '.$eta, 4), 12);
                $xeta = $eta;
                }
            $nr++;
            $nri++;

            $link = '<a href="' . PATH . 'dci/disciplina/view/' . $line['id_di'] . '">';
            $linka = '</a>';
            $sx .= bsc($link . $nr . '. ' . $line['di_codigo'] . ' '.$line['di_disciplina']. $linka,8, 'border-top border-secondary small');

            $link = '<a href="' . PATH . 'dci/docentes/view/' . $line['id_di'] . '">';
            $linka = '</a>';
            $sx .= bsc($link.$line['dc_nome']. $linka,4,'border-top border-secondary small');
        }
        return $sx;
    }
}
