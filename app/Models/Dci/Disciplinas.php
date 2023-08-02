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
        'id_di', 'di_curso', 'di_disciplina',
        'di_multi', 'di_codigo', 'di_etapa',
        'di_tipo', 'di_crd', 'di_ch',
        'di_ext'
    ];
    protected $typeFields    = [
        'hidden', 'sql:id_c:c_curso:curso', 'string',
        'string', 'string', 'op:1&1º:2&2º:3&3º:4&4º:5&5º:6&6º:7&7º:8&8º:9&Eletiva',
        'op:Obrigatória&Obrigatória:Eletiva&Eletiva',
        'op:1&1:2&2:3&3:4&4:5&5:6&6',
        'op:15&15:30&30:45&45:60&60:75&75:90&90',
        'op:0&0:15&15:30&30:45&45:60&60:75&75:90&90',
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

    var $path = PATH.'/dci/disciplinas/';
    var $path_back = PATH . '/dci/disciplinas/';

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
            $etapa = '';

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
        $sem = 2;
        $Cursos = new \App\Models\Dci\Cursos();
        $cursos = $Cursos->select();
        $curso = get("curso");
        $sf = '';
        $m = [];
        $m['Departamento'] = PATH . '/dci';
        $m['Semestre'] = PATH . '/dci/semestre/1';
        $sf .= breadcrumbs($m);
        $sf .= form_open();
        $sf .= form_dropdown('curso',$cursos,$curso);
        $sf .= form_submit('action',lang('brapci.submit'));
        $sf .= form_close();
        $sem = 1;
        $this
            ->join('curso','di_curso = id_c')
            ->join('encargos','e_disciplina = id_di')
            ->join('docentes', 'e_docente = id_dc','LEFT')
            ->join('horario', 'e_horario = id_h','LEFT')
            ->where('id_di > 0')
            ->where('e_semestre',$sem);
        if ($curso != '')
            {
                $this->where('e_curso',$curso);
            }
        $this->orderBy('id_c,di_etapa,h_hora_ini,di_codigo');
        $dt = $this->findAll();


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
                $codigo = $line['di_codigo'];
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
                        $w[$curso][$etapa][$h_dia][$h_hora_ini.'-'.$h_hora_fim] = '<p>'.$link. $codigo.' -'.$nome.$linka. '<br><span class="italic">'.nbr_author($docente,7). '</span></p>';
                    }
                    //pre($line);
            }
        $sc = '<ul>'.$sa.'</ul>'.$sx;
        $wd = ['SEG','TER','QUA','QUI','SEX'];
        $hr = ['08h30','09h20','====','10h30','11h20','12h10','13h30','14h20','---','15h30','16h20','18h30','19h20','---','20h30','21h20'];
        $sb = '';
        $sx = '';
        $sa = '';


        foreach ($w as $curso => $d1) {
            $sb .= '<tr><td colspan=10>'.h($curso, 2). '</td></tr>';
            foreach ($d1 as $etapa => $d2) {
                $etapa_label = $etapa . 'º etapa';
                if ($etapa == 9) { $etapa_label = 'Eletiva'; }
                $sb .= '<tr><td colspan=10>'.h($etapa_label, 4).'</td></tr>';
                $sb .= '<tr>';

                foreach ($wd as $id => $day) {

                    $sb .= '<td width="16%" valign="top" class="border border-secondary p-2">';
                    $sb .= $day;

                    if (isset($d2[$day]))
                        {
                            $d3 = $d2[$day];
                            foreach ($d3 as $hora => $curso) {
                                $sb .= '<br><span style="font-size: 0.75em;">'.$hora  . $curso . '</span>';
                            }
                        }
                }
                $sb .= '</tr>';
            }
        }

            $sa = '<table border=1 style="border: 1px solid #000;">';
            $sa .= '<tr>'.$sb.'</tr>';
            $sa .= '</table>';
            $sx .= bs(bsc($sf,12, 'nopr d-print-none').bsc($sa,12).bsc($sc,12));
        return $sx;
    }

    function show_semestre_row($id = 0, $curso = 0)
    {
        $sem = 2;
        $sx = '';
        $m = [];
        $m['Departamento'] = PATH . '/dci';
        $m['Semestre'] = PATH . '/dci/semestre/2';
        $sf = breadcrumbs($m);
        $sem = 1;
        $this
            ->join('curso', 'di_curso = id_c')
            ->join('encargos', 'e_disciplina = id_di')
            ->join('docentes', 'e_docente = id_dc', 'LEFT')
            ->join('horario', 'e_horario = id_h', 'LEFT')
            ->where('id_di > 0')
            ->where('id_h > 0')
            ->where('e_semestre', $sem)
            ->orderBy('id_c,di_codigo,di_etapa,h_hora_ini');
        $dt = $this->findAll();


        $w = array(
            'di_codigo'=>'text-center',
            'di_disciplina' => 'text-start',
            'dc_nome' => 'text-start',
            'h_dia' => 'text-center',
            'h_hora_ini' => 'text-center',
            'h_hora_fim' => 'text-center',
            'di_ch' => 'text-center'
            );
        $sh = '<tr>
                <th>código</th>
                <th>disciplina</th>
                <th>professor</th>
                <th>dia</th>
                <th>início</th>
                <th>fim</th>
                <th>CH</th>
                </tr>';
        $xcurso = '';
        $sx .= '<table width="100%" style="font-size: 0.8em;">';
        foreach ($dt as $id => $line) {
            $curso = $line['c_curso'];
            if ($curso != $xcurso)
                {
                    $xcurso = $curso;
                    $sx .= '<tr><th colspan=10" class="pt-3 h3">'.$curso.'</tr>';
                    $sx .= $sh;
                }
            $sx .= '<tr>';
            foreach($w as $fld=>$class)
                {
                    $sx .= '<td class="border border-secondary p-1 '.$class.'">'.$line[$fld].'</td>';
                }
            $sx .= '</tr>'.cr();
        }
        $sx .= '</table>';
        $sx = bs(bsc($sf,12).bsc($sx,12));
        return $sx;
    }

    function edit($id)
        {
            $this->id = $id;
            $sx = bs(bsc(form($this),12));
            return $sx;
        }

    function list()
        {
            $sx = tableview($this);
            return $sx;
        }


}
