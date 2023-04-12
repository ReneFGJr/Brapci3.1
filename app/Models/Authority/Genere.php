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

	function summary()
		{
			$sx = '';
			$Authority = new \App\Models\Authority\Index();
			$dt = $Authority
				->select('a_class,a_genere,count(*) as total')
				->groupBy('a_genere,a_class')
				->orderBy('a_class,a_genere')
				->findAll();
			$sx .= '<table style="width: 100%;">';
			$sa = '';
			$sb = '';
			$sc = '';
			$sd = '';
			foreach($dt as $id=> $line)
				{
					$sa.= '<th width="33%" class="text-light bg-dark text-center">'.lang('brapci.genere_'.$line['a_genere']).'</th>';
					$sb .= '<td class="text-center" style="font-size: 1.6em;">'.$line['total'].'</td>';
				}
			$sx .= '<tr>'.$sa.'</tr>';
			$sx .= '<tr>' . $sb . '</tr>';
			$sx.= '</table>';
			return $sx;
		}

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
