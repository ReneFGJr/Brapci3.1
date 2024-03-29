<?php

namespace App\Models\Oaipmh;

use CodeIgniter\Model;

class ListIdentifiers extends Model
{
	protected $DBGroup              = 'oai';
	protected $table                = 'oai_listidentify';
	protected $primaryKey           = 'id_oai';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_oai', 'oai_update', 'oai_status',
		'oai_id_jnl', 'oai_identifier', 'oai_datestamp',
		'oai_setSpec', 'oai_deleted', 'oai_rdf'
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

	function check()
	{
		$dt['oai_status'] = 9;
		$this->set($dt)->where('oai_deleted', 1)->update();
	}

	function painel_mini($s = 0, $jnl = 0)
	{
		$cp = 'oai_status';
		$this->select($cp . ', count(*) as total');
		if ($jnl > 0) {
			$this->where('oai_id_jnl', $id);
		}
		if ($s > 0) {
			$this->where('oai_status', $s);
		}
		$dt = $this
			->groupBy($cp)
			->orderBy($cp)
			->findAll();

		pre($dt);
	}

	function painel($id)
	{
		$this->check();
		$cp = 'oai_status';
		$dt = $this
			->select($cp . ', count(*) as total')
			->where('oai_id_jnl', $id)
			->groupBy($cp)
			->orderBy($cp)
			->findAll();
		$sx = h('OAI-PMH', 6);
		$sx .= '<table class="full small">';
		foreach ($dt as $idx => $line) {
			$link = '<a href="' . PATH . '/journals/oai/' . $id . '/' . $line['oai_status'] . '">';
			$linka = '</a>';
			$sx .= '<tr  class="border-top border-secondary p-2">';
			$sx .= '<td width="65%">';
			$sx .= $link;
			$sx .= lang('brapci.oai_status_' . $line['oai_status']);
			$sx .= $linka;
			$sx .= '</td>';
			$sx .= '<td class="text-center">';
			$sx .= $line['total'];
			$sx .= '</td>';
			$sx .= '</tr>';
		}
		$sx .= '</table>';
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

	function summary($idj, $issue)
	{
		$DT = [];
		$cp = 'li_s as status';
		$this->select($cp . ', count(*) as total');
		$this->where('li_jnl', $idj);
		$this->where('li_issue', $issue);
		$this->groupBy('li_s');
		$dt = $this->findAll();

		foreach ($dt as $id => $lst) {
			$DT['status_' . $lst['status']] = $lst['total'];
		}
		return $DT;
	}

	function resume($idj)
	{
		$rs = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
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
		$show = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
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
