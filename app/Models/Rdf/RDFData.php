<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFData extends Model
{
	var $DBGroup              = 'rdf';
	protected $table                = PREFIX . 'rdf_data';
	protected $primaryKey           = 'id_d';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_d', 'd_r1', 'd_r2', 'd_p', 'd_library', 'd_literal'
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

	function change($d1, $d2)
	{
		$d1 = round($d1);
		$d2 = round($d2);

		if (($d1 == 0) or ($d2 == 0)) {
			return "";
		}
		/* Part 1 */
		$this->set('d_r1', $d1);
		$this->where('d_r1', $d2);
		$this->update();

		/* Part 2 */
		$this->set('d_r2', $d1);
		$this->where('d_r2', $d2);
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
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$idp = $RDFClass->class($prop);

		$d['d_r1'] = $id1;
		$d['d_r2'] = $id2;
		$d['d_p'] = $idp;
		$d['d_library'] = LIBRARY;
		$d['d_literal'] = 0;
		$rst = $this->where('d_r1', $id1)->where('d_r2', $id2)->FindAll();
		if (count($rst) == 0) {
			$rst = $this->where('d_r2', $id1)->where('d_r1', $id2)->FindAll();
			if (count($rst) == 0) {
				$this->insert($d);
			}
			$rst = $this->where('d_r1', $id1)->where('d_r2', $id2)->FindAll();
		}
		$id = $rst[0]['id_d'];
		return $id;
	}

	function check($dt)
	{
		foreach ($dt as $field => $value) {
			$this->where($field, $value);
			//echo '<br>'.$field.'==>'.$value;
		}
		$dts = $this->first();

		if (!is_array($dts)) {

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
						if (file_exists($name))
							{
								$name = URL . '/' . $name;
								$sx .= bsc('<img src="' . base_url($name) . '" class="img-thumbnail border border-secondary">', 2);
								$sx .= bsc('', 8);
							} else {
								$sx .= bsc("Erro na carga do arquivo - ".$name,10);
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
		$sql .= "order by c_class, d_r1, d_r2, n_name";


		$dt = (array)$this->db->query($sql)->getResult();
		for ($r = 0; $r < count($dt); $r++) {
			$dt[$r] = (array)$dt[$r];
		}
		return ($dt);
	}
}
