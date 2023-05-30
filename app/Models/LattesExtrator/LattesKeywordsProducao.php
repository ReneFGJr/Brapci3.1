<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesKeywordsProducao extends Model
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

    function register($prod, $key,$type)
        {
            $dt = $this
                ->where('kp_keyword',$key)
                ->where('kp_producao', $prod)
                ->where('kp_tipo',$type)
                ->first();
            if ($dt == '')
                {
                    $dt['kp_keyword'] = $key;
                    $dt['kp_producao'] = $prod;
                    $dt['kp_tipo'] = $type;
                    $this->set($dt)->insert();
                }
            return true;
        }

    function csv($prj=0)
        {
            $dt = $this
                ->select('lp_authors, ky_name, kp_producao')
                ->join('LattesProducao', '(id_lp = kp_producao) and (kp_tipo = "A")')
                ->join('brapci_tools.projects_harvesting_xml', '(hx_id_lattes =  lp_author) and (hx_project = ' . $prj . ')')
                ->join('lattes_keywords', 'kp_keyword = id_ky')
                ->where('1=1')
                ->findAll();

            $xcap = '';
            $sx = '';
            $xname = '';
            foreach($dt as $id=>$line)
                {
                    $prod = $line['kp_producao'];
                    $name = $line['lp_authors'];
                    if ($prod != $xcap)
                        {
                           if ($xcap != '') { $sx .=  cr(); }
                            $sx .= '"'.$name.'",';
                            $xcap = $prod;
                        }
                    $sx .= '"'.$line['ky_name'].'",';
                }
            $sx .= cr();

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=brapci_tools_keywords_" . date("Ymd-His") . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo 'AUTHOR,KEY1,KEY2,KEY3,KEY4,KEY5,KEY6'.cr();
        echo utf8_decode($sx);
        exit;

        }
}
