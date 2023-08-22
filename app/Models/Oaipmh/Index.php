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

	function index($d1, $d2)
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
			default:
				echo "OPS OAI $d1,$d2";
				exit;
		}

		return $sx;
	}

	function identify($d1, $d2)
	{
		$sx = '';
		$id = $d2;
		$Identify = new \App\Models\Oaipmh\Identify();
		$Soucers = new \App\Models\Base\Sources();
		$dt = $Soucers->find($d2);
		if ($dt == '') {
			return bsmessage('Harvesting Identify - erro no id', 3);
		} else {
			$sx .= $Identify->getIdentify($dt);
		}
		return $sx;
	}

	function api($d1,$d2)
		{
			$RSP = [];
			switch($d1)
				{
					case 'getIssue':
						$GetRecords = new \App\Models\Oaipmh\GetRecords();
						$RSP = $GetRecords->getrecord(0,$d2);
						//$RSP['status'] = $GetRecords->status;
						break;
					default:
						$RSP['status'] = '404';
						$RSP['messagem'] = 'Function not found - '.$d1.'-'.$d2;

				}
			return $RSP;
		}


	function getregister($d1,$d2)
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
		if ($Socials->getAccess("#ADM#CAT"))
			{
				$sx .= $Cover->cover_upload_bnt($jid);
				$sx .= '<br>';
			}

		$sx .= '<img src="'.$Cover->cover($jid).'" class="img-fluid">';
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

	function resume($idj,$issue)
		{
			$dt = $this->to_harvesting_group($idj,$issue);
			$dsp = array(0,0,0,0,0,0,0,0,0,0,0);
			foreach($dt as $id=>$line)
				{
					$dsp[$line['li_s']] = $line['total'];
				}
			$sx = '<table width="100%">';
			$sx .= '<tr>';
			for ($r=0;$r <= 9;$r++)
				{
					$link = '<a href="'.PATH. '/proceedings/oai/I'.$issue.'/status/?status='.$r.'">';
					$linka = '</a>';
					if ($dsp[$r] == 0)
						{
							$linka = '';
							$link = '';
						}
					$sx .= '<td width="10%">'. $link.$dsp[$r].$linka.'</td>';
				}
			$sx .= '</tr>';
			$sx .= '</table>';
			return $sx;
		}

	function to_harvesting_group($idj, $issue, $st = 1)
	{
		$OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
		if ($idj > 0) {
			$dt = $OAI_ListIdentifiers
					->select("count(*) as total, li_s")
					->where('li_jnl', $idj)
					->groupBy("li_s")
					->findAll();
		} else {
			$dt = $OAI_ListIdentifiers
					->select("count(*) as total, li_s")
					->where('li_issue', $issue)
					->groupBy("li_s")
					->findAll();
		}
		return $dt;
	}


	function to_harvesting($idj, $issue,$st=1)
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
