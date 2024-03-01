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
			case 'problems':
				$sx .= $this->problems($type,$d3);
				break;
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
				break;
			case 'searchAjax':
				$SEARCH = new \App\Models\ElasticSearch\Search();
				$Elasticsearch = new \App\Models\ElasticSearch\Index();
				$_POST['offset'] = 9999999;
				$dt = $SEARCH->search(GET("query"));
				return $dt;
				break;
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
				$sx .= bsmessage("Command not found '".$d1."'");
				break;
		}
		return $sx;
	}

	function problems($d1,$d2)
		{
			$RDF = new \App\Models\Rdf\RDF();
			$Register = new \App\Models\ElasticSearch\Register();
			$dt = $Register->where('year < 1950')->findAll(5);
			$sx = 'Total '.count($dt).' problems with year';
			$sx .= '<ol>';
			foreach($dt as $id=>$line)
				{
					if ($line['ldl_title'] == '::Sem ´título::')
						{
							$ida = $line['article_id'];
							$RDF->exclude($ida);
							$Register->where('id',$line['id'])->delete();
							echo h("DELETADO");
						}
					pre($line,false);
					$url = $line['article_id'];
					$sx .= '<li>';
					$sx .= anchor('v/'.$line['article_id']) . ' - ' .$line['type'];
					$sx .= '</li>';
				}
			$sx .= '</ol>';
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
			if (isset($dt['error']))
				{
					$sx .= $dt['error'];
					return $sx;
				}

			switch($type)
				{
					case 'book':
						$sx .= $this->show_works_books($dt);
						break;
					case 'benancib':
						$sx .= $this->show_works_benancib($dt);
						break;
					case 'journal':
						$sx .= $this->show_works_article($dt);
						break;
					default:
						$sx .= $this->show_works_benancib($dt);
						break;
				}

				return $sx;
		}

	function show_works_books($dt)
	{
		$cover = '';
		$year = '';
		$bg = 'btn-primary';
		$RDF = new \App\Models\Rdf\RDF();
		$COVER = new \App\Models\Base\Cover();
		$sx = '';
		if ($dt['total']==0) {
			$sx .= bs(bsc(bsmessage(lang('brapci.nothing_locate_found'),3)));
			return $sx;
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
						$cover = '<img src="' . URL . '/' . $COVER->book($line['id']) . '" class="img-fluid img-thumb" >';
						$cover = '<a href="' . PATH . '/books/v/' . $line['id'] . '">' . $cover . '</a>';
						break;
					case 'BookChapter':
						$bg = 'bg-brapcilivros';
						$cover = '<img src="' . URL . '/' . $RDF->c($line['id'],'cover') . '" class="img-fluid" >';
						$cover = '<a href="' . PATH . '/books/v/' . $line['id'] . '">' . $cover . '</a>';
						break;
				}

			$TAG = '<span class="btn small pt-0 pb-0 '.$bg.'">'.$line['type']. '</span><br>';
			$score = '<br><span style="font-size: 0.6em;">(Score: ' . number_format($line['score'], 3, '.', ',') . ')</span>';
			//$score .= '<br>'.$line['id'];
			$tb = '<table width="100%">';
			$tb .= '<tr>';
			$tb .= '<td width="20%" class="p-2">'.$cover.'</td>';
			$tb .= '<td width="80%" valign="top" class="p-2">' . $TAG . $RDF->c($line['id']) . $score. '</td>';
			$tb .= '</tr>';
			$tb .= '</table>';
			$sx .= bsc($tb,6);
		}
		$sx = bs($sx);
		return $sx;
	}

	function show_works_article($dt)
	{
		$cover = '';
		$year = '';

		$RDF = new \App\Models\Rdf\RDF();
		$MARK = new \App\Models\Base\Mark();
		$Cover = new \App\Models\Base\Cover();
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
			$sz = 11;
			if (isset($line['jnl']))
				{
					$cover = '<img src="'.$Cover->cover($line['jnl']). '" class="img-fluid mb-3 text-end" >';
					$sz--;
					$sx .= bsc($cover,1);
				}

			if (isset($line['jnl'])) {
				$year = $line['year'];
			}
			$txt = '';
			$txt .= $MARK->mark($line['id']);
			$txt .= '<a href="'.PATH.'/v/'.$line['id'].'" class="href">'.$RDF->c($line['id']).'</a>'.cr();
			$txt .= ' <sup>(Score: ' . number_format($line['score'], 3, '.', ','). ')</sup>';
			$sx .= bsc($txt , $sz, 'mb-3');
			$sx .= bsc($year,1,'text-end');
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
			$RDF = new \App\Models\RDF2\RDF();
			$sx = '';
			switch($d1)
				{
					case 'year_without':
						$sx .= h(msg('brapci.year_without'),3);
						$API = new \App\Models\ElasticSearch\Register();
						$sx .= $API->whithout_year();
					break;
					case 'erros':
						$API = new \App\Models\ElasticSearch\Register();
						switch($d2)
							{
								case 'pdf':
									$sx .= h('PDF Erros',2);
									$dt = $API->where('PDF',0)->findAll();
									$sx .= h('Total: ' . count($dt), 6);
									foreach($dt as $id=>$line)
										{
											$link = '<a href="'.
												'https://brapci.inf.br/#/v/'. $line['ID'].
												'" target="_blank">';
											$linka = '</a>';
											$name = $line['CLASS'].': '.$line['ID'];
											$name .= ' - '.$line['LEGEND'];
											$sx .= '<li>'.$link.$name.$linka.'</li>'.cr();
										}
								break;
							}
					break;
				}
			$sx = bs(bsc($sx,12));
			return $sx;
		}

		function export($type)
			{
				$Register = new \App\Models\ElasticSearch\Register();
				$user = get('user');
				$row = explode(',',get('row'));
				$dd = [];
				$dd['type'] = $type;
				$dd['user'] = $user;
				$dd['item'] = $row;

				$cp = '*';
				$Register->select($cp);
				/*
				foreach($row as $id=>$line)
					{
						$Register->Orwhere('ID',$line);
					}
				*/
				$dt = $Register->findALl(10);

				$sx = '';
				$sep = ';';
				foreach($dt as $i=>$line)
					{
						$sx .= $line['ID'] . $sep;
						$sx .= $line['CLASS'].$sep;
						$sx .= $line['YEAR'] . $sep;
						$sx .= $line['TITLE'] . $sep;
						$sx .= $line['AUTHORS'] . $sep;
						$sx .= $line['SESSION'] . $sep;
						$sx .= chr(13);
					}

				header("Content-type: text/csv");
				echo $sx;
				exit;
			}


}