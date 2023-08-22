<?php

namespace App\Models\Oaipmh;

use CodeIgniter\Model;

class ListIdentifiers extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'source_listidentifier';
	protected $primaryKey           = 'id_li';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_li', 'li_identifier', 'li_setSpec',
		'li_status', 'li_jnl', 'li_update',
		'li_s', 'li_u', 'li_datestamp',
		'li_issue', 'is_works', 'is_oai_token',
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

	function row($l)
	{
		$sx = '';
		$sx .= $l['li_identifier'];
		$sx .= ' - ' . $l['li_setSpec'];
		$st = $l['li_s'];
		switch ($st) {
			case '0':
				$st = 'new';
				break;
			case '1':
				$st = 'started process';
				break;
			case '2':
				$st = 'process';
				break;
			case '3':
				$st = '???????';
				break;
			case '9':
				$st = 'procceed';
				break;
		}
		$sx .= ' ['.$st.' - '. $l['li_s'].']';
		return $sx;
	}

	function harvesting_issue($dt)
	{
		$Issue = new \App\Models\Base\Issues();
		$sx = '';

		$OAI = new \App\Models\Oaipmh\Index();

		$url = trim($dt['is_url_oai']);
		$url .= '?';
		$url .= 'verb=ListIdentifiers';

		if (strlen(trim($dt['is_oai_token'])) > 0) {
			$url .= '&';
			$url .= 'resumptionToken=' . $dt['is_oai_token'];
		} else {
			$url .= '&';
			$url .= 'metadataPrefix=oai_dc';
		}

		$xml = $OAI->_call($url);
		$xml = (array)simplexml_load_string($xml);

		/********************************************************** REG */
		if (!isset($xml['ListIdentifiers'])) {
			pre($xml);
			$sx .= h($url, 4);
			$sx .= '<div class="alert alert-danger">';
			$sx .= '<h4>Erro</h4>';
			$sx .= '<p>Não foi possível obter a lista de identificadores.</p>';
			$sx .= '</div>';
			$dd['is_oai_token'] = '';
			$Issue->set($dd)->where('id_is', $dt['id_is'])->update();
			$sx = bs(bsc($sx, 12));
			return ($sx);
		}
		$reg = (array)$xml['ListIdentifiers'];

		$token = '';
		if (isset($reg['resumptionToken']))
			{
				$token = $reg['resumptionToken'];
			}


		$dd['is_oai_token'] = $token;
		if ($token == '') {
			$dd['is_oai_update'] = date("Y-m-d H:i:s");
		} else {
		}

		$Issue->set($dd)->where('id_is', $dt['id_is'])->update();

		$reg = (array)$reg['header'];

		$sx .= h('Harvesting', 2);
		$sx .= h(anchor($url), 6);

		for ($r = 0; $r < count($reg); $r++) {
			$rg = (array)$reg[$r];
			$dd = array();
			$dd['li_identifier'] = $rg['identifier'];
			$dd['li_jnl'] = $dt['is_source'];
			$dd['li_datestamp'] = $rg['datestamp'];
			$dd['li_setSpec'] = $rg['setSpec'];
			$dd['li_issue'] = $dt['id_is'];
			$sx .= $this->register($dd);
		}
		$sx .= '<hr>Total ' . $this->update_works($dt['id_is']) . ' ' . lang('brapci.works');
		$sx = bs(bsc($sx, 12));

		if ($token != '') {
			$dt = $Issue->find($dt['id_is']);
			$sx .= '<hr>' . $token . '<hr>';
			$sx .= $this->harvesting_issue($dt);
		}
		return $sx;
	}

	function update_status($idi, $status)
	{
		$da['li_s'] = $status;
		$da['li_u'] = date("Y-m-d H:i:s");
		$this->set($da)->where('id_li', $idi)->update();
		return "";
	}

	function update_works($idi)
	{
		$Issues = new \App\Models\Base\Issues();
		$dt = $this->where('li_issue', $idi)->findAll();
		$da['is_works'] = count($dt);
		$Issues->set($da)->where('id_is', $idi)->update();
		return count($dt);
	}

	function harvesting($dt)
	{
		$sx = '';

		$OAI = new \App\Models\Oaipmh\Index();
		$url = trim($dt['jnl_url_oai']);
		$method = $dt['jnl_oai_method'];

		$url .= 'https://ebbc.inf.br/ojs/index.php/ebbc/oai';
		$url .= '?';
		$url .= 'verb=ListIdentifiers';
		$url .= '&';
		$url .= 'metadataPrefix=oai_dc';

		$xml = $OAI->_call($url);
		$xml = (array)simplexml_load_string($xml);

		/********************************************************** REG */
		$reg = (array)$xml['ListIdentifiers'];
		$reg = (array)$reg['header'];

		$sx .= h('Harvesting', 2);
		$sx .= h(anchor($url), 6);

		for ($r = 0; $r < count($reg); $r++) {
			$rg = (array)$reg[$r];
			$dd = array();
			$dd['li_identifier'] = $rg['identifier'];
			$dd['li_jnl'] = $dt['id_jnl'];
			$dd['li_datestamp'] = $rg['datestamp'];
			$dd['li_setSpec'] = $rg['setSpec'];
			$dd['li_issue'] = 0;
			$sx .= $this->register($dd);
		}
		return $sx;
	}

	function summary($idj,$issue)
		{
			$DT = [];
			$cp = 'li_s as status';
			$this->select($cp.', count(*) as total');
			$this->where('li_jnl',$idj);
			$this->where('li_issue', $issue);
			$this->groupBy('li_s');
			$dt = $this->findAll();

			foreach($dt as $id=>$lst)
				{
					$DT[$lst['status']] = $lst['total'];
				}
			return $DT;
		}

	function resume($idj)
	{
		$rs = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
		$sx = '';
		$dt =
			$this
			->select('li_s, count(*) as total')
			->where('li_jnl', $idj)
			->groupBy('li_s')
			->findAll();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$st = $line['li_s'];
			$rs[$st] = $line['total'];
		}
		$show = array(1, 2, 3, 4, 5, 9);
		foreach ($show as $n => $i) {
			$msg = '<span class="small">' . lang('brapci.oai_status_' . $n) . '</span>';
			$msg .= h($rs[$n]);
			$sx .= bsc($msg, 2, 'text-center');
		}
		$sx = bs($sx);
		return $sx;
	}

	function register($dt)
	{
		$sx = '';
		$dd = $this
			->where('li_identifier', $dt['li_identifier'])
			->where('li_jnl', $dt['li_jnl'])
			->findAll();
		if (count($dd) == 0) {
			$dt['li_status'] = 'active';
			$dt['li_update'] = '0';
			$dt['li_s'] = 1;
			$dt['li_u'] = date("Y-m-d H:i:s");
			$this->set($dt)->insert();
			$sx .= $dt['li_identifier'] . ' <span class="text-success">Inserido</span>';
			$sx .= '<br>';
		} else {
			$sx .= '<tt>. </tt>';
		}
		return $sx;

		//		'id_li', 'li_identifier', 'li_setSpec',
		//'li_status', 'li_jnl', 'li_update',
		//'li_s', 'li_u'
	}
}
