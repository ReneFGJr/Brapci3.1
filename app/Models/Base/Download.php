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

    function download_tools($id)
    {
        $sx = bsicone('harvesting');
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);

        $data = $dt['data'];

        foreach($data as $idx=>$line)
            {
                $class = $line['c_class'];

                switch($class)
                    {
                        case 'hasRegisterId':
                            $sx .= $this->download_methods($line,$id);
                    }
            }
        echo $sx;
        return $sx;
    }

    function download_methods($dt,$id)
        {
            $name = $dt['n_name'];

            if (substr($name,0,4) == 'http')
                {
                    $url = $name;
                    echo h($url,5);
                    $fileURL = $this->ocs_2($url);
                    if (substr($fileURL,0,4) == 'http')
                        {
                            $DownloadPDF = new \App\Models\Bots\DownloadPDF();
                            $dir = $DownloadPDF->directory($id);
                            $filePDF = $dir.'work_' . strzero($id, 8) . '.pdf';

                            echo "Aguarde...";

                            $txtFile = read_link($fileURL);
                            file_put_contents($filePDF, $txtFile);
                            $id = $DownloadPDF->create_FileStorage($id, $filePDF);

                            echo metarefresh(0);
                        }
                }
        }

    function ocs_2($url)
        {
            if (strpos($url, 'paper/view'))
                {
                    $txt = read_link($url);
                    if ($pos = strpos($txt, 'citation_pdf_url'))
                        {
                            $txt = substr($txt,$pos,300);
                            $st = 'content="';
                            $txt = substr($txt,strpos($txt,$st)+strlen($st),strlen($txt));
                            $txt = substr($txt,0,strpos($txt,'"'));
                            if (substr($txt,0,4) == 'http')
                                {
                                    return $txt;
                                }
                        }
                    echo "OK";
                    exit;
                }
        }

    function download_pdf($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $data = $dt['concept'];
        $class = $data['c_class'];
        $file = 'NOT FOUND';
        switch ($class) {
            case 'Book':
                $idf = $RDF->extract($dt, 'hasFileStorage');
                if ($idf[0] > 0) {
                    $dtf = $RDF->le($idf[0]);
                    $file = $dtf['concept']['n_name'];
                }
                break;
            case 'Proceeding':
                $idf = $RDF->extract($dt, 'hasFileStorage');
                if (count($idf) > 0) {
                    if ($idf[0] > 0) {
                        $dtf = $RDF->le($idf[0]);
                        $file = $dtf['concept']['n_name'];

                        if (file_exists($file)) {
                            header('Content-type: application/pdf');
                            readfile($file);
                            exit;
                        }
                    }
                }

                echo '<center>';
                echo h('File not found in this server (' . $class . ')', 4);
                echo $file;
                echo '</center>';
                echo '<hr>';

                $Socials = new \App\Models\Socials();
                if ($Socials->getAccess("#ADM#CAT#BEN")) {
                    echo $this->download_tools($id);
                }
                exit;
        }
    }
}
