<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesOrientacao extends Model
{
	protected $DBGroup              = 'lattes';
	protected $table                = 'LattesOrientacoes';
	protected $primaryKey           = 'id_lo';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_lo', 'lo_author', 'lo_brapci_rdf',
		'lo_type', 'lo_title', 'lo_natureza',
		'lo_ano', 'lo_lang', 'lo_doi',
		'lo_tipo_orientacao', 'lo_orientando', 'lo_instituicao',
		'lo_ppg', 'lo_orientado_lattes', '',
		'', '', '',
		'', '', ''
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

	function zerezima_dados_xml($id)
	{
		$this->where('lo_author', $id)->delete();
		return true;
	}

	function resume($id)
	{
		$rst = array(0, 0, 0, 0, 0, 0, 0, 0, 0);

		$dt = $this->select('count(*) as total, lo_natureza')
			->where('lo_author', $id)
			->groupBy('lo_natureza')
			->findAll();
		if (count($dt) > 0) {
			for ($r = 0; $r < count($dt); $r++) {
				$line = $dt[$r];
				$tp = $line['lo_natureza'];
				switch ($tp) {
					case 'IC':
						$rst[1] = $line['total'];
						break;
					case 'TC':
						$rst[0] = $line['total'];
						break;
					case 'DA':
						$rst[3] = $line['total'];
						break;
					case 'MA':
						$rst[2] = $line['total'];
						break;
					case 'PD':
						$rst[4] = $line['total'];
						break;
					case 'EP':
						$rst[5] = $line['total'];
						break;
					case 'OT':
						$rst[5] = $line['total'];
						break;
					default:
						echo "<br>OPS->" . $tp;
						break;
				}
			}
		}
		return $rst;
	}

	function orietacao($id)
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

	function tipo_orientacao($nr)
	{
		switch ($nr) {
			case '':
				return '-';
				break;
			case 'ORIENTADOR_PRINCIPAL':
				return 'O';
				break;
			case 'CO_ORIENTADOR':
				return 'C';
			default:
				echo $nr;
				exit;
		}
	}

	function natureza($nr)
	{
		switch ($nr) {
			case 'ORIENTACAO-DE-OUTRA-NATUREZA':
				return 'OT';
				break;
			case 'MONOGRAFIA_DE_CONCLUSAO_DE_CURSO_APERFEICOAMENTO_E_ESPECIALIZACAO':
				return 'EP';
				break;
			case 'INICIACAO_CIENTIFICA':
				return 'IC';
				break;
			case 'TRABALHO_DE_CONCLUSAO_DE_CURSO_GRADUACAO':
				return 'TC';
				break;
			case 'Supervisão de pós-doutorado':
				return 'PD';
				break;
			case 'Tese de doutorado':
				return 'DA';
				break;
			case 'Dissertação de mestrado':
				return 'MA';
				break;
			default:
				echo $nr;
				exit;
		}
	}

	function orientacao_xml($id)
	{

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

		if (isset($xml['OUTRA-PRODUCAO'])) {
			$prod = (array)$xml['OUTRA-PRODUCAO'];

			if (isset($prod['ORIENTACOES-CONCLUIDAS'])) {
				$prod2 = (array)$prod['ORIENTACOES-CONCLUIDAS'];

				if (isset($prod2['ORIENTACOES-CONCLUIDAS-PARA-MESTRADO'])) {
					$orie = (array)$prod2['ORIENTACOES-CONCLUIDAS-PARA-MESTRADO'];
					$tp1 = 'DADOS-BASICOS-DE-ORIENTACOES-CONCLUIDAS-PARA-MESTRADO';
					$tp2 = 'DETALHAMENTO-DE-ORIENTACOES-CONCLUIDAS-PARA-MESTRADO';
					$this->orientacoes($id, $orie, $tp1, $tp2);
				}

				if (isset($prod2['ORIENTACOES-CONCLUIDAS-PARA-DOUTORADO'])) {
					$orie = (array)$prod2['ORIENTACOES-CONCLUIDAS-PARA-DOUTORADO'];
					$tp1 = 'DADOS-BASICOS-DE-ORIENTACOES-CONCLUIDAS-PARA-DOUTORADO';
					$tp2 = 'DETALHAMENTO-DE-ORIENTACOES-CONCLUIDAS-PARA-DOUTORADO';
					$this->orientacoes($id, $orie, $tp1, $tp2);
				}

				if (isset($prod2['ORIENTACOES-CONCLUIDAS-PARA-POS-DOUTORADO'])) {
					$orie = (array)$prod2['ORIENTACOES-CONCLUIDAS-PARA-POS-DOUTORADO'];
					$tp1 = 'DADOS-BASICOS-DE-ORIENTACOES-CONCLUIDAS-PARA-POS-DOUTORADO';
					$tp2 = 'DETALHAMENTO-DE-ORIENTACOES-CONCLUIDAS-PARA-POS-DOUTORADO';
					$this->orientacoes($id, $orie, $tp1, $tp2);
				}

				//pre($xml);

				if (isset($prod2['OUTRAS-ORIENTACOES-CONCLUIDAS'])) {
					$orie = (array)$prod2['OUTRAS-ORIENTACOES-CONCLUIDAS'];
					$tp1 = 'DADOS-BASICOS-DE-OUTRAS-ORIENTACOES-CONCLUIDAS';
					$tp2 = 'DETALHAMENTO-DE-OUTRAS-ORIENTACOES-CONCLUIDAS';
					$this->orientacoes($id, $orie, $tp1, $tp2);
				}
			}
		}

		//pre($orie);
	}

	function orientacoes($id, $orie, $tp1, $tp2)
	{
		if (!isset($orie[0])) {
			$oriee = array();
			$oriee[0] = (array)$orie;
			$orie = $oriee;
		}
		$Lang = new \App\Models\Language\Lang();
		$LattesExtrator = new \App\Models\LattesExtrator\Index();
		$LattesInstituicao = new \App\Models\LattesExtrator\LattesInstituicao();

		for ($r = 0; $r < count($orie); $r++) {

			//pre($orie[$r]);
			$line = (array)$orie[$r];

			$dados = (array)$line[$tp1];
			$dados = (array)$dados['@attributes'];

			$p = array();
			$p['lo_author'] = $id;
			$p['lo_brapci_rdf'] = 0;
			$p['lo_ano'] = $dados['ANO'];
			$p['lo_natureza'] = $this->natureza($dados['NATUREZA']);

			if ((!isset($dados['TIPO'])) and (($p['lo_natureza'] == 'DA') or ($p['lo_natureza'] == 'PD'))) {
				$p['lo_type'] = 'A';
			} else {
				$p['lo_type'] = substr($dados['TIPO'], 0, 1);
			}

			$p['lo_title'] = $dados['TITULO'];

			$p['lo_pais'] = $dados['PAIS'];
			$p['lo_lang'] = $Lang->code($dados['IDIOMA']);
			if (isset($dados['DOI'])) {
				$p['lo_doi'] = $dados['DOI'];
			} else {
				$p['lo_doi'] = '';
			}

			/***************************** Parte II */
			$dados = (array)$line[$tp2];
			$dados = (array)$dados['@attributes'];

			if (!isset($dados['TIPO-DE-ORIENTACAO'])) {
				if (!isset($dados['TIPO-DE-ORIENTACAO-CONCLUIDA'])) {
					echo "OPS";
					pre($dados);
				} else {
					$p['lo_tipo_orientacao'] = $this->tipo_orientacao($dados['TIPO-DE-ORIENTACAO-CONCLUIDA']);
				}
			} else {
				$p['lo_tipo_orientacao'] = $this->tipo_orientacao($dados['TIPO-DE-ORIENTACAO']);
			}
			$p['lo_orientando'] = $dados['NOME-DO-ORIENTADO'];
			$p['lo_instituicao'] = $dados['CODIGO-INSTITUICAO'];
			$p['lo_ppg'] = $dados['CODIGO-CURSO'];
			if (isset($dados['NUMERO-ID-ORIENTADO'])) {
				$p['lo_orientado_lattes'] = $dados['NUMERO-ID-ORIENTADO'];
			} else {
				$p['lo_orientado_lattes'] = '';
			}

			$LattesInstituicao->instituicao($dados['CODIGO-INSTITUICAO'], 'PPG ' . $dados['NOME-DO-CURSO']);
			$LattesInstituicao->instituicao($dados['CODIGO-CURSO'], $dados['NOME-DA-INSTITUICAO']);

			$rst = $this->where('lo_author', $id)
				->where('lo_type', $p['lo_type'])
				->where('lo_title', $p['lo_title'])
				->where('lo_ano', $p['lo_ano'])
				->where('lo_natureza', $p['lo_natureza'])
				->where('lo_title', $p['lo_title'])
				->findAll();

			if (count($rst) == 0) {
				$this->insert($p);
			}
		}
		return 'ok';
	}

	function csv($id)
	{
		$dt = $this
			->join('brapci_tools.projects_harvesting_xml', 'lo_author  = hx_id_lattes')
			->join('lattesdados', 'lo_author = lt_id')
			->where('hx_project', $id)
			->findAll();

		$sx = 'IDLATTES,NAME,TITLE,YEAR,LANG,TYPE,NAMEO' . chr(13);
		foreach ($dt as $id => $line) {
			$sa = '';
			$sa .= '"' . $line['lo_author'] . '",';
			$sa .= '"' . $line['lt_name'] . '",';
			$sa .= '"' . $line['lo_title'] . '",';
			$sa .= '"' . $line['lo_natureza'] . '",';
			$sa .= '"' . $line['lo_ano'] . '",';
			$sa .= '"' . $line['lo_lang'] . '",';
			$sa .= '"' . $line['lo_tipo_orientacao'] . '",';
			$sa .= '"' . $line['lo_orientando'] . '",';
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
