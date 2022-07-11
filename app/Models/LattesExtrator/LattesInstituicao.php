<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesInstituicao extends Model
{
	protected $DBGroup              = 'lattes';
	protected $table                = 'LattesInstituicao';
	protected $primaryKey           = 'id_inst';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_inst', 'inst_codigo', 'inst_nome'
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

	function instituicao($id = '', $nome = '')
	{
		$dt = $this->where('inst_codigo', $id)->findAll();
		if (count($dt) == 0) {
			$dd['inst_codigo'] = $id;
			$dd['inst_nome'] = $nome;
			$this->insert($dd);
		}
	}
}