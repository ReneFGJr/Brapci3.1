<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class RPIImport extends Model
{
    protected $DBGroup          = 'patent';
    protected $table            = 'rpi_issue';
    protected $primaryKey       = 'id_rpi';
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

    var $SourceType = 'none';
    var $SourceFile = '';

    function import($id)
    {
        $RPI = new \App\Models\Patent\RPI;
        $sx = h('Importação' . ' Nr.' . $id, 2);
        $sx .= h('Source: ' . $this->source($id), 5);

        $dt = $this->le_nr($id);
        $sx .= h('Status actual: ' . $dt['rpi_status'], 5);

        echo 'Method: ['.$dt['rpi_status'].']'.cr();

        /* Importa dados */
        switch ($dt['rpi_status']) {
            case '6':
                $sx .= $this->method_01_agents($id);
                break;
            case '5':
                $sx .= $this->method_01_despacho($id);
                break;
            case '4':
                $sx .= $this->method_01_patent_nr($id);
                break;
            case '3':
                $sx .= $this->method_01_issue_sections($id);
                break;
            case '2':
                $sx .= $this->method_01_issue_data($id);
                break;
        }
        return $sx;
    }

    function proccess($id = -1)
    {
        echo 'Process RPI nº'.$id.cr();
        $sx = h(lang('patent.proccess'), 2);
        if ($id < 0) {
            $sx .= h(lang('patent.proccess_next'), 4);
        }
        $dn = 0;

        if ($id <= 0) {

            $RPIIssue = new \App\Models\Patent\RPIIssue;
            $dt = $RPIIssue->select('max(id_rpi) as id')->where('rpi_status', 2)->findAll();

            $di = round($dt[0]['id']);
            if ($di > 0) {
                $dt = $RPIIssue->find($di);
                $dn = $dt['rpi_nr'];
            }
        } else {
            $dn = $id;
        }

        if ($dn > 0) {
            /******************** IMPORT */
            echo 'PROCCESS AT '.date("Y-m-d H:i:s") .' START'. cr();
            $sx .= $this->import($dn);
            echo 'PROCCESS AT '.date("Y-m-d H:i:s") .' END'. cr();
        } else {
            $sx .= bsmessage(lang('petent.rpi_import_no_data'), 3);
        }
        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    /*************************************************** Basic Functions */
    function le_nr($id)
    {
        $dt = $this->where('rpi_nr', $id)->findAll();
        return ($dt[0]);
    }
    function le($id)
    {
        $dt = $this->where('id_rpi', $id)->findAll();
        return ($dt[0]);;
    }


    function source($id)
    {
        $sx = '';
        $this->SourceType = 'none';
        $this->SourceFile = '';

        $dir = '../.tmp/.inpi/patent/';
        $fls = scandir($dir);
        /*********** Busca XML */
        for ($r = 0; $r < count($fls); $r++) {
            if ((strpos($fls[$r], '.xml') > 0) and (strpos($fls[$r],'_'.$id.'_')))
            {
                $file = $dir . $fls[$r];
                $sx .= $file;
                $this->SourceFile = $file;
                $this->SourceType = 'xml';
                return $sx;
            }
        }
        return $sx;
    }


    /*************************************************** Import Methods */
    function method_01_agents($id)
        {
        $sx = '';
        switch ($this->SourceType) {
            case 'xml':
                $RPIIssue = new \App\Models\Patent\RPIIssue;
                $RPIAgents = new \App\Models\Patent\RPIAgents;
                $RPIAgents->import($id, $this->SourceFile);
                $RPIIssue->register($id, 7);
                break;
            default:
                $sx = bsmessage('Source type not defined', 3);
                break;
        }
        return $sx;
        }
    function method_01_despacho($id)
        {
        $sx = '';
        switch ($this->SourceType) {
            case 'xml':
                $RPIIssue = new \App\Models\Patent\RPIIssue;
                $RPIDespacho = new \App\Models\Patent\RPIDespacho;
                $sx .= $RPIDespacho->import($id, $this->SourceFile);
                $RPIIssue->register($id, 6);
                break;
            default:
                $sx = bsmessage('Source type not defined', 3);
                break;
        }
        return $sx;
        }
    function method_01_patent_nr($id)
        {
        $sx = '';
        switch ($this->SourceType) {
            case 'xml':
                $RPIIssue = new \App\Models\Patent\RPIIssue;
                $RPIPatentNR = new \App\Models\Patent\RPIPatentNR;
                $sx .= $RPIPatentNR->import($id, $this->SourceFile);
                $RPIIssue->register($id, 5);
                break;
            default:
                $sx = bsmessage('Source type not defined', 3);
                break;
        }
        return $sx;
        }
    function method_01_issue_sections($id)
        {
        $sx = '';
        switch ($this->SourceType) {
            case 'xml':
                $RPIIssue = new \App\Models\Patent\RPIIssue;
                $RPISection = new \App\Models\Patent\RPISections;
                $sx .= $RPISection->import($id,$this->SourceFile);
                $RPIIssue->register($id, 4);
                break;
            default:
                $sx = bsmessage('Source type not defined', 3);
                break;
        }
        return $sx;
        }

    function method_01_issue_data($id)
        {
            $sx = '';
            $dt = $this->le_nr($id);

            switch($this->SourceType)
                {
                    case 'xml':
                        $this->method_01_issue_data_xml($id);
                        break;
                    default:
                        echo 'Source type not found - ['.$this->SourceType.']'.cr();
                        break;
                }
            return $sx;
        }

    function method_01_issue_data_xml($id)
        {
            $xml = (array)simplexml_load_file($this->SourceFile);
            if (isset($xml['@attributes']))
                {
                    $xml = $xml['@attributes'];
                    $date = $xml['dataPublicacao'];
                    $date = brtos($date);
                    $date = substr($date,0,4).'-'.
                            substr($date,4,2).'-'.
                            substr($date,6,2);

                    $RPIIssue = new \App\Models\Patent\RPIIssue;
                    $data['rpi_data'] = $date;
                    $RPIIssue->set($data)->where('rpi_nr',$id)->update();
                    echo 'Data: '.$date.cr();
                    $RPIIssue->register($id, 3);
                    $sx = metarefresh(PATH . COLLECTION . '/proccess/' . $id, 1);
                    return $sx . bsmessage('Date publish registred', 1);
                }
            return bsmessage('Erro: Publish date not found',3);
        }

    function method_xml($file, $id_issue)
    {
        $RPISection = new \App\Models\Patent\RPISections();
        $RPIPatente = new \App\Models\Patent\RPIPatentNR();
        $RPIDespacho = new \App\Models\Patent\RPIDespacho();
        $sx = '';
        $xml = (array)simplexml_load_file($file);
        $id_sec = 0;

        /************************ DATA DA PUBLICAÇÂO */
        pre($xml);

        $RPISection = new \App\Models\Patent\RPISections;



            $link = '<a href="' . (PATH . '/patente/v/' . $idp) . '" target="_new">';
            $linka = '</a>';
            $sx .= 'Patente: ' . $link . $patenteNR . $linka . ' - ' . $dataDeposito . '<br>' . cr();

            /*********** AUTORES & INVENTORES */
            if (isset($reg['titulo'])) {
                $titulo = $reg['titulo'];
                echo $sx;
                echo $idp . '===' . $titulo;
                //exit;
            }

            /*********** DESPACHO *************/
            if (isset($reg['comentario'])) {
                $coment = (string)$reg['comentario'];
                $RPIDespacho->register($idp, $id_issue, $id_sec, $coment);
            }

        return $sx;
    }
}
