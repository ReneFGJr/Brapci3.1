<?php

namespace App\Models\Language;

use CodeIgniter\Model;

class Lang extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'langs';
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

	function code($x)
		{
			$x = ascii(mb_strtolower($x));
			switch($x)
				{
					case 'italiano':
						return 'it';
						break;
					case 'outros':
						return 'ot';
						break;
					case 'dinamarques':
						return 'dn';
						break;
					case 'assames':
						return 'as';
						break;
					case 'frances':
						return 'fr';
						break;
					case 'ingles':
						return 'en';
						break;
					case 'samoano':
						return 'sm';
						break;
					case 'pt-BR':
						return 'pt-BR';
						break;
					case 'espanhol':
						return 'es';
						break;
					case 'portugues':
						return 'pt-BR';
						break;
					default:
						return 'xx';
						echo 'OPS language '.$x;
						exit;
				}
			return $lang;
		}
}
