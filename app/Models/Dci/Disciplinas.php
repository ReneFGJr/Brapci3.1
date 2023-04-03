<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Disciplinas extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'disciplinas';
    protected $primaryKey       = 'id_di';
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
            case 'edit':
                $sx .= $this->edit($d2);
                break;
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

    function candidatas($dt, $sem)
    {
        $curso_pref = $dt['dc_curso'];
        $id_doc = $dt['id_dc'];

        $op1 = get("opt1");
        $op2 = get("opt2");
        $act = get("action");
        $turm = get("turma");

        if ($act != '') {
            if ($act == 'Onerar >>') {
                $this->register($id_doc, $sem, $op1, $turm);
            }

            if ($act == '<<< Desonerar') {
                $this->remove($id_doc, $sem, $op2);
            }
        }

        $dt = $this
            //->select('id_di, di_curso, di_etapa,di_disciplina, di_codigo')
            ->join('curso', 'di_curso = id_c')
            ->join('encargos', '((e_semestre = ' . $sem . ') and (e_disciplina = id_di))', 'LEFT')

            //->where('di_curso',$curso_pref)
            ->orderBy('c_curso, di_etapa')
            ->findAll();

        $opt1 = [];
        $opt2 = [];
        $xet = 0;
        $xcurso = '';
        foreach ($dt as $id => $line) {
            $curso = $line['c_curso'];
            $turmaN = $line['e_turma'];

            $et = $line['di_etapa'];
            if ($et != $xet) {
                $etapa = 'Etapa ' . $et;
                $opt[$etapa] = [];
                $xet = $et;
            }
            $codigo = $line['id_di'];
            $name = $line['di_codigo'] . ' ' . nbr_title($line['di_disciplina']);

            $mult = $line['di_multi'];

            if ($line['e_docente'] == $id_doc) {
                if ($mult == true) {
                    $opt1[$curso . '-' . $etapa][$codigo] = $name;
                    $opt2[$curso . '-' . $etapa][$codigo . '.' . $turmaN] = $name . ' (' . $line['e_turma'] . ')';
                } else {
                    $opt2[$curso . '-' . $etapa][$codigo] = $name . ' (' . $line['e_turma'] . ')';
                }
            } else {
                if ($line['e_docente'] > 0) {
                } else {
                    $opt1[$curso . '-' . $etapa][$codigo] = $name;
                }
            }
        }

        /*************/
        $turma = [];
        $turma['U'] = 'Única';
        $turma['1'] = 'Turma 1';
        $turma['2'] = 'Turma 2';
        $turma['3'] = 'Turma 3';

        $sx = form_open(PATH . 'dci/docentes/view/' . $id_doc);

        $sa = form_dropdown('opt1', $opt1, $op1, ['size' => 20, 'class' => 'full']);
        $sb = form_dropdown('opt2', $opt2, $op2, ['size' => 20, 'class' => 'full']);

        $act = '';
        $act .= form_label('Turma');
        $act .= form_dropdown('turma', $turma, $turm, ['size' => 1, 'class' => 'full']);
        $act .= '<br><br>';
        $act .= '<input type="submit" class="btn btn-secondary full" name="action" value="Onerar >>">';
        $act .= '<br><br>';
        $act .= '<input type="submit" class="btn btn-secondary full" name="action" value="<<< Desonerar">';

        $sx .= '<table><tr valign="top">
                            <td width="45%">' . $sa . '</td>
                            <td width="10%" class="p-3">' . $act . '</td></td>
                            <td width="45%">' . $sb . '</td>
                        </tr></table>';
        $sx .= form_close();

        return $sx;
    }

    function register($pro, $sem, $disc, $turm)
    {
        $Encargos = new \App\Models\Dci\Encargos();
        $Encargos->register($pro, $sem, $disc, $turm);
    }

    function remove($pro, $sem, $disc)
    {
        $Encargos = new \App\Models\Dci\Encargos();
        $Encargos->remove($pro, $sem, $disc);
    }

    function show_semestre($id = 0, $curso = 0)
    {
        $sem = 1;
        $dt = $this
            ->join('curso','di_curso = id_c')
            ->join('encargos','e_disciplina = id_di')
            ->join('docentes', 'e_docente = id_dc','LEFT')
            ->join('horario', 'e_horario = id_h','LEFT')
            ->where('id_di > 0')
            ->where('e_semestre',1)
            ->orderBy('id_c,di_etapa,h_hora_ini,di_codigo')
            ->findAll();


        $xetapa = '';
        $xcurso = '';
        //$h = ['08h30','09h20','']

        $sx = '';
        $sa = '';

        $w = [];
        foreach($dt as $id=>$line)
            {
                $sx = '';
                $xcurso = $line['di_curso'];
                $nome = $line['di_disciplina'];
                $etapa = $line['di_etapa'];
                $h_hora_ini = $line['h_hora_ini'];
                $h_hora_fim = $line['h_hora_fim'];
                $h_dia = $line['h_dia'];
                $docente = $line['dc_nome'];
                $curso = $line['c_curso'];

                $hora = $line['id_h'];
                if ($hora == '')
                    {
                        $link = '<a href="'.PATH.'/dci/encargos/edit/'.$line['id_e'].'" target="_blank">';
                        $linka = '</a>';
                        $sa .= '<li>'. $link. $nome. $linka. ' ('.$etapa.'º Etapa)</li>';
                    } else {
                        $link = '<a href="' . PATH . '/dci/encargos/edit/' . $line['id_e'] . '" target="_blank">';
                        $linka = '</a>';
                        $w[$curso][$etapa][$h_dia][$h_hora_ini] = '<p>'.$link.$nome.$linka. '</p><p class="italic">'.nbr_author($docente,7). '</p>';
                    }
                    //pre($line);
            }
        $sc = '<ul>'.$sa.'</ul>'.$sx;
        $wd = ['SEG','TER','QUA','QUI','SEX','SAB'];
        $hr = ['08h30','09h20','====','10h30','11h20','12h10','13h30','14h20','---','15h30','16h20','18h30','19h20','---','20h30','21h20'];
        $sb = '';
        $sx = '';


        foreach ($w as $curso => $d1) {
            $sb .= h($curso, 2);
            foreach ($d1 as $etapa => $d2) {
                $sb .= '<tr><td colspan=6>'.h($etapa . 'º etapa', 4).'</td></tr>';
                foreach ($wd as $id => $day) {
                    if (isset($d2[$day]))
                        {
                            $d3 = $d2[$day];
                            foreach ($d3 as $hora => $curso) {
                                $sb .= '<td width="16%" valign="top" class="border border-secondary p-2">';
                                $sb .= $day;

                                $sb .= '<br><span style="font-size: 0.75em;">'.$hora . ' - ' . $curso . '</span>';
                                $sb .= '</td>';
                            }
                        } else {
                                $sb .= '<td width="16%" valign="top">'.$day.'</td>';
                        }
                }
            }
        }

            $sx = '<table border=1 style="border: 1px solid #000;"><tr>'.$sb.'</tr></table>';
            $sx .= $sc;
        return $sx;
    }

    function list()
        {
            $dt = $this->findAll();
            pre($dt);
        }


}
