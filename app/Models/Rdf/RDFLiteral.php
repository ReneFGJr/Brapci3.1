<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFLiteral extends Model
{
	var $DBGroup              = 'rdf';
	var $table                = PREFIX . 'rdf_name';
	protected $primaryKey           = 'id_n';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_n', 'n_name', 'n_lock', 'n_lang'
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

	function atualiza($data, $id)
	{
		$n_name = $data['n_name'];
		$n_lang = $data['n_lang'];
		$sql = "update " . $this->table . "
				set n_name = '$n_name', n_lang = '$n_lang' where id_n = $id";
		$this->query($sql);
		return 1;
	}

	function le($id)
	{
		$dt = $this->find($id);
		return $dt;
	}

	function name($name, $lg = 'pt-BR', $force = 1)
	{
		$dt = $this->where('n_name', $name)->First();
		if (!is_array($dt)) {
			if ($force == 1) {
				$data['n_name'] = $name;
				$data['n_lock'] = 0;
				$data['n_lang'] = $lg;
				$id = $this->insert($data);
				return $id;
			} else {
				return 0;
			}
		}
		return $dt['id_n'];
	}
}