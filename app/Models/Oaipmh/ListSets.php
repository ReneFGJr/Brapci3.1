<?php

namespace App\Models\Oaipmh;

use CodeIgniter\Model;

class ListSets extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'source_listsets';
	protected $primaryKey           = 'id_ls';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_ls', 'ls_setSpec', 'ls_setName',
		'ls_description', 'ls_journal', 'updated_at',
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

	public function getAll($data)
	{
		$OAI = new \App\Models\Oaipmh\Index();
		if (isset($data['jnl_url_oai'])) {
			$url = $OAI->url($data, 'ListSets');
			$tp = 1;
		} else {
			$id = 0;
			if (round($id) <= 0) {
				echo "ERRO 450 - Journal ID note found";
				exit;
			}
		}

		$cnt = read_link($url);
		$xml = simplexml_load_string($cnt);

		if (isset($xml->responseDate))
			{
					$sets = (array)$xml->ListSets;
					$set = (array)$sets['set'];
					//->set;
					foreach($set as $id=>$dta)
						{
							$this->register($data['jnl_frbr'], (string)$dta->setSpec, (string)$dta->setName);
						}
			}
	}

	function register($jnl,$setSpec,$setName)
		{
			$dt = array();
			$dt['ls_setSpec'] = $setSpec;
			$dt['ls_setName'] = $setName;
			$dt['ls_description'] = '';
			$dt['ls_journal'] = $jnl;
			$dt['updated_at'] = date("Y-m-d H:i:s");

			$dta = $this
				->where('ls_setSpec',$setSpec)
				->where('ls_journal', $jnl)
				->first();

			if ($dta == '')
				{
					$id = $this->set($dt)->insert();
				} else {
					$id = $dta['id_ls'];
				}
			return $id;
		}

	function resume()
		{
			$sx = '';
			$sx .= 'RESUME';
			return $sx;
		}
}
