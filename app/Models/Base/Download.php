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

    function download_methods($dt, $idc)
    {
        $RDF = new \App\Models\RDF2\RDF();

        $IssueWorks = new \App\Models\Base\IssuesWorks();
        $dw = $IssueWorks->where('siw_work_rdf', $idc)->first();
        if ($dw == []) {
            echo '<hr>';
            echo "Erro de Download methods WORK Issue";
            exit;
        }
        $jnl = $dw['siw_journal'];


        if (isset($dt['Caption'])) {
            $name = $dt['Caption'];
        } else {
            $name = 'ERRO';
        }

        if (strpos($name, '/XIXENANCIB/')
                or (strpos($name, 'xviiienancib/'))
                or (strpos($name, 'http://'))
                or (strpos($name, 'www.periodicos.ufpb.br/ojs/'))) {
            $nameO = $name;
            $name = troca($name, '/XIXENANCIB/', '/XIX_ENANCIB/');
            $name = troca($name, '/xviiienancib/', '/XVIII_ENANCIB/');
            $name = troca($name, 'http://www.periodicos.ufpb.br/ojs/', 'https://www.pbcib.com/');
            $name = troca($name, 'http://', 'https://');

            $RDFLiteral = new \App\Models\RDF2\RDFliteral();
            $dtd = $RDFLiteral->where('n_name',$nameO)->first();
            $ddd = $RDFLiteral->first();
            $ddd['n_name'] = $name;
            //$RDFLiteral->set($ddd)->where('id_n', $ddd['id_n'])->update();
        }


        if (substr($name, 0, 4) == 'http') {
            $name = troca($name, 'http://', 'https://');
            $url = $name;
            echo h('<a href="' . $url . '">' . $url . '</a>', 5);

            /******************************************************* Recupera via OCS2 */
            $fileURL = $this->ocs_2($url);

            if ($fileURL == '')
                {
                    echo "Não foi possível acessar o PDF, provavelmente a revista não disponibilizou o arquivo.";
                    echo '<hr>Clique no link acima para verificar na revista';
                    return "";
                }

            if (substr($fileURL, 0, 4) == 'http') {
                echo "OK";
                $dir = $this->directory($idc);
                echo "OK2";
                $filePDF = $dir . 'work_' . strzero($idc, 8) . '#' . strzero($jnl, 5) . '.pdf';

                $data = array();
                echo view('Brapci/Headers/header', $data);
                echo '<center>';
                echo '<img src="' . URL . '/img/thema/wait.gif">';
                echo '<br>';
                echo 'Aguarde...';
                echo '</center>';

                //echo metarefresh($fileURL,5);
                //exit;

                $txtFile = read_link($fileURL);
                file_put_contents($filePDF, $txtFile);
                $id = $this->create_FileStorage($idc, $filePDF);
                echo metarefresh('', 1);
            }
        }
    }

    function create_FileStorage($id, $filename)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFproperty = new \App\Models\RDF2\RDFproperty();

        $dt = [];
        $dt['Name'] = $filename;
        $dt['Lang'] = 'nn';
        $dt['Class'] = 'FileStorage';

        $id_prop = $RDFproperty->getProperty('hasFileStorage');

        $r2 = $RDFconcept->createConcept($dt);
        $this->updatePropierties($r2);

        $prop = 'hasFileStorage';
        $id_prop = $RDFproperty->getProperty($prop);
        $RDFdata->register($id, $id_prop, $r2, 0);

        /************ Atualizar Dataset */
        $Elastic = new \App\Models\ElasticSearch\Register();
        $dd['PDF'] = 1;
        $Elastic->set($dd)->where('ID',$id)->update();
        return $r2;
    }

    function updateLiteral($id, $pr, $name, $lang = 'nn')
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFproperty = new \App\Models\RDF2\RDFproperty();
        $RDFLiteral = new \App\Models\RDF2\RDFliteral();

        $idp = $RDFproperty->getProperty($pr);
        $idl = $RDFLiteral->register($name, $lang);
        echo 'LDL>'. $idl.'<br>';
        $idd = $RDFdata
            ->where('d_r1', $id)
            ->where('d_p', $idp)
            ->first();
        if ($idd == []) {
            $d = [];
            $d['d_r1'] = $id;
            $d['d_p'] = $idp;
            $d['d_r2'] = 0;
            $d['d_literal'] = $idl;
            $RDFdata->set($d)->insert();
        } else {
            $d = [];
            $d['d_literal'] = $idl;
            $RDFdata->set($d)->where('id_d', $idd['id_d']);
        }
    }

    function updatePropierties($id)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFproperty = new \App\Models\RDF2\RDFproperty();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFLiteral = new \App\Models\RDF2\RDFliteral();
        $dt = $RDF->le($id);
        $prop = [
            'hasFileType' => 'FileType',
            'hasFileSize' => 'Literal',
            'prefLabel' => 'Literal',
            'hasDateTime' => 'Date',
            'hasFileStorage'=>'ID'
        ];

        $file = $dt['concept']['n_name'];
        if (file_exists($file)) {
            foreach ($prop as $pr => $cl) {
                $pr = trim($pr);
                echo '[' . $pr . '==' . $cl;
                $dr = $RDF->extract($dt, $pr);

                switch ($pr) {
                    case 'hasFileType':
                        $path_parts = pathinfo($file);
                        $extension = UpperCase($path_parts['extension']);
                        $dx = [];
                        $dx['Name'] = $extension;
                        $dx['Lang'] = 'nn';
                        $dx['Class'] = $cl;
                        $r3 = $RDFconcept->createConcept($dx);
                        $id_prop = $RDFproperty->getProperty($pr);
                        $RDFdata->register($id, $id_prop, $r3, 0);
                        break;
                    case 'hasDateTime':
                        $date = date("Y-m-d");
                        $this->updateLiteral($id,$pr,$date);
                        break;
                    case 'prefLabel':
                        $file = date("Y-m-d");
                        $this->updateLiteral($id, $pr, $file);
                        break;
                    case 'hasFileSize':
                        $size = filesize($file);
                        $this->updateLiteral($id, $pr, $size);
                        break;
                }
            }
        }
    }

    function ocs_2($url)
    {
        $Otxt = 'VAZIO';

        /*************************** Tratamentos */
        $url = troca($url, '//seer.ufs.br/index.php/', '//periodicos.ufs.br/');

        if (strpos($url, 'article/view')) {
            $txt = read_link($url);
            $Otxt = $txt;

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

            /********************* EBOOK */
            if (strpos($Otxt,'article__btn pdf') > 0)
                {
                    $pos == strpos($Otxt, 'article__btn pdf');
                    $txt = substr($Otxt,$pos,300);
                    echo '<br>'.$txt;
                    $txt = substr($txt,strpos($txt,'http'),300);
                echo '<br>' . $txt;
                    $txt = substr($txt,0,strpos($txt,'"'));
                echo '<br>' . $txt;
                    echo h($txt);
                    exit;
                }

            /********************* Article Download */
            if (strpos($Otxt, 'article/download/') > 0) {
                $pos == strpos($Otxt, 'article/download/');
                while (substr($Otxt,$pos,1) != '"')
                    {}
                $txt = substr($Otxt,$pos-100,100);
                $txt = substr($txt,strpos($txt,'http'),100);
                echo '==='.$pos;
                pre($txt);

            }



            /********************* IFRAME */
            if (strpos($txt,'<iframe')) {
                $pos = strpos($txt,'<iframe');
                $txt = substr($txt,$pos+13,300);
                $txt = substr($txt,0,strpos($txt,'"'));
                if (strpos($txt, '?file='))
                    {
                        $txt = substr($txt,strpos($txt, '?file=')+6,strlen($txt));
                        $txt = urldecode($txt);
                    }
                if (substr($txt,0,4) == 'http')
                    {
                        return $txt;
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
        }
        exit;
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
    function directory($id)
    {
        if ($id == 0) {
            $dir = '../.tmp/pdf/';
        } else {
            $dir = '_repository/';
            dircheck($dir);
            $nr = strzero($id, 8);
            $dir .= substr($nr, 0, 2) . '/';
            $dir .= substr($nr, 2, 2) . '/';
            $dir .= substr($nr, 4, 2) . '/';
            $dir .= substr($nr, 6, 2) . '/';
        }
        $d = explode('/', $dir);
        $dir = $d[0];
        for ($r = 1; $r < count($d); $r++) {
            $dir .= '/' . $d[$r];
            //echo '<br>==>' . $dir;
            dircheck($dir);
        }
        return $dir;
    }
}
