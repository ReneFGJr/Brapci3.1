<?php

namespace App\Models\Api\Lattes;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'lattes_curriculo';
    protected $primaryKey       = 'id_cv';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cv_name', 'cv_NRO_ID_CNPQ',
        'cv_SGL_PAIS', 'COD_AREA', 'cv_COD_NIVEL',
        'DT_ATUALIZA', 'DTA_CARGA', 'updated_at',
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

    function index($d1='',$d2='',$d3='')
        {
            switch($d1)
                {
                    case 'extract':
                        echo $this->extract_from_markers();
                        exit;
                        break;
                    default:
                        break;
                }
        }

    function registre_id_lattes($id,$data,$force=true)
        {
            if ($force == true)
                {
                    $dt = array();
                } else {
                    $dt = $this->where('cv_NRO_ID_CNPQ', $id)->findAll();
                }

            if (count($dt) == 0)
                {
                    $data['cv_NRO_ID_CNPQ'] = $id;
                    $data['cv_SGL_PAIS'] = $data[1];
                    $data['COD_AREA'] = $data[3];
                    $data['cv_COD_NIVEL'] = $data[4];
                    $data['DT_ATUALIZA'] = stodus(brtos($data[2]));
                    $data['DTA_CARGA'] = date("Y-m-d");
                    $this->insert($data);
                    return true;
                }
            return false;
        }

    function extract_from_markers()
        {
            $sx = '';
            $url = 'http://memoria.cnpq.br/web/portal-lattes/extracoes-de-dados';
            $url = 'http://memoria.cnpq.br/documents/313759/83395da6-f582-46bc-a308-060a6ec1ceaa';

            $file = '../_Documments/Lattes/R358737.csv';
            if (file_exists($file)) {
                    /********************* Read Line by Line */
                        $offset = round(get("offset"));
                        $nr = 0;
                        $limit = 500;
                        $handle = fopen($file, "r");
                        if ($handle) {
                            while (($line = fgets($handle)) !== false) {
                                /*************** In Range Scopo */
                                if (($nr > 0) and ($nr >= $offset) and ($nr <= ($offset+$limit)))
                                    {
                                        $line = troca($line, '"', '');
                                        $fld = explode(",", $line);

                                        if ($this->registre_id_lattes($fld[0],$fld))
                                            {
                                                $sx .= ($fld[0].' - '.$fld[1]).'<br>';
                                            } else {
                                                $sx .= ($fld[0].' - '.$fld[1].' - Already') . '<br>';
                                            }
                                    }
                                /************** Stop ************/
                                if ($nr >= ($offset + $limit + 1))
                                    {
                                        $sx .= metarefresh(PATH.COLLECTION.'/lattes/extract/?offset='.$nr,3);
                                        break;
                                    }
                                $nr++;
                            }
                            fclose($handle);
                        }
                    return $sx;
                }


        }
}
