<?php

namespace App\Models\Oaipmh;

use CodeIgniter\Model;

class GetRecords extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'source_listrecords';
	protected $primaryKey           = 'id_lr';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_lr', 'lr_identifier', 'lr_datestamp',
		'lr_setSpec', 'lr_status', 'lr_jnl',
		'lr_procees', 'lr_issue', 'lr_local_file',
		'lr_work'
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

	var $status = '000';

	function getrecord($id = 0, $issue = 0)
	{
		$sx = '';
		if ($issue > 0) {
			$OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
			$dt = $OAI_ListIdentifiers
				->where('li_issue', $issue)
				->where('li_s', 2)
				->orderby('li_u')
				->findAll();

			if (count($dt) == 0) {
				$OAI_ListIdentifiers->getlastquery();
				$sx .= lang('brapci.nothing_to_harvesting');
				$this->status = '202';
			} else {
				$id = $dt[0]['id_li'];
				$sx .= $this->harvesting($id);
				$this->status = '200';
			}
		}
		return $sx;
	}

	function harvesting($id)
	{
		$RSP = [];
		$OAI = new \App\Models\Oaipmh\Index();
		// https://ebbc.inf.br/ojs/index.php/ebbc/oai?verb=GetRecord&metadataPrefix=oai_dc&identifier=oai:ojs.pkp.sfu.ca:article/4

		$OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
		$dt = $OAI_ListIdentifiers
			->select("*")
			->join('source_issue', 'li_issue = id_is', 'LEFT')
			->join('source_source', 'li_jnl = id_jnl', 'LEFT')
			->find($id);

		if ($dt['is_url_oai'] != '') {
			$url = trim($dt['is_url_oai']);
			$id_li = $dt['id_li'];
		} else {
			$url = trim($dt['jnl_url_oai']);
		}

		$reg = $dt['li_identifier'];
		$url .= '?verb=GetRecord';
		$url .= '&';
		$url .= 'metadataPrefix=oai_dc';
		$url .= '&';
		$url .= 'identifier=' . $reg;

		//pre($dt);

		/********************************* Sincroniza tabelas e atualizações */
		$verif = $this->where('lr_identifier', $reg)->where('lr_jnl', $dt['jnl_frbr'])->findAll();
		if (count($verif) > 0) {
			if ($id_li > 0) {
				$OAI_ListIdentifiers->update_status($id_li, 9);
				$RSP['message'] = lang('brapci.already_process ');
				$RSP['status'] = '202';
				return $RSP;
			}
		}

		$dir = $OAI->dir_tmp($dt['id_li']);
		$file = $dir . 'GetRegister.xml';

		if (file_exists($file)) {
			$da = filectime($file);
			$txt = file_get_contents($file);
			$RSP['file_status'] .= "CACHED";
		} else {
			$txt = $OAI->_call($url);
			$RSP['file_status'] .= "DOWNLOAD";
			if (strlen($txt) > 0)
				{
					dircheck($dir);
					file_put_contents($file,$txt);
				}
		}


		/******************************* METHODS */
		$method = $dt['jnl_oai_method'];
		$RSP['method'] = $method;

		switch ($method) {
			case 0:
				$RSP['result']= $this->Method_00($dt, $txt, $file);
				break;
		}
		return $sx;
	}

	/************************************************ Method 00 */
	function Method_00($dt, $txt, $file = '')
	{
		$RSP = [];
		$txt = troca($txt, 'oai_dc:', '');
		$txt = troca($txt, 'dc:', '');
		$xml = (array)simplexml_load_string($txt);

		$GR = (array)$xml['GetRecord'];
		$GR = (array)$GR['record'];

		$header = (array)$GR['header'];
		$metadata = (array)$GR['metadata'];

		$reg = $dt['li_identifier'];
		$prefLabel = 'A' . strzero($dt['id_li'], 9) . '_' . trim($reg);

		$RDF = new \App\Models\Rdf\RDF();

		/* Craido Trabalho */
		$idp = $RDF->concept($prefLabel, 'Proceeding');

		$RSP['URL'] = PATH.'/v/'.$idp;

		$metadata = (array)$metadata['dc'];

		/************************************************ ISSUE */
		$issue = $metadata['source'];
		$id_issue = $dt['is_source_issue'];
		$RDF->propriety($id_issue, 'hasIssueProceedingOf', $idp, 0);

		/************************************************ Titulo */
		$title = nbr_title($metadata['title']);
		$rsp['title'] = $title;
		$prop = 'brapci:hasTitle';
		$lang = 'pt-BR';
		$literal = $RDF->literal($title, $lang);
		$RDF->RDF_literal($title, $lang, $idp, $prop);

		/************************************************ Autores */
		$auth = array();
		$authors = $metadata['creator'];
		if (!is_array($authors)) {
			$authors = array($authors);
		}
		for ($r = 0; $r < count($authors); $r++) {
			$aut = (string)$authors[$r];
			$author = '';
			$inst = '';

			if ($pos = strpos($aut, ';')) {
				$author = substr($aut, 0, $pos);
				$inst =	trim(substr($aut, $pos + 1, strlen($aut)));
			} else {
				$author = $aut;
			}
			$name = nbr_author($author, 1);
			$id_auth = $RDF->concept($name, 'Person');
			$RDF->propriety($idp, 'hasAuthor', $id_auth, 0);
			/******************************** Vinculo Instituicional */
			if ($inst != '') {
				$id_org = $RDF->concept($inst, 'CorporateBody');
				$RDF->propriety($id_auth, 'affiliatedWith', $id_org, 0);
			}
		}


		/************************************************ Subject */
		$AI = new \App\Models\AI\NLP\Language();
		$auth = array();
		if (isset($metadata['subject'])) {
			$subject = $metadata['subject'];
			if (!is_array($subject)) {
				$subject = array($subject);
			}

			for ($r = 0; $r < count($subject); $r++) {
				$sub = (string)$subject[$r];
				$sub = troca($sub, '.', ';');
				$sub = troca($sub, ',', ';');
				$sub = explode(';', $sub);

				for ($y = 0; $y < count($sub); $y++) {
					$term = trim($sub[$y]);
					$term = nbr_title($term);
					if ($term != '') {
						$lang = $AI->getTextLanguage($term);
						$id_sub = $RDF->concept($term, 'Subject', $lang);
						$RDF->propriety($idp, 'hasSubject', $id_sub, 0);
					}
				}
			}
		}

		/************************************************ Section */
		$Sections = new \App\Models\Base\Sections();
		$sec = $Sections->normalize($dt['li_setSpec'], $dt['id_jnl']);
		$id_sec = $RDF->concept($sec, 'ProceedingSection');
		$RDF->propriety($idp, 'hasSectionOf', $id_sec, 0);

		/************************************************ Abstract */
		if (isset($metadata['description'])) {
			$abs = nbr_title($metadata['description']);
			if ($abs != '') {
				$lang = $AI->getTextLanguage($abs);
				$literal = $RDF->literal($abs, $lang);
				$RDF->propriety($idp, 'hasAbstract', 0, $literal);
			}
		}

		/************************************************ identifier */
		if (isset($metadata['identifier'])) {
			$identifier = $metadata['identifier'];
			if ($identifier != '') {
				if (!is_array($identifier))
					{
						$$identifier = [$identifier];
					}
				foreach($identifier as $id=>$ln)
					{
						$literal = $RDF->literal($ln, '');
						$RDF->propriety($idp, 'hasRegisterId', 0, $literal);
					}
			}
		}

		$dd['lr_identifier'] = $reg;
		$dd['lr_datestamp'] = $dt['li_datestamp'];
		$dd['lr_setSpec'] = $dt['li_setSpec'];
		$dd['lr_status'] = 9;
		$dd['lr_jnl'] = $dt['jnl_frbr'];
		$dd['lr_procees'] = '2';
		$dd['lr_issue'] = $dt['li_s'];
		$dd['lr_local_file'] = $file;
		$dd['lr_work'] = $idp;

		$dr = $this
			->where('lr_identifier',$reg)
			->where('lr_issue',$dt['li_s'])
			->findAll();
		//pre($dr);
		if (count($dr) == 0)
			{
				$this->set($dd)->insert();
			} else {
				$this->set($dd)
				->where('lr_identifier', $reg)
				->where('lr_issue', $dt['li_s'])
				->update();
			}


		/***************** Atualiza */
		$OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
		$OAI_ListIdentifiers->update_status($dt['id_li'], 9);

		return $RSP;
	}
}
