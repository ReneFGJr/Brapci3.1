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
			if ($x == 'pt') { $x = 'portugues'; }
			if ($x == 'en') { $x = 'english'; }
			switch($x)
				{
					case 'english':
						return 'en';
						break;
					case 'bretao':
						return 'bt';
						break;
					case 'it':
						return 'it';
						break;
					case 'italiano':
						return 'it';
						break;
					case 'outros':
						return '??';
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
						return 'eng';
						break;
					case 'samoano':
						return 'sm';
						break;
					case 'por':
						return 'por';
						break;
					case 'espanhol':
						return 'spn';
						break;
					case 'portugues':
						return 'por';
						break;
					case 'pt_br':
						return 'por';
					default:
						return "??";
						//return 'xx';
						echo 'OPS language ['.$x.']'.$x;
						exit;
				}
			return $lang;
		}
}
