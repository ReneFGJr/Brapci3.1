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
		'id_ad', 'ad_id', 'ad_inst_cod', 'ad_inst',
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

	function resume($id)
	{
		$dt = $this->where('ad_id',$id)->first();
		if ($dt == '') { return ""; }
		$sx = '';
		$style="";
		$sx .= '<table width="100%" class="card mt-2"> ';
		$sx .= '<tr>';
		$sx .= '<td width="32" class="p-2 text-center" style="font-size: 0.6em; ' . $style . '">' . bsicone('homefill', 32)  . '</td>';
		$sx .= '<td style="font-size: 0.6em;">';
		$sx .= '<b>'.$dt['ad_inst'].'</b>';
		$sx .= '<br>';
		$sx .= $dt['ad_inst_orgao'];
		$sx .= '</td>';
		$sx .= '</tr>';
		$sx .= '</table>';

		return $sx;
	}

	function zerezima_dados_xml($id)
	{
		$this->where('ad_id', $id)->delete();
		return true;
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
		if (!isset($arti['ENDERECO-PROFISSIONAL']))
			{
				return "";
			}
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

	function csv($id)
	{
		$dt = $this
			->join('brapci_tools.projects_harvesting_xml', 'ad_id  = hx_id_lattes')
			->join('lattesdados', 'ad_id = lt_id')
			->where('hx_project', $id)
			->findAll();

		$sx = 'IDLATTES,NAME,INSTITUTION,DEPARTAMENT,UNIT,COUNTRY,STATE,CITY' . chr(13);
		foreach ($dt as $id => $line) {

			$sa = '';
			$sa .= '"' . $line['ad_id'] . '",';
			$sa .= '"' . $line['lt_name'] . '",';
			$sa .= '"' . $line['ad_inst'] . '",';
			$sa .= '"' . $line['ad_inst_orgao'] . '",';
			$sa .= '"' . $line['ad_inst_unidade'] . '",';
			$sa .= '"' . $line['ad_country'] . '",';
			$sa .= '"' . $line['ad_uf'] . '",';
			$sa .= '"' . $line['ad_cidade'] . '",';
			$sa = troca($sa, chr(13), '');
			$sa = troca($sa, chr(10), '');
			$sx .= $sa . chr(13);
		}

		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=brapci_tools_affiliation_" . date("Ymd-His") . ".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $sx;
		exit;
	}


}