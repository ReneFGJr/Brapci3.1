<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class RPIImport extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rpiimports';
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

    function proccess($id = -1)
    {
        $sx = '';
        $dn = 0;
        if ($id <= 0) {

            $RDPIssue = new \App\Models\Patent\RPIIssue;
            $dt = $RDPIssue->select('max(id_rpi) as id')->where('rpi_status', 2)->findAll();
            $di = round($dt[0]['id']);
            if ($di > 0) {
                $dt = $RDPIssue->find($di);
                $dn = $dt['rpi_nr'];
            }
        } else {
            $dn = $id;
        }

        if ($dn > 0) {
            $sx .= $this->import($dn);
        }
        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    function import($id)
    {
        $RPI = new \App\Models\Patent\RPI;
        $sx = h('Importação', 2);
        /* Importa dados */

        /* 1- Abrir arquivo de dados */
        $dir = '../.tmp/.inpi/patent/';
        $fls = scandir($dir);

        for ($r = 0; $r < count($fls); $r++) {
            if (strpos($fls[$r], '.xml') > 0) {
                $file = $dir . $fls[$r];
                $sx .= h('Arquivo: ' . $file, 3);
                $sx .= $this->method_xml($file, $id);
            }
        }

        $file = 'P' . $id . '.xml';
        $filename = $dir . $file;

        if (file_exists($dir . $file)) {
            $this->method_xml($dir . $file);
        } else {
            $sx .= bsmessage('File not found: ' . $filename, 3);
        }
        return $sx;
    }

    function method_xml($file, $id_issue)
    {
        $RPISection = new \App\Models\Patent\RPISections();
        $RPIPatente = new \App\Models\Patent\RPIPatentNR();
        $RPIDespacho = new \App\Models\Patent\RPIDespacho();
        $sx = '';
        $xml = (array)simplexml_load_file($file);
        $id_sec = 0;

        $RPISection = new \App\Models\Patent\RPISections;
        $despacho = (array)$xml['despacho'];
        $xcode = '';
        for ($r=0;$r < count($despacho);$r++)
            {
                $reg = (array)$despacho[$r];

                $code = $reg['codigo'];
                $desc = $reg['titulo'];

                if ($code != $xcode)
                    {
                        $sx .= h($code . ' - ' . $desc, 3);
                        $id_sec = $RPISection->register($code,$desc);
                        $id_sec = $id_sec['id_rsec'];
                        $xcode = $code;
                    }

                /******************* PATENT PROCESSO */
                $processo = (array)$reg['processo-patente'];
                $patenteNR = (string)$processo['numero'];
                $dataDeposito = '1000-01-01';
                if (isset($processo['data-deposito']))
                    {
                        $dataDeposito = (string)$processo['data-deposito'];
                    }
                $idp = $RPIPatente->register($patenteNR);
                $link = '<a href="'.(PATH.'/patente/v/'. $idp).'" target="_new">';
                $linka = '</a>';
                $sx .= 'Patente: ' . $link.$patenteNR.$linka . ' - ' . $dataDeposito . '<br>'.cr();

                /*********** AUTORES & INVENTORES */
                if (isset($reg['titulo']))
                    {
                        $titulo = $reg['titulo'];
                        echo $sx;
                        echo $idp.'==='.$titulo;
                        //exit;
                    }

                /*********** DESPACHO *************/
                if (isset($reg['comentario']))
                    {
                        $coment = (string)$reg['comentario'];
                        $RPIDespacho->register($idp, $id_issue, $id_sec,$coment);
                    }
            }
        return $sx;
    }
}
