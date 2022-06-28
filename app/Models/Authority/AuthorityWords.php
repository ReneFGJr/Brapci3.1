<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class AuthorityWords extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'brapci_authority.AuthorityWords';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_w','w_term','w_term_o'
	];
	protected $typeFields        = [
		'hidden','string:40','string:40'
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

	function prepare_text($fr='')
		{
			$ft = strip_tags($fr);
			$ft = troca($ft,'(',' ( ');
			$ft = troca($ft,')',' ) ');
			$ft = troca($ft,'-',' - ');
			$ft = troca($ft,'[',' [ ');
			$ft = troca($ft,']',' ] ');			
			return $ft;
		}

	function process($fr='')
		{		
			$fr = $this->prepare_text($fr);
			$wds = explode(' ',$fr);

			for ($r=0;$r < count($wds);$r++)
				{
					$term = ascii(mb_strtolower($wds[$r]));
					$term_o = $wds[$r];
					$this->where('w_term',$term);
					$dt = $this->findAll();
					if (count($dt) == 0)
						{
							$dt['w_term'] = $term;
							$dt['w_term_o'] = $term_o;
							$this->insert($dt);
						}
				}
		}
}
