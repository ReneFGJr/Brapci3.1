<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFClassProperty extends Model
{
	var $DBGroup              = 'rdf';
	protected $table                = PREFIX.'rdf_data';
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
