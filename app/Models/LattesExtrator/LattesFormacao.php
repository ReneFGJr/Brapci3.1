<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesFormacao extends Model
{
	protected $DBGroup              = 'lattes';
	protected $table                = 'brapci_lattes.lattesformacao';
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

	function zerezima_dados_xml($id)
	{
		$this->where('f_id', $id)->delete();
		return true;
	}

	function resume($id)
	{
		$sx = '';
		$dt = $this->where('f_id', $id)
			->orderBy('f_ano_ini DESC')
			->findAll();
		$sx_g = '';
		$sx_d = '';
		$sx_m = '';

		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$type = $line['f_type'];
			switch ($type) {
				case 'G':
					$sx .= $this->view_graduate($line);
					break;
				case 'M':
					$sx .= $this->view_posgrade($line);
					break;
				case 'D':
					$sx .= $this->view_posgrade($line);
					break;
			}
		}
		$sx .= $sx_d;
		$sx .= $sx_m;
		$sx .= $sx_g;

		if (count($dt) > 0)
			{
				$sx = h('<b>'.lang('brapci.academoc.formation'). '</b>',6,'text-center mt-2').$sx;
			}

		return $sx;
	}

	function csv($id)
	{
		$dt = $this
			->join('brapci_tools.projects_harvesting_xml', 'f_id  = hx_id_lattes')
			->where('hx_project', $id)
			->findAll();

		$sx = 'IDLATTES,TYPE,INSTITUTION,COURSE,STATUS,YEAR_I,YEAR_F' . chr(13);
		foreach ($dt as $id => $line) {
			$sa = '';
			$sa .= '"' . $line['f_id'] . '",';
			$sa .= '"' . $line['f_type'] . '",';
			$sa .= '"' . $line['f_inst'] . '",';
			$sa .= '"' . $line['f_curso'] . '",';
			$sa .= '"' . $line['f_situacao'] . '",';
			$sa .= '"' . $line['f_ano_ini'] . '",';
			$sa .= '"' . $line['f_ano_fim'] . '",';
			$sa = troca($sa, chr(13), '');
			$sa = troca($sa, chr(10), '');
			$sx .= $sa . chr(13);
		}

		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=brapci_tools_academic_formation_" . date("Ymd-His") . ".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $sx;
		exit;
	}

	function view_graduate($dt)
	{
		$style = ' color: gray;';
		$msg_type = '<a href="https://www.academia.org.br/nossa-lingua/reducoes" target="_blank" title="sigla conforme Academia Brasileira de Letras (ABL)">Grad.</a>';

		$sx = '';
		$sx .= '<table>';
		$sx .= '<tr>';
		$sx .= '<td width="32" class="p-2 text-center" style="font-size: 0.6em; ' . $style . '">' . bsicone('graduate', 32) . '<br>' . $msg_type . '</td>';
		$sx .= '<td style="font-size: 0.6em;">';
		$sx .= '<b>';
		$sx .= $dt['f_ano_ini'] . '-' . $dt['f_ano_fim'];
		$sx .= '</b>';
		$sx .= '<br>';
		$sx .= $dt['f_inst'];
		$sx .= '<br>';
		$sx .= $dt['f_curso'];
		$sx .= '</td>';
		$sx .= '</tr>';
		$sx .= '</table>';
		return $sx;
	}
	function view_posgrade($dt)
	{
		$style = ' color: gray;';
		$msg_type= '<a href="https://www.academia.org.br/nossa-lingua/reducoes" target="_blank" title="sigla conforme Academia Brasileira de Letras (ABL)">M.e</a>';
		if ($dt['f_type'] == 'D')
			{
			$msg_type = '<a href="https://www.academia.org.br/nossa-lingua/reducoes" target="_blank" title="sigla conforme Academia Brasileira de Letras (ABL)">Dr.</a>';
			$style = ' style="color: black"';
			}
		$sx = '';
		$sx .= '<table>';
		$sx .= '<tr>';
		$sx .= '<td width="32" class="p-2 text-center" style="font-size: 0.6em; '.$style.'">' . bsicone('posgrade', 32). '<br>'. $msg_type . '</td>';
		$sx .= '<td style="font-size: 0.6em;">';
		$sx .= '<b>';
		$sx .= $dt['f_ano_ini'] . '-' . $dt['f_ano_fim'];
		$sx .= '</b>';
		$sx .= '<br>';
		$sx .= $dt['f_inst'];
		$sx .= '<br>';
		$sx .= $dt['f_curso'];
		$sx .= '</td>';
		$sx .= '</tr>';
		$sx .= '</table>';
		return $sx;
	}

	function formacao($id)
	{
		$tela = '';
		$dt = $this->where('lp_author', $id)->orderBy('lp_ano', 'desc')->findAll();
		if (count($dt) == 0) {
			return "";
		}
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
		$mestrado = $this->extract($id, 'MESTRADO', $arti);
		$doutorado = $this->extract($id, 'DOUTORADO', $arti);
		return '';
	}

	function register($id, $dt)
	{
		$dta = $this
			->where('f_id', $id)
			->where('f_inst_cod', $dt['f_inst_cod'])
			->where('f_ano_ini', $dt['f_ano_ini'])
			->where('f_ppg', $dt['f_ppg'])
			->findAll();
		if (count($dta) == 0) {
			$this->set($dt)->insert();
		} else {
			$idx = $dta[0]['id_f'];
			$this->set($dt)
				->where('id_f', $idx)
				->update();
		}
	}

	function extract($id, $type = 'GRADUACAO', $arti = array())
	{
		if (!isset($arti[$type])) { return array(); }

		$curso = (array)$arti[$type];
		$dt = array();
		$dt['f_id'] = $id;
		$dt['f_type'] = substr($type, 0, 1);

		if (!isset($curso[0]))
			{
				if ($curso != '')
				{
					$c = $curso;
					$curso = array($curso);
				} else {
					$curso = array();
				}
			}
		if (count($curso) > 0)
		{
			for ($r=0;$r < count($curso);$r++)
			{
				$curs = (array)$curso[$r];
				$curs = (array)$curs['@attributes'];
				$dt['f_inst'] = $curs['NOME-INSTITUICAO'];
				$dt['f_inst_cod'] = $curs['CODIGO-INSTITUICAO'];
				$dt['f_curso'] = $curs['NOME-CURSO'];
				$dt['f_curso_area'] = $curs['CODIGO-AREA-CURSO'];
				$dt['f_situacao'] = $this->status($curs['STATUS-DO-CURSO']);
				$dt['f_ano_ini'] = $curs['ANO-DE-INICIO'];
				$dt['f_ano_fim'] = $curs['ANO-DE-CONCLUSAO'];
				if (!isset($curs['NOME-COMPLETO-DO-ORIENTADOR'])) {
					$dt['f_orientador'] = '';
					$dt['f_orientador_lattes'] = '';
				} else {
					$dt['f_orientador'] = $curs['NOME-COMPLETO-DO-ORIENTADOR'];
					$dt['f_orientador_lattes'] = $curs['NUMERO-ID-ORIENTADOR'];
				}

				$dt['f_ppg'] = $curs['CODIGO-CURSO-CAPES'];
				$dt['f_ano_fim'] = $curs['ANO-DE-CONCLUSAO'];
				$this->register($id, $dt);
			}
		} else {
			echo "ERRO";
			pre($curso);
			$dt = array();
		}

		return $dt;
	}

	function status($t)
	{
		switch ($t) {
			case 'CONCLUIDO':
				return "C";
				break;
			case 'INCOMPLETO':
				return "I";
				break;
			default:
				echo "FORMACAO STATUS:$t";
				exit;
		}
	}
}
