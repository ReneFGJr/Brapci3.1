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

	function index($d1,$d2)
		{
			echo "$d1==$d2";
		}

	function url($dt)
		{
			pre($dt);
		}

	function logo()
		{
			$sx = '';
			$sx .= '<span style="margin-right: 5px; font-size: 0.6em; border: 1px solid #000; padding: 2px 5px;">OAI-PMH</span>';
			return $sx;
		}

	function painel($dt)
		{
			$sx = h(lang('brapci.painel'),4);
			if ($dt['jnl_url_oai'] != '')
				{
					$sx .= $this->logo();
					$url = PATH.'bots/harvesting/identify/'.$dt['id_jnl'];
					$link = '<a href="#" onclick="newxy2(\''.$url.'\',600,600);">';
					$linka = '</a>';
					$sx .= $link.bsicone('upload').$linka;
 				} else {
					$sx .= bsmessage(lang('brapci.oaipmh_not_defined'),3);
				}
			return $sx;
		}

	function dir_tmp($id)
		{
			$nr = strzero($id, 9);

			$dir = '.tmp';
			dircheck($dir);
			$dir .= '/'.substr(date("Y"),0,2);
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


	function to_harvesting($idj,$issue)
		{
			$OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
			if ($idj > 0)
				{
					$dt = $OAI_ListIdentifiers->where('li_jnl',$idj)->where('li_s',1)->findAll();
				} else {
					$dt = $OAI_ListIdentifiers->where('li_issue', $issue)->where('li_s', 1)->findAll();
				}
			return count($dt);
		}

	function links($id)
		{
			$sx = '['.$id.']';
			$sx = '<a href="'.PATH.COLLECTION.'/oai/'.$id.'/listidentifiers">'.bsicone('harvesting').'</a>';
			return $sx;
		}

	function _call($url, $headers=array())
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
		if ($code == '200')
			{
				return $response;
			} else {
				echo h('ERRO CURL: '.$code);
				exit;
			}
		}


}
