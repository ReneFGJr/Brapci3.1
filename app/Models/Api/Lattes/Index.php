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

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        $sx = '';
        switch ($d1) {
            case 'kton':
                $KtoN = new \App\Models\Api\Lattes\KtoN();
                $sx .= $KtoN->list($d2);
                break;
            case 'extract':
                echo $this->extract_from_markers();
                exit;
                break;
            default:
                break;
        }
        return $sx;
    }

    function checkID($code)
    {
        $Lattes = new \App\Models\Lattes\Index();
        return $Lattes->checkID($code);
    }

    function registre_id_lattes($id, $data, $force = true)
    {
        if ($force == true) {
            $dt = array();
        } else {
            $dt = $this->where('cv_NRO_ID_CNPQ', $id)->findAll();
        }

        if (count($dt) == 0) {
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

    function harvesting_next($prj)
    {
        $sx = '';
        $LattesExtrator = new \App\Models\LattesExtrator\Index();
        $ProjectsHarvestingXml = new \App\Models\Tools\ProjectsHarvestingXml();
        $LattesDados = new \App\Models\LattesExtrator\LattesDados();

        /* PHASE I ****************************** Localiza Próxima Coleta */
        $dt = $ProjectsHarvestingXml
            ->where('hx_project', $prj)
            ->where('hx_status', 0)
            ->limit(1)
            ->orderby("updated_at")
            ->first();

        /* PHASE II - MARCA REGISTRO DE ATUALIZACAO */
        $dta['updated_at'] = date("Y-m-d H:i:s");
        $ProjectsHarvestingXml->set($dta)->where('hx_project', $prj)->update();

        /* SE EXISTE DADOS PARA COLETAR vai para proxima faase ****************/
        if ($dt != '') {
            /* PHASE III ******************************* Coleta dadados */
            $id_lattes = $dt['hx_id_lattes'];

            $LT = new \App\Models\LattesExtrator\Index();
            $dt = $LT->harvesting($id_lattes);

            /* PHASE IV ******************* Checa se existe arquivo *****/

            $sx .= '<br>Harvesting ' . $id_lattes;
            $file_xml = '../.tmp/lattes/' . $id_lattes . '.xml';
            if (file_exists($file_xml)) {
                $sx .= '<br>File XML OK';
            } else {
                $sx .= "<br>File $file_xml OK";
            }

            /* PHASE V ****************** Recupera dados do Pesquisador *****/
            $dta = $LattesDados->le_id($id_lattes);
            if (count($dta) == 0) {
                echo "ERRO: registro vazio";
                exit;
            }
            $dt = array();
            $dt['hx_name'] = $dta['lt_name'];
            $dt['hx_updated'] = $dta['lt_atualizacao'];
            $dt['updated_at'] = date("Y-m-d H:i:s");
            $dt['hx_status'] = 1;
            $ProjectsHarvestingXml->set($dt)->where('hx_id_lattes', $id_lattes)->update();
            $sx .= cr() . 'Updated';

            $sx .= metarefresh('',2);

            echo $sx;
            exit;
        }
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
                    if (($nr > 0) and ($nr >= $offset) and ($nr <= ($offset + $limit))) {
                        $line = troca($line, '"', '');
                        $fld = explode(",", $line);

                        if ($this->registre_id_lattes($fld[0], $fld)) {
                            $sx .= ($fld[0] . ' - ' . $fld[1]) . '<br>';
                        } else {
                            $sx .= ($fld[0] . ' - ' . $fld[1] . ' - Already') . '<br>';
                        }
                    }
                    /************** Stop ************/
                    if ($nr >= ($offset + $limit + 1)) {
                        $sx .= metarefresh(PATH . COLLECTION . '/lattes/extract/?offset=' . $nr, 3);
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
