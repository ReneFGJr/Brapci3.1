<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Docentes extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'docentes';
    protected $primaryKey       = 'id_dc';
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
                    case 'view':
                        $sx .= $this->view($d2);
                        break;
                    default:
                        $sx .= bs($this->list($d2));
                        break;
                }
            return $sx;
        }

    function report_encargos($sem)
        {
            $sx = '';
            $dt = $this
                ->select('id_e,dc_nome,c_curso,e_turma,di_disciplina,di_codigo,di_tipo,di_crd,di_ch,di_ext,di_tipo,c_bg')
                ->join('curso','id_c = dc_curso')
                ->join('encargos', 'e_docente = id_dc and dc_curso = e_curso and e_semestre = '.$sem)
                ->join('disciplinas', 'e_disciplina = id_di')
                ->orderBy('dc_nome, di_disciplina')
                ->findAll();

            $enc = [];
            foreach($dt as $id=>$line)
                {
                    $docente = $line['dc_nome'];
                    $ide = $line['id_e'];

                    $enc[$docente][$ide] = $line;
                }

            $sx = '<table class="table full" style="font-size: 0.7em;">';
            $sx .= '<tr>
            <th>Docente</th>
            <th>Disciplina - Curso</th>
            <th>Curso</th>
            <th>Turma</th>
            <th>Crd.</th>
            <th>Hora/Sala</th>
            <th>Total Crd.</th>
            </tr>';
            foreach($enc as $nome=>$dados)
                {
                    $sx .= '<tr>';
                    $sx .= '<td width="30%" rowspan="$x$" style="background-color: $cor$;" class="border border-secondary fw-bold">'.$nome. '</td>';

                    /* Disciplinas */
                    $sd = '';
                    $nc = 0;
                    $crt = 0;
                    $bg = 'FF0000';
                    foreach($dados as $idd=>$ddados)
                        {
                            $nc++;
                            $crt = $crt + $ddados['di_crd'];
                            if ($nc > 1)
                                {
                                    $sx .= '<tr>';
                                }
                            $sx .= '<td width="35%" style="background-color: $cor$;" class="border border-secondary">'.$ddados['di_codigo'].' - '.$ddados['di_disciplina'].' <sup>'.$ddados['di_tipo'].'</td>';
                            $sx .= '<td width="10%" style="background-color: $cor$;" class="border border-secondary text-center">' . $ddados['c_curso'].'</sup></td>';
                            $sx .= '<td width="2%" class="border border-secondary text-center">' . $ddados['e_turma'] . '</td>';
                            $sx .= '<td width="2%" class="border border-secondary  text-center">' . $ddados['di_crd'] . '</td>';
                            $sx .= '<td width="10%" class="border border-secondary  text-center">' . '' . '</td>';
                            if ($nc > 1) {
                                $sx .= '</tr>';
                            } else {
                                $sx .= '<td rowspan="$x$" class="text-center border border-secondary h3">$t$</td>';
                            }

                        }
                    $sx .= '</tr>';
                    $sx = troca($sx,'$x$',$nc);
                    $sx = troca($sx, '$cor$', $bg);
                    $sx = troca($sx, '$t$', $crt);
                    //pre($dados);
                }
            $sx .= '</table>';
            return $sx;
        }

    function view($id)
        {
            $sem = 1;
            $dt = $this->find($id);
            $sx = '';
            $sx .= $this->header($dt);

            $Encargos = new \App\Models\Dci\Encargos();

            $sx .= $Encargos->view($id,$sem);

            $sx .= bs($Encargos->indications($dt,$sem));
            return $sx;
        }

    function header($dt)
        {
            $sx = bs(bsc(h($dt['dc_nome']).'<hr>', 12));
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
