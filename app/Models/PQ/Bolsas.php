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
		'id_bb','bs_tipo','bs_nivel'
		,'bs_start','bs_finish','BS_IES'

	];
	protected $typeFields        = [
		'hidden','sql:id_mod:mod_sigla:brapci_pq.modalidades','op:2&2:1D&1D:1C&1C:1B&1B:1A&1A',
		'string','string','string'
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

	function edit($id)
		{
			$this->id = $id;
			$this->path = PATH.'popup/pq_bolsa_edit?id='.$id.'&'	;
			$this->path_back = 'wclose';	
			$sx = h(lang('pq.pq_editar'),2);
			$sx .= form($this);

			$sx = bs(bsc($sx,12));
			return ($sx);
		}

	function historic_researcher($id)
		{
			$dt = $this
				->join('modalidades','id_mod = bs_tipo', STR_PAD_LEFT)
				->where('bb_person',$id)
				->orderBy('bs_start DESC')
				->findAll();

			$sx = '';
			for ($r=0;$r < count($dt);$r++)
				{
					$line = $dt[$r];
					$year = substr($line['bs_start'],0,4);
					$link = onclick(PATH.'popup/pq_bolsa_edit/?id='.$line['id_bb'],800,400);
					$linka = '</span>';
					$edit = '<div style="float: right;">'.$link.bsicone('edit',20).$linka.'</div>';
					$sx .= '
					<div class="card mb-2" style="width: 100%;">
					
					<div class="card-body"> 
						'.$edit.'
						<img style="margin-right: 25px; height: 50px; float: left;"" src="'.URL.'/img/logo/logo_cnpq.png'.'" alt="CNPq">
						<h5 class="card-title">'.$line['mod_descricao'].' '.$line['mod_sigla'].$line['bs_nivel']. '('.$year.')</h5>
						<p class="card-text"><b>'.$line['BS_IES'].'</b>'.
						' - '.
						stodbr(sonumero($line['bs_start'])).
						' - '.
						stodbr(sonumero($line['bs_finish'])).
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

	function year_list($tp=0)
		{
		$RDF = new \App\Models\Rdf\RDF();
		
		$sx = '';
		$ord = get("order");

		$limit_char = 9999;
		switch($ord)
			{
				case 'bs_nome':
					$order = 'bs_nome, bs_start';
					$class = "";
					break;
				case 'BS_IES':
					$order = 'BS_IES, bs_nome';
					$class = "BS_IES";
					break;

				default:
					$order = 'bs_start DESC, bs_nome';
					$class = "bs_start";
					$limit_char = 4;
					break;
			}

		$data = date("Y-m-y");
		$this->join('modalidades', 'modalidades.id_mod = bolsas.bs_tipo')
			->join('bolsistas', 'bolsistas.id_bs = bolsas.bb_person');
			if ($tp == 1)
				{
					$this->where("bs_finish >= '". $data."'");
				}					
			$dt = $this->orderBy($order)
			->findAll();

			$xyear = '';
			$sx .= h(lang('pq.total').': ' .count($dt),6);
			$sx .= '<table class="table table-striped">';			
			$th = '<tr class="small">
				<th width="3%">'.lang('pq.nr').'</th>
				<th width="50%">'.'<a href="?order=bs_nome">'.lang('pq.bs_nome').'</a></th>
				<th width="5%">'.'<a href="?order=mod_modalidade">'.lang('pq.mod_modalidade').'</a></th>
				<th width="10%">'.'<a href="?order=bs_start">'.lang('pq.bs_start').'</a></th>
				<th width="10%">'.'<a href="?order=bs_finish">'.lang('pq.bs_finish').'</a></th>
				<th width="10%">'.'<a href="?order=BS_IES">'.lang('pq.BS_IES').'</a></th>
				</tr>'.cr();
			$nr = 0;

			/********** Header */
			if ($class == '') { $sx .= $th; }

			for($r=0;$r < count($dt);$r++)
				{
					$line = $dt[$r];
					if ($class != '')
						{
							$year = substr($line[$class],0,$limit_char);
						} else {
							$year= $xyear;
						}
					
					if ($year != $xyear)
						{
							$xyear = $year;
							$sx .= '<tr><td colspan=4><h3>' . $year . '</h3></td></tr>';
							$sx .= $th;
							$nr = 0;
						}
						$nr++;

						$linka = '</a>';
						if ($line['bs_rdf_id'] > 0)
							{
								$link = $RDF->link(array('id_cc'=>$line['bs_rdf_id']),'text-secondary');
							} else {
								$link = '<a href="'.PATH.MODULE.'pq/viewid/'.$line['id_bs'].'" class="text-secondary">*';
							}
						
						$sx .= '<tr>';
						$sx .= '<td>'.$nr.'</td>';
						$sx .= '<td>'.$link.$line['bs_nome'].$linka.'</td>';
						$sx .= '<td>'.$link.$line['mod_sigla'].$line['bs_nivel'].$linka.'</td>';
						$sx .= '<td>'.$link.$line['bs_start'].$linka.'</td>';
						$sx .= '<td>'.$link.$line['bs_finish'].$linka.'</td>';
						$sx .= '<td>'.$link.$line['BS_IES'].$linka.'</td>';
						$sx .= '</tr>';
						$sx .= cr();
				}
			$sx .= '</table>';
		return $sx;
	}	

	function bolsista_list()
	{
		$dt = (array)$this->resume_data(1);
		$person = (array)$dt['person'];
		$bolsista = (array)$person['bolsista'];
		$bolsa = array();
		ksort($bolsista);
		foreach ($bolsista as $name => $data) {
			$nome = (string)$name;
			$bs = '';
			foreach ($data as $mod => $year) {
				$year = (array)$year;
				$bsa = '';
				ksort($year);
				$bsa .= '<b>' . $mod . '</b>: (';
				$n = 0;
				foreach ($year as $ano => $total) {
					if ($n > 0) {
						$bsa .= ', ';
					}
					$n++;
					$bsa .= $ano;
				}
				$bsa .= ')';
				$bs .= bsc($bsa, 2);
			}
			$bolsa[$name] = ($bs);
		}
		$sx = '';
		foreach ($bolsa as $name => $html) {
			$sx .= bs(bsc($name, 12) . bsc('', 1) . $html);
		}
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
