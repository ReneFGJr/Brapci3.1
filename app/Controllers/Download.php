<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", '');

class Download extends BaseController
{
    function download($id ='', $id2 ='', $id3 ='', $id4 = '')
    {
        /* Bibliografia */
        if ($id == 'bib')
            {
                $this->download_export($id2);
            }

        /* TOOLS download */
        if ($id == 'temp') {
            return $this->download_tools($id2,$id3,$id4);
        }

        $RDF = new \App\Models\RDF2\RDF();
        $dt = $RDF->le($id);

        if (($dt == []) or (!isset($dt['concept']['c_class'])))
            {
                $sx = $RDF->E404();
                $sx .= bsmessage("Item não existe - Download PDF - ".$id);
                //pre($dt);
                return $sx;
            }

        $class = $dt['concept']['c_class'];

        switch ($class) {
            case 'Article':
                $id = $this->download_01($dt);
                break;
            case 'Proceeding':
                $id = $this->download_01($dt);
                break;
            case 'Book':
                $id = $this->download_01($dt);
                break;
            case 'FileStorage':
                $id = $this->download_02($dt);
                break;
            default:
                echo "Download Class:".$class;
                exit;
        }
        if ($id > 0)
            {
                $Download = new \App\Models\Base\Download();
                $Download->download_pdf($id);
            } else {
                echo "ERRO NO ACESSO AO PDF";
            }
        exit;
    }

    function download_tools($d1,$d2,$d3)
        {
            $file = '../.tmp/'.$d1.'/'.$d2;
            if (file_exists($file))
                {
                $ext = substr($d2, -3, 3);
                $utf8 = 0;

                Header('Content-Description: File Transfer');
                switch($ext)
                    {
                        case 'csv':
                            header('Content-Type: text/csv; charset=utf-8');
                            break;
                        default:
                            Header('Content-Type: application/force-download');
                            break;
                    }

                Header('Content-Disposition: attachment; filename='.$d2);
                $txt = file_get_contents($file);
                echo $txt;
                exit;
                } else {
                    echo "File not found $file";
                }
            exit;
        }

    function download_export($file)
        {
            $ext = substr($file,-3,3);
            $filePath = '.tmp/export/'.$file;

            switch($ext)
                {
                    case 'ris':
                        header('Content-Type: application/x-research-info-systems');
                        break;
                    case 'doc':
                        header('Content-Type: text/html');
                        break;
                    default:
                        header('Content-Type: text/csv');
                        break;
                }

            header('Content-Description: File Transfer');
            header('Access-Control-Allow-Origin: *');
            header('Content-Disposition: attachment; filename=' . basename($filePath));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
            exit;
        }

    function download_01($dt)
        {
            $ida = $dt['concept']['id_cc'];

            $RDF = new \App\Models\RDF2\RDF();
                $id = $RDF->extract($dt, 'hasFileStorage','A');
                /* Se não identificado o PDF */
                if (!isset($id[0]))
                    {
                        $Download = new \App\Models\Base\Download();
                        $links = [];
                        foreach($dt['data'] as $idz=>$line)
                            {
                                if (trim($line['Property']) == 'hasRegisterId')
                                    {
                                        array_push($links,$line['Caption']);
                                    }
                                if (trim($line['Property']) == 'hasUrl') {
                                    array_push($links, $line['Caption']);
                                }
                            }
                        if ($links != [])
                            {
                                $line = [];
                                $line['Caption'] = $links[0];
                                $Download->download_methods($line, $ida);
                            } else {
                                echo "ERROR 404 - Coleta PDF";
                                exit;
                            }

                        $id = $RDF->extract($dt, 'hasFileStorage');

                        if (!isset($id[0])) {
                            echo "<br>PDF não disponível";
                            exit;
                        }

                    }
                $id = $id[0];
                return $id;
        }

    function download_02($dt)
    {
        $file = $dt['concept']['n_name'];
        $Download = new \App\Models\Base\Download();
        $Download->send_file($file);
        exit;
        return 0;
    }
}