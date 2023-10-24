<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFData extends Model
{
	var $DBGroup              = 'rdf';
	var $table                = PREFIX . 'rdf_data';
	protected $primaryKey           = 'id_d';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_d', 'd_r1', 'd_r2', 'd_p', 'd_library', 'd_literal', 'd_user', 'd_update'
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

	function literal($id, $prop, $name, $lang = '')
	{
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$idp = $RDFClass->class($prop);

		$RDFLiteral = new \App\Models\Rdf\RDFLiteral();
		$d['d_literal'] = $RDFLiteral->name($name, $lang);
		$d['d_library'] = LIBRARY;
		$d['d_r1'] = $id;
		$d['d_r2'] = 0;
		$d['d_p'] = $idp;

		$rst = $this->where('d_r1', $id)->where('d_literal', $d['d_literal'])->FindAll();
		if (count($rst) == 0) {
			$this->insert($d);
			$rst = $this->where('d_r1', $id)->where('d_literal', $d['d_literal'])->FindAll();
		}
		$id = $rst[0]['id_d'];
		return $id;
	}

	function countProp($class)
		{
			$RDF = new \App\Models\Rdf\RDF();
			$Class1 = $RDF->getClass($class);
			$dt = $this->select('count(*) as total')
				->where('d_p',$Class1)
				->first();
			return $dt;
		}
	function check_duplicates()
	{
		$sql = "select d_r1,d_r2,d_p,d_literal,count(*) as total, d_library, max(id_d) as max
					from " . PREFIX . "rdf_data
					group by d_r1,d_r2,d_p,d_literal, d_library
					having count(*) > 1";
		$dt = $this->db->query($sql)->getResultArray();

		for ($r = 0; $r < count($dt); $r++) {
			$this->where('id_d', $dt[$r]['max'])->delete();
			if ($r > 100) {
				break;
			}
		}
		return count($dt);
	}

	function changeInvert($id)
		{
			$dt = $this->find($id);
			if ($dt != '')
			{
				$d1 = $dt['d_r1'];
				$d2 = $dt['d_r2'];
				$dt['d_r1'] = $d2;
				$dt['d_r2'] = $d1;
				$this->set($dt)->where('id_d',$id);
			}
			return '';
		}

	function changeProp($po, $pd)
	{
		$RDF = new \App\Models\Rdf\RDF();

		$Class1 = $RDF->getClass($po);
		$Class2 = $RDF->getClass($pd);

		/* Part da Troca */
		$this->set('d_p', $Class2);
		$this->where('d_p', $Class1);
		$this->update();

		return 0;
	}

	function remove($d2)
	{
		/* Part 3 */
		$RDFConcept = new \App\Models\Rdf\RDFConcept();
		$RDFConcept->where('id_cc', $d2)->delete();
	}

	function set_rdf_data($id1, $prop, $id2)
	{
		$sx = $this->propriety($id1, $prop, $id2);
		return $sx;
	}

	function propriety($id1, $prop, $id2)
	{
		$Socials = new \App\Models\Socials();
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$idp = $RDFClass->class($prop);

		$d['d_r1'] = $id1;
		$d['d_r2'] = $id2;
		$d['d_p'] = $idp;
		$d['d_library'] = LIBRARY;
		$d['d_literal'] = 0;
		$d['d_update'] = date("Y-m-d H:i:s");
		$user = $Socials->getUser();
		$d['d_user'] = $user;
		$rst = $this->where('d_r1', $id1)->where('d_r2', $id2)->first();
		if ($rst == '') {
			$rst = $this->where('d_r2', $id1)->where('d_r1', $id2)->first();
			if ($rst == '') {
				$this->insert($d);
				$rst = $this->where('d_r1', $id1)->where('d_r2', $id2)->first();
			} else {
				$rst = $this->where('d_r2', $id1)->where('d_r1', $id2)->first();
			}
		}
		$id = $rst['id_d'];
		return $id;
	}

	function report($type)
	{
		$sx = h($type);
		switch ($type) {
			case 'index':
				$user = get("user");
				$year = get("year");
				if ($year == '') {
					$year = date("Y");
				}
				$data = date("Y-m-d");
				if ($user != '') {
					$dt = $this
						->select("count(*) as total, id_us, us_nome")
						->select("year(d_update) as year,month(d_update) as month,day(d_update) as day")
						->join('users', 'id_us = d_user')
						->where('year(d_update) = "' . $year . '"')
						->where('d_user = "' . $user . '"')
						->groupBy('id_us, us_nome, year,month,day')
						->orderBy('us_nome,year desc, month desc, day desc')
						->findAll();
					$us = '';
				} else {
					$users = $this
						->select("count(*) as total")
						->select("id_us,us_nome")
						->join('users', 'd_user = id_us')
						->where('year(d_update) = "' . $year . '"')
						->groupBy('id_us,us_nome')
						->orderBy('us_nome')
						->findAll();
					$us = '<hr>' . h(lang('brapci.users'), 5);
					$us .= '<ul>';
					foreach ($users as $id => $line) {
						$us .= '<li>' . anchor(PATH . '/admin/reports/catalog_manutention/revision/?user=' . $line['id_us'], $line['us_nome']) . ' (' . $line['total'] . ')</li>';
					}
					$us .= '</ul>';


					$dt = $this
						->select("count(*) as total")
						->select("year(d_update) as year,month(d_update) as month,day(d_update) as day")
						->where('year(d_update) = "' . $year . '"')
						->groupBy('year,month,day')
						->orderBy('year desc, month desc, day desc')
						->findAll();
				}

				$m = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
				for ($r = 1; $r <= 12; $r++) {
					for ($y = 1; $y <= $m[($r - 1)]; $y++) {
						$d[$r][$y] = 0;
					}
				}
				$mx = array();
				$max = 10;
				foreach ($dt as $id => $line) {
					$mes = $line['month'];
					$dia = $line['day'];
					$total = $line['total'];
					if ($total > $max) {
						$max = $total;
					}
					$d[$mes][$dia] = $total;
				}
				$sx .= '<table width="100%">';
				$sx .= '<tr>';
				$sx .= '<th style="width: 120px; font-size: 0.5em;">' . lang('brapci.month') . '/' . lang('brapci.day') . '</th>';
				for ($dia = 1; $dia <= 31; $dia++) {
					$sx .= '<th class="text-center" style="font-size: 0.5em;">' . $dia . '</th>';
				}
				$sx .= '</tr>';

				foreach ($d as $mes => $line) {
					$sx .= '<tr>';
					$sx .= '<td style="width: 120px;">' . mes_extenso($mes) . '</td>';
					foreach ($line as $idl => $total) {
						$bcor = '80';
						$xcor = 128 + round(128 - $total / $max * 128);
						$xcor = UpperCase(dechex($xcor));
						if (strlen($xcor) == 1) {
							$xcor = '0' . $xcor;
						}

						if ($total == 0) {
							$bcor = 'F0';
							$xcor = 'F0';
						}
						$cor = "#" . $bcor . $xcor . $bcor;
						$sx .= '<td align="center">';
						$sx .= '<span title="' . $total . '" style="margin-right:1px; color: ' . $cor . '">';
						$sx .= bsicone('square', 18);
						$sx .= '</span>';
						$sx .= '</td>';
					}
					$sx .= '</tr>';
				}
				$sx .= '</table>';

				$sx .= $us;
		}
		return $sx;
	}

	function check($dt)
	{
		foreach ($dt as $field => $value) {
			$this->where($field, $value);
		}
		$dts = $this->first();

		if (!is_array($dts)) {
			$Socials = new \App\Models\Socials();
			$dt['d_user'] = $Socials->getUser();
			$dt['d_update'] = date("Y-m-d H:i:s");
			$this->insert($dt);
			return true;
		}
		return false;
	}
	function exclude($id)
	{
		$this->where('d_r1', $id);
		$this->ORwhere('d_r2', $id);
		$dt = $this->FindAll();

		for ($r = 0; $r < count($dt); $r++) {
			$dd = $dt[$r];
			$dd['d_r1'] = $dd['d_r1'] * (-1);
			$dd['d_r2'] = $dd['d_r2'] * (-1);
			$dd['d_literal'] = $dd['d_literal'] * (-1);
			$this->set($dd)->where('id_d', $dd['id_d'])->update();
		}
	}

	function view_data($dt)
	{
		$RDF = new \App\Models\Rdf\RDF();
		$sx = '';
		if (!isset($dt['concept']['id_cc'])) {
			return '';
		}
		$ID = $dt['concept']['id_cc'];
		if (isset($dt['data'])) {
			$dtd = $dt['data'];
			for ($qr = 0; $qr < count($dtd); $qr++) {
				if ($qr > 100) {
					$sx .= bsc(bsmessage('Limite de 100 registros'), 12);
					break;
				}
				$line = (array)$dtd[$qr];
				$sx .= bsc(
					'<small>' . lang($line['prefix_ref'] . ':' .
						$line['c_class'] . '</small>'),
					2,
					'supersmall border-top border-1 border-secondary my-2'
				);

				$class = $line['c_class'];
				//if ($class == 'hasCover') { $class = 'hasTumbNail';}
				switch ($class) {
					case 'hasTumbNail':
						$name = $line['n_name'];
						if (file_exists($name)) {
							$name = URL . '/' . $name;
							$sx .= bsc('<img src="' . base_url($name) . '" class="img-thumbnail border border-secondary">', 2);
							$sx .= bsc('', 8);
						} else {
							$sx .= bsc("Erro na carga do arquivo - " . $name, 10);
						}
						break;
					default:
						if ($line['d_r2'] != 0) {
							if ($ID == $line['d_r2']) {
								$link = (PATH . COLLECTION . '/v/' . $line['d_r1']);
								$txt = $RDF->c($line['d_r1']);
								if (strlen($txt) > 0) {
									$link = '<a href="' . $link . '">' . $txt . '</a>';
								} else {
									$txt = 'not found:' . $line['d_r1'];
									$link = '<a href="' . $link . '">' . $txt . '</a>';
								}
							} else {
								$link = (PATH . COLLECTION . '/v/' . $line['d_r2']);
								$txt = $RDF->c($line['d_r2']);
								if (strlen($txt) > 0) {
									$link = '<a href="' . $link . '">' . $txt . '</a>';
								} else {
									$txt = 'not found:' . $line['d_r2'];
									$link = '<a href="' . $link . '">' . $txt . '</a>';
								}
							}
							$sx .= bsc($link, 10, 'border-top border-1 border-secondary my-2');
						} else {
							$txt = $line['n_name'];
							$lang = $line['n_lang'];
							if (strlen($lang) > 0) {
								$txt .= ' <sup>(' . $lang . ')</sup>';
							}
							if (substr($txt, 0, 4) == 'http') {
								$txt = '<a href="' . $line['n_name'] . '" target="_blank">' . $txt . '</a>';
							}
							$sx .= bsc($txt, 10, 'border-top border-1 border-secondary my-2');
						}
				}
			}
		}
		return bs($sx);
	}

	function le($id)
	{
		$sql = "select ";
		$sql .= " DISTINCT
    		rdf_name.id_n, rdf_name.n_name, rdf_name.n_lang,
			rdf_class.c_class, rdf_class.c_prefix, rdf_class.c_type,
			rdf_prefix.prefix_ref, rdf_prefix.prefix_url,
    		rdf_data.*,
			prefix_ref, prefix_url,
			n2.n_name as n_name2,
			n2.n_lang as n_lang2
			";
		$sql .= "from " . PREFIX . "rdf_data ";
		$sql .= "left join " . PREFIX . "rdf_name ON d_literal = rdf_name.id_n ";
		$sql .= "left join " . PREFIX . "rdf_class ON rdf_data.d_p = rdf_class.id_c ";
		$sql .= "left join " . PREFIX . "rdf_prefix ON rdf_class.c_prefix = rdf_prefix.id_prefix ";

		$sql .= "left join " . PREFIX . "rdf_concept as rc2 ON rdf_data.d_r2 = rc2.id_cc ";
		$sql .= "left join " . PREFIX . "rdf_name as n2 ON n2.id_n = rc2.cc_pref_term ";


		$sql .= "where (d_r1 = $id) OR (d_r2 = $id)";
		$sql .= "order by c_class, id_d, d_r1, d_r2, n_name";


		$dt = (array)$this->db->query($sql)->getResult();
		for ($r = 0; $r < count($dt); $r++) {
			$dt[$r] = (array)$dt[$r];
		}
		return ($dt);
	}
}
