<?php

namespace App\Models\PQ;

use CodeIgniter\Model;

class Bolsas extends Model
{
	protected $DBGroup              = 'pq';
	protected $table                = 'bolsas';
	protected $primaryKey           = 'id_bb';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_bb', 'bs_tipo', 'bs_nivel',
		'bs_start', 'bs_finish', 'BS_IES',
		'bb_person'

	];
	protected $typeFields        = [
		'hidden', 'sql:id_mod:mod_sigla:brapci_pq.modalidades*', 'op:2&2:1D&1D:1C&1C:1B&1B:1A&1A*',
		'string*', 'string*', 'string*',
		'hidden'
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

	public $id = 0;
	public $path = '';
	public $path_back = '';

	function download()
		{
			$dt = $this
				->join('brapci_pq.bolsistas', 'id_bs = bb_person')
				->join('brapci_pq.modalidades', 'id_mod = bs_tipo')
				->orderBy('bs_start, bs_nome')
				->findAll();
			$csv = 'ID,START,END,NAME,INSTITUTION,IDLATTES,MOD,NIVEL,BOLSA';
			$sep = ',';
			foreach($dt as $id=>$line)
				{
					$csv .= cr();
					$csv .= "'".$line['id_bb']."'".$sep;
					$csv .= "'" . $line['bs_start'] . "'" . $sep;
					$csv .= "'" . $line['bs_finish'] . "'" . $sep;
					$csv .= "'" . $line['bs_nome'] . "'" . $sep;
					$csv .= "'" . $line['BS_IES'] . "'" . $sep;
					$csv .= "'" . $line['bs_lattes'] . "'" . $sep;
					$csv .= "'" . $line['mod_sigla'] . "'" . $sep;
					$csv .= "'" . $line['bs_nivel'] . "'" . $sep;
					$csv .= "'" . trim($line['mod_sigla']).trim($line['bs_nivel']) . "'";
				}
			$dir = '.tmp/pq/';
			dircheck($dir);
			$file = $dir . 'pq_bolsistas_'.date("Y_M").'.csv';
			file_put_contents($file,$csv);
			$msg = 'Click <a href="/'.$file.'">aqui</a> para download do Dataset';
			$sx = bsmessage($msg);

			return $sx;
		}

	function bolsa_ano_tipo()
	{
		$label = [];
		$dt = $this
			->where('bs_ativo', 1)
			->findAll();
		$bs = [];
		$ini = 1990;
		$fim = date("Y");
		$dz = [];
		for ($y = $ini; $y<=$fim;$y++)
			{
				array_push($label, $y);
				array_push($dz,0);
			}
		foreach ($dt as $id => $line) {
			$nv = trim($line['bs_nivel']);
			if (!isset($bs[$nv]))
				{
					$bs[$nv] = $dz;
				}
		}

		foreach ($dt as $id => $line) {
			$yearI = round(substr($line['bs_start'], 0, 4));
			$yearF = round(substr($line['bs_finish'], 0, 4));
			$mod = trim($line['bs_nivel']);
			$loop = 0;
			for ($y = $yearI; $y < $yearF; $y++) {
				if ($y <= date("Y")) {
					$pos = $y - $ini;
					$bs[$mod][$pos] = $bs[$mod][$pos] + 1;
				}

				if ($loop++ > 10) {
					break;
				}
			}
		}
		ksort($bs);
		$RSP['label'] = $label;
		$RSP['data'] = $bs;
		return $RSP;
	}

	function bolsa_ano()
	{
		$dt = $this
			->where('bs_ativo', 1)
			->findAll();
		$bs = [];
		foreach ($dt as $id => $line) {
			$yearI = round(substr($line['bs_start'], 0, 4));
			$yearF = round(substr($line['bs_finish'], 0, 4));
			$loop = 0;
			for ($y = $yearI; $y < $yearF; $y++) {
				if ($y <= date("Y")) {
					if (isset($bs[$y])) {
						$bs[$y] = $bs[$y] + 1;
					} else {
						$bs[$y] = 1;
					}
				}

				if ($loop++ > 10) {
					break;
				}
			}
		}
		ksort($bs);
		return $bs;
	}

	function selo($dt)
	{
		$sx = '';
		$sx .= '<div class="text-center p-1" style="width: 100%; border: 1px solid #000; border-radius: 10px;  line-height: 80%;">';
		$sx .= '<span style="font-size: 16px;">' . $dt['bs_nivel'] . '</span>';
		$sx .= '<br>';
		$sx .= '<b style="font-size: 12px; ">' . $dt['bs_ano'] . '</b>';
		$sx .= '</div>';
		return $sx;
	}

	function bolsas_pesquisador($id)
	{
		$dt = $this
			->join('bolsistas', 'id_bs = bb_person')
			->join('modalidades', 'id_mod = bs_tipo')
			->where('bs_lattes', $id)
			->findAll();
		return ($dt);
	}

	function edit($id)
	{
		$this->id = $id;
		$this->path = PATH . '/popup/pq_bolsa_edit/' . $id . '?id=' . $id . '&pq=' . get('pq') . '&';
		$this->path_back = 'wclose';
		if (get("pq") != '') {
			$_POST['bb_person'] = get("pq");
		}
		$sx = h(lang('pq.pq_editar'), 2);
		$sx .= form($this);

		$sx = bs(bsc($sx, 12));
		return ($sx);
	}

	function btn_new($id)
	{
		$sx = '';
		$Socials = new \App\Models\Socials();
		$access = $Socials->getAccess("#ADM");
		if ($access) {
			$url = URL . '/popup/pq_bolsa_edit/?id=0&pq=' . $id;
			$sx .= '<span class="ms-1">' . onclick($url, 800, 400) . bsicone('plus', 20) . '</a></th>';
		}
		return $sx;
	}

	function historic_researcher($id)
	{
		$Socials = new \App\Models\Socials();
		$access = $Socials->getAccess("#ADM");

		$dt = $this
			->join('bolsistas', 'id_bs = bb_person')
			->join('modalidades', 'id_mod = bs_tipo', 'LEFT')
			->where('bb_person', $id)
			->ORwhere('bs_lattes', $id)
			->orderBy('bs_start DESC')
			->findAll();

		$sx = '';
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$year = substr($line['bs_start'], 0, 4);
			if ($access == true) {
				$link = onclick(URL . '/popup/pq_bolsa_edit/?id=' . $line['id_bb'], 800, 400);
				$linka = '</span>';
				$link_del = confirm(URL . '/popup/pq_bolsa_delete/?id=' . $line['id_bb'], 800, 400);
				$linka_del = '</span>';

				$edit = '<div style="float: right;">';
				$edit .= $link_del . bsicone('trash', 20) . $linka_del;
				$edit .= $link . bsicone('edit', 20) . $linka;
				$edit .= '</div>';
			} else {
				$link = '';
				$link_del = '';
				$linka = '';
				$linka_del = '';

				$edit = '';
			}
			/************************************** */

			/************************************** */
			$sx .= '
					<div class="card mb-2" style="width: 100%;">

					<div class="card-body">
						' . $edit . '
						<img style="margin-top: 10px; margin-bottom: 10px; margin-right: 25px; height: 30px; float: left;"" src="' . URL . '/img/logo/logo_cnpq.png' . '" alt="CNPq">
						<h5 class="card-title">' . $line['mod_descricao'] . ' ' . $line['mod_sigla'] . $line['bs_nivel'] . '(' . $year . ')</h5>
						<p class="card-text"><b>' . $line['BS_IES'] . '</b>' .
				' - ' .
				stodbr(sonumero($line['bs_start'])) .
				' - ' .
				stodbr(sonumero($line['bs_finish'])) .
				'</p>
					</div>
					</div>
					';
		}
		return $sx;
	}

	function resume()
	{
		helper('highchart');
		$dt = (array)$this->resume_data();

		$year = (array)$dt['year'];
		$sx = bs(bsc($this->resume_graph_bolsa_ano($year), 12));
		return $sx;
	}

	function year_summary($tp = 0)
	{
		$AI_geo = new \App\Models\AI\Geo\Institution\Index();
		$sx = '';
		$RDF = new \App\Models\Rdf\RDF();
		$data = date("Y-m-y");
		$this->join('modalidades', 'modalidades.id_mod = bolsas.bs_tipo')
			->join('bolsistas', 'bolsistas.id_bs = bolsas.bb_person');
		if ($tp == 1) {
			$this->where("bs_finish >= '" . $data . "'");
		}
		$dt = $this->findAll();

		$inst = array();
		$tipo = array();
		$venc = array();
		$geo = array();

		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$BS_IES = UpperCaseSQL($line['BS_IES']);
			$BOLSA = $line['mod_sigla'] . $line['bs_nivel'];
			$YEAR = substr($line['bs_finish'], 0, 4);

			/************ Institução */
			if (!isset($inst[$BS_IES])) {
				$inst[$BS_IES] = 0;
			}
			$inst[$BS_IES] = $inst[$BS_IES] + 1;

			/************* Bolsa */
			if (!isset($tipo[$BOLSA])) {
				$tipo[$BOLSA] = 0;
			}
			$tipo[$BOLSA] = $tipo[$BOLSA] + 1;

			/************** Vencimento */
			if (!isset($venc[$YEAR])) {
				$venc[$YEAR] = 0;
			}
			$venc[$YEAR] = $venc[$YEAR] + 1;

			/************* GEO */
			$GEO = $AI_geo->code($BS_IES);
			if (!isset($geo[$GEO])) {
				$geo[$GEO] = 0;
			}
			$geo[$GEO] = $geo[$GEO] + 1;
		}

		$sx .= '<table class="table">';
		$sx .= '<tr><th>Instituição</th><th>Bolsa</th><th>Vencimento</th></tr>';
		$sx .= '<tr>';
		$sx .= '<td width="15%">';
		$sx .= msg('brapci.total') . ' ' . count($dt);
		ksort($inst);
		foreach ($inst as $key => $value) {
			$sx .= '<br>' . $key . ' ' . $value;
		}
		$sx .= '</>';

		$sx .= '<td width="15%">';
		$sx .= msg('brapci.total') . ' ' . count($dt);
		ksort($tipo);
		foreach ($tipo as $key => $value) {
			$sx .= '<br>' . $key . ' ' . $value;
		}
		$sx .= '</td>';

		$sx .= '<td width="15%">';
		$sx .= msg('brapci.total') . ' ' . count($dt);
		ksort($venc);
		foreach ($venc as $key => $value) {
			$sx .= '<br>' . $key . ' ' . $value;
		}
		$sx .= '</td>';


		$sx .= '<td width="55%">';
		$states['title'] = 'Bolsistas PQ ativos por Estado';
		$states['data'] = '';
		foreach ($geo as $key => $value) {
			$states['data'] .= "['" . $key . "', " . $value . "],";
		}
		$sx .= view('HighChart/geo_brazil', $states);
		$sx .= '</td>';

		$sx .= '</tr>';
		$sx .= '</table>';
		return $sx;
	}

	function year_list($tp = 0)
	{
		$RDF = new \App\Models\Rdf\RDF();

		$sx = '';
		$ord = get("order");

		$limit_char = 9999;
		switch ($ord) {
			case 'bs_nome':
				$order = 'bs_nome, bs_start';
				$class = "";
				break;

			case 'BS_IES':
				$order = 'BS_IES, bs_nome';
				$class = "BS_IES";
				break;

			default:
				if ($tp == 1) {
					$order = 'bs_nome, bs_start';
					$class = "bs_nome";
					$limit_char = 4;
				} else {
					$order = 'bs_start DESC, bs_nome';
					$class = "bs_start";
					$limit_char = 4;
				}
				break;
		}

		$data = date("Y-m-y");
		$this->join('modalidades', 'modalidades.id_mod = bolsas.bs_tipo')
			->join('bolsistas', 'bolsistas.id_bs = bolsas.bb_person');
		if ($tp == 1) {
			$this->where("bs_finish >= '" . $data . "'");
		}
		$dt = $this->orderBy($order)
			->findAll();

		$xyear = '';
		$sx .= h(lang('pq.total') . ': ' . count($dt), 6);
		$sx .= '<table class="table table-striped">';
		$th = '<tr class="small">
				<th width="3%">' . lang('pq.nr') . '</th>
				<th width="54%">' . '<a href="?order=bs_nome">' . lang('pq.bs_nome') . '</a></th>
				<th width="5%">' . '<a href="?order=mod_modalidade">' . lang('pq.mod_modalidade') . '</a></th>
				<th width="15%">' . '<a href="?order=bs_start">' . lang('pq.bs_start') . '</a></th>
				<th width="15%">' . '<a href="?order=bs_finish">' . lang('pq.bs_finish') . '</a></th>
				<th width="10%">' . '<a href="?order=BS_IES">' . lang('pq.BS_IES') . '</a></th>
				</tr>' . cr();

		$nr = 0;

		/********** Header */
		if ($class == '') {
			$sx .= $th;
		} else {
			if ($tp == 1) {
				$sx .= $th;
			}
		}

		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			if ($class != '') {
				$year = substr($line[$class], 0, $limit_char);
			} else {
				$year = $xyear;
			}

			if ($year != $xyear) {
				$xyear = $year;
				if ($tp == 0) {
					$sx .= '<tr><td colspan=4><h3>' . $year . '</h3></td></tr>';
					$sx .= $th;
				}
				//$nr = 0;
			}
			$nr++;

			$linka = '</a>';
			$link = '<a href="' . PATH . MODULE . 'pq/viewid/' . $line['bs_lattes'] . '" class="text-secondary">*';

			$sx .= '<tr>';
			$sx .= '<td>' . $nr . '</td>';
			$sx .= '<td>' . $link . $line['bs_nome'] . $linka . '</td>';
			$sx .= '<td>' . $link . $line['mod_sigla'] . $line['bs_nivel'] . $linka . '</td>';
			$sx .= '<td>' . $link . $line['bs_start'] . $linka . '</td>';
			$sx .= '<td>' . $link . $line['bs_finish'] . $linka . '</td>';
			$sx .= '<td>' . $link . $line['BS_IES'] . $linka . '</td>';
			$sx .= '</tr>';
			$sx .= cr();
		}
		$sx .= '</table>';
		return $sx;
	}

	function bolsista_list()
	{
		$sx = '';
		$dt = (array)$this->resume_data(1);
		$person = (array)$dt['person'];
		$bolsista = (array)$person['bolsista'];
		$bolsa = array();
		ksort($bolsista);
		$sx .= '<table class="table">';
		foreach ($bolsista as $name => $data) {
			$nome = (string)$name;
			$sx .= '<tr><td>'.$nome. '</td>';
			foreach ($data as $mod => $years) {
				$dd['bs_nivel'] = $mod;
				$years = (array)$years;

				ksort($years);

				foreach ($years as $year => $t) {
					$dd['bs_ano'] = $year;
					$sx .= '<td>'.$this->selo($dd). '</td>';
				}
			}
			$sx .= '</tr>' . cr();
		}
		$sx .= '</table>';
		return $sx;
	}

	function resume_graph_bolsa_ano($dt)
	{
		$c = 0;
		$ss = cr();
		$st = '';
		$sx = '';
		$cores = array(
			'#0000FF', '#FF0000', '#00FF00', '#FF00FF', '#00FFFF', '#FFFF00', '#FF00FF', '#FFFFFF',
			'#000080', '#800000', '#008000', '#800080', '#008080', '#808000', '#800080', '#808080',
			'#FFFF80', '#80FFFF', '#FF8FF0', '#8FF080', '#FF8080', '#808FF0', '#8FF080', '#808080'
		);
		$years = array();
		$vlr = array();
		ksort($dt);
		foreach ($dt as $year => $data) {
			$data = (array)$data;
			$st .= "'$year',";
			$years[$year] = $year;
			foreach ($data as $mod => $tot) {
				$vlr[$mod][$year] = $tot;
			}
		}

		ksort($years);
		foreach ($vlr as $mod => $data) {
			foreach ($years as $ano => $id) {
				if (!isset($vlr[$mod][$ano])) {
					$vlr[$mod][$ano] = 0;
				}
			}
			$year = $vlr[$mod];
			ksort($year);
			$vlr[$mod] = $year;
		}

		foreach ($vlr as $mod => $data) {
			$ss .= "\t{ name: '$mod', data: [";
			foreach ($data as $ano => $total) {
				$ss .= "$total,";
			}
			$ss .= ']},' . cr();
		}

		$ss = 'series: [' . $ss . ']';
		$data['title'] = 'Bolsas PQ';
		$data['dados'] = $ss;
		$data['categorias'] = $st;
		$data['id'] = 'BolsasAno';
		$sx = highchart_column($data);


		return ($sx);
	}
	function resume_data($force = 0)
	{
		$file = '../.tmp/pq/bolsas.json';
		if ((!file_exists($file)) or ($force == 1)) {
			$dt = $this->join('modalidades', 'modalidades.id_mod = bolsas.bs_tipo')
			->join('bolsistas', 'bolsistas.id_bs = bolsas.bb_person')
			->findAll();
			$dd = array();

			for ($r = 0; $r < count($dt); $r++) {
				$line = $dt[$r];
				$year_ini = substr($line['bs_start'], 0, 4);
				$year_fim = substr($line['bs_start'], 0, 4);
				$sigla = $line['mod_sigla'];
				$nivel = $sigla . $line['bs_nivel'];
				$nome = $line['bs_nome'];

				$IES = $line['BS_IES'];
				/******************************************************************* Ano */
				if (isset($dd['year'][$year_ini][$nivel])) {
					$dd['year'][$year_ini][$nivel]++;
				} else {
					$dd['year'][$year_ini][$nivel] = 1;
				}
				/******************************************************************* Modalidade */
				if (isset($dd['person']['bolsista'][$nome][$nivel][$year_ini])) {
					$dd['person']['bolsista'][$nome][$nivel][$year_ini]++;
				} else {
					$dd['person']['bolsista'][$nome][$nivel][$year_ini] = 1;
				}
			}
			$dy = $dd['year'];
			$dm = $dd['person'];
			krsort($dy);
			krsort($dm);
			$dd['year'] = $dy;
			$dd['person'] = $dm;

			dircheck('../.tmp');
			dircheck('../.tmp/pq');
			$json = json_encode($dd);
			file_put_contents($file, $json);
		}
		$json = file_get_contents($file);
		$dd = json_decode($json);
		return $dd;
	}
}
