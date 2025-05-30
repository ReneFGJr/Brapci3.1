<?php

namespace App\Models\ReverseIndex;

use CodeIgniter\Model;

class Index extends Model
{
	protected $DBGroup              = 'elastic';
	protected $table                = 'ri_words';
	protected $primaryKey           = 'id_w';
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

	function index($d1, $d2 = '', $d3 = '')
	{
		$RSP = [];
		switch ($d1) {
			case 'query_author':
				$RSP = $this->query_author();
				break;
			default:
				$RSP['status'] = '400';
				$RSP['msg'] = 'Bad Request';
				$RSP['data'] = 'Invalid action';
		}
		return $RSP;
	}

	function query_author()
	{
		$RiAuthors = new \App\Models\ReverseIndex\RiAuthors();
		$RSP = [];
		$q = trim(get('q'));
		$names = explode(' ', $q);
		$limit = get('limit');
		if ($limit == '') {
			$limit = 30;
		}

		$names = preg_split('/\s+/', $q);

		$model = new RiAuthors();

		// Agrupa os where para buscar todos os termos em ANY order
		$model->groupStart();
		foreach ($names as $name) {
			$model->Like('au_name', $name);
		}
		$model->groupEnd();

		$results = $model
			->orderBy('au_name', 'ASC')
			->limit($limit)
			->findAll();

		// Formata resposta para o autocomplete
		$payload = array_map(function ($row) {
			return [
				'id'   => $row['id_au'],       // ajuste o campo de ID conforme seu schema
				'name' => $row['au_name'],
			];
		}, $results);

		return $payload;
	}
}
