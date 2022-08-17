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
		'id_lr', '	lr_identifier', 'lr_datestamp',
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

	function getrecord($id = 0, $issue = 0)
	{
		$sx = '';
		if ($issue > 0) {
			$OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
			$dt = $OAI_ListIdentifiers
				->where('li_issue', $issue)
				->where('li_s', 1)
				->orderby('li_u')
				->findAll();

			$id = $dt[0]['id_li'];
			$sx .= $this->harvesting($id);
		}
		return $sx;
	}

	function harvesting($id)
	{
		$OAI = new \App\Models\Oaipmh\Index();
		$sx = '';
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
		$sx .= h(anchor($url), 6);

		//pre($dt);

		/********************************* Sincroniza tabelas e atualizações */
		$verif = $this->where('lr_identifier',$reg)->where('lr_jnl',$dt['jnl_frbr'])->findAll();
		if (count($verif) > 0)
			{
				if ($id_li > 0)
					{
						$OAI_ListIdentifiers->update_status($id_li, 9);
						$sx = bsmessage(lang('brapci.already_process ' . $reg), 4);
						$sx .= metarefresh('',1);
						return $sx;
					}
			}

		$dir = $OAI->dir_tmp($dt['id_is']);
		$file = $dir . 'GetRegister.xml';
		if (file_exists($file)) {
			$txt = file_get_contents($file);
			$sx .= "CACHED<br>";
		} else {
			$txt = $OAI->_call($url);
			$sx .= "DOWNLOAD<br>";
		}

		/******************************* METHODS */
		$method = $dt['jnl_oai_to_harvesting'];
		$sx .= h('Method '.$method,3);
		switch ($method) {
			case 0:
				$sx .= $this->Method_00($dt, $txt);
				break;
		}
		return $sx;
	}

	/************************************************ Method 00 */
	function Method_00($dt, $txt)
	{
		$txt = troca($txt, 'oai_dc:', '');
		$txt = troca($txt, 'dc:', '');
		$xml = (array)simplexml_load_string($txt);

		$GR = (array)$xml['GetRecord'];
		$GR = (array)$GR['record'];

		$header = (array)$GR['header'];
		$metadata = (array)$GR['metadata'];

		$reg = $dt['li_identifier'];
		$prefLabel = 'A'.strzero($dt['id_li'],9).'_'.trim($reg);
		echo h($prefLabel);

		$RDF = new \App\Models\Rdf\RDF();
		$idp = $RDF->concept($prefLabel, 'Proceeding');
		echo '===>'.$idp;

		$metadata = (array)$metadata['dc'];

		/************************************************ ISSUE */
		$issue = $metadata['source'];
		$id_issue = $RDF->concept($issue, 'IssueProceeding');
		$RDF->propriety($id_issue, 'hasIssueProceedingOf', $idp, 0);

		/************************************************ Titulo */
		$title = nbr_title($metadata['title']);
		$prop = 'brapci:hasTitle';
		$lang = 'pt-BR';
		$literal = $RDF->literal($title,$lang);
		$RDF->RDF_literal($title, $lang, $idp,$prop);

		/************************************************ Autores */
		$auth = array();
		$authors = $metadata['creator'];
		for ($r=0;$r < count($authors);$r++)
			{
				$aut = (string)$authors[$r];
				$author = '';
				$inst = '';

				if ($pos = strpos($aut,';'))
					{
						$author = substr($aut,0,$pos);
						$inst =	trim(substr($aut, $pos+1,strlen($aut)));
					} else {
						$author = $aut;
					}
				$name = nbr_author($author, 1);
				$id_auth = $RDF->concept($name, 'Person');
				$RDF->propriety($idp, 'hasAuthor', $id_auth, 0);
				/******************************** Vinculo Instituicional */
				if ($inst != '')
					{
						$id_org = $RDF->concept($inst, 'CorporateBody');
						$RDF->propriety($id_auth, 'hasAuthor', $id_org, 0);
					}
			}


		/************************************************ Subject */
		$auth = array();
		$subject = $metadata['subject'];
		for ($r = 0; $r < count($subject); $r++) {
			$aut = (string)$subject[$r];
			$sub = '';

			if ($pos = strpos($aut, ';')) {
				$author = substr($aut, 0, $pos);
			} else {
				$author = $aut;
			}
			$name = nbr_author($author, 1);
			$id_auth = $RDF->concept($name, 'Person');
			$RDF->propriety($idp, 'hasAuthor', $id_auth, 0);
		}





		$dd['lr_identifier'] = $reg;
		$dd['lr_datestamp'] = '';
		$dd['lr_setSpec'] = '';
		$dd['lr_status'] = '';
		$dd['lr_jnl'] = '';
		$dd['lr_procees'] = '';
		$dd['lr_issue'] = '';
		$dd['lr_local_file'] = '';
		$dd['lr_work'] = '';

		pre($xml);
	}
}
