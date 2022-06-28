<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Charsets extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'charsets';
	protected $primaryKey           = 'id';
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

	function convert($d1,$d2,$d3)
		{
			$sx = '';
			$txt = get("dd1");
			
			if (strlen($txt) > 0)
				{
					$t1 = utf8_decode($txt);
					$t2 = utf8_encode($txt);
					$t1 = '<textarea class="form-control" rows="10">'.$t1.'</textarea>';
					$t2 = '<textarea class="form-control" rows="10">'.$t2.'</textarea>';
					$sx .= bsc(h('ai.without_utf8',3).$t1,6).bsc(h('ai.with_utf8',3).$t2,6);
					$sx = bs($sx);
				}
			return $sx;
		}
}
