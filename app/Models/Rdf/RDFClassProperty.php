<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFClassProperty extends Model
{
	var $DBGroup              = 'rdf';
	var $table                = PREFIX.'rdf_data';
	protected $primaryKey           = 'id_d';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'd_r1','d_p','d_r2','d_literal','d_library'
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
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$dt = $RDFClass->le($id);

		$sx = h($RDF->show_class($dt[0]));
		$sx .= '<hr style="border: 1px solid #000;">';

		/************* Classe Registradas */
		//$sa = $this->class_propriety($id);
		$sa = $this->propretyClass($id);
		$sb = '';

		$sx = bs(bsc($sx, 12));
		$sx .= bs(bsc($sa, 4) . bsc($sb, 8), 'container-fluid');

		return $sx;
		}

	function propretyClass($id)
		{
			$dt = $this
				->select('id_c, c_class')
				->join('rdf_concept','id_cc = d_r2')
				->join('rdf_class', 'cc_class = id_c')
				->where('d_p',$id)
				->groupBy('id_c', 'c_class')
				->findAll();

			$sx = '<ul>';
			for ($r=0;$r < count($dt);$r++)
				{
					$link = '<a href="'.PATH. '/rdf/class/view/'.$dt[$r]['id_c'].'">';
					$linka = '</a>';
					$sx .= '<li>'.$link.$dt[$r]['c_class'].$linka.'</li>';
				}
			$sx .= '</ul>';
			return $sx;
		}
	function edit($d1,$d2)
		{
			$sx = '';
			$sx .= form_open();
			$sx .= '<table class="table">';
			$sx .= '<tr><td>'.msg('d_r1').'</td><td>'.$d1.'</td></tr>';
			$sx .= '</table>';
			$sx .= form_close();
			return $sx;
		}

	function relation($data)
		{
			$this->where('d_r1',$data['d_r1']);
			$this->where('d_r2',$data['d_r2']);
			$this->where('d_p',$data['d_p']);
			$this->where('d_literal',$data['d_literal']);
			$this->where('d_library',$data['d_library']);
			$dt = $this->First();

			if (!is_array($dt))
				{
					$this->insert($data);
					$dt = $this->relation($data);
				}
			return($dt);
		}
}
