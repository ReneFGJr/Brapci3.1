<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RDFFormCheck extends Model
{
	protected $DBGroup              = 'rdf';
	protected $table                = 'rdf_form_class';
	protected $primaryKey           = 'id_sc';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_sc', 'sc_class', 'sc_propriety',
		'sc_range', 'sc_ativo', 'sc_ord',
		'sc_library', 'sc_global', 'sc_group'
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

	function check($id)
	{
		$cp = 'sc_library, sc_propriety, id_sc, sc_group, sc_class, sc_range, sc_ativo, sc_visible';
		$cpo = ', max(sc_global) as sc_global, min(sc_ord) as sc_ord ';
		$sql = "select $cp $cpo from rdf_form_class where  $id and sc_visible = 1 and  sc_ativo = 1";
		$sql .= " group by " . $cp;
		$dt = $this
			->select($cp . $cpo)
			->where('sc_class', $id)
			->groupby($cp)
			->orderby('sc_class')
			->FindAll();

		$dd = array();
		$de = array();
		$xclass = "";
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$library = $line['sc_library'];
			$class = $line['sc_class'];
			if ($library != 0 and $library != LIBRARY) {
				if ($class != $xclass) {
					array_push($dd, $line);
				}
				$xclass = $class;
			} else {
				$prop = $line['sc_propriety'];
				$de[$prop] = 1;
			}
		}
		$fn = 0;
		for ($r = 0; $r < count($dd); $r++) {
			$data = $dd[$r];
			$prop = $data['sc_propriety'];
			if (!isset($de[$prop])) {
				unset($data['id_sc']);
				$data['sc_library'] = LIBRARY;
				$data['sc_global'] = LIBRARY;
				$this->insert($data);
				$fn++;
			}
		}
		if ($fn == 0) {
			$sx = lang('rdf.no_class_insert');
		} else {
			$sx = $fn . ' ' . lang('rdf.class_inserted');
		}
		$sx = bsmessage($sx);
		$sx = bs(bsc($sx));

		return $sx;

		/*********************************************************** PHASE II */
		$sql = "select cc_class, d_p from rdf_data
						INNER JOIN rdf_concept on d_r1 = id_cc
						where 1=1
						group by cc_class, d_p
						order by cc_class, d_p";
		$dt = $this->db->query($sql);
		$dt = $dt->getResult();
		$li = 0;
		for ($r = 0; $r < count($dt); $r++) {
			$li++;
			if ($li > 500) {
				break;
			}
			$line = (array)$dt[$r];
			$class = $line['cc_class'];
			$prop = $line['d_p'];
			$da = $this->where('sc_class', $class)->where('sc_propriety', $prop)->FindAll();
			if (count($da) == 0) {
				$data = array();
				$data['sc_class'] = $class;
				$data['sc_propriety'] = $prop;
				$data['sc_range'] = $class;
				$data['sc_ord'] = $class;
				$data['sc_ativo'] = 1;
				$data['sc_library'] = LIBRARY;
				$data['sc_global'] = LIBRARY;
				$this->insert($data);
				echo ".";
			}
		}

		/********************************** Excluir Duplicatadas */
		$dt = $this
			->select('count(*) as total, max(id_sc) as id_max, sc_class, sc_propriety, sc_library')
			->where('sc_class', $id)
			->groupBy('sc_class, sc_propriety, sc_library')
			->FindAll();

		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			if ($line['total'] > 1) {
				$this->where('id_sc', $line['id_max'])->delete();
			}
		}
		return $sx;
	}
}