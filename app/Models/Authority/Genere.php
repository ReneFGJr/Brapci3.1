<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class Genere extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'generes';
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

	function image($type)
		{
			switch($type)
				{
					case 'M':
						$img = URL . '/img/genre/no_image_he.jpg';
						break;
					case 'F':
						$img = URL . '/img/genre/no_image_she.jpg';
						break;
					default:
						$img = URL . '/img/genre/no_image_she_he.jpg';
						break;
				}
			return $img;
		}
}
