<?php

namespace App\Models\Oaipmh;

use CodeIgniter\Model;

class Index extends Model
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

	function index($d1 = '', $d2 = '', $d3 = '')
	{
		$sx = '';
		switch ($d1) {
			case 'identify':
				$sx .= $this->identify($d1, $d2);
				break;
				/*************************************************************** get register */
			case 'getregister':
				$sx .= $this->getregister($d1, $d2);
				break;
			case 'getReg':
				$sx .= $this->getReg($d2, $d3);
				break;
			default:
				echo "OPS OAI $d1,$d2";
				exit;
		}

		return $sx;
	}

	function getReg($id, $tp = '')
	{
		$Source = new \App\Models\Base\Sources();
		$file = $Source->filename($id);

		if ($tp == 'json') {

			$file = troca($file, '.xml', '.json');
			if (file_exists($file)) {
				$txt = file_get_contents($file);
				$txt = json_decode($txt);
				pre($txt);
			} else {
				echo "Arquivo não existe ".$file;
			}
			exit;
		} else {
			if (file_exists($file)) {
				$xml = file_get_contents($file);
				//$xml = utf8_encode($xml);
				$xml = troca($xml, 'oai_dc:', '');
				$xml = troca($xml, 'dc:', '');
				//$xml = troca($xml, 'oai_dc:', '');
				//$xml = troca($xml, 'oai_dc:', '');
				for($r=0;$r < 32;$r++)
					{
						$xml = troca($xml, chr($r), '['.$r.']');
					}
				pre($xml);

				try {
					$xml = (array)simplexml_load_string($xml);
				} catch (Exception $e) {
					echo 'Caught exception: ',  $e->getMessage(), "\n";
				}

				pre($xml);
				exit;
			}
		}
		echo "File not harvesting " . $file;
		exit;
	}

	function identify($d1, $d2)
	{
		$RSP = [];
		$id = $d2;
		$Identify = new \App\Models\Oaipmh\Identify();
		$Soucers = new \App\Models\Base\Sources();
		$dt = $Soucers->find($d2);
		if ($dt == '') {
			$RSP['status'] = '404';
			$RSP['message'] = bsmessage('Harvesting Identify - erro no id', 3);
		} else {
			$RSP['status'] = '200';
			$RSP = $Identify->Identify($id, $dt);
		}
		return $RSP;
	}

	function api($d1, $d2)
	{
		$RSP = [];
		switch ($d1) {
			case 'identify':
				$RSP = $this->identify($d1, $d2);
				break;
			case 'getIssue':
				$GetRecords = new \App\Models\Oaipmh\GetRecords();
				$RSP = $GetRecords->getrecord(0, $d2);
				if (isset($RSP['message']['result']['URL'])) {
					$RSP['rdf'] = sonumero($RSP['message']['result']['URL']);
				}
				break;
			default:
				$RSP['status'] = '404';
				$RSP['messagem'] = 'Function not found - ' . $d1 . '-' . $d2;
		}
		return $RSP;
	}


	function getregister($d1, $d2)
	{
		$sx = '';
		$GetRecords = new \App\Models\Oaipmh\GetRecords();
		$ListSets = new \App\Models\Oaipmh\ListSets();
		$Soucers = new \App\Models\Base\Sources();
		/************************* Recupera Journal */
		$dt = $Soucers->find($d2);

		/************************* Recupera ListSets */
		$ListSets->getAll($dt);
		return $sx;
	}

	function url($data, $verb)
	{
		$url = trim($data['jnl_url_oai']);
		$scielo = '';
		if ($data['jnl_scielo'] == '1') {
			$scielo = '&set=' . $data['jnl_oai_set'];
		}
		switch ($verb) {
			case 'ListSets':
				$url .= '?verb=ListSets';
				break;

			case 'GetRecord':
				$url .= '?verb=GetRecord&metadataPrefix=oai_dc&identifier=';
				break;

			case 'GetRecordNlm':
				$url .= '?verb=GetRecord&metadataPrefix=nlm&identifier=';
				break;
			case 'ListIdentifiers':
				if (strlen($data['jnl_oai_token']) > 5) {
					$url .= '?verb=ListIdentifiers&resumptionToken=' . trim($data['jnl_oai_token']);
					$url .= $scielo;
				} else {
					$url .= '?verb=ListIdentifiers&metadataPrefix=oai_dc';
					$url .= $scielo;
				}
				break;
			case 'identify':
				$url .= '?verb=Identify';
				break;
		}
		return ($url);
	}

	function logo()
	{
		$sx = '';
		$sx .= '<span style="margin-right: 5px; font-size: 0.6em; border: 1px solid #000; padding: 2px 5px;">OAI-PMH</span>';
		return $sx;
	}

	function painel($dt)
	{
		$jid = $dt['id_jnl'];
		$sx = h(lang('brapci.painel'), 4);
		if ($dt['jnl_url_oai'] != '') {
			$sx .= $this->logo();

			if ($dt['jnl_frbr'] == 0) {
				$url = PATH . 'bots/harvesting/identify/' . $dt['id_jnl'];
				$link = '<a href="#" onclick="newxy2(\'' . $url . '\',600,600);">';
				$linka = '</a>';
				$sx .= '&nbsp;' . $link . bsicone('upload') . $linka;
			}

			$url = PATH . 'admin/source/edit/' . $dt['id_jnl'];
			$link = '<a href="' . $url . '">';
			$linka = '</a>';
			$sx .= '&nbsp;' . $link . bsicone('edit') . $linka;

			/* Habilita Coleta para Journals */
			if (($dt['jnl_collection'] == 'JA') or ($dt['jnl_collection'] == 'JE')) {
				$url = PATH . 'bots/harvesting/getregister/' . $dt['id_jnl'];
				$link = '<a href="' . $url . '">';
				$linka = '</a>';
				$sx .= '&nbsp;' . $link . bsicone('harvesting') . $linka;
			}
		} else {
			$sx .= bsmessage(lang('brapci.oaipmh_not_defined'), 3);
		}

		$Socials = new \App\Models\Socials();
		$Cover = new \App\Models\Base\Cover();

		$Cover = new \App\Models\Base\Cover();
		$sx .= '<hr>';
		if ($Socials->getAccess("#ADM#CAT")) {
			$sx .= $Cover->cover_upload_bnt($jid);
			$sx .= '<br>';
		}

		$sx .= '<img src="' . $Cover->cover($jid) . '" class="img-fluid">';
		return $sx;
	}

	function dir_tmp($id)
	{
		$nr = strzero($id, 9);

		$dir = '.tmp';
		dircheck($dir);
		$dir .= '/' . substr(date("Y"), 0, 2);
		dircheck($dir);
		$dir .= '/' . substr(date("Y"), 2, 2);
		dircheck($dir);

		$dir .= '/' . substr($nr, 0, 3);
		dircheck($dir);
		$dir .= '/' . substr($nr, 3, 3);
		dircheck($dir);
		$dir .= '/' . substr($nr, 6, 3);
		dircheck($dir);
		$dir .= '/';
		return $dir;
	}

	function resume($idj = 0, $issue = 0)
	{
		$dt = $this->to_harvesting_group($idj, $issue);
		$sx = h(msg('brapci.oaipmh'), 4);
		$sx .= '<ul style="font-size: 0.7em;">';
		foreach ($dt as $id => $line) {
			$link = '<a href="' . PATH . '/journals/oai/' . $idj . '/' . $line['oai_status'] . '">';
			$linka = '</a>';

			$dsp[$line['oai_status']] = $line['total'];
			$sx .= '<li>' . $link . msg('brapci.oai_status_' . $line['oai_status']) . $linka . ' (' . $line['total'] . ')' . '</li>';
		}
		$sx .= '</ul>';
		return $sx;
	}

	function to_harvesting_group($idj, $issue, $st = 1)
	{
		$OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
		if ($idj > 0) {
			$dt = $OAI_ListIdentifiers
				->select("count(*) as total, oai_status")
				->where('oai_id_jnl', $idj)
				->groupBy("oai_status")
				->findAll();
		} else {
			$dt = $OAI_ListIdentifiers
				->select("count(*) as total, oai_status")
				->where('oai_issue', $issue)
				->groupBy("oai_status")
				->findAll();
		}
		return $dt;
	}


	function to_harvesting($idj, $issue, $st = 1)
	{
		$OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
		if ($idj > 0) {
			$dt = $OAI_ListIdentifiers->where('li_jnl', $idj)->where('li_s', $st)->findAll();
		} else {
			$dt = $OAI_ListIdentifiers->where('li_issue', $issue)->where('li_s', $st)->findAll();
		}
		return count($dt);
	}

	function links($id)
	{
		$sx = '[' . $id . ']';
		$sx = '<a href="' . PATH . COLLECTION . '/oai/' . $id . '/listidentifiers">' . bsicone('harvesting') . '</a>';
		return $sx;
	}

	function _call($url, $headers = array())
	{

		//$headers = array('Accept: application/json', 'Content-Type: application/json',);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($code == '200') {
			return $response;
		} else {
			echo h('ERRO CURL: ' . $code);
			exit;
		}
	}
}
