<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesEndereco extends Model
{
	protected $DBGroup              = 'lattes';
	protected $table                = 'lattesendereco';
	protected $primaryKey           = 'id_ed';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_ad', 'ad_id', 'ad_inst_cod',
		'ad_inst_orgao', 'ad_inst_orgao_cod', 'ad_inst_unidade',
		'ad_inst_unidade_cod', 'ad_country', 'ad_uf',
		'ad_cep', 'ad_cidade', 'ad_url',

	];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	function selo($total, $desc)
	{
		$sx = '';
		$sx .= '<div class="text-center p-1 mb-3" style="width: 100%; border: 1px solid #000; border-radius: 10px;  line-height: 80%;">';
		$sx .= '<span style="font-size: 16px;">' . $total . '</span>';
		$sx .= '<br>';
		$sx .= '<b style="font-size: 12px; ">' . $desc . '</b>';
		$sx .= '</div>';
		return $sx;
	}

	function resume($id)
	{
		$dt = $this->select('count(*) as total, lp_author')
			->where('lp_author', $id)
			->groupBy('lp_author')
			->findAll();
		if (count($dt) > 0) {
			return $dt[0]['total'];
		}
		return 0;
	}

	function formacao($id)
	{
		$tela = '';
		$dt = $this->where('lp_author', $id)->orderBy('lp_ano', 'desc')->findAll();
		$tela .= '<ol>';
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$tela .= '<li>' . $line['lp_authors'] . '. ' . $line['lp_title'] . '. ';
			$tela .= '<b>' . $line['lp_journal'] . '</b>';
			if (strlen($line['lp_vol']) > 0) {
				$tela .= ', ' . $line['lp_vol'];
			}
			if (strlen($line['lp_nr']) > 0) {
				$tela .= ', ' . $line['lp_nr'];
			}
			$tela .= ', ' . $line['lp_ano'];
			$tela .= '</li>';
		}
		$tela .= '</ol>';
		return $tela;
	}

	function dados_xml($id)
	{
		$LattesInstituicao = new \App\Models\LattesExtrator\LattesInstituicao();
		$Lang = new \App\Models\Language\Lang();
		$LattesExtrator = new \App\Models\LattesExtrator\Index();
		$file = $LattesExtrator->fileName($id);
		if (!file_exists($file)) {
			echo "ERRO NO ARQUIVO " . $file;
			exit;
		}
		$xml = (array)simplexml_load_file($file);

		$xml = (array)$xml;
		$prod = (array)$xml['DADOS-GERAIS'];
		$arti = (array)$prod['ENDERECO'];
		$arti = (array)$arti['ENDERECO-PROFISSIONAL'];
		$arti = (array)$arti['@attributes'];

		$dt['ad_id'] = $id;
		$dt['ad_inst'] = $arti['NOME-INSTITUICAO-EMPRESA'];
		$dt['ad_inst_cod'] = $arti['CODIGO-INSTITUICAO-EMPRESA'];
		$dt['ad_inst_orgao'] = $arti['NOME-ORGAO'];
		$dt['ad_inst_orgao_cod'] = $arti['CODIGO-ORGAO'];
		$dt['ad_inst_unidade'] = $arti['NOME-UNIDADE'];
		$dt['ad_inst_unidade_cod'] = $arti['CODIGO-UNIDADE'];
		$dt['ad_country'] = $arti['PAIS'];
		$dt['ad_uf'] = $arti['UF'];
		$dt['ad_cep'] = $arti['CEP'];
		$dt['ad_cidade'] = $arti['CIDADE'];
		$dt['ad_bairro'] = $arti['BAIRRO'];
		$dt['ad_url'] = $arti['HOME-PAGE'];

		$this->register($id,$dt);
		return '';
	}

	function register($id,$dt)
		{
			$dta = $this
				->where('ad_id',$id)
				->findAll();
			if (count($dta) == 0)
				{
					$this->set($dt)->insert();
				} else {
					$idx = $dta[0]['ad_id'];
					$this->set($dt)
					->where('ad_id',$idx)
					->update();
				}
		}

	function status($t)
		{
			switch($t)
				{
					case 'CONCLUIDO':
						return "C";
						break;
					default:
						echo "FORMACAO STATUS:$t";
						exit;
				}
		}
}