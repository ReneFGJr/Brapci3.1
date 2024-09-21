<?php

namespace App\Models\ICR;

use CodeIgniter\Model;

class ProducaoJournalAno extends Model
{
	protected $DBGroup              = 'icr';
	protected $table                = 'icr_producao_journal_ano';
	protected $primaryKey           = 'id_pjs';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_pjs','pjs_journal','pjs_ano','pjs_tipo','pjs_total'
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

	function get($jid)
		{
			$dt = $this
				->select('pjs_journal as journal, pjs_ano as year, pjs_total as total')
				->where('pjs_journal',$jid)
				->findAll();
			return $dt;
		}

	function createIndex($jid)
		{
			$sql = "TRUNCATE brapci_icr.".$this->table;
			$this->db->query($sql);

			$Elastic = new \App\Models\ElasticSearch\Register();

			$row = $Elastic
				->select('count(*) as total, JOURNAL, YEAR')
				->where('JOURNAL',$jid)
				->where('`USE`', 0)
				->groupBy('JOURNAL,YEAR')
				->orderBy('JOURNAL,YEAR')
				->findAll();
			foreach ($row as $id => $line) {
				$dd = [];
				$dd['pjs_journal'] = $line['JOURNAL'];
				$dd['pjs_ano'] = $line['YEAR'];
				$dd['pjs_tipo'] = 'WORK';
				$dd['pjs_total'] = $line['total'];
				$this->set($dd)->insert();
		}
		$RSP = [];
		$RSP['status'] = '200';
		$RSP['message'] = 'Reindex Success ['.$jid.']';
		$RSP['journal'] = $jid;
		return $RSP;
	}
}
