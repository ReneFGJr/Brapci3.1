<?php

namespace App\Models\PGCD;

use CodeIgniter\Model;

class PlansForm extends Model
{
	protected $DBGroup              = 'pgcd';
	protected $table                = 'plan_form';
	protected $primaryKey           = 'id_pf';
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

	function form($pr,$tab)
		{
			$Plans = new \App\Models\PGCD\Plans();
			$dt = $Plans->find($pr);

			$form = $dt['p_form'];

			$dt = $this
				//->select('pf_acronic, pf_name')
				->join('plan_form_fields','plf_plan_id = id_pf')
				->join('plan_form_section','plf_plan_section = id_pfs')
				->join('plan_form_values','pv_field = id_plf','left')
				->where('id_pf',$form)
				->where('id_pfs',$tab)
				->orderBy('plf_ord')
				->findAll();

			$sx = '';
			for ($r=0;$r < count($dt);$r++)
				{
					$data = $dt[$r];
					$view = 'PGCD/Forms/'.strtolower($data['plf_type']);
					$sx .= view($view,$data);
				}
			return $sx;
		}
}
