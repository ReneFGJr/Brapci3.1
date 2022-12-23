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

	function IDs($dt)
		{
			$sx = '';
			$lattes = $dt['a_lattes'];
			if ($lattes != '')
				{
					$sx .= $this->lattesID(($lattes));
				}
			return $sx;
		}

	function lattesID($id,$type='icone')
		{
			if ($id == 0) { return ""; }
			$link = 'http://lattes.cnpq.br/'.$id;
			$sx = '<a href="'.$link.'" target="_new">';
			switch($type)
				{
					default:
						$img = URL.'/img/icons/logo_lattes_mini.png';
						$sx .= '<img src="'.$img.'" style="height: 32px;">';
				}
			$sx .= '</a>';
			return $sx;
		}

	function brapciID($id, $type = 'icone')
	{
		if ($id == 0) {
			return "";
		}
		$link = URL.'/v/' . $id;
		$sx = '<a href="' . $link . '" target="_new">';
		switch ($type) {
			default:
				$img = URL . '/img/icons/logo_brapci_mini.png';
				$sx .= '<img src="' . $img . '" style="height: 32px;">';
		}
		$sx .= '</a>';
		return $sx;
	}

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
