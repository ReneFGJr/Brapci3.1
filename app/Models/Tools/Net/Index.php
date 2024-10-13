<?php

namespace App\Models\Tools\Net;

use CodeIgniter\Model;

use function RectorPrefix20220609\dump_node;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function execPython($endpoint='', $argumento='')
    {
        $cmd = '/data/Brapci3.1/bots/TOOLS/txt4net.py';
        $cmd .= ' ';
        $cmd .= get("dest");
        $python = '/usr/bin/python3'; // Isso depende do seu sistema

        // Caminho para o script Python
        $script_python = '/data/Brapci3.1/bots/TOOLS/'.$endpoint.'.py';

        // Monta o comando para executar o Python com o script e o argumento
        $cmd = "$python $script_python '$argumento'";
        file_put_contents('/data/Brapci3.1/.tmp/CMD',$cmd);
        $comando = escapeshellcmd($cmd);

        // Executa o script Python e captura a saída
        $saida = shell_exec($comando);

        return $saida;
    }

    function index($d1, $d2, $d3, $d4 = '')
    {
        $sx = '';
        switch ($d2) {
            case 'txt4net':
                $RSP = [];
                $RSP['status'] = '200';
                $RSP['file'] = get("file");
                $RSP['post'] = $_POST;
                $RSP['get'] = $_GET;
                $RSP['response'] = $this->execPython('txt4net',$RSP['file']);
                break;
            default:
                $RSP = [];
                $RSP['status'] = '400';
                $RSP['message'] = 'Method not informed';
                $RSP['GET'] = $_GET;
                $RSP['POST'] = $_POST;
                $RSP['FILES'] = $_FILES;
                $RSP['REQUEST'] = $_REQUEST;
                $RSP['d1'] = $d1;
                $RSP['d2'] = $d2;
        }

        if (($d2 != 'import') and ($d2 != 'in')) {
            header('Access-Control-Allow-Origin: *');
            header("Content-Type: application/json");
        }

        echo json_encode($RSP);
        exit;
    }

    function file_download($txt, $type = '.net')
    {
        $arquivo = 'brapci_' . date("Ymd_His") . $type;
        //header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        //header ("Pragma: no-cache");
        header("Content-type: application/x-msexcel");
        header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
        echo $txt;
        exit;
    }

    function trata($txt)
    {
        $txt = troca($txt, '; ', ';');
        $txt = troca($txt, '"', '');
        for ($r = 0; $r < 32; $r++) {
            if (($r != 13) and ($r != 10)) {
                $txt = troca($txt, chr($r), ' ');
            }
        }

        $t  = array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ã', 'õ', 'Â', 'Ô', 'ä', 'ë', 'ï', 'ö', 'ü', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü', 'â', 'ê', 'î', 'ô', 'û', 'Â', 'Ê', 'Ô', 'Û', 'ç', 'Ç');
        $tr = array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'a', 'o', 'A', 'O', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'O', 'U', 'c', 'C');
        for ($r = 0; $r < count($tr); $r++) {
            $t1 = $t[$r];
            $t2 = $tr[$r];
            $txt = troca($txt, $t1, $t2);
            $txt = troca($txt, utf8_decode($t1), $t2);
        }
        for ($r = 128; $r < 255; $r++) {
            $txt = troca($txt, chr($r), ' ');
        }
        $txt = troca($txt, '  ', ' ');
        return ($txt);
    }

    function csv_to_matrix_ocorrencia($txt)
    {
        $txt = $this->trata($txt);
        $txt = troca($txt, '.,', ';');
        $txt = troca($txt, ';', '£');
        $txt = troca($txt, chr(10), ';');
        $txt = troca($txt, chr(13), ';');
        $lns = explode(';', $txt);

        $nx = array();
        $ns = array();
        $nf = array();

        for ($r = 0; $r < count($lns); $r++) {
            $mn = $lns[$r];
            $mn = troca($mn, '£', ';');
            $an = explode(';', $mn . ';');
            $au = [];

            $ax = array();
            $ai = array();
            for ($z = 0; $z < count($an); $z++) {
                $nn = $an[$z];
                if (!isset($ax[$nn])) {
                    $nn = trim($nn);
                    if ($nn != '') {
                        array_push($ai, $nn);
                        $ax[$nn] = $nn;
                    }
                }
            }

            $au = $ai;

            for ($a = 0; $a < count($au); $a++) {
                if (get("dd1") == '1') {
                    $mm = nbr_author($au[$a], 5);
                } else {
                    $mm = $au[$a];
                }

                $mm = troca($mm, ',', '');
                $mm = troca($mm, '. ', '');
                $mm = troca($mm, '.', '');
                $au[$a] = $mm;
            }

            for ($a = 0; $a < count($au); $a++) {
                $mm = $au[$a];
                if (isset($ns[$mm])) {
                    $ns[$mm] = $ns[$mm] + 1;
                } else {
                    $ns[$mm] = 1;
                    array_push($nf, $mm);
                }

                /* monta matriz */
                if ($a == 0) {
                    /**************** Primeiro Autor **********************/
                    if (isset($nx[$au[0]][$au[0]])) {
                        $nx[$au[0]][$au[0]] = $nx[$au[0]][$au[0]] + 1;
                    } else {
                        $nx[$au[0]][$au[0]] = 1;
                    }
                } else {
                    /*************** Outros autores ***********************/
                    for ($b = 0; $b < $a; $b++) {
                        $ma = $au[$b];

                        if (isset($nx[$ma][$mm])) {
                            $nx[$ma][$mm] = $nx[$ma][$mm] + 1;
                            $nx[$mm][$ma] = $nx[$mm][$ma] + 1;
                        } else {
                            $nx[$ma][$mm] = 1;
                            $nx[$mm][$ma] = 1;
                        }
                    }
                }
            }
        }

        /*  matriz */
        $sx = '#;';
        foreach ($nf as $key => $val1) {
            $sx .= '' . $val1 . ';';
        }
        $sx .= '' . cr();
        foreach ($nf as $key => $val1) {
            $sx .= '' . $val1 . ';';
            foreach ($nf as $key2 => $val2) {
                if ((isset($nx[$val1][$val2])) and ($val1 != $val2)) {
                    $sx .= '' . $nx[$val1][$val2] . ';';
                } else {
                    $sx .= '0;';
                }
            }
            $sx .= '' . cr();
        }
        $sx  .= '';
        return ($sx);
    }

    function csv_to_count($txt, $opt)
    {
        set_time_limit(3600);
        $txt = $this->trata($txt);
        $txt = troca($txt, '.,', ';');
        $txt = troca($txt, chr(10), ';');
        $txt = troca($txt, chr(13), ';');
        $lns = explode(';', $txt);
        $auth = [];

        foreach ($lns as $id => $aul) {
            $aul = trim($aul);
            if ($aul != '') {
                $aul = explode(';', $aul);
                foreach ($aul as $idx => $au) {
                    if ($opt[0] != '') {
                        $mm = nbr_author($au, 2);
                    } else {
                        $mm = $au;
                    }
                    if (isset($auth[$mm])) {
                        $auth[$mm] = $auth[$mm] + 1;
                    } else {
                        $auth[$mm] = 1;
                    }
                }
            }
        }
        $rsp = 'element;total' . cr();
        foreach ($auth as $name => $total) {
            $rsp .= $name . ';' . $total . cr();
        }
        return $rsp;
    }

    function csv_to_net($txt, $opt)
    {
        set_time_limit(3600);
        $txt = $this->trata($txt);
        $txt = troca($txt, '.,', ';');
        $txt = troca($txt, ';', '£');
        $txt = troca($txt, chr(10), ';');
        $txt = troca($txt, chr(13), ';');
        $lns = explode(';', $txt);

        $nx = array();
        $ns = array();
        $nf = array();
        $nz = array();
        for ($r = 0; $r < count($lns); $r++) {
            $mn = $lns[$r];
            $mn = troca($mn, '£', ';');
            $aa = explode(';', $mn . ';');
            $au = [];

            for ($a = 0; $a < count($aa); $a++) {
                if ($opt[0] != '') {
                    $mm = nbr_author($aa[$a], 2);
                } else {
                    $mm = $aa[$a];
                }

                $mm = troca($mm, ',', '');
                $mm = troca($mm, '. ', '');
                $mm = troca($mm, '.', '');
                $mm = trim($mm);
                if ($mm != '') {
                    $au[$a] = $mm;
                }
            }

            for ($a = 0; $a < count($au); $a++) {
                $a = troca($a, '#', '=');
                if (trim($a) == '') {
                    $a = 'NnN';
                }
                try {
                    $mm = $au[$a];
                } catch (Exception $e) {
                    $mm = 0;
                }

                if (isset($ns[$mm])) {
                    $ns[$mm] = $ns[$mm] + 1;
                } else {
                    $ns[$mm] = 1;
                    array_push($nf, $mm);
                }
                /* monta matriz */
                if ($a == 0) {
                    /**************** Primeiro Autor **********************/
                    if (isset($nx[$au[0]][$au[0]])) {
                        $nx[$au[0]][$au[0]] = $nx[$au[0]][$au[0]] + 1;
                    } else {
                        $nx[$au[0]][$au[0]] = 1;
                    }
                } else {
                    /*************** Outros autores ***********************/
                    for ($b = 0; $b < $a; $b++) {
                        $ma = $au[$b];
                        if (isset($nx[$ma][$mm])) {
                            $nx[$ma][$mm] = $nx[$ma][$mm] + 1;
                            $nx[$mm][$ma] = $nx[$mm][$ma] + 1;
                        } else {
                            $nx[$ma][$mm] = 1;
                            $nx[$mm][$ma] = 1;
                        }
                    }
                }
            }
        }
        sort($nf);
        /*  matriz */
        $sx = '*Vertices ' . count($nf) . cr();
        $max = 10;
        foreach ($nf as $key => $val1) {
            if ($ns[$val1] > $max) {
                $max = $ns[$val1];
            }
        }
        foreach ($nf as $key => $val1) {
            $n1 = number_format($ns[$val1] / $max * 10, 4);
            //$sx .= ($key+1).' "'.$val1.'" '.$n1.' '.$ns[$val1].' '.$ns[$val1].' '.cr();
            $sx .= ($key + 1) . ' "' . $val1 . '" ellipse x_fact ' . $n1 . ' y_fact ' . $n1 . ' fos 1 ic LightYellow lc Blue ' . cr();
        }

        $sx .= '*Edges' . cr();

        foreach ($nf as $key1 => $val1) {
            foreach ($nf as $key2 => $val2) {
                if ($val1 < $val2) {
                    if (isset($nx[$val1][$val2])) {
                        if (isset($nx[$val1][$val2])) {
                            $tot = $nx[$val1][$val2];
                        } else {
                            $tot = 0;
                        }
                        $sx .= ($key1 + 1) . ' ' . ($key2 + 1) . ' ' . $tot . cr();
                    } else {
                    }
                }
            }
        }

        return ($sx);
    }
}
