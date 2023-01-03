<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesFormacao extends Model
{
	protected $DBGroup              = 'lattes';
	protected $table                = 'lattesformacao';
	protected $primaryKey           = 'id_f';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_f', 'f_id', 'f_type',
		'f_inst', 'f_inst_cod', 'f_curso',
		'f_curso_area', 'f_situacao', 'f_ano_ini',
		'f_ano_fim', 'f_orientador', 'f_orientador_lattes',
		'f_ppg',

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
		$arti = (array)$prod['FORMACAO-ACADEMICA-TITULACAO'];

		$graduacao = $this->extract($id, 'GRADUACAO', $arti);
		$mestrado = $this->extract($id,'MESTRADO', $arti);
		$doutorado = $this->extract($id, 'DOUTORADO', $arti);
		return '';
	}

	function register($id,$dt)
		{
			$dta = $this
				->where('f_id',$id)
				->where('f_inst_cod',$dt['f_inst_cod'])
				->where('f_ano_ini', $dt['f_ano_ini'])
				->where('f_ppg', $dt['f_ppg'])

				->findAll();
			if (count($dta) == 0)
				{
					$this->set($dt)->insert();
				} else {
					$idx = $dta[0]['id_f'];
					$this->set($dt)
					->where('id_f',$idx)
					->update();
				}
		}

	function extract($id,$type='GRADUACAO', $arti=array())
		{
		$curso = (array)$arti[$type];
		$dt = array();
		$dt['f_id'] = $id;
		$dt['f_type'] = substr($type,0,1);
		$curso = (array)$curso['@attributes'];
		$dt['f_inst'] = $curso['NOME-INSTITUICAO'];
		$dt['f_inst_cod'] = $curso['CODIGO-INSTITUICAO'];
		$dt['f_curso'] = $curso['NOME-CURSO'];
		$dt['f_curso_area'] = $curso['CODIGO-AREA-CURSO'];
		$dt['f_situacao'] = $this->status($curso['STATUS-DO-CURSO']);
		$dt['f_ano_ini'] = $curso['ANO-DE-INICIO'];
		$dt['f_ano_fim'] = $curso['ANO-DE-CONCLUSAO'];
		if (!isset($curso['NOME-COMPLETO-DO-ORIENTADOR']))
			{
				$dt['f_orientador'] = '';
				$dt['f_orientador_lattes'] = '';
			} else {
				$dt['f_orientador'] = $curso['NOME-COMPLETO-DO-ORIENTADOR'];
				$dt['f_orientador_lattes'] = $curso['NUMERO-ID-ORIENTADOR'];
			}

		$dt['f_ppg'] = $curso['CODIGO-CURSO-CAPES'];
		$dt['f_ano_fim'] = $curso['ANO-DE-CONCLUSAO'];
		$this->register($id, $dt);

		return $dt;
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