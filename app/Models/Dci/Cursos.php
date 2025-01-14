<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Cursos extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'curso';
    protected $primaryKey       = 'id_c';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_c', 'c_curso', 'c_departamento', 'c_bg'];
    protected $tpeFields    = ['hidden', 'string', 'string', 'string', 'string'];

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

    var $path = PATH . '/dci/cursos';
    var $path_back = PATH . '/dci/cursos';

    function select()
    {
        $dt = $this->findAll();
        $Cursos = [];
        foreach ($dt as $id => $line) {
            $Cursos[$line['id_c']] = $line['c_curso'];
        }
        return $Cursos;
    }

    function index($d1, $d2, $d3, $d4)
    {
        $sx = '';

        $mn = [];
        $mn['Departamento'] = base_url('/dci/');
        $mn['Cursos'] = base_url('/dci/cursos');
        $sx .= breadcrumbs($mn);

        switch ($d1) {
            case 'viewid':
                $sx = $this->view($d2);
                break;
            case 'import':
                $sx = $this->inport($d2);
                break;
            default:
                $st = tableview($this);
                $sx .= bs(bsc($st));
        }
        return $sx;
    }

    function inport($curso)
    {
        $dt = $this->find($curso);
        $ct = file_get_contents($dt['c_url_ufrgs']);
        $ct = troca($ct, 'text-align: left">', '">]');
        $ct = troca($ct, 'text-align: center">', '">]');
        $ct = substr($ct, strpos($ct, '<legend class="" style="font-size: 12px;font-weight: bold">Etapa'),strlen($ct));
        $ct = substr($ct,0,strpos($ct, '<legend class="" style="font-size: 12px;font-weight: bold">Libera'));
        $ct = troca($ct, '<br/>','[XX]');

        $ct = strip_tags($ct);
        $ct = utf8_encode($ct);

        $cc = [];
        for ($r = 1; $r < 10; $r++) {
            $cr = $this->cursosImporta('Etapa ' . $r, $ct, $r, $curso);
            $cc = array_merge($cc, $cr);
        }

        $cr = $this->cursosImporta('Sem Etapa', $ct, '9', $curso);
        $cc = array_merge($cc, $cr);

        $Disciplinas = new \App\Models\Dci\Disciplinas();
        $sx = '';
        foreach ($cc as $id => $line) {
            $dt = $Disciplinas
                ->where('di_curso', $curso)
                ->where('di_codigo', $line['di_codigo'])
                ->first();

            if ($dt != []) {
                $Disciplinas
                    ->set($line)
                    ->where('di_curso', $curso)
                    ->where('di_codigo', $line['di_codigo'])
                    ->update();
                $sx .= '<li><tt>' . $dt['di_codigo'] . ' - ' . $dt['di_disciplina'] . ' <b>Atualizado</b></tt></li>';
            } else {
                $Disciplinas->set($line)->insert();
                $sx .= '<li><tt>' . $line['di_codigo'] . ' - ' . $line['di_disciplina'] . ' <b>Inserido</b></tt></li>';
            }
        }
        return bs(bsc($sx));
    }

    function cursosImporta($t, $txt, $etapa = '', $curso = '')
    {
        $t = substr($txt, strpos($txt, $t) + strlen($t), strlen($txt));
        $t = troca($t, '&nbsp;', ' ');
        //  $t = troca($t, ']', '');
        if (strpos($t, 'Diversificada Complementar'))
            {
                $t = substr($t, 0, strpos($t, 'Diversificada Complementar')-40);
            }
        if (strpos($t, 'Liberada')) {
            $t = substr($t, 0, strpos($t, 'Liberada'));
        }
        $t = troca($t, 'Liberações', '');


        if ($etapa != 9) {
            if (strpos($t, 'Etapa')) {
                $t = substr($t, 0, strpos($t, 'Etapa'));
            } else {
                echo "SEM ETAPA $etapa";
                exit;
            }
        } else {
            if (strpos($t, 'Sem Etapa')) {
                $t = substr($t,strpos($t, 'Sem Etapa'),strlen($t));
            }
        }

        $te = 'Carga Horária Extensão (CHE)';
        $t = substr($t, strpos($t, $te) + strlen($te), strlen($t));
        $te = explode("\n", $t);

        $tr = [];
        foreach ($te as $id => $line) {


            $ln = trim($line);
            $ln = troca($ln, '[XX]', '');
            if (strpos($ln, '-'))
                {
                    $ln = substr($ln,0,strpos($ln, '-'));
                }

            if ($ln != '') {
                array_push($tr, $ln);
            }
        }
        $cr = [];

        for ($r = 0; $r < count($tr) - 1; $r += 6) {
            $nome = $tr[$r + 1];
            $nome = troca($nome, ']', '');
            $c = [];

            if (isset($tr[$r + 5])) {
                $c['di_curso'] = $curso;
                $c['di_etapa'] = $etapa;
                $c['di_codigo'] = troca(trim($tr[$r]), ']', '');
                $c['di_disciplina'] = $nome;
                $c['di_tipo'] = troca($tr[$r + 2], ']', '');
                $c['di_crd'] = troca($tr[$r + 3], ']', '');
                $c['di_ch'] = troca($tr[$r + 4], ']', '');
                if (isset($tr[$r + 5])) {
                    $c['di_ext'] = troca($tr[$r + 5], ']', '');
                } else {
                    $c['di_ext'] = -1;
                }

                if (strlen($c['di_codigo']) == 8) {
                    array_push($cr, $c);
                }
            } else {
                echo h("ERRO====>" . $r);
                echo h('Etapa ' . $etapa);
                pre($tr);
            }
        }
        return $cr;
    }

    function view($id)
    {
        $dt = $this->find($id);
        $sx = '';
        $sx .= view('DCI/curso_header', $dt);
        if ($dt['c_url_ufrgs'] != '') {
            $sx .= bs(bsc('<a href="' . base_url('dci/cursos/import/' . $id) . '">Importar dados do curso</a>'));
        }

        $Disciplinas = new \App\Models\Dci\Disciplinas();
        $sx .= $Disciplinas->mostraDisciplina($id);
        return $sx;
    }
}
