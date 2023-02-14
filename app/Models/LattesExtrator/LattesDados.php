<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesDados extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'lattesdados';
    protected $primaryKey       = 'id_lt';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_lt', 'lt_id', 'lt_idk',
        'lt_name', 'lt_atualizacao', 'lt_nacionalidade_id',
        'lt_orcid'
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

    function le_id($id)
    {
        $dta =
            $this
            ->where('lt_id', $id)
            ->findAll();

            if (count($dta) == 0)
                {
                    return array();
                }
        return $dta[0];
    }

    function zerezima_dados_xml($id)
        {
            $this->where('lt_id',$id)->delete();
            return true;
        }

    function dados_xml($id)
    {
        $dt = array();
        $Lang = new \App\Models\Language\Lang();
        $LattesExtrator = new \App\Models\LattesExtrator\Index();
        $LattesInstituicao = new \App\Models\LattesExtrator\LattesInstituicao();
        $file = $LattesExtrator->fileName($id);
        if (!file_exists($file)) {
            echo "ERRO NO ARQUIVO " . $file;
            exit;
        }
        $xml = (array)simplexml_load_file($file);

        $xml = (array)$xml;

        /*************************************** Data e Hora da atualização */
        $aut = (array)$xml['@attributes'];
        $data = $aut['DATA-ATUALIZACAO'];
        $data = substr($data, 4, 4) . '-' . substr($data, 2, 2) . '-' . substr($data, 0, 2);
        $hora = $aut['HORA-ATUALIZACAO'];
        $hora = substr($hora, 0, 2) . ':' . substr($hora, 2, 2) . ':' . substr($hora, 4, 2);
        $dt['lt_atualizacao'] = $data . ' ' . $hora;

        /************************************** NOME *************************/
        $dados = (array)$xml['DADOS-GERAIS'];
        $aut = (array)$dados['@attributes'];
        $dt['lt_id'] = $id;
        $dt['lt_name'] = trim((string)$aut['NOME-COMPLETO']);
        $dt['lt_name_cit'] = trim((string)$aut['NOME-EM-CITACOES-BIBLIOGRAFICAS']);

        $dt['lt_DIVULGACAO'] = trim((string)$aut['PERMISSAO-DE-DIVULGACAO']);

        $dt['lt_NASCIMENTO_CIDADE'] = trim((string)$aut['CIDADE-NASCIMENTO']);
        $dt['lt_NASCIMENTO_ESTADO'] = trim((string)$aut['UF-NASCIMENTO']);
        $dt['lt_NASCIMENTO_PAIS'] = trim((string)$aut['PAIS-DE-NASCIMENTO']);

        if (isset($aut['ORCID-ID']))
            {
                $dt['lt_orcid'] = trim((string)$aut['ORCID-ID']);
                $dt['lt_orcid'] = troca($dt['lt_orcid'], 'https://orcid.org/', '');
                $dt['lt_orcid'] = troca($dt['lt_orcid'], 'http://orcid.org/', '');
            } else {
                $dt['lt_orcid'] = '';
            }
        $this->dados($dt);
        return ($dt);
    }

    function dados($dt)
    {
        $dta =
            $this
            ->where('lt_id', $dt['lt_id'])
            ->findAll();
        if (count($dta) == 0) {
            $this->set($dt)->insert();
        } else {
            $this->set($dt)->where('lt_id', $dt['lt_id'])->update();
        }
    }
}
