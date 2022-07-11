<?php

namespace App\Models\PGCD;

use CodeIgniter\Model;

class PlansFormSections extends Model
{
	protected $DBGroup              = 'pgcd';
	protected $table                = 'plan_form_section';
	protected $primaryKey           = 'id_pfs';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

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

	function index()
		{
			
		}

	function tabs($id)
		{
			$dt = $this
				->where('pfs_form',$id)
				->orderBy('pfs_order')
				->findAll();
			return $dt;
		}
}
