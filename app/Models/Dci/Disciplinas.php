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
                        $w[$curso][$etapa][$h_dia][$h_hora_ini] = '<p>'.$nome. '</p><p class="italic">'.nbr_author($docente,7). '</p>';
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
