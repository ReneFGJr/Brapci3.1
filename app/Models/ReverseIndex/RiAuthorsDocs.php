<?php

namespace App\Models\ReverseIndex;

use CodeIgniter\Model;

class RiAuthorsDocs extends Model
{
	protected $DBGroup              = 'elastic';
	protected $table                = 'ri_authors_docs';
	protected $primaryKey           = 'id_ad';
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

	function recoverDocs($docs = [])
	{
		$RSP = [];
		if (empty($docs)) {
			$RSP['status'] = '400';
			$RSP['msg'] = 'Bad Request';
			$RSP['data'] = 'Invalid action';
			return $RSP;
		}

		$dt = $this->select('ad_author, ad_doc')
				->whereIn('ad_author', $docs)
				->groupBy('ad_author, ad_doc')
				->findAll();
		$AUTHORS = [];

		foreach ($dt as $key => $value) {
			$ndDoc = $value['ad_doc'];
			if (!isset($AUTHORS[$ndDoc])) {
				$AUTHORS[$ndDoc] = 0;
			}
			$AUTHORS[$ndDoc]++;
		}

		/******************* Seleção */
		$doa = [];
		foreach ($AUTHORS as $k => $v) {
			if ($v == count($docs)) {
				$doa[] = $k;
			}
		}

		$RiAuthors = new \App\Models\ReverseIndex\RiAuthors();
		$RSP = $RiAuthors
			->select('*')
			->whereIn('id_au', $doa)
			->findAll();
		return $RSP;
	}
}
