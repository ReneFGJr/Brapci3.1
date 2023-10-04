<?php

namespace App\Models\Oaipmh;

use CodeIgniter\Model;

class Identify extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'files';
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

	function resume($id = 0)
	{
		$sx = '';
		$sx .= 'RESUME';

		return $sx;
	}

	function getIdentify($dt)
	{
		$dts = $this->identify(0, $dt);
		$RDF = new \App\Models\Rdf\RDF();
		$RDFConcept = new \App\Models\Rdf\RDFConcept();
		$Soucers = new \App\Models\Base\Sources();

		if ($dts['jnl_frbr'] == 0) {
			/********* Registra Journal */
			switch ($dts['jnl_collection']) {
				case 'JA':
					$setName = $dts['repositoryName'];
					$class = 'Journal';
					$dtx = array();
					$dtx['Literal']['skos:prefLabel'] = $setName;
					$dtx['Class'] = $class;
					$dts['jnl_frbr'] = $RDFConcept->concept($dtx);

					$Soucers->set($dts)->where('id_jnl', $dt['id_jnl'])->update();

					/****************************************************** Propriedades */
					/* ID JNL */
					$jnl = 'jnl:' . $dt['id_jnl'];
					$term = $RDF->literal($jnl, 'NnN');
					$RDF->propriety($dts['jnl_frbr'], 'brapci:hasIdRegister', 0, $term);


					if (strlen(trim($dts['jnl_url'])) > 0) {
						$term = $RDF->literal(trim($dt['jnl_url']), 'NnN');
						$RDF->propriety($dts['jnl_frbr'], 'hasUrl', 0, $term);
					}

					if (strlen(trim($dts['adminEmail'])) > 0) {
						$term = $RDF->literal(trim($dts['adminEmail']), 'NnN');
						$RDF->propriety($dts['jnl_frbr'], 'dc:hasEmail', 0, $term);
					}
					break;
			}
		}

		wclose();
	}

	public function identify($id, $data = array())
	{
		$OAI = new \App\Models\Oaipmh\Index();
		$tp = 0;

		if (isset($data['jnl_url_oai'])) {
			$url = $OAI->url($data, 'identify');
			$tp = 1;
		} else {
			if (round($id) <= 0) {
				echo "ERRO 450 - Journal ID note found";
				exit;
			}
		}

		$cnt = read_link($url);
		$xml = simplexml_load_string($cnt);

		$dt = array();
		$dt['id'] = $id;
		$dt['repositoryName'] = $this->xml_value($xml->Identify->repositoryName);
		$dt['protocolVersion'] = $this->xml_value($xml->Identify->protocolVersion);
		$dt['adminEmail'] = $this->xml_value($xml->Identify->adminEmail);
		$dt['deletedRecord'] = $this->xml_value($xml->Identify->deletedRecord);
		$dt['granularity'] = $this->xml_value($xml->Identify->granularity);
		$dt['baseURL'] = $this->xml_value($xml->Identify->baseURL);
		$dt['responseDate'] = $this->xml_value($xml->responseDate);
		$dt = array_merge($data, $dt);

		if ($tp == 0) {
			$this->frbr->journal($dt);
			$dt['issue'] = $this->getListSets($id);
		} else {
			$dt['issue'] = $this->getListSets($id);
			return ($dt);
		}
	}

	public function getListSets($id)
		{
			$RSP = [];
			return $RSP;
		}

	public function xml_value($x)
	{
		if (strlen($x) == 0) {
			return ("");
		}
		foreach ($x as $key => $value) {
			return ((string)$value);
		}
	}

	function harvesting($dt)
	{
		$OAI = new \App\Models\Oaipmh\Index();
		$url = $OAI->url($dt);
		pre($dt);
	}
}