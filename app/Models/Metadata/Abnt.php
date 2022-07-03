<?php

namespace App\Models\Metadata;

use CodeIgniter\Model;

class Abnt extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'abtns';
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

	function show($dt,$type='A')
		{
			switch($type)
				{
					default:
					$tela = $this->abnt_article($dt);
				}
			return $tela;
		}
	function abnt_article($dt)
		{
			$title = trim(html_entity_decode($dt['title']));
			$title = trim(mb_strtolower($title));
			$tu = mb_strtoupper($title);
			$tu = mb_substr($tu,0,1);
			$te = mb_substr($title,1);
			$title = $tu.$te;
		
			$tela = '';
			$tela .= '<div class="abtn-article">';
			$tela .= $dt['author'];
			$tela .= '. '.$title;
			$tela .= '. <b>'.nbr_author($dt['journal'],7).'</b>';
			if (strlen($dt['volume']) > 0)
				{			
					$tela .= ', '.$dt['volume'];
				}
			if (strlen($dt['number']) > 0)
				{			
					$tela .= ', '.$dt['number'];
				}
			$tela .= ', '.$dt['year'];
			if (strlen($dt['pages']) > 0)
				{
					$tela .= ', p '.$dt['pages'];
				}
			$tela .= '.';
			
			$tela .= '</div>';
			return $tela;
		}
}
