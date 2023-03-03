<?php

namespace App\Models\PQ;

use CodeIgniter\Model;

class CNPQ extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cnpqs';
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

    function crawler()
    {
        $sx = '';
        $Bolsistas = new \App\Models\PQ\Bolsistas();
        $Bolsas = new \App\Models\PQ\Bolsas();
        $Modalidades = new \App\Models\PQ\Modalidades();

        $url = 'http://plsql1.cnpq.br/divulg/RESULTADO_PQ_102003.prc_comp_cmt_links?V_COD_DEMANDA=200310&V_TPO_RESULT=CURSO&V_COD_AREA_CONHEC=60700009&V_COD_CMT_ASSESSOR=AC';
        $txt = read_link($url);
        $txt = utf8_encode($txt);

        $txt = substr($txt, strpos($txt, '<a name'), strlen($txt));

        $txt = troca($txt, chr(13), '');
        $txt = troca($txt, chr(10), '<#>');
        $txt = troca($txt, '<tr>', '#!#');
        $txt = troca($txt, '<td', ';<ta');
        $txt = troca($txt, '</td>', '');
        $txt = troca($txt, chr(8), ';');

        while (strpos($txt, '<#><#>')) {
            $txt = troca($txt, '<#><#>', '<#>');
        }

        $txt = troca($txt, '#!#', chr(10));

        /****************************************************************************************** HARVESTING */
        $txt = strip_tags($txt);
        $ln = explode(chr(10), $txt);
        $i = 0;
        foreach ($ln as $id => $line) {
            if ($i > 2) {
                $dd = explode(';', $line);
                $dt = $Bolsistas->where('bs_nome', $dd[1])->findAll();

                if (count($dt) == 0) {
                    $da['bs_nome'] = $dd[1];
                    $da['bs_rdf'] = 0;
                    $da['bs_lattes'] = 'NI';
                    $idp = $Bolsistas->set($da)->insert();
                } else {
                    $idp = $dt[0]['id_bs'];
                }

                if (!isset($dd[3]))
                    {
                        break;
                    }

                $di = $dd[3];
                $di = substr($di, 6, 4) . '-' . substr($di, 3, 2) . '-' . substr($di, 0, 2);
                $df = $dd[4];
                $df = substr($df, 6, 4) . '-' . substr($df, 3, 2) . '-' . substr($df, 0, 2);

                $tp = substr($dd[2], 0, 2);
                if ($tp == 'PQ') {
                    $db['bb_person'] = $idp;
                    $db['bs_tipo'] = 1;
                    $db['bs_nivel'] = substr($dd[2],3,2);
                    $db['bs_start'] = $di;
                    $db['bs_finish'] = $df;
                    $db['BS_IES'] = $dd[5];
                    $db['bs_ativo'] = 1;

                    $dx = $Bolsas
                        ->where('bb_person', $idp)
                        ->where('bs_finish', $df)
                        ->findAll();
                    if (count($dx) == 0) {
                        $Bolsas->set($db)->insert();
                        $sx .= $dd[1] . ' - Insert<br>';
                    } else {
                        $sx .= $dd[1]. ' - Update<br>';
                        $Bolsas->set($db)->where('id_bb', $dx[0]['id_bb'])->update();
                    }
                }
            }
            $i++;
        }
        return bs(bsc($sx));
    }
}
