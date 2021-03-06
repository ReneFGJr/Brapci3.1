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

	function paginations($dt)
	{
		$sx = '';
		$sta = $dt['start'];
		$off = $dt['offset'];
		$total = $dt['total'];
		if ($total > 0) {
			$sx = '<nav aria-label="Page navigation search">
				<ul class="pagination">' . cr();
			$sx .= '<li class="page-item me-5"><a class="page-link" href="#">Total: ' . $total . '</a></li>' . cr();
			if ($sta > 1) {
				$sx .= '<li class="page-item">';
				$sx .= '<a class="page-link" href="#" aria-label="Previous">';
				$sx .= '<span aria-hidden="true">&laquo;</span>';
				$sx .= '<span class="sr-only">Previous</span>';
				$sx .= '</li>' . cr();
			}
			$pags = round($total / $off) + 1;

			$limit = 5;
			/************************** Miolo */
			if ($sta < 3) {
				$stai = 1;
			} else {
				$stai = $sta - 2;
			}

			/*********************** Mosta p??ginas */
			for ($r = $stai; $r < $pags; $r++) {

				if ($sta == $r) {
					$sx .= '<li class="page-item active">';
					$sx .= '<a class="page-link" href="#">' . $r . '</a>';
					$sx .= '</li>' . cr();
				} else {
					$sx .= '<li class="page-item">';
					$sx .= '<a class="page-link" href="#">' . $r . '</a>';
					$sx .= '</li>' . cr();
				}
				$limit--;
				if ($limit <= 0) {
					break;
				}
			}
			if ($limit <= 0) {
				/******************** Pagina final */
				$sx .= '<li class="page-item">';
				$sx .= '<a class="page-link" href="#">...' . $pags . '</a>';
				$sx .= '</li>' . cr();

				$sx .= '<li class="page-item">';
				$sx .= '<a class="page-link" href="#" aria-label="Next">';
				$sx .= '<span aria-hidden="true">&raquo;</span>';
				$sx .= '<span class="sr-only">Next</span>';
				$sx .= '</a></li>';
			}
			$sx .= '</ul></nav>';
		}
		return $sx;
	}

	function show_works($dt)
	{
		$RDF = new \App\Models\Rdf\RDF();
		$sx = '';
		if (!isset($dt['total'])) {
			return '';
		}

		$sa = 'Total ' . $dt['total'];
		$sa .= ', mostrando ' . $dt['start'] . '/' . $dt['offset'];
		$sa = $this->paginations($dt);
		$sx = bsc($sa, 12);

		for ($r = 0; $r < count($dt['works']); $r++) {
			$line = $dt['works'][$r];
			$sx .= bsc($RDF->c($line['id']) . ' <sup>(Score: ' . number_format($line['score'], 3, '.', ',') . ')</sup>', 6, 'mb-3');
		}
		$sx = bs($sx);
		return $sx;
	}

	function index($d1 = '', $d2 = '', $d3 = '')
	{
		$RDF = new \App\Models\Rdf\RDF();
		$API = new \App\Models\ElasticSearch\API();
		$sx = '';
		$sx .= breadcrumbs();
		switch ($d1) {
			case 'search':
				$SEARCH = new \App\Models\ElasticSearch\Search();
				$Elasticsearch = new \App\Models\ElasticSearch\Index();
				$_POST['offset'] = 50;
				$dt = $SEARCH->search(GET("query"));
				$sx = $Elasticsearch->show_works($dt);

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