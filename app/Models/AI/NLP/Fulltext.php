<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Fulltext extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'fulltexts';
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

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        $PDF = new \App\Models\AI\FILE\pdf();
        $sx = '';
        if ($d2 == '') {
            $d2 = '101614';
        }

        $sx .= h("FULLTEXT - PRE");
        $cmd = '/usr/bin/python3 /data/Brapci3.1/bots/ROBOTi/TRADUCTOR.py ' . $d2;
        $sx .= troca(shell_exec($cmd),chr(10),'<br>');
        $sx .= '<hr>EXEC<hr>';
        $sx .= exec($cmd);
        return $sx;

        $files = $this->files($d2);

        if (!isset($files[0]))
            {
                return "Arquivos não existem";
                exit;
            }

        if (isset($files[1])) {
            $sx .= $files[1];
            if (!file_exists($files[1])) {
                /* Convert */
                $PDF->pdf_to_txt($files[0], $files[1]);
            }
        } else {
            $sx = bsmessage('Original not found', 3);
        }

        $txt = file_get_contents($files[1]);
        $txt = $this->process($txt);


        $txt = troca($txt, '[CR]', '');
        $txt = troca($txt, '  ', ' ');

        /************ E-mail */
        $pattern = '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}\b/i';
        $txt = $this->findTxt($txt, $pattern, 'email');
        /************ DOI */
        $pattern = '/10\.\d{4,9}\/[-._;()\/:A-Z0-9]+/i';
        $txt = $this->findTxt($txt, $pattern, 'DOI');
        /************ LINK */
        $pattern = '/https?:\/\/[^\s]+/i';
        $txt = $this->findTxt($txt, $pattern, 'link');
        /************ Volume */
        $pattern = '/\b(v\.|vol\.|volume)\s*\d+\b/i';
        $txt = $this->findTxt($txt, $pattern, 'vln');
        /************ Numero */
        $pattern = '/\b(n\.|num\.|número)\s*\d+\b/i';
        $txt = $this->findTxt($txt, $pattern, 'nmb');
        /************ Página */
        $pattern = '/\b(p\.|pp\.|página|páginas)\s*\d+(-\d+)?\b/i';
        $txt = $this->findTxt($txt, $pattern, 'pgn');

        require("vc/autoridade.php");
        foreach ($vc as $t1 => $t2) {
            $txt = troca($txt, $t1, $t2);
        }
        require("vc/data.php");
        foreach ($vc as $t1 => $t2) {
            $txt = troca($txt, $t1, $t2);
        }

        require("vc/metodologia.php");
        foreach ($vc as $t1 => $t2) {
            $txt = troca($txt, $t1, $t2);
        }

        $txt = ascii($txt);
        $txt = mb_strtoupper($txt);
        $ch = ['.',',',';','?','!'];
        foreach($ch as $chr)
            {
                $txt = troca($txt,$chr,' '.$chr);
            }

        require("vc/subject.php");
        foreach ($vc as $t1 => $t2) {
            $txt = troca($txt, ' '.$t1.' ', ' '.$t2.' ');
        }

        foreach ($ch as $chr) {
            $txt = troca($txt, ' '.$chr, $chr);
        }

        require("vc/places.php");
        foreach ($vc as $t1 => $t2) {
            //$txt = troca($txt, $t1, $t2);
        }

        $txt = mb_strtolower($txt);

        $st = 'Resultados';
        $st .= $this->keywords($txt,$d2);

        $txt = troca($txt,chr(13),'<br>');

        $sx = '';
        $sx .= bsc('<b>'.$files[1]. '</b>'.'<hr>',12);
        $sx .= bsc($txt,8,'small');
        $sx .= bsc($st,4);
        $sx = bs($sx);

        return $sx;
    }

    function keywords($txt, $ID)
        {
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $RDFdata = new \App\Models\RDF2\RDFdata();

            $tx = substr($txt,strpos($txt,'{keywords}'),strlen($txt));
            if ($pos = strpos($tx,'{resumo}'))
                {
                    $tx = substr($tx,0,$pos);
                }
            $tx = troca($tx, '{sigla:"','');
            $tx = troca($tx, '{keywords} ', '');
            $tx = troca($tx, '"}', '');
            $tx = troca($tx, '; ', ';');
            $tx = troca($tx, ' ;', ';');
            $tx = trim($tx);

            /********** Termina com ponto */
            if (substr($tx,strlen($tx)-1,1) == '.')
                {
                    $tx = substr($tx,0,strlen($tx)-1);
                }

            /********** KEYWORDS */
            $ky = explode(';',$tx);
            $lang = 'pt';

            foreach($ky as $id=>$key)
                {
                    $ky[$id] = mb_convert_case($key, MB_CASE_TITLE, 'UTF-8');
                    $dd = [];
                    $dd['Name'] = $ky[$id];
                    $dd['Lang'] = $lang;
                    $dd['Class'] = 'Subject';
                    $IDC = $RDFconcept->createConcept($dd);

                    $id_prop = 'hasSubject';
                    $lit = 0;
                    $RDFdata->register($ID, $id_prop, $IDC, $lit);
                }

            pre($ky);

        }

    function findTxt($txt, $pattern, $var)
    {
        preg_match_all($pattern, $txt, $matches);
        $match = [];
        foreach ($matches[0] as $ide2 => $term2) {
            $match[strzero(strlen($term2),5).$term2] = strlen($term2);
        }
        krsort($match);

        foreach ($match as $term2 => $ide2) {
            $term2 = substr($term2,5,strlen($term2));
            switch ($var) {
                case 'vln':
                    $termX = trim(substr($term2, strpos($term2, ' '), 20));
                    break;
                case 'nmb':
                    $termX = trim(substr($term2, strpos($term2, ' '), 20));
                    break;
                case 'pgn':
                    $termX = trim(substr($term2, strpos($term2, ' '), 20));
                    break;
                default:
                    $termX = $term2;
            }
            if ($termX != '') {
                $txt = troca($txt, $term2, '{' . $var . ':"' . $termX . '"}');
            }

        }
        return $txt;
    }

    function files($id)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $dt = $RDF->le($id);
        $files = [];

        $file = $RDF->extract($dt, 'hasFileStorage');
        if ($file != '') {
            $fileTXT = troca($file, '.pdf', '.txt');
            $files[0] = $file;
            $files[1] = $fileTXT;
        }
        return $files;
    }

    function show_form()
    {
        $txt = get('text');
        $txt = $this->process($txt);
        $sx = '';
        $sx .= form_open();
        $sx .= form_textarea(array('name' => 'text', 'value' => $txt, 'class' => 'form-control-lm', 'style' => 'width: 100%;'));
        $sx .= form_submit(array('name' => 'actiom', 'value' => lang('brapci.save')));
        $sx .= form_close();

        $sx .= 'Insira o texto do capítulo';

        $sx .= '<div>' . $this->show_result($this->json) . '</div>';
        return $sx;
    }

    function process($txt)
    {
        if ($txt == '') {
            return "";
        }
        $txt = troca($txt, chr(10), chr(13));
        $txt = troca($txt, '•', '');
        $txt = troca($txt, '⇒', '');
        $txt = troca($txt, "'", '');
        $txt = troca($txt, '"', '');
        $txt = troca($txt, '“', '');
        $txt = troca($txt, '“', '”');

        $txt = troca($txt, chr(13) . chr(13), '[CR]');
        $txt = troca($txt, chr(13) . chr(13), '[CR]');
        $txt = troca($txt, chr(13) . chr(13), '[CR]');

        for($r=1;$r <= 9;$r++)
            {
                $txt = troca($txt, 'v.'.$r,'v. '.$r);
                $txt = troca($txt, 'vol.' . $r, 'v. ' . $r);
                $txt = troca($txt, 'n.' . $r, 'n. ' . $r);
                $txt = troca($txt, 'num.' . $r, 'n. ' . $r);
                $txt = troca($txt, 'p.' . $r, 'p. ' . $r);
            }

        while (strpos(' ' . $txt, chr(13) . chr(13))) {
            $txt = troca($txt, chr(13) . chr(13), chr(13));
        }

        /******************* JUNTAR LINHAS * virgula e ponto e virgula */
        $txt = troca($txt, ',' . chr(13), ', ');
        $txt = troca($txt, ';' . chr(13), '; ');
        $txt = troca($txt, ', ' . chr(13), ', ');
        $txt = troca($txt, '; ' . chr(13), '; ');

        $ln = explode(chr(13), $txt);
        $end = false;
        $up = false;

        $txt = '';
        $l = [];

        /********************************** Linhas Repetidas */
        foreach ($ln as $id => $line) {
            if (!isset($l[$line])) {
                $l[$line] = 1;
            } else {
                $l[$line] = $l[$line] + 1;
            }
        }

        foreach ($ln as $id => $line) {
            if ($l[$line] > 1) {
                unset($ln[$id]);
            }
        }



        /******************* JUNTAR LINHAS * caixa baixa */
        $lid = 0;
        foreach ($ln as $id => $line) {
            $line = trim($line);
            $char = substr(ascii($line), 0, 1);
            if ((($char >= 'a') and ($char <= 'z'))
                or ($char == '(')
                or ($char == ')')
                or ($char == ',')
                or ($char == ';')
            ) {
                $tln = trim($ln[$lid]) . ' [CR]' . trim($line);
                $ln[$lid] = $tln;
                unset($ln[$id]);
            } else {
                $lid = $id;
            }
        }


        /********************************* So numeros */
        foreach ($ln as $id => $line) {
            $line = troca($line, ' ', '');
            if ($line == sonumero($line)) {
                unset($ln[$id]);
            }
        }

        $txt = '';
        foreach ($ln as $id => $t) {
            $txt .= trim($t) . chr(13);
        }
        return $txt;
    }

    function show_result($sx)
    {
        return "";
    }
}
