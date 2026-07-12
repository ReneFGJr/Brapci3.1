<?php

namespace App\Models\Base;
helper('markdown');

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

    function getText($idc)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $dt = $RDF->le($idc);
        $files = $RDF->extract($dt, 'hasFileStorage' , 'A');
        $dir = $this->directory($idc);

        if (count($files) > 0)
            {
                $idf = $files[0];
                $dt = $RDF->le($idf);
                $file = $dt['concept']['n_name'];
            } else {
                return [];
            }

        $RSP = [];
        $RSP['status'] = '404';
        $RSP['message'] = 'File not found';

        $fileMD = troca($file, '.pdf', '.md');
        $fileTXT = troca($file, '.pdf', '.txt');

        if (file_exists($fileMD))
            {
                $TXT = markdown_to_html(file_get_contents($fileMD));
                $file = $fileMD;
            }
        else if (file_exists($file))
            {
                $TXT = shell_exec('pdftotext '.$file.' '.$fileTXT);
                $file = $fileTXT;
            }

            $RSP['status'] = '200';
            $RSP['message'] = 'Success';
            $RSP['file'] = $file;
            $RSP['full'] = $TXT;
            //$RSP['line'] = $this->explode_line($RSP['full']);
            return $RSP;

            $fileEMAIL = troca($file,'.txt', '_email.json');
            if (file_exists($dir.$fileEMAIL))
                {
                    $RSP['email'] = json_decode(file_get_contents($dir.$fileEMAIL));
                } else {
                    $RSP['email'] = ['none'];
                }

            $fileEMAIL = troca($file,'.txt', '_cited.json');
            if (file_exists($dir.$fileEMAIL))
                {
                    $RSP['cited'] = json_decode(file_get_contents($dir.$fileEMAIL));
                } else {
                    $RSP['cited'] = ['none'];
                }

        return $RSP;
    }

    function explode_line($txt)
        {
            $txt = troca($txt,chr(10),chr(13));
            $txt = troca($txt, chr(13).chr(13), chr(13));
            $ln = explode(chr(13),$txt);
            return $ln;
        }

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

            private function showWaitingScreen(): void
            {
                echo view('Brapci/Headers/header');

                echo <<<HTML
                    <center>
                        <img src="{$GLOBALS['URL']}/img/thema/wait.gif">
                        <br>
                        Aguarde...
                    </center>
                 HTML;
            }

    private function normalizeUrl(string $url): string
    {
        $original = $url;

        $replace = [
            '/XIXENANCIB/'                     => '/XIX_ENANCIB/',
            '/xviiienancib/'                   => '/XVIII_ENANCIB/',
            'http://www.periodicos.ufpb.br/ojs/' => 'https://www.pbcib.com/',
            'http://'                          => 'https://',
        ];

        $url = str_replace(
            array_keys($replace),
            array_values($replace),
            $url
        );

        if ($original !== $url) {
            $RDFLiteral = new \App\Models\RDF2\RDFliteral();

            if ($literal = $RDFLiteral->where('n_name', $original)->first()) {
                $literal['n_name'] = $url;
                //$RDFLiteral->update($literal['id_n'], $literal);
            }
        }

        return $url;
    }

    public function download_methods(array $dt, int $idc): void
    {
        $RDF = new \App\Models\RDF2\RDF();
        $IssueWorks = new \App\Models\Base\IssuesWorks();

        // ==========================================================
        // Recupera dados do trabalho
        // ==========================================================
        $work = $IssueWorks->where('siw_work_rdf', $idc)->first();

        if (!$work) {
            throw new \RuntimeException('IssueWork não localizado para RDF: ' . $idc);
        }

        $journal = $work['siw_journal'];
        $url = $dt['Caption'] ?? '';

        if (empty($url)) {
            return;
        }

        // ==========================================================
        // Corrige URLs antigas
        // ==========================================================
        $url = $this->normalizeUrl($url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return;
        }

        echo h('Processando download do PDF', 2);
        echo h('<a href="' . $url . '">' . $url . '</a>', 5);

        // ==========================================================
        // Localiza PDF
        // ==========================================================
        $fileURL = $this->ocs_2($url);

        if (empty($fileURL)) {
            echo "Não foi possível acessar o PDF.<hr>";
            echo "Clique no link acima para verificar na revista.";
            exit;
        }

        // Alguns eventos aceitam apenas HTTP
        if (
            str_contains($fileURL, 'rev-ib.unam.mx') ||
            str_contains($fileURL, 'XIX_ENANCIB') ||
            str_contains($fileURL, 'XVIII_ENANCIB')
        ) {
            $fileURL = str_replace('https://', 'http://', $fileURL);
        } else {
            $fileURL = str_replace('http://', 'https://', $fileURL);
        }

        echo h('PDF localizado: <a href="' . $fileURL . '">' . $fileURL . '</a>', 5);

        // ==========================================================
        // Download
        // ==========================================================
        $directory = $this->directory($idc);

        $pdfFile = sprintf(
            '%swork_%08d#%05d.pdf',
            $directory,
            $idc,
            $journal
        );

        $this->showWaitingScreen();

        $content = read_link($fileURL);

        if (!$content) {
            echo "Erro ao realizar download.";
            return;
        }

        file_put_contents($pdfFile, $content);

        $this->create_FileStorage($idc, $pdfFile);

        echo metarefresh('', 1);
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
        $Elastic->set($dd)->where('ID', $id)->update();
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
        echo 'LDL>' . $idl . '<br>';
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
            'hasFileStorage' => 'ID'
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
                        $this->updateLiteral($id, $pr, $date);
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
            $txt = read_link($url, 'curl', True);
            $Otxt = $txt;

            if ($pos = strpos($txt, 'citation_pdf_url')) {
                $txt = substr($txt, $pos, 300);
                $st = 'content="';
                $txt = substr($txt, strpos($txt, $st) + strlen($st), strlen($txt));
                $txt = substr($txt, 0, strpos($txt, '"'));
                if (substr($txt, 0, 4) == 'http') {
                    echo "<h4>Localizado $txt</h4>";
                    return $txt;
                }
                if (strpos($txt, 'noframes')) {
                    $url = troca($url, 'paper/view', 'paper/viewPaper');
                    $txt = read_link($url);
                    echo 'Change: ' . $url;
                }
            }

            /********************* EBOOK */
            if (strpos($Otxt, '_btn pdf') > 0) {
                echo "<br>Methodo BTN PDF";
                $pos = strpos($Otxt, '_btn pdf');
                $txt = substr($Otxt, $pos, 300);
                $txt = substr($txt, strpos($txt, 'http'), 300);
                $txt = substr($txt, 0, strpos($txt, '"'));

                if (substr($txt, 0, 4) == 'http') {
                    echo "<br>Lendo " . $txt;
                    $Otxt = read_link($txt);
                }
            }

            /********************* Article Download */
            if (strpos($Otxt, 'article/download/') > 0) {
                $pos = strpos($Otxt, 'article/download/');
                while (substr($Otxt, $pos, 1) != '"') {
                    $pos--;
                }
                $txt = substr($Otxt, $pos + 1, 200);
                $txt = substr($txt, 0, strpos($txt, '"'));
                if (substr($txt, 0, 4) == 'http') {
                    return $txt;
                }
            }



            /********************* IFRAME */
            if (strpos($txt, '<iframe')) {
                $pos = strpos($txt, '<iframe');
                $txt = substr($txt, $pos + 13, 300);
                $txt = substr($txt, 0, strpos($txt, '"'));
                if (strpos($txt, '?file=')) {
                    $txt = substr($txt, strpos($txt, '?file=') + 6, strlen($txt));
                    $txt = urldecode($txt);
                }
                if (substr($txt, 0, 4) == 'http') {
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
        echo "<br>Não foi possível localizar o PDF";
        echo "<br>URL: " . $url;
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
