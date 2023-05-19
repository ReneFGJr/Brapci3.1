<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesProducaoTecnica extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'lattesproducaotecnicas';
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

    function zerezima_dados_xml($id)
    {
        $this->where('lt_author', $id)->delete();
        return true;
    }

    function producao_xml($id)
    {
		$Lang = new \App\Models\Language\Lang();
		$LattesExtrator = new \App\Models\LattesExtrator\Index();
		$file = $LattesExtrator->fileName($id);
		if (!file_exists($file)) {
			echo "ERRO NO ARQUIVO " . $file;
			exit;
		}
		$xml = (array)simplexml_load_file($file);

		$xml = (array)$xml;
    }

/*
if (count($rst) == 0) {
    $idp = $this->insert($p);
} else {
    $idp = $dt[0]['id_le'];
}
/****************** KEYWORDS */
/*
if (isset($line['PALAVRAS-CHAVE'])) {
    $Keywords = new \App\Models\LattesExtrator\LattesKeywords();
    $dados = (array)$line['PALAVRAS-CHAVE'];
    $dados = (array)$dados['@attributes'];
    $Keywords->keyword_xml($idp, $dados, 'C');
}
*/
}
