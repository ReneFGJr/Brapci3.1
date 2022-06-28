<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class AuthotityIds extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'brapci_authority.AuthorityNames';
	protected $primaryKey           = 'id_a';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_a','a_lattes','a_ordid'
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

	function LattesFindID($id)
		{
			$tela = '';
			$Api = new \App\Models\Api\Endpoints();
			$AuthorityNames = new \App\Models\Authority\AuthorityNames();

			$dt = $AuthorityNames->find($id);
			$name = trim($dt['a_prefTerm']);

			$dta = $Api->LattesFindID($name);

			if (isset($dta['result']))
				{
					$dtc = $dta['result'];
					if (count($dtc) == 1)
						{
							$data['id_a'] = $dt['id_a'];
							foreach($dtc as $id_lattes=>$name)
								{
									$data['a_lattes'] = $id_lattes;
									$sql = "update ".$this->table." set a_lattes = '".$id_lattes."' where id_a = ".$dt['id_a'];

									$this->query($sql);
									$tela .= metarefresh(base_url(PATH.MODULE.'/index/viewid/'.$dt['id_a']));
								}
						} else {
							echo '<pre>';
							print_r($dta);
							echo '</pre>';
							exit;
						}
				}
			return $tela;
		}
}
