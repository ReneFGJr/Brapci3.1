<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFClass extends Model
{
	var $DBGroup              = 'rdf';
	var $table                = PREFIX . 'rdf_class';
	protected $primaryKey           = 'id_c';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_c','c_class', 'c_prefix', 'c_type', 'c_url', 'c_equivalent'
	];

	protected $typeFields        = [
		'hidden','string*', 'sql:id_prefix:prefix_ref:rdf_prefix*', 'op:C&Classe:P&Propriety*', 'string', 'sql:id_c:c_class:rdf_class:c_type=\'C\''
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

	function view($id)
		{
			$RDF = new \App\Models\Rdf\RDF();
			$RDFForm = new \App\Models\Rdf\RDFForm();
			$dt = $RDF->le($id);

			if (!isset($dt['concept'])) {
				return "";
			}

			$class = $dt['concept'];
			$sx = h($RDF->show_class($class));
			$sx .= '<hr style="border: 1px solid #000;">';

			/************* Classe Registradas */
			$sa = $this->class_propriety($id);
			$sb = $RDFForm->form_class_edit($id,$class['id_cc']);

			$sx = bs(bsc($sx,12));
			$sx .= bs(bsc($sa,4).bsc($sb,8),'container-fluid');

			return $sx;

		}

	function get($namespace)
		{
		$cp = 'id_c as id, prefix_url as prefixURL, prefix_ref as prefix, c_class as Class, c_type as Type, c_url as url, c_equivalent';
		//$cp = '*';
		$dt = $this->select($cp)
			->join('rdf_prefix', 'id_prefix = c_prefix')
			->where('c_class', $namespace)
			->orderBy('c_class')
			->findAll();
		return $dt;
		}
	function getAll()
		{
			$dt = [];
			$dt['Classes'] = $this->getClasses();
			$dt['Properties'] = $this->getProperties();
			return $dt;
		}

	function getClasses()
	{
		$cp = 'id_c as id, prefix_ref as prefix, c_class as Class, c_type as Type, c_url as url';
		//$cp = '*';
		$dt = $this->select($cp)
				->join('rdf_prefix', 'id_prefix = c_prefix')
				->where('c_type','C')
				->where('c_equivalent', 0)
				->orderBy('c_class')
				->findAll();
		return $dt;
	}

	function getProperties()
	{
		$cp = 'id_c as id, prefix_ref as prefix, c_class as Class, c_type as Type, c_url as url';
		//$cp = '*';
		$dt = $this->select($cp)
			->join('rdf_prefix', 'id_prefix = c_prefix')
			->where('c_type', 'P')
			->where('c_equivalent', 0)
			->orderBy('c_class')
			->findAll();
		return $dt;
	}

	/************************************************************************ Relation Class / Proprity */
	function class_propriety($id)
		{
			$sx = '';
			$RDFData = new \App\Models\Rdf\RDFConcept();
			$dt = $RDFData
				->select('c_class, id_c, prefix_ref')
				->join('rdf_data','id_cc = d_r1')
				->join('rdf_class', 'd_p = id_c')
				->join('rdf_prefix', 'c_prefix = id_prefix')
				->where('cc_class',$id)
				->orderby('prefix_ref, c_class, id_c, prefix_ref')
				->groupby('c_class,id_c')
				->findAll();
			$sx .= '<b>'.lang('rdf.propriety_class_related'). '</b>';
			$sx .= '<ul>';
			for ($r=0;$r < count($dt);$r++)
				{
					$line = $dt[$r];
					$name = $line['prefix_ref'].':'.$line['c_class'];
					$sx .= '<li>'.$name.'</li>';
				}
			$sx .= '</ul>';
			return $sx;
		}

	function edit($id)
	{
		$this->id = $id;
		$this->path = PATH. '/rdf/class/';
		$this->path_back = PATH . '/rdf/class';
		$sx = h('rdf.Class');
		$sx .= form($this);
		$sx = bs(bsc($sx,12));
		return $sx;
	}

	function list($tp = 'C')
	{
		if ($tp == 'C')
			{
				$sx = h('rdf.classes');
				$view = 'class/view';
				$label = 'class';
				$edit = 'class/edit';
			} else {
				$sx = h('rdf.propriety');
				$view = 'property/view';
				$label = 'propriety';
				$edit = 'property/edit';
			}

		$dt = $this
			->join('rdf_prefix', 'c_prefix = id_prefix', 'left')
			->where('c_type', $tp)
			->orderBy("c_class")
			->findAll();
		$sx .= '<div style="column-count: 3;">';
		$sx .= '<ul>';
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$link = '<a href="' . PATH . COLLECTION . '/'. $view.'/' . $line['id_c'] . '">';
			$linka = '</a>';
			$sx .= '<li>' . $link . $line['c_class'] . $linka . '</li>';
		}
		$sx .= '</ul>';
		$sx .= '</div>';

		$sx .= '<a href="'.PATH.'/rdf/class/edit/0'.'" class="btn btn-primary">'.lang('rdf.new_'.$label).'</a>';
		$sx = bs(bsc($sx, 12));
		return $sx;
	}

	function le($id)
	{
		$dt = $this
			->join('rdf_prefix', 'c_prefix = id_prefix', 'LEFT')
			->where('id_c', $id)
			->findAll();
		return $dt;
	}

	function class($c, $force = True)
	{
		$this->Prefix = new \App\Models\Rdf\RDFPrefix();
		$this->Prefix->DBGroup = $this->DBGroup;

		if (strpos($c, ':')) {
			$prefix = substr($c, 0, strpos($c, ':'));
			$Prefix = $this->Prefix->prefixo($prefix);
			$Class = substr($c, strpos($c, ':') + 1, strlen($c));
		} else {
			$Class = $c;
			$prefix = '';
			$Prefix = 0;
		}

		/* Localiza todos as classes */
		$ID = $this->where('c_prefix', $Prefix)->where('c_class', $Class)->first();
		if (is_array($ID) == 0) {
			$ID = $this->where('c_class', $Class)->first();
			if (is_array($ID) == 0) {
				$data['c_class'] = $Class;
				$data['c_prefix'] = $Prefix;
				/*********************** Tipo */
				$data['c_type'] = 'C';
				if (substr($Class, 0, 1) == strtolower(substr($Class, 0, 1))) {
					$data['c_type'] = 'P';
				}

				$data['c_url'] = $Class;
				if ($force == True) {
					$this->insert($data);
					$ID = $this->where('c_prefix', $Prefix)->where('c_class', $Class)->first();
				} else {
					return (-1);
				}
			}
		}
		return $ID['id_c'];
	}

	function inport($url = '')
	{
		$sx = '';
		$ID = 3;
		$ID_file = 9;
		$URL = 'http://cedapdados.ufrgs.br';
		$IDP = 'hdl:20.500.11959/CedapDados/' . $ID . '/' . $ID_file;
		$url = $URL . '/api/access/datafile/:persistentId?persistentId=' . $IDP;
		$lang = 'pt-BR';
		$dir = '.tmp';
		$file = md5($url);
		$filename = $dir . '/' . $file;
		return $filename;
		exit;

		/* Leitura do Arquivo */
		if (!is_dir($dir)) {
			mkdir($dir);
		}
		if (file_exists($filename)) {
			$txt = file_get_contents($filename);
		} else {
			/************************************* */
			$txt = file_get_contents($url);
			file_put_contents($filename, $txt);
		}
		$txt = str_replace(array('"'), array(''), $txt);
		$lns = explode(chr(10), $txt);
		$hd = explode(chr(9), $lns[0]);

		for ($r = 01; $r < count($lns); $r++) {
			$ln = explode(chr(9), $lns[$r]);
			if (count($ln) > 1) {
				for ($y = 0; $y < count($hd); $y++) {
					$dt[$hd[$y]] = $ln[$y];
				}

				$dz = $this->where('prefix_ref', $dt[$hd[0]])->findAll();

				if (isset($dz[0])) {
				} else {
					$this->insert($dt);
				}
			}
		}
		$sx .= bsmessage('DataSet File inported', 1);
		return $sx;
	}
}