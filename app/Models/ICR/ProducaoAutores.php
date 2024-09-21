<?php

namespace App\Models\ICR;

use CodeIgniter\Model;

class ProducaoAutores extends Model
{
	protected $DBGroup          	= 'elastic';
	protected $table            	= 'dataset';
	protected $primaryKey       	= 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_pjs',
		'pjs_journal',
		'pjs_ano',
		'pjs_tipo',
		'pjs_total'
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

	function get($jid, $year_ini = 2019, $year_end = 2023)
	{
		$dt = $this
			->select('YEAR, AUTHORS')
			->where('JOURNAL', $jid)
			->where('YEAR >= ' . $year_ini)
			->where('YEAR <= ' . $year_end)
			->findAll();
		$AUTHORS = $this->analyse($dt);
		return $AUTHORS;
	}

	function analyse($dt)
	{
		$AUTH = [];
		foreach ($dt as $id => $line) {
			$names = $line['AUTHORS'];
			$names = explode(';', $names);
			$namesArt = [];
			echo '=====================' . cr();
			print_r($line);
			foreach ($names as $id => $name) {
				$name = trim($name);
				echo $name.'<br>'.cr();
				if (!isset($namesArt[$name])) {
					if (!isset($AUTH[$name])) {
						$AUTH[$name] = 0;
					}
					$AUTH[$name] = $AUTH[$name] + 1;
				}
				$namesArt[$name] = 1;
			}
		}
		arsort($AUTH);
		return $AUTH;
	}
}
