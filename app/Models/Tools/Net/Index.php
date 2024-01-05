<?php

namespace App\Models\Tools\Net;

use CodeIgniter\Model;

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

    function index($d1, $d2, $d3, $d4 = '')
    {
        $sx = '';
        switch ($d1) {
            case 'txt4count':
                $chk = ['', '', '', '', ''];
                if (get("author_abrev") != '') {
                    $chk[0] = 'checked';
                }
                $sx = '';
                $sa = h(lang('tools.ARS_txt4count'), 2);
                $sa .= form_open_multipart();
                $sa .= form_upload('files');
                $sa .= form_submit('action', lang('brapci.send'));
                $sa .= '<br>';
                $sa .= '<span class="small">Seleciona o arquivo para processar e click em enviar</span>';
                $sa .= '<br>';
                $sa .= '<br>';
                $sa .= h(lang('tools.Options'), 2);
                $sa .= form_checkbox('author_abrev', '1', $chk[0]) . ' ' . lang('tools.txt4net.author_abrev');
                $sa .= form_close();

                $sb = '';
                $sb .= h('Instruções');
                for ($r = 0; $r < 20; $r++) {
                    $nn = 'tools.txt4count.line_' . $r;
                    $ln = lang($nn);
                    if ($nn != $ln) {
                        $sb .= '<p>' . ' ' . $ln . '</p>';
                    }
                }

                $sx = bs(bsc($sa, 6) . bsc($sb, 6));

                if (isset($_FILES['files']['tmp_name'])) {
                    if ($_FILES['files']['error'] == 0) {
                        $txt = file_get_contents($_FILES['files']['tmp_name']);
                        $net = $this->csv_to_count($txt, $chk);
                        $this->file_download($net, '.csv');
                    }
                }

                return $sx;
                break;

            case 'txt4net':
                $chk = ['', '', '', '', ''];
                if (get("author_abrev") != '') {
                    $chk[0] = 'checked';
                }
                $sx = '';
                $sa = h(lang('tools.ARS_txt4net'), 2);
                $sa .= form_open_multipart();
                $sa .= form_upload('files');
                $sa .= form_submit('action', lang('brapci.send'));
                $sa .= '<br>';
                $sa .= '<span class="small">Seleciona o arquivo para processar e click em enviar</span>';
                $sa .= '<br>';
                $sa .= '<br>';
                $sa .= h(lang('tools.Options'), 2);
                $sa .= form_checkbox('author_abrev', '1', $chk[0]) . ' ' . lang('tools.txt4net.author_abrev');
                $sa .= form_close();

                $sb = '';
                $sb .= h('Instruções');
                for ($r = 0; $r < 20; $r++) {
                    $nn = 'tools.txt4net.line_' . $r;
                    $ln = lang($nn);
                    if ($nn != $ln) {
                        $sb .= '<p>' . $ln . '</p>';
                    }
                }

                $sx = bs(bsc($sa, 6) . bsc($sb, 6));

                if (isset($_FILES['files']['tmp_name'])) {
                    if ($_FILES['files']['error'] == 0) {
                        $txt = file_get_contents($_FILES['files']['tmp_name']);
                        $net = $this->csv_to_net($txt, $chk);
                        $this->file_download($net, '.net');
                    }
                }
                return $sx;
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
        $rsp = 'element;total'.cr();
        foreach($auth as $name=>$total)
            {
                $rsp .= $name.';'.$total.cr();
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
            $au = explode(';', $mn . ';');

            for ($a = 0; $a < count($au); $a++) {
                if ($opt[0] != '') {
                    $mm = nbr_author($au[$a], 2);
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
