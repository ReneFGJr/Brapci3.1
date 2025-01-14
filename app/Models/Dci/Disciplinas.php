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
        'id_di',
        'di_curso',
        'di_disciplina',
        'di_multi',
        'di_codigo',
        'di_etapa',
        'di_tipo',
        'di_crd',
        'di_ch',
        'di_ext'
    ];
    protected $typeFields    = [
        'hidden',
        'sql:id_c:c_curso:curso',
        'string',
        'string',
        'string',
        'op:1&1º:2&2º:3&3º:4&4º:5&5º:6&6º:7&7º:8&8º:9&Eletiva',
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

    var $path = PATH . '/dci/disciplinas/';
    var $path_back = PATH . '/dci/disciplinas/';
    var $id;

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '')
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
                $sx .= $this->mark($d2, $d3, $d4, $d5);
                break;
            case 'view':
                $sx .= $this->viewid($d2, $d3, $d4);
                break;
            default:
                $sx .= bs($this->list($d2));
                break;
        }
        return $sx;
    }

    function mostraDisciplina($id)
    {
        $Semestre = new \App\Models\Dci\Semestre();
        $sem = $Semestre->getSemestre('ID');

        $dt = $this
            ->join('encargos', 'id_di = e_disciplina and e_semestre = ' . $sem, 'left')
            ->join('docentes', 'e_docente = id_dc', 'left')
            ->join('horario_dia', 'e_dia = hd_dia', 'left')
            ->join('horario_hora', 'e_horario = id_hora', 'left')
            ->where('di_curso', $id)
            ->orderBy('di_etapa, di_codigo')
            ->findAll();

        $et = '';
        $sx = '<table class="table full">';
        $xdisciplina = '';
        foreach ($dt as $id => $line) {
            $etapa = $line['di_etapa'];
            if ($etapa != $et) {
                $etapaLabel = 'Etapa ' . $etapa;
                $sx .= '<tr><td colspan=10">' . h($etapaLabel, 6) . '</td></tr>';
                $et = $etapa;
            }

            $act = '<a href="' . base_url('/dci/encargos/edit/0?e_semestre=' . $sem . '&e_turma=U&e_disciplina=' . $line['id_di']) . '"><i class="bi bi-person-plus"></i></a>';

            if ($xdisciplina != $line['di_disciplina']) {
                $sx .= '<tr>';
                $sx .= '<td width="10%" class="text-center">' . $line['di_codigo'] . '</td>';
                $sx .= '<td width="60%">' . $line['di_disciplina'];
                /* Docentes */


                if ($line['e_semestre'] == $sem) {

                    /****************************************** */
                    foreach ($dt as $idd => $lined) {
                        if ($lined['e_disciplina'] == $line['e_disciplina']) {
                            $link = '<a href="' . base_url(PATH . 'dci/encargos/edit/' . $lined['id_e']) . '">';
                            $linka = '</a>';
                            $sx .= '<br><span class="fst-italic ms-3">' . $link . nbr_author($lined['dc_nome'], 7) . $linka . '</span>';
                            if ($lined['e_credito'] == 0) {
                                $sx .= ' (<span style="color: red">' . $lined['e_credito'] . ' crd</span>)';
                            } else {
                                $sx .= ' (' . $lined['e_credito'] . ' crd)';
                            }

                            $sx .= ' - ' . $lined['hd_dia_name'] . ' ' . $lined['hora_inicio'];
                        }
                    }
                }
                $sx .= '</td>';
                $sx .= '<td width="10%" class="text-center">' . $line['di_tipo'] . '</td>';
                $sx .= '<td width="5%" class="text-center">' . $line['di_crd'] . '</td>';
                $sx .= '<td width="5%" class="text-center">' . $line['di_ch'] . '</td>';
                $sx .= '<td width="5%" class="text-center">' . $line['di_ext'] . '</td>';
                $sx .= '<td width="5%" class="text-center">' . $act . '</td>';
                $sx .= '</tr>';
                $xdisciplina = $line['di_disciplina'];
            }
        }
        $sx .= '</table>';
        return bs(bsc($sx, 12));
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
        $Semestre = new \App\Models\Dci\Semestre();
        $sem = $Semestre->getSemestre('ID');

        $Cursos = new \App\Models\Dci\Cursos();
        $cursos = $Cursos->select();
        $curso = get("curso");
        $sf = '';
        $m = [];
        $m['Departamento'] = PATH . '/dci';
        $m['Semestre'] = PATH . '/dci/semestre/' . $sem;
        $sf .= breadcrumbs($m);
        $sf .= form_open();
        $sf .= form_dropdown('curso', $cursos, $curso);
        $sf .= form_submit('action', lang('brapci.submit'));
        $sf .= form_close();

        $this
            ->join('encargos', 'e_disciplina = id_di')
            ->join('curso', 'di_curso = id_c')
            ->join('docentes', 'e_docente = id_dc', 'LEFT')
            ->join('horario_dia', 'e_dia = hd_dia', 'LEFT')
            ->join('horario_hora', 'e_horario = id_hora', 'LEFT')
            ->where('id_di > 0')
            ->where('e_semestre', $sem);
        if ($curso != '') {
            $this->where('di_curso', $curso);
        }
        $this->orderBy('di_curso, di_etapa, hd_dia, hora_inicio, di_codigo');
        $dt = $this->findAll();

        $curso = [];


        $w = [];
        foreach ($dt as $id => $line) {
            $xcurso = $line['c_curso'];
            $xetapa = $line['di_etapa'];

            if (!isset($curso[$xcurso])) {
                $curso[$xcurso] = [];
            }

            if (!isset($curso[$xcurso][$xetapa])) {
                $curso[$xcurso][$xetapa] = [];
            }

            if (!isset($curso[$xcurso][$xetapa][$line['hd_dia_name']])) {
                $curso[$xcurso][$xetapa] = ['SEG' => [], 'TER' => [], 'QUA' => [], 'QUI' => [], 'SEX' => []];
            }

            if (!isset($curso[$xcurso][$xetapa][$line['hd_dia_name']][$line['hora_inicio']])) {
                $curso[$xcurso][$xetapa][$line['hd_dia_name']][$line['hora_inicio']] = [];
            }

            $curso[$xcurso][$xetapa][$line['hd_dia_name']][$line['hora_inicio']][] = $line;
        }
        $sx = '';
        foreach ($curso as $ncurso => $etapa) {
            $sx .= '<table class="table">';
            $sx .= '<tr><th colspan=10 class="h3">' . $ncurso . '</th></tr>';
            foreach ($etapa as $etapa => $dia) {
                $sx .= '<tr><th colspan=10 class="h4 text-center">Etapa ' . $etapa . '</th></tr>';
                $sh = '<tr>';
                $sc = '<tr>';
                foreach ($dia as $dia => $hora) {
                    $sh .= '<th width="20%">' . $dia . '</th>';
                    $sc .= '<td style="font-size: 0.7em;">';

                    foreach ($hora as $hora => $disc) {
                        $sc .= h($hora, 6);
                        $xcod = '';
                        foreach ($disc as $idd => $line) {
                            $cod = $line['di_codigo'];
                            $link = '<a href="' . base_url('/dci/encargos/edit/' . $line['id_e']) . '" target="_blank">';
                            $linka = '</a>';
                            if ($xcod != $cod)
                            {
                                $xcod = $cod;
                                $sc .= $line['di_codigo'];
                                $sc .= ' - ';
                                $sc .= $line['di_disciplina'];
                                $sc .= '<br><span class="small">' . $line['di_tipo'] . '</span>';
                                $sc .= '<br>'.$line['e_credito'].'/'.$line['di_crd'].' crd';
                                $sc .= '<li class="small"><i>'. $link.$line['dc_nome']. $linka.'</i></li>';
                                #$sc .= '<td><a href="'.base_url(PATH.'dci/encargos/edit/0?e_semestre='.$sem.'&e_turma=U&e_disciplina='.$line['id_di']).'"><i class="bi bi-person-plus"></i></a></td>';
                            } else {
                                $sc .= '<li class="small"><i>' . $link . $line['dc_nome'] . $linka . '</i></li>';
                            }
                            #$sc .= '<td><a href="'.base_url(PATH.'dci/encargos/edit/0?e_semestre='.$sem.'&e_turma=U&e_disciplina='.$line['id_di']).'"><i class="bi bi-person-plus"></i></a></td>';
                        }
                    }
                    $sx .= '</td>';
                }
                $sc .= '</tr>';
                $sh .= '</tr>';
                $sx .= $sh;
                $sx .= $sc;
            }
            $sx .= '</table>';
        }
        return bs(bsc($sx,12));
    }

    function show_semestre_disciplinas($id = 0, $curso = 0)
    {
        $sem = 1;
        $sx = '';
        $m = [];
        $m['Departamento'] = PATH . '/dci';
        $m['Semestre'] = PATH . '/dci/semestre/2';
        $sf = breadcrumbs($m);
        $this
            ->join('curso', 'di_curso = id_c')
            ->join('encargos', 'e_disciplina = id_di')
            ->join('docentes', 'e_docente = id_dc', 'LEFT')
            ->join('horario', 'e_horario = id_h', 'LEFT')
            ->where('id_di > 0')
            //->where('id_h > 0')
            ->where('e_semestre', $sem)
            ->orderBy('di_disciplina,id_c,di_codigo,di_etapa,h_hora_ini');
        $dt = $this->findAll();

        $w = array(
            'di_codigo' => 'text-center',
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

            $sx .= '<tr>';
            foreach ($w as $fld => $class) {
                $sx .= '<td class="border border-secondary p-1 ' . $class . '">' . $line[$fld] . '</td>';
            }
            $sx .= '</tr>' . cr();
        }
        $sx .= '</table>';
        $sx = bs(bsc($sf, 12) . bsc($sx, 12));
        return $sx;
    }

    function show_semestre_row($id = 0, $curso = 0)
    {
        $sem = 3;
        $sx = '';
        $m = [];
        $m['Departamento'] = PATH . '/dci';
        $m['Semestre'] = PATH . '/dci/semestre/2';
        $sf = breadcrumbs($m);
        $this
            ->join('curso', 'di_curso = id_c')
            ->join('encargos', 'e_disciplina = id_di')
            ->join('docentes', 'e_docente = id_dc', 'LEFT')
            ->join('horario_dia', 'e_dia = id_hd', 'LEFT')
            ->join('horario_hora', 'e_horario = id_hora', 'LEFT')
            ->where('id_di > 0')
            //->where('id_h > 0')
            ->where('e_semestre', $sem)
            ->orderBy('id_c,di_codigo,di_etapa,hora_inicio');
        $dt = $this->findAll();

        $w = array(
            'di_codigo' => 'text-center',
            'di_disciplina' => 'text-start',
            'dc_nome' => 'text-start',
            'hd_dia_name' => 'text-center',
            'hora_inicio' => 'text-center',
            'di_ch' => 'text-center'
        );
        $sh = '<tr>
                <th>código</th>
                <th>disciplina</th>
                <th>professor</th>
                <th>dia</th>
                <th>início</th>
                <th>CH</th>
                </tr>';
        $xcurso = '';
        $sx .= '<table width="100%" style="font-size: 0.8em;">';
        foreach ($dt as $id => $line) {
            $curso = $line['c_curso'];
            if ($curso != $xcurso) {
                $xcurso = $curso;
                $sx .= '<tr><th colspan=10" class="pt-3 h3">' . $curso . '</tr>';
                $sx .= $sh;
            }
            $sx .= '<tr>';
            foreach ($w as $fld => $class) {
                $sx .= '<td class="border border-secondary p-1 ' . $class . '">' . $line[$fld] . '</td>';
            }
            $sx .= '</tr>' . cr();
        }
        $sx .= '</table>';
        $sx = bs(bsc($sf, 12) . bsc($sx, 12));
        return $sx;
    }

    function edit($id)
    {
        $this->id = $id;
        $sx = bs(bsc(form($this), 12));
        return $sx;
    }

    function list()
    {
        $sx = tableview($this);
        return $sx;
    }
}
