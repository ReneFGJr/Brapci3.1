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

	function index($d1 = '', $type = '', $d3 = '')
	{
		$RDF = new \App\Models\Rdf\RDF();
		$API = new \App\Models\ElasticSearch\API();
		$sx = '';
		$bread = ['Admin' => PATH . '/admin'];
		$sx .= breadcrumbs($bread);
		switch ($d1) {
			case 'update_index':
				$url = PATH . '/admin/elastic/update/';
				$url2 = PATH . '/admin';
				$sx .= 'Confirma exportação de dados?';
				$sx .= '<hr>';
				$sx .= 'Essa operação pode demorar algum tempo';
				$sx .= '<br/>';
				$sx .= form_confirm($url, $url2);
				$Register = new \App\Models\ElasticSearch\Register();

				set_time_limit(600);
				if (get("confirm" != '')) {
					$Register->update_index();
				}
				return $sx;
			case 'searchAjax':
				$SEARCH = new \App\Models\ElasticSearch\Search();
				$Elasticsearch = new \App\Models\ElasticSearch\Index();
				$_POST['offset'] = 9999999;
				$dt = $SEARCH->search(GET("query"));
				return $dt;
			case 'search':
				$SEARCH = new \App\Models\ElasticSearch\Search();
				$Elasticsearch = new \App\Models\ElasticSearch\Index();
				$_POST['offset'] = 50;
				$dt = $SEARCH->search(GET("query"), $type);
				$sx = $Elasticsearch->show_works($dt, $type);
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
		return $sx;
	}

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

			/*********************** Mosta páginas */
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

	function show_works($dt,$type)
		{
			$sx = '';
			switch($type)
				{
					case 'book':
						$sx = $this->show_works_books($dt);
						break;
					case 'benancib':
						$sx = $this->show_works_benancib($dt);
						break;
					default:
						$sx = $this->show_works_benancib($dt);
						break;
				}

				return $sx;
		}

	function show_works_books($dt)
	{
		$RDF = new \App\Models\Rdf\RDF();
		$COVER = new \App\Models\Base\Cover();
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

			switch($line['type'])
				{
					case 'Book':
						$bg = 'btn-primary';
						$cover = '<img src="' . URL . '/' . $COVER->book($line['id']) . '" class="img-fluid" >';
						$cover = '<a href="' . PATH . '/books/v/' . $line['id'] . '">' . $cover . '</a>';
						break;
					case 'BookChapter':
						$bg = 'bg-brapcilivros';
						$cover = '<img src="' . URL . '/' . $COVER->book($line['id']) . '" class="img-fluid" >';
						$cover = '<a href="' . PATH . '/books/v/' . $line['id'] . '">' . $cover . '</a>';
						break;
				}

			$TAG = '<span class="btn small pt-0 pb-0 '.$bg.'">'.$line['type']. '</span><br>';
			$score = '<br><span style="font-size: 0.6em;">(Score: ' . number_format($line['score'], 3, '.', ',') . ')</span>';
			//$score .= '<br>'.$line['id'];
			$tb = '<table width="100%">';
			$tb .= '<tr>';
			$tb .= '<td width="20%">'.$cover.'</td>';
			$tb .= '<td width="80%" valign="top">' . $TAG . $RDF->c($line['id']) . $score. '</td>';
			$tb .= '</tr>';
			$tb .= '</table>';
			$sx .= bsc($tb,6);
		}
		$sx = bs($sx);
		return $sx;
	}

	function show_works_benancib($dt)
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

	function show_error($d1,$d2,$d3)
		{
			$RDF = new \App\Models\Rdf\RDF();
			$sx = '';
			switch($d1)
				{
					case 'erros':
						$API = new \App\Models\ElasticSearch\Register();
						switch($d2)
							{
								case 'pdf':
									$sx .= h('PDF Erros',2);
									$dt = $API->select('article_id')->where('pdf',0)->findAll();
									$sx .= h('Total: ' . count($dt), 6);
									foreach($dt as $id=>$ida)
										{
											$sx .= '<li>'.$RDF->c($ida['article_id']).'</li>'.cr();
										}
								break;
							}
					break;
				}
			$sx = bs(bsc($sx,12));
			return $sx;
		}


}