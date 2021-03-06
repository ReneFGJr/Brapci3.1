<?php

namespace App\Models\Bots;

use CodeIgniter\Model;

class DownloadPDF extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_bots.article_to_download';
    protected $primaryKey       = 'id_d';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_d', 'd_article', 'd_method',
        'd_url', 'd_lasupdate'
    ];

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

    function toHarvesting($id)
    {
        $dt = $this->where('d_article', $id)->findAll();
        if (count($dt) == 0) {
            $dt['d_article'] = $id;
            $dt['d_method'] = 0;
            $dt['d_lasupdate'] = date("Y-m-d");
            $dt['d_url'] = '';
            $this->insert($dt);
        }
    }

    /********************************************************************* HARVESTING */
    function harvesting()
    {
        $sx = '';
        $dt = $this
            ->where('d_method <> 99')
            ->orderBy('d_method')
            ->limit(1)
            ->findAll();

        if (count($dt) > 0) {
            $dt = $dt[0];
            $id = $dt['d_article'];
            $mt = $dt['d_method'];

            /************************************* RDF */
            $RDF = new \App\Models\Rdf\RDF();
            $dd = $RDF->le($id);

            switch ($mt) {
                case '9':
                    /************ HARVESTING */
                    $sx .= $this->harveting_pdf($id);
                    break;
                case '0':
                    $http = $RDF->extract($dd, 'hasRegisterId');
                    $txt = $this->getFile($http[0]);
                    $mth = $this->method_identify($txt, $id);
                    $sx .= 'Harvesting ' . $http[0] . cr();
                    $sx .= 'Method: ' . $mth . cr();
                    break;
            }
        }
        return $sx;
    }

    function getFile($http)
    {
        $txt = file_get_contents($http);
        return $txt;
    }

    /************************************************************************* UPDATE */
    function harvesting_update($id, $method = '', $url = '')
    {
        $dt['d_lasupdate'] = date("Y-m-d");
        if ($method != '') {
            $dt['d_method'] = round($method);
        }
        if ($url != '') {
            $dt['d_url'] = trim($url);
        }
        $this->set($dt)->where('d_article', $id)->update();
    }

    /****************************************************************** CHECK METHOD */
    function method_identify($txt, $id)
    {
        $Metadata = new \App\Models\Bots\Metadata();

        /***************************************************************** METHOD 1 */
        if (strpos($txt, 'citation_pdf_url') > 0) {
            $url = $Metadata->extract_dc('citation_pdf_url', $txt);
            /************************************ Existe HTML */
            if (substr($url, 0, 4) == 'http') {
                /****************************** TO HARVESTING */
                $this->harvesting_update($id, 9, $url);
            }
        }
        /***************************************************************** METHOD 2 */
        if (strpos($txt, 'frame src="') > 0) {
            $pos = strpos($txt, 'frame src="');
            $url = substr($txt, $pos, 200);
            $url = substr($url, strpos($url, '"') + 1, 200);
            $url = substr($url, 0, strpos($url, '"'));
            $txt = file_get_contents($url);
            $this->method_identify($txt, $id);
        }
        return "";
    }

    /******************************************************************* HARVESTING */
    function harveting_pdf($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $this->where('d_article', $id)->findAll();
        if (count($dt) > 0) {
            $line = $dt[0];
            $url = $line['d_url'];
            $fileO = $this->file_temp_file($id, $this->getFile($url));
            $fileD = $this->directory($id) . 'article_' . strzero($id, 8) . '.pdf';
            echo $fileO . '<br>';
            echo $fileD;
            rename($fileO, $fileD);
            $idf = $this->create_FileStorage($id, $fileD);
            $prop = 'hasFileStorage';
            $RDF->propriety($id, $prop, $idf, 0);
            $this->harvesting_update($id, 99);
            return h('IDF:' . $idf);
        }
    }

    function file_temp_file($id, $txt)
    {
        $dir = $this->directory(0);
        $fileO = $dir . md5($id) . '.pdf';
        file_put_contents($fileO, $txt);
        return $fileO;
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
            $dir .= substr($nr, 5, 2) . '/';
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
    function create_FileStorage($id, $filename)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $RDFData = new \App\Models\Rdf\RDFData();
        $RDFLiteral = new \App\Models\Rdf\RDFLiteral();

        $r2 = $RDF->RDF_concept($filename, 'FileStorage');

        /* TIPO DO ARQUIVO */
        $r3 = $RDF->RDF_concept('PDF', 'FileType', 'pt-BR', '');
        $prop = 'hasFileType';
        $RDF->propriety($r2, $prop, $r3, 0);

        /* Tamanho do Arquivo */
        $prop = 'hasFileSize';
        $size = filesize($filename);
        $id_size = $RDFLiteral->name($size, 'pt-BR');
        $RDF->propriety($r2, $prop, 0, $id_size);

        /* DATA DA COLETA DO ARQUIVO */
        $prop = 'hasDateTime';
        $idd = $RDF->RDF_concept(DATE("Y-m-d"), 'Date');
        $RDF->propriety($r2, $prop, $idd, 0);

        $prop = 'hasFileStorage';
        $RDF->propriety($id, $prop, $r2, 0);

        return $r2;
    }

    /*********************************************************** CHECK METHOD */
    function check_method($txt, $id)
    {
        $sx = '';
        /**************************************************************** citation_pdf_url */
        if ($pos = strpos($txt, 'citation_pdf_url')) {
            $file = substr($txt, $pos, strlen($txt));
            $file = substr($file, strpos($file, '="') + 2, strlen($file));
            $file = substr($file, 0, strpos($file, '"/>'));
            $temp = file_get_contents($file);
            dircheck('../.tmp/');
            dircheck('../.tmp/pdf/');
            $file_tmp = '../.tmp/pdf/' . md5($file) . '.pdf';
            file_put_contents($file_tmp, $temp);
            $content_type =  mime_content_type($file_tmp);
            if ($content_type == 'application/pdf') {
                $this->save_pdf_article($id, $file_tmp);
                $sx .= '<span class="btn btn-primary" style="width: 100%;">Harvesting</span>';
            }
        }
        /**************************************************************** citation_pdf_url */
        if ($pos = strpos($txt, 'frame src="')) {
            echo '====>' . $pos;
        }
        return $sx;
    }
}