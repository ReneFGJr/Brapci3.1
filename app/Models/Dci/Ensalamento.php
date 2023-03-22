<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Ensalamento extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'sala_aula_hoario_sem';
    protected $primaryKey       = 'id_sahs';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_sahs', 'sa_encargo', 'sa_sala', 'sa_weekday'
        , 'sa_hi', 'sa_hf', 'sa_mi', 'sa_mf', 'sa_status'
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

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $sx = '';
        switch ($d1) {

            case 'view':
                $sx .= $this->viewid($d2,$d3,$d4,$d5);
                break;
            default:
                $sx .= bs($this->list($d2));
                break;
        }
        return $sx;
    }

    function register($ide,$disc,$sala,$dia,$hi)
    {
        $Disciplinas = new \App\Models\Dci\Disciplinas();
        $dt = $Disciplinas->find($ide);
        $wd = $this->dias();

        if (!isset($wd[$dia]))
            {
                echo "OPS Dia da semana não existe ".$dia;
                exit;
            }

        $cred = $dt['di_crd'];
        $hora = explode('h',$hi);
        $horaf = $hora;
        for($r=0;$r < $cred;$r++)
            {
                $horaf[1] = $horaf[1] + 50;
                if ($horaf[1] > 59)
                    {
                        $horaf[1] = $horaf[1] - 60;
                        $horaf[0] = $horaf[0] + 1;
                    }
            }

        $livre = true;
        $dr = $this->where('sa_encargo',$disc)->findAll();
        if (count($dr) == 0) { $livre = false; }

        $dr = $this
                ->where('sa_sala', $ide)
                ->where('sa_weekday', $dia)
                ->where('sa_hi', $hora[0])
                ->findAll();
        if (count($dr) == 0) {
            $livre = false;
        }

        if ($livre)
        {
            $data = [];
            $data['sa_encargo'] = $disc;
            $data['sa_sala'] = $ide;
            $data['sa_weekday'] = $dia;
            $data['sa_hi'] = $hora[0];
            $data['sa_hf'] = $hora[1];
            $data['sa_mi'] = $horaf[0];
            $data['sa_mf'] = $horaf[1];
            $data['sa_status'] = 1;
            $sx .= "Inserted";
            $this->set($data)->insert();
        } else {
            $sx = bs(bsc(bsmessage("ERRO, sala já ocupada",3)),12);
        }

        return $sx;
    }

    function mark($ids,$d2,$d3,$d4)
        {
            $sx = '';
            $sem = 1;

            $sx .= bs(bsc(h($d2.'-'.$d3,4)),12);

            $act = get("action");
            $disciplina = get("disciplina");
            if (($act != '') and ($disciplina != ''))
                {
                    $sala = $ids;
                    $dia = $d2;
                    $hora = $d3;
                    $sx .= $this->register($ids,$disciplina,$sala,$dia,$hora);
                }

            $Encargos = new \App\Models\Dci\Encargos();
            $dt = $Encargos
                ->join('disciplinas','e_disciplina = id_di')
                ->join('docentes', 'e_docente = id_dc')
                ->join('curso', 'e_curso = id_c')
                ->join('sala_aula_hoario_sem', 'id_e = sa_encargo','LEFT')
                ->where('e_semestre',$sem)
                ->orderBy('c_curso, di_etapa')
                ->findAll();

            $disciplinas = [];
            foreach($dt as $id=>$line)
                {
                    $etapa = $line['di_etapa'];
                    $curso = $line['c_curso'];
                    if ($etapa < 9)
                        {
                            $curso .= ' - Etapa '.$etapa;
                        } else {
                            $curso .= ' - Eletiva';
                        }
                    $name = $line['di_codigo'] . ' - '. $line['di_disciplina'];

                    $ide = $line['id_e'];
                    if ($line['id_sahs']==0)
                        {
                            $disciplinas[$curso][$ide] = $name;
                        }

                }
            $sala = get("sala");

            $sx .= form_open(PATH . '/dci/salas/mark/'.$ids.'/'.$d2.'/'.$d3);
            $sa = form_dropdown('disciplina', $disciplinas, $disciplina, ['size' => 20, 'class' => 'full']);
            $sb = form_submit(array("name"=>'action','value'=>'Alocar','Class'=>'btn btn-secondary full'));
            $sx .= bs(bsc($sa,10).bsc($sb,2));
            $sx .= form_close();

            return $sx;
        }

    function dias()
        {
            $hora = ['08h30', '10h30', '09h30', '13h30', '18h30'];
            $ds = ['SEG' => $hora, 'TER' => $hora, 'QUA' => $hora, 'QUI' => $hora, 'SEX' => $hora, 'SAB' => $hora];
            return $ds;
        }

    function viewid($idx)
        {
            $dt = $this
                    ->where('sa_sala',$idx)
                    ->findAll();

            $dss = [];
            foreach($dt as $id=>$line)
                {
                    $dia = $line['sa_weekday'];
                    $hora = strzero($line['sa_hi'],2).'h'.strzero($line['sa_hf'],2);
                    $disc = 'XXXX';
                    $dss[$dia][$hora] = $disc;
                }
            $sx = bsc('Ensalamento',12);
            $ds = $this->dias();

            foreach($ds as $dia=>$dados)
                {
                    $sa = h($dia,4);

                    $sh = '';

                    foreach($dados as $id=>$hora)
                        {
                            $link = '<a href="'.PATH.'/dci/salas/mark/'.$idx.'/'.$dia.'/'.$hora.'">';
                            $linka = '</a>';
                            $sh .= $link.$hora.$linka;

                            /****************** */
                            if (isset($dss[$dia][$hora]))
                                {
                                    $sa .= $dss[$dia][$hora];
                                    $sa .= '<br>';
                                }
                            $sh .= '<hr>';
                        }
                    $sx .= bsc($sa.$sh,2);
                }

            $sx = bs($sx);

            return $sx;

        }

    function header($dt)
        {
            $sx = '';
            $sx .= h($dt['sala_predio'].' - '.$dt['sala_nome'],2);
            $sx = bs(bsc($sx,12));
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
