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
		$RSP = [];
		$q = trim(get('q'));
		$limit = get('limit');
		if ($limit == '') $limit = 50;

		$w = explode(' ', $q);

		$docs = [];
		foreach ($w as $k => $v) {
			$docs[] = $this->where('w_name', $v)->findAll($limit);
		}
		$docsx = [];
		foreach ($docs as $k => $v) {
			$docsx[$k] = $v[0]['id_w'];
		}
		if (count($docsx) == 0) {
			$RSP['status'] = '400';
			$RSP['msg'] = 'Bad Request';
			$RSP['data'] = 'Invalid action';
			return $RSP;
		}
		$recoverDocs = new \App\Models\ReverseIndex\RiAuthorsDocs();
		$docsR = $recoverDocs->recoverDocs($docsx);

		$RSP['status'] = '200';
		$RSP['msg'] = 'OK';
		$RSP['data'] = $w;
		$RSP['docs'] = $docsR;

		return $RSP;
	}
}
