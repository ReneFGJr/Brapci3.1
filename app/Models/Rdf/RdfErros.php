<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RdfErros extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'rdf_erros';
	protected $primaryKey           = 'id_erro';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_erro','erro_id','erro_msg','erro_nr','erro_data'
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

	function append($erro,$msg,$id)
		{
			$dt['erro_id'] = $id;
			$dt['erro_msg'] = $msg;
			$dt['erro_nr'] = $erro;
			$this->insert($dt);
			return 1;
		}
}
