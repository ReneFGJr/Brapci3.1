<?php

namespace App\Models\ElasticSearch;

use CodeIgniter\Model;

class Index extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'indices';
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
		$API = new \App\Models\ElasticSearch\API();
		$sx = '';
		$sx .= breadcrumbs();
		switch ($d1) {
			case 'search':
				$SEARCH = new \App\Models\ElasticSearch\Search();
				$dt = $SEARCH->search(GET("query"));
				pre($dt);
				$sx .= 'Busca';
				break;
			case 'formTest':
				$sx .= $API->formTest();
				break;
			case 'status':
				$dt = $API->status();
				$sx .= '<table class="table table-sm table-striped">';
				$sx .= '<tr><th width="25%" class="text-end small">' . lang('elasticsearch.parameter') . '</th><th class="small">' . lang('elasticsearch.value') . '</th></tr>';
				foreach ($dt as $id => $value) {
					$sx .= '<tr><td class="text-end"><b>' . $id . '</b></td><td>' . $value . '</td></tr>';
				}
				$sx .= '</table>';
				break;
			case 'settings':
				$dt = $API->settings();
				$sx .= '<table class="table table-sm table-striped">';
				$sx .= '<tr><th width="25%" class="text-end small">' . lang('elasticsearch.parameter') . '</th><th class="small">' . lang('elasticsearch.value') . '</th></tr>';

				foreach ($dt as $id => $value) {
					if (is_array($value)) {
						$value = json_encode($value);
					}
					$sx .= '<tr><td class="text-end"><b>' . $id . '</b></td><td>' . $value . '</td></tr>';
				}
				$sx .= '</table>';
				break;
			default:
				$sx .= $this->menu();
				break;
		}
		return bs(bsc($sx, 12));
	}

	function menu()
	{
		$sx = '';
		$s = array();
		$s['elasticsearch.search'] = 'res/elasctic/search';
		$s['elasticsearch.index'] = 'res/elasctic/index';
		$s['elasticsearch.status'] = 'res/elasctic/status';
		$s['elasticsearch.settings'] = 'res/elasctic/settings';
		$s['elasticsearch.formTest'] = 'res/elasctic/formTest';
		$sx .= '<ul>';
		foreach ($s as $service => $url) {
			if ($url == '') {
				$sx .= '<hr>';
			} else {
				$sx .= '<li><a href="' . base_url(PATH . $url) . '">' . $service . '</a></li>';
			}
		}
		$sx .= '</ul>';
		return $sx;
	}
}