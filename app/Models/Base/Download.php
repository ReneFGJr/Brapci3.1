<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Download extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'downloads';
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

    function show_resources($dt)
    {
        $ok = 0;
        $sx = '';
        /************************************************************ PDF */
        if (isset($dt['PDF_id'])) {
            $PDF_id = $dt['PDF_id'];
            for ($ro = 0; $ro < count($PDF_id); $ro++) {
                $url = PATH . '/download/' . $PDF_id[$ro];
                $data['pdf'] = $url;
                $data['id'] = $PDF_id[$ro];
                $sx .= view('Brapci/Base/PDF', $data);
            }
            $ok = 1;
        }
        /************************************************************ PDF */
        if (isset($dt['DOI'])) {
            $sx .= view('Brapci/Base/DOI', $dt);
            $ok = 1;
        }
        if (1 == 2) {
            /*************************** DOWNLOAD PDF - AUTOBOT */
            $DownloadBot = new \App\Models\Bots\DownloadPDF();
            $sx .= $DownloadBot->toHarvesting($id_cc);
            $URL = explode(';', $url);
            for ($r = 0; $r < count($URL); $r++) {
                $data['URL'] = $URL[$r];
                $sx .= view('Brapci/Base/PDFno', $data);
            }
        }
        return $sx;
    }

    function download_tools($id)
    {
        $sx = bsicone('harvesting');
        $RDF = new \App\Models\RDF2\RDF();
        $dt = $RDF->le($id);

        $data = $dt['data'];

        foreach ($data as $idx => $line) {
            $class = $line['c_class'];

            switch ($class) {
                case 'hasRegisterId':
                    $sx .= $this->download_methods($line, $id);
            }
        }
        echo $sx;
        return $sx;
    }

    function download_methods($dt, $id)
    {
        if (isset($dt['Caption'])) {
            $name = $dt['Caption'];
        } else {
            $name = 'ERRO';
        }

        if (strpos($name, '/XIXENANCIB/') or (strpos($name, 'xviiienancib/'))) {
            $name = troca($name, '/XIXENANCIB/', '/XIX_ENANCIB/');
            $name = troca($name, '/xviiienancib/', '/XVIII_ENANCIB/');
            $RDFLiteral = new \App\Models\Rdf\RDFLiteral();
            $dt['n_name'] = $name;
            $RDFLiteral->set($dt)->where('id_n', $dt['id_n'])->update();
        }

        if (substr($name, 0, 4) == 'http') {
            $url = $name;
            echo h($url, 5);
            $fileURL = $this->ocs_2($url);

            if (substr($fileURL, 0, 4) == 'http') {
                $DownloadPDF = new \App\Models\Bots\DownloadPDF();
                $dir = $DownloadPDF->directory($id);
                $filePDF = $dir . 'work_' . strzero($id, 8) . '.pdf';

                $data = array();
                echo view('Brapci/Headers/header', $data);
                echo '<center>';
                echo '<img src="' . URL . '/img/thema/wait.gif">';
                echo '<br>';
                echo 'Aguarde...';
                echo '</center>';

                metarefresh($fileURL);
                exit;

                $txtFile = read_link($fileURL);
                file_put_contents($filePDF, $txtFile);
                $id = $DownloadPDF->create_FileStorage($id, $filePDF);

                echo metarefresh('', 5);
            }
        }
    }

    function ocs_2($url)
    {
        if (strpos($url, 'article/view')) {
            $txt = read_link($url);
            if ($pos = strpos($txt, 'citation_pdf_url')) {
                $txt = substr($txt, $pos, 300);
                $st = 'content="';
                $txt = substr($txt, strpos($txt, $st) + strlen($st), strlen($txt));
                $txt = substr($txt, 0, strpos($txt, '"'));
                if (substr($txt, 0, 4) == 'http') {
                    return $txt;
                }
                if (strpos($txt, 'noframes')) {
                    $url = troca($url, 'paper/view', 'paper/viewPaper');
                    $txt = read_link($url);
                    echo 'Change: ' . $url;
                }
            }
        }


        if (strpos($url, 'paper/view')) {
            $txt = read_link($url);

            if (strpos($txt, 'noframes')) {
                $url = troca($url, 'paper/view', 'paper/viewPaper');
                $txt = read_link($url);
                echo 'Change: ' . $url;
            }
            if ($pos = strpos($txt, 'citation_pdf_url')) {
                $txt = substr($txt, $pos, 300);
                $st = 'content="';
                $txt = substr($txt, strpos($txt, $st) + strlen($st), strlen($txt));
                $txt = substr($txt, 0, strpos($txt, '"'));
                if (substr($txt, 0, 4) == 'http') {
                    return $txt;
                }
            } else {
                echo 'ERRO: ' . $url;
                echo '<br>Size: ' . strlen($txt);
                echo $txt;
            }

            exit;
        }
    }

    function send_file($file)
    {
        if (file_exists($file)) {
            header('Content-type: application/pdf');
            readfile($file);
            exit;
        } else {
            echo '<center>';
            echo h('File not found in this server (' . $file . ')', 4);
            echo $file;
            echo '</center>';
            echo '<hr>';
            exit;
        }
    }

    function download_pdf($id)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $dt = $RDF->le($id);

        $data = $dt['concept'];
        $class = $data['c_class'];
        $file = 'NOT FOUND';
        switch ($class) {
            case 'FileStorage':
                $file = trim($dt['concept']['n_name']);
                $this->send_file($file);
                break;
            case 'Book':
                $idf = $RDF->extract($dt, 'hasFileStorage');
                if ($idf[0] > 0) {
                    $dtf = $RDF->le($idf[0]);
                    $file = $dtf['concept']['n_name'];
                }
                $this->send_file($file);
                break;
            case 'Proceeding':
                $idf = $RDF->extract($dt, 'hasFileStorage');
                if (count($idf) > 0) {
                    if ($idf[0] > 0) {
                        $dtf = $RDF->le($idf[0]);
                        $file = $dtf['concept']['n_name'];
                        $this->send_file($file);
                    }
                }
                break;
        }
    }
}
