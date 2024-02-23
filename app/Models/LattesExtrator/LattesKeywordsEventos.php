<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesKeywordsEventos extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'lattes_keyword_producao';
    protected $primaryKey       = 'id_kp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kp', 'kp_keyword','kp_producao','kp_tipo'
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

    function csv($prj=0)
        {
            $dt = $this
                ->select('le_authors, le_ano, ky_name, kp_producao, le_title')
                ->join('lattesproducao_evento', '(id_le = kp_producao) and (kp_tipo = "E")')
                ->join('brapci_tools.projects_harvesting_xml', '(hx_id_lattes =  le_author) and (hx_project = ' . $prj . ')')
                ->join('lattes_keywords', 'kp_keyword = id_ky')
                ->where('1=1')
                ->findAll();

            $xcap = '';
            $sx = '';
            $xname = '';
            foreach($dt as $id=>$line)
                {
                    $prod = $line['kp_producao'];
                    $name = '"'.$line['le_authors']. '","' . $line['le_ano'] . '","' . $line['le_title'] . '"';
                    if ($prod != $xcap)
                        {
                           if ($xcap != '') { $sx .=  cr(); }
                            $sx .= $name;
                            $xcap = $prod;
                        }
                    $sx .= '"'.$line['ky_name'].'",';
                }
            $sx .= cr();

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=brapci_tools_keywords_proceeding_" . date("Ymd-His") . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo 'AUTHOR,YEAR,TITLE,KEY1,KEY2,KEY3,KEY4,KEY5,KEY6'.cr();
        echo utf8_decode($sx);
        exit;

        }
}
