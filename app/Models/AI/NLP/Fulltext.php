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
        echo "$d1,$d2,$d3";
        $files = $this->files($d2);
        if (isset($files[1]))
            {
                $sx .= $files[1];
                if (!file_exists($files[1]))
                    {
                    /* Convert */
                   $PDF->pdf_to_txt($files[0],$files[1]);
                    }
            } else {
                $sx = bsmessage('Original not found',3);
            }

            $txt = file_get_contents($files[1]);
            $txt = $this->process($txt);
            pre($txt);
        return $sx;
    }

    function files($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $files = [];

        $id_file = $RDF->extract($dt, 'hasFileStorage');
        if (count($id_file) > 0) {
            $dtf = $RDF->le($id_file[0]);
            $file = $dtf['concept']['n_name'];
            $fileTXT = troca($file, '.pdf', '.txt');
            $files[0] = $file;
            $files[1] = $fileTXT;
        } else {

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
        $txt = troca($txt, '•', '');
        $txt = troca($txt, '⇒', '');
        $txt = troca($txt, "'", '"');
        $txt = troca($txt, chr(10), chr(13));
        while (strpos(' ' . $txt, chr(13) . chr(13))) {
            $txt = troca($txt, chr(13) . chr(13), chr(13));
        }
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

        /********************************* So numeros */
        foreach ($ln as $id => $line) {
            $line = troca($line, ' ', '');
            if ($line == sonumero($line)) {
                unset($ln[$id]);
            }
        }


        foreach ($ln as $id => $line) {
            $line = trim($line);
            $first = substr($line, 0, 1);
            $up = ($first == UpperCase($first));
            $lt = ord($first);

            $lt = (($lt >= 65) and ($lt <= 90));

            $last = substr($line, strlen($line) - 1, 1);
            $end = false;
            //echo $up.'='.$lt.'='.$line.'<br>';
            if ($last == '.' or $last == '?' or $last == '!') {
                $end = true;
            }
            //echo '<br>'.$up.'-'.$end.'-'.$line;
            if (($up) and ($lt)) {
                $txt .= chr(13) . $line;
            } else {
                $txt .= ' ' . $line;
            }
        }
        return $txt;
    }

    function show_result($sx)
    {
        return "";
    }
}
