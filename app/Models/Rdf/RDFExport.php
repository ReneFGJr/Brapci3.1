<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFExport extends Model
{
	protected $DBGroup              = 'rdf';
	protected $table                = PREFIX . 'rdfexports';
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

	function exportNail($id)
	{
		$this->RDF = new \App\Models\RDF\RDF();
		$Covers = new \App\Models\Book\Covers();
		$dt = $this->RDF->le($id);
		$class = $dt['concept']['c_class'];
		switch ($class) {
			case 'Manifestation':
				$isbn = substr($dt['concept']['n_name'], 5, 13);
				$cover_nail = $Covers->get_cover($isbn);
				return $cover_nail;
				break;
		}
	}

	function recover_authors($dt)
	{
		/************************************************** Authors */
		$authors = $this->RDF->recovery($dt['data'], 'hasAuthor');
		$auths = '';
		$auth = array();
		for ($r = 0; $r < count($authors); $r++) {
			$idr = $authors[$r][1];
			if (strlen($auths) > 0) {
				$auths .= '; ';
			}

			$auth_name = strip_tags($this->RDF->c($idr));
			$auth_id = $idr;
			$auths .= $auth_name;
			array_push($auth, array('name' => $auth_name, 'id' => $auth_id));
		}
		return $auth;
	}

	function recover_title($dt)
	{
		$title = $this->RDF->recovery($dt['data'], 'hasTitle');
		if (isset($title[0][2])) {
			$title = nbr_title($title[0][2]);
		} else {
			$title = '## FALHA NO TÍTULO ##';
		}
		return $title;
	}

	function recover_subject($dt)
	{
		$Subject = $this->RDF->recovery($dt['data'], 'hasSubject');
		return $Subject;
	}
	function recover_issue($dt, $id, $tp)
	{
		$issue1 = $this->RDF->recovery($dt['data'], 'hasIssueOf');
		$issue2 = $this->RDF->recovery($dt['data'], 'hasIssueProceedingOf');
		$issue = array_merge($issue1, $issue2);

		/* EMPTY */
		if (!isset($issue[0])) {
			//pre($dt);
			return array('NoN', 0);
		}

		$issues = array();
		for ($r = 0; $r < count($issue); $r++) {
			echo ".";
			$line = $issue[$r];
			$id1 = $line[0];
			$id2 = $line[1];
			if ($id1 == $id) {
				$idx = $id2;
			} else {
				$idx = $id1;
			}
			$issues = array($this->RDF->c($idx), $idx);
		}
		if (count($issues) == 0) {
			echo "OPS ISSUE";
			pre($issue);
		}

		return $issues;
	}

	/****************************************************************** ARTICLE / PROCEEDING */
	function export_article($dt, $id, $tp = 'A')
	{
		$RDF = new \App\Models\Rdf\RDF();
		$ABNT = new \App\Models\Metadata\Abnt();
		$publisher = '';

		if (count($dt['data']) == 0) {
			return "OPS " . $id . '<br>';
		}

		$elastic = array();
		$elastic['article_id'] = $id;
		$elastic['id_jnl'] = '';
		$elastic['title'] = '';
		$elastic['authors'] = '';
		$elastic['abstract'] = '';
		$elastic['subject'] = '';
		$elastic['year'] = '';
		$elascti['journal'] = '';
		$elascti['pagination_ini'] = '';
		$elascti['pagination_end'] = '';

		$tela = 'Export Article';
		$dta['id'] = $id;
		/*************************************************** Authors */
		$auths = $this->recover_authors($dt);
		$auths_text = '';
		for ($r = 0; $r < count($auths); $r++) {
			if ($auths_text != '') {
				$auths_text .= '; ';
			}
			$auths_text .= $auths[$r]['name'] . ' ';
		}
		$dta['author'] = $auths;
		$elastic['authors'] = strtolower(ascii($auths_text));

		/***************************************************** Title */
		$title = $this->recover_title($dt);
		$title = $this->RDF->string($title);
		$dta['title'] = $title;
		$elastic['title'] = strtolower(ascii(($title)));

		/***************************************************** Issue */
		$ISSUE = new \App\Models\Base\Issues();

		$issue1 = $RDF->extract($dt, 'hasIssueProceedingOf');
		$issue2 = $RDF->extract($dt, 'hasIssueOf');
		$dti = array();

		if (isset($issue1[0]) or isset($issue2[0])) {
			if (isset($issue1[0])) {
				$dti = $ISSUE->where('is_source_issue', $issue1[0])->findAll();
			} else {
				if (isset($issue2[0])) {
					$dti = $ISSUE->where('is_source_issue', $issue2[0])->findAll();
				}
			}


			if (count($dti) > 0) {
				$year = $dti[0]['is_year'];
				$source = $dti[0]['is_source'];
				$elastic['id_jnl'] = $source;
			} else {
				if (isset($issue1[0])) {
					$issue1 = $RDF->le($issue1[0]);
				} else {
					$issue1 = $RDF->le($issue2[0]);
				}
				$year = sonumero($issue1['concept']['n_name']);
				$year = round(substr($year, strlen($year) - 4, 4));
			}
			if (($year < 1960) or ($year >= (date("Y") + 1))) {
				$year = '';
			}
		}
		$elastic['year'] = $year;

		/******************** YEAR, PLACE, SOURCE */
		$issue = $this->recover_issue($dt, $id, $tp);
		$dta['issueRDF'] = $issue[1];
		if ($dta['issueRDF'] > 0) {
			$dti = $this->RDF->le($dta['issueRDF']);
			for ($r = 0; $r < count($dti['data']); $r++) {
				$line = $dti['data'][$r];
				switch ($line['c_class']) {
					case 'hasDateTime':
						$dta['year'] = $line['n_name2'];
						$elastic['year'] = $dta['year'];
						break;
					case 'hasPlace':
						$dta['place'] = $line['n_name2'];
						break;
					case 'prefLabel':
						$dta['Source'] = $line['n_name'];
						break;
				}
			}
		}
		$issue = $this->RDF->string($issue[0], 1);

		/************************************************ Proceeding */
		$dta['journal'] = $issue;
		if ($tp == 'P') {
			$issue = '<b>Anais...</b> ' . $issue;
		}
		$dta['issue'] = $issue;

		/**************************************************** PAGE */
		$abs = $this->RDF->recovery($dt['data'], 'hasAbstract');
		$abs = $this->RDF->string_array($abs, 1);
		$abs = strip_tags($abs);
		$abs = troca($abs, chr(13), ' ');
		$abs = troca($abs, chr(10), ' ');
		$abs = trim($abs);
		$dta['abstract'] = $abs;
		$elastic['abstract'] = strtolower(ascii($abs));
		/************************************************ SUBJECT */
		$subject = array();
		$subj = '';
		for ($r = 0; $r < count($dt['data']); $r++) {
			$line = $dt['data'][$r];
			if ($line['c_class'] == 'hasSubject') {
				$s_name = $line['n_name2'];
				$s_lange = $line['n_lang2'];
				$s_id = $line['d_r2'];
				array_push($subject, array('term' => $s_name, 'lang' => $s_lange, 'ID' => $s_id));
				$subj .= strtolower(ascii($s_name)) . ' ';
			}
		}
		$dta['subject'] = $subject;
		$elastic['subject'] = $subj;

		/**************************************************** PAGE */
		$pagf =  $this->RDF->recovery($dt['data'], 'hasPageEnd');
		$pagi =  $this->RDF->recovery($dt['data'], 'hasPageStart');
		$pagf = $this->RDF->string($pagf, 1);
		$pagi = $this->RDF->string($pagi, 1);
		$dta['pagi'] = $pagi;
		$dta['pagf'] = $pagf;

		if (strlen($pagf . $pagi) > 0) {
			$issue = substr($issue, 0, strlen($issue) - 5) .
				'p. $p, ' .
				substr($issue, strlen($issue) - 5, 5);
			$p = trim($pagi);
			$elascti['pagination_ini'] = $p;
			if (strlen($pagf) > 0) {
				$elascti['pagination_end'] = $pagf;
				if (strlen($p) > 0) {
					$p .= '-';
				}
				$p .= $pagf;
			}
			$issue = troca($issue, '$p', $p);
		}

		/************************************************ Section */
		$section = array();
		for ($r = 0; $r < count($dt['data']); $r++) {
			$line = $dt['data'][$r];

			if ($line['c_class'] == 'hasSectionOf') {
				$s_name = $line['n_name2'];
				$s_lange = $line['n_lang2'];
				$s_id = $line['d_r2'];
				array_push($section, array('section' => $s_name, 'ID' => $s_id));
			}
		}
		$dta['section'] = $section;

		/****************************************************** MOUNT */
		$publisher = '';

		/* Formata para Artigo ABNT */
		if ($tp == 'A') {
			$issue = '<b>' .
				substr($issue, 0, strpos($issue, ',')) .
				'</b>' .
				substr($issue, strpos($issue, ','), strlen($issue));
		}
		$name = strip_tags($auths_text . '. ');
		$name .= '<a href="' . (PATH . '$COLLECTION/v/' . $id) . '" class="article" target="_blanl_' . $id . '">' . $title . '</a>';
		$name .= '. $b$' . $publisher . '$/b$' . $issue;
		$name = troca($name, '$b$', '<b>');
		$name = troca($name, '$/b$', '</b>');
		$this->saveRDF($id, $name, 'name.nm');

		/******************************** Section */
		$section = json_encode($dta['section']);
		$this->saveRDF($id, $section, 'section.json');

		/******************************** Authors */
		$journal = json_encode($dta['journal']);
		$this->saveRDF($id, $journal, 'journal.name');

		/******************************** Authors */
		$autores = json_encode($dta['author']);
		$this->saveRDF($id, $autores, 'authors.json');

		/******************************** Subject */
		$this->saveRDF($id, json_encode($subject), 'keywords.json');

		/********************************* YEAR */
		$year = sonumero($issue);
		$year = round(substr($year, strlen($year) - 4, 4));
		if (($year > 1950) and ($year <= (date("Y") + 1))) {
			$this->saveRDF($id, $year, 'year.nm');
		}
		$this->saveRDF($id, $dt['concept']['c_class'], 'class.nm');

		$this->saveRDF($id, json_encode($dta), 'name.json');

		$elastic_json = json_encode($elastic);
		$this->saveRDF($id, $elastic_json, 'article.json');
		$class = trim($dt['concept']['c_class']);

		/**************************************************/
		$td = '<td>';
		$tdx = '</td>';

		/**************************************** ID */
		$ln = $td . $id . $tdx;

		/************************************* TITLE */
		$ln .= $td . $title . $tdx;

		/*********************************** AUTHORS */
		$ln .= $td . $auths_text . $tdx;

		/********************************** JOURNALS */
		$ln .= $td . $dta['journal'] . $tdx;

		/************************************** YEAR */
		$ln .= $td . $year .  $tdx;

		/************************************** SECTIONS */
		$sect = '';
		for ($r = 0; $r < count($dta['section']); $r++) {
			$k = $dta['section'][$r];
			$sect = $k['section'];
		}
		$ln .= $td .  $sect . $tdx;

		/*********************************** SUBJECT */
		$subj = array('pt' => '', 'en' => '', 'es' => '');
		for ($r = 0; $r < count($subject); $r++) {
			$k = $subject[$r];
			$key = trim($k['term']);
			$lang = $k['lang'];
			if ($key != '') {
				switch ($lang) {
					case 'pt-BR':
						$subj['pt'] .= ';' . $key;
						break;

					case 'es-ES':
						$subj['es'] .= ';' . $key;
						break;

					case 'en':
						$subj['en'] .= ';' . $key;
						break;
				}
			}
		}
		$ln .= $td . $subj['pt'] . $tdx;
		$ln .= $td . $subj['en'] . $tdx;
		$ln .= $td . $subj['es'] . $tdx;

		$ln .= $td . $pagi . $tdx;
		$ln .= $td . $pagf . $tdx;

		$ln = troca($ln,'>;','>');
		$ln = troca($ln,'"','');
		echo '<table border=1><tr>' . $ln . '</tr></table>';

		exit;
		$elasticRegister  = new \App\Models\ElasticSearch\Register();
		$elasticRegister->register($id, $class);
		return '';
	}

	function export_person($dt, $id)
	{
		$sx = '';
		$name = $dt['concept']['n_name'];
		$name = nbr_author($name, 1);
		$name = '<a href="' . (PATH . '$COLLECTION' . '/v/' . $id) . '" class="author">' . $name . '</a>';
		$this->saveRDF($id, $name, 'name.nm');
		return $sx;
	}

	function export_journal($dt, $id)
	{
		$sx = 'JOURNAL';
		$name = $dt['concept']['n_name'];
		$name = nbr_author($name, 7);
		$name = '<a href="' . (PATH . '$COLLECTION' . '/v/' . $id) . '" class="author">' . $name . '</a>';
		$this->saveRDF($id, $name, 'name.nm');
		return $sx;
	}

	function export_issueproceedings($dt, $id)
	{
		$name = $dt['concept']['n_name'];
		$year = sonumero($name);
		$year = substr($year, strlen($year) - 4, 4);

		$this->saveRDF($id, $name, 'name.nm');
		$this->saveRDF($id, $year, 'year.nm');
		return "";
	}

	function export_issue($dt, $id)
	{

		/******************************** ISSUE */
		$class = $dt['concept']['c_class'];
		$vol = $this->RDF->recovery($dt['data'], 'hasPublicationVolume');
		$nr = $this->RDF->recovery($dt['data'], 'hasPublicationNumber');
		$year1 = $this->RDF->recovery($dt['data'], 'dateOfPublication');
		$year2 = $this->RDF->recovery($dt['data'], 'hasDateTime');
		$year = array_merge($year1, $year2);
		$place = $this->RDF->recovery($dt['data'], 'hasPlace');
		$publish = $this->RDF->recovery($dt['data'], 'hasIssue');

		$dc['id'] = $id;

		/****************************************************** PUBLISH **/
		$namePublish = strip_tags($this->RDF->c($publish[0][1]));
		$dc['publish'] = array(
			'id' => $publish[0][1],
			'name' => $namePublish
		);

		/********************************************************** YEAR */
		if (isset($year[0][1])) {
			$nameYear = $this->RDF->c($year[0][1]);
			$dc['year'] = array(
				'id' => $year[0][1],
				'name' => $nameYear
			);
		} else {
			if ($dt['concept']['n_name'] == 'ISSUE:') {
				$RDFErros = new \App\Models\Rdf\RdfErros();
				$RDFErros->append(1, 'RDF ISSUE ERROR - SEM ANO', $id);
				$name = $namePublish . '==ERRO==' . $id;
				$this->saveRDF($id, $name, 'name.nm');
				$this->saveRDF($id, 0, 'year.nm');
				return "";
			}
		}

		/********************************************************** VOL. */
		if (isset($vol[0][1])) {
			$nameVol = $this->RDF->c($vol[0][1]);
			$dc['vol'] = array(
				'id' => $vol[0][1],
				'name' => $nameVol
			);
		} else {
			$nameVol = '';
		}

		/********************************************************** NR. **/
		if (isset($nr[0][1])) {
			$nameNr = $this->RDF->c($nr[0][1]);
			$dc['nr'] = array(
				'id' => $nr[0][1],
				'name' => $nameNr
			);
		} else {
			$nameNr = '';
		}
		/******************************************************** PLACE **/
		if (isset($place[0][1])) {
			$namePlace = $this->RDF->c($place[0][1]);
			$dc['place'] = array(
				'id' => $place[0][1],
				'name' => $namePlace
			);
		} else {
			$dc['place'] = array();
			$namePlace = '';
		}

		$issue = $namePublish;
		if (strlen($nameNr) > 0) {
			$issue .= ', ' . $nameNr;
		}
		if (strlen($namePlace) > 0) {
			$issue .= ', ' . $namePlace;
		}
		if (strlen($nameVol) > 0) {
			$issue .= ', ' . $nameVol;
		}
		if (strlen($nameYear) > 0) {
			$issue .= ', ' . $nameYear;
		}
		$issue .= '.';

		$dc['abnt'] = $issue;

		/**************************************************** Vancouver */
		$issue_vancouver = $namePublish;
		if (strlen($nameYear) > 0) {
			$issue_vancouver .= '. ' . $nameYear;
		}
		//if (strlen($namePlace) > 0) { $issue_vancouver .= ', '.$namePlace; }
		if (strlen($nameVol) > 0) {
			$issue_vancouver .= ' ' . sonumero($nameVol);
		}
		if (strlen($nameNr) > 0) {
			$issue_vancouver .= '(' . sonumero($nameNr) . ')';
		}
		$issue_vancouver .= '.';

		$dc['vancouver'] = $issue_vancouver;

		$name = $issue;
		$this->saveRDF($id, $name, 'name.nm');
		$this->saveRDF($id, $issue, 'name.abnt');
		$this->saveRDF($id, $issue_vancouver, 'name.vancouver');
		$this->saveRDF($id, $nameYear, 'year.nm');
		$this->saveRDF($id, json_encode($dc), 'issue.json');
		return "";
	}

	function export_geral($dt, $id)
	{
		$name = trim($dt['concept']['n_name']);

		if (!isset($dt['concept']['id_cc'])) {
			return 'NoN';
		}

		if (strlen($name) == 0) {
			$name = $this->RDF->recovery($dt['data'], 'prefLabel');
			$name = trim($name[0][2]);
		}
		if (strlen($name) > 0) {
			$this->saveRDF($id, $name, 'name.nm');
		}
		return '';
	}

	function export($id, $FORCE = false)
	{
		$tela = '';

		/*****************************************************************/
		switch ($id) {
			case 'index_authors':
				$tela .= $this->export_index_list_all($FORCE, 'Person', $id);
				return $tela;
				break;
			case 'index_subject':
				$tela .= $this->export_index_list_all($FORCE, 'Subject', $id);
				return $tela;
				break;
			case 'index_corporatebody':
				$tela .= $this->export_index_list_all($FORCE, 'CorporateBody', $id);
				return $tela;
				break;
			case 'index_journal':
				$tela .= $this->export_index_list_all($FORCE, 'Journal', $id);
				return $tela;
				break;
			case 'index_proceeding':
				$tela .= $this->export_index_list_all($FORCE, 'Proceeding', $id);
				return $tela;
				break;
		}

		$this->RDF = new \App\Models\RDF\RDF();
		$dir = $this->RDF->directory($id);
		$file = $dir . 'name.nm';
		if ((file_exists($file)) and ($FORCE == false)) {
			return '';
		}

		$dt = $this->RDF->le($id, 0);
		if (!isset($dt['concept']['c_class'])) {
			return '';
		}
		$prefix = $dt['concept']['prefix_ref'];
		$class = $prefix . ':' . $dt['concept']['c_class'];
		$name = ':::: ?' . $class . '? ::::';
		//echo '<br>'.$name.'-->'.$class;

		switch ($class) {
				/*************************************** SERIE NAME */
			case 'brapci:Image':
				$this->export_imagem($dt, $id);
				break;
				/*************************************** SERIE NAME */
			case 'brapci:hasSerieName':
				$this->export_geral($dt, $id);
				break;
				/*************************************** ARTICLE */
			case 'brapci:Article':
				$this->export_article($dt, $id, 'A');
				break;

			case 'brapci:Proceeding':
				$this->export_article($dt, $id, 'P');
				break;

				/*************************************** ISSUE ***/
			case 'dc:Issue':
				$this->export_issue($dt, $id);
				break;

				/*************************************** ISSUE ***/
			case 'brapci:IssueProceeding':
				$this->export_issueproceedings($dt, $id);
				break;

				/*************************************** ISSUE ***/
			case 'foaf:Person':
				$this->export_person($dt, $id);
				break;

				/*************************************** VOLUME */
			case 'brapci:PublicationVolume':
				$this->export_geral($dt, $id);
				break;

				/************************************** COUTNRY */
			case 'brapci:Country':
				$this->export_geral($dt, $id);
				break;

				/*************************************** VOLUME */
			case 'dc:ArticleSection':
				$this->export_geral($dt, $id);
				break;

				/*************************************** Number */
			case 'brapci:Number':
				$this->export_geral($dt, $id);
				break;

				/*************************************** Gender */
			case 'brapci:Gender':
				$this->export_geral($dt, $id);
				break;

				/******************************* Corporate Body */
			case 'frbr:CorporateBody':
				$this->export_geral($dt, $id);
				break;

				/************************************** SECTION */
			case 'brapci:ProceedingSection':
				$this->export_geral($dt, $id);
				break;

				/************************************** Subject */
			case 'dc:Subject':
				$this->export_geral($dt, $id);
				break;

				/*************************************** PLACE **/
			case 'frbr:Place':
				$this->export_geral($dt, $id);
				break;

				/*************************************** NUMBER */
			case 'brapci:PublicationNumber':
				$this->export_geral($dt, $id);
				break;

				/*************************************** Date ***/
			case 'brapci:Date':
				$this->export_geral($dt, $id);
				break;

			case 'dc:Journal':
				$this->export_journal($dt, $id);
				break;

			case 'brapci:FileStorage':
				$this->export_geral($dt, $id);
				break;

			default:
				//echo '<br> Exportando ====>' . $name;
				$this->export_geral($dt, $id);
				break;
		}
		$tela .= '<a href="' . (PATH . '$COLLECTION' . '/v/' . $id) . '">' . $name . '</a>';
		return $tela;
	}

	function export_imagem($d1, $d2)
	{
		$img = '';
		$tumb = '';
		$data = $d1['data'];
		$id = $d1['concept']['id_cc'];

		for ($r = 0; $r < count($data); $r++) {
			$line = $data[$r];
			/* TumbNail */
			if ($line['c_class'] == 'hasTumbNail') {
				$tumb = trim($line['n_name']);
			}
			/* Image - DIR*/
			if ($line['c_class'] == 'hasFileDirectory') {
				$img = trim($line['n_name']) . $img . 'image.jpg';
			}
			/* TumbNail */
			if ($line['c_class'] == 'hasImage') {
				$img = trim($line['n_name']);
			}
		}
		if ((strlen($img) > 0) and (file_exists($img))) {
			if (substr(strtolower($img), 0, 4) != 'http') {
				$img = URL . '/' . $img;
			}
			$this->saveRDF($id, $img, 'image.url');
		}
		if ((strlen($tumb) > 0) and (file_exists($tumb))) {
			if (substr(strtolower($tumb), 0, 4) != 'http') {
				$tumb = URL . '/' . $tumb;
			}
			$this->saveRDF($id, $tumb, 'tumb.url');
		}
		$this->saveRDF($id, $img, 'name.nm');
		$this->saveRDF($id, 'image', 'class.nm');
		return "";
	}

	function export_index_list_all($lt = 0, $class = 'Person', $url = '')
	{
		$this->RDF = new \App\Models\Rdf\RDF();
		$this->RDFConcept = new \App\Models\Rdf\RDFConcept();
		$nouse = 0;
		$dir = '../.tmp';
		dircheck($dir);
		$dir = '../.tmp/indexes/';
		dircheck($dir);
		$dir = '../.tmp/indexes/' . $class . '/';
		dircheck($dir);

		$sx = h('Index ' . $class . ' List', 1);

		if ($lt < 65) {
			$lt = 65;
		}

		if (($lt >= 65) and ($lt <= 90)) {
			$ltx = chr(round($lt));
			$txt = $this->create_index_html($ltx, $class, 0);
			$file = $dir . '/index_' . $ltx . '.php';
			$hdl = fopen($file, 'w+');
			fwrite($hdl, $txt);
			fclose($hdl);
			$sx .= bs_alert('success', msg('Export_author') . ' #' . $ltx . '<br>');
			$sx .= '<meta http-equiv="refresh" content="3;' . (PATH . '$COLLECTION' . 'rdf/export/' . $url . '/' . ($lt + 1)) . '">';
		} else {
			$sx .= bsmessage('rdf.export_success', 1);
			$sx .= $this->RDF->btn_return();
		}
		$sx = bs(bsc($sx, 12));
		return ($sx);
	}

	function create_index_html($lt = 'G', $class = 'Person', $nouse = 0)
	{
		$f = $this->RDF->getClass($class, 0);
		$wh = '';
		if ($nouse == 1) {
			$wh .= " and C1.cc_use = 0 ";
		}
		if (strlen($lt) > 0) {
			$wh .= " and (N1.n_name like '$lt%') ";
		}

		$sql = "select N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
                       N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use
                        FROM rdf_concept as C1
                        INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n
                        LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
                        LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
                        where C1.cc_class = " . $f . " $wh  and C1.cc_use = 0
                        ORDER BY N1.n_name";



		$rlt = (array)$this->db->query($sql)->getResult();

		$l = '';
		$sx = '';

		for ($r = 0; $r < count($rlt); $r++) {
			$line = (array)$rlt[$r];
			$idx = $line['id_cc'];
			$name_use = trim($line['n_name']);

			//$link = '<a href="' . . '" class="text-secondary" style="font-size: 85%;">';
			$link = $this->RDF->link($line);
			$linka = '</a>';

			$xl = substr(UpperCaseSql(strip_tags($name_use)), 0, 1);
			if ($xl != $l) {
				if ($l != '') {
					$sx .= '</ul>';
					$sx .= '</div>';
					$sx .= '</div>';
				}
				$linkx = '<a name="' . $xl . '" tag="' . $xl . '"></a>';
				$sx .= '<div class="row"><div class="col-md-1 text-right">';
				$sx .= '<h1 style="font-size: 500%;">' . $xl . '</h1></div>';
				$sx .= '<div class="col-md-11">';
				$sx .= '<ul style="list-style: none; columns: 300px 4; column-gap: 0;">';
				$l = $xl;
			}

			$name = $link . $name_use . $linka . ' <sup style="font-size: 70%;"></sup>';
			$sx .= '<li>' . $name . '</li>' . cr();
		}
		$sx .= '</ul>';
		$sx .= '</div></div>';
		$sx .= '<div class="row"><div class="col-md-12">';
		$sx .= '<b>' . msg('total_subject') . ' ' . number_format(count($rlt), 0, ',', '.') . ' ' . msg('registers') . '</b>';
		$sx .= '</div></div>';

		return ($sx);
	}

	function saveRDF($id, $value, $file)
	{
		$this->RDF = new \App\Models\RDF\RDF();
		$dir = $this->RDF->directory($id);
		$file = $dir . $file;
		file_put_contents($file, $value);
		return true;
	}
}
