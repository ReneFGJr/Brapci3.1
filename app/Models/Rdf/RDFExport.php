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

	function __construct()
	{
		$this->RDF = new \App\Models\RDF\RDF();
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
		$class = $prefix . ':' . trim($dt['concept']['c_class']);
		$name = ':::: ' . $class . ' ::::';
		//echo '<br>'.h('#'.$name.'==>'.$id);

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
				$this->export_proceeding($dt, $id, 'P');
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

			case 'brapci:Book':
				$this->export_book($dt, $id);
				break;
			case 'brapci:BookChapter':
				$this->export_bookChapter($dt, $id);
				break;

			default:
				//echo '<br> Exportando ====>' . $name;
				$this->export_geral($dt, $id);
				break;
		}
		$tela .= '<a href="' . (PATH . COLLECTION . '/v/' . $id) . '">' . $name . '</a>';
		return $tela;
	}


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

	function saveCSV($id,$sep = 'td')
		{
			/*************************** Separador */
			switch($sep)
				{
					default:
						$line_start = '<tr>';
						$line_end = '</tr>';
						$sep_ini = '<td>';
						$sep_end = '</td>';
						break;
				}
			$dir = $this->RDF->directory($id);
			$csv = array(
					'Authors'=> 'json',
					'Title'=>'name',
					'Journal'=>'name',
					'Year' => 'name',
					'Sections' => 'json',
					'Keywords_pt-BR'=>'json',
					'Keywords_en' => 'json',
					'Keywords_es' => 'json',
					'Keywords_fr' => 'json',
					);
			$csvRow = '' . cr();
			$csvRow .= $line_start;
			foreach ($csv as $fields => $type)
				{
						$csvRow .= $sep_ini . UpperCase($fields) . '' . $sep_end . cr();
				}
			$csvRow .= $line_end.cr();

			$csvRow .= $line_start.cr();
			foreach($csv as $fields=>$type)
				{
					$file = $dir.$fields.'.'.$type;
					if (file_exists($file))
						{
							switch($type)
								{
									case 'json':
									$n = array();
									$json = json_decode(file_get_contents($file));
									$name = implode(";", $json);
									$csvRow .= $sep_ini.$name.$sep_end;
									break;

									case 'name':
									$name = file_get_contents($file);
									$csvRow .= $sep_ini . $name . $sep_end . cr();
									break;

									default:
										echo '==>'.$type;
										break;
								}
						} else {
							$csvRow .= $sep_ini . '' . $sep_end.cr();
						}
				}
			$csvRow .= $line_end;
			$this->saveData($id, 'csv', $csvRow);
		}

	function saveData($id,$type,$dta)
		{
			switch($type)
				{
					case 'Cover':
						$COVER = new \App\Models\Base\Cover();
						$img = $COVER->book($dta['ID']);
						$this->saveRDF($id, $img, 'cover.img');
						break;
					case 'Elastic':
						$dta = json_encode($dta);
						$this->saveRDF($id, $dta, 'metadata.json');
						break;
					case 'csv':
						$this->saveRDF($id, $dta, 'metadata.csv');
						break;
					case 'name':
						$this->saveRDF($id, $dta, 'name.nm');
						break;
					case 'abnt':
						$this->saveRDF($id, $dta, 'work_abnt.nm');
						break;
					case 'Class':
						$name = $dta['Class'];
						$this->saveRDF($id, $name, 'class.nm');
						break;
					case 'Pages':
						$pages = '';
						if (isset($dta['Pages'])) {
							$pages .= $dta['Pages'];
						}
						if (isset($dta['pagi'])) {
							$pages .= $dta['pagi'].'-';
						}
						if (isset($dta['pagf'])) {
							$pages .= $dta['pagf'];
						}
						$this->saveRDF($id, $pages, 'pages.nm');
						break;
					case 'Title':
						if (isset($dta['title']))
							{
								$name = $dta['title'];
							} else {
								$name = '';
							}

						$this->saveRDF($id, $name, 'Title.name');
						break;
					case 'Year':
						if (isset($dta['Issue']['Year'])) {
							$name = trim($dta['Issue']['Year']);
							$this->saveRDF($id, $name, 'Year.name');
						}
						break;
					case 'Place':
						if (isset($dta['Issue']['Place'])) {
							$name = trim($dta['Issue']['Place']);
							$this->saveRDF($id, $name, 'Place.name');
						}
						break;
					case 'Keywords':
						if (isset($dta['Keywords']))
							{
								$name = json_encode($dta['Keywords']);
								$this->saveRDF($id, $name, 'Keywords.json');

								$name = '';
								$idiomas = array();
								foreach($dta['Keywords'] as $key=>$lang)
									{
										if ($lang == 'es-ES') { $lang = 'es'; }
										if (!isset($idiomas[$lang]))
											{
												$idiomas[$lang] = array();
											}
										$idiomas[$lang][] = $key;
										$name .= $key.';';
									}
								$this->saveRDF($id, $name, 'Keywords.name');
								foreach($idiomas as $lang=>$keys)
									{
										$this->saveRDF($id, json_encode($keys), 'Keywords_'.$lang.'.json');
									}
							}
						break;
					case 'Authors':
						if (isset($dta['Authors']))
						{
							$name = json_encode($dta['Authors']);
							$this->saveRDF($id, $name, 'Authors.json');

							$name = trim(implode('; ', $dta['Authors']));
							$this->saveRDF($id, $name, 'Authors.name');
						}
						break;

					case 'Journal':
						if (isset($dta['Issue']['Journal']))
						{
							$name = trim($dta['Issue']['Journal']);
							$this->saveRDF($id, $name, 'Journal.name');
						}
						break;

					case 'Section':
						if (isset($dta['Sections']))
							{
								$name = json_encode($dta['Sections']);
								$this->saveRDF($id, $name, 'Sections.json');

								$name = trim(implode(';',$dta['Sections']));
								$this->saveRDF($id, $name, 'Sections.name');
							} else {
								echo "Section NOT FOUND";
							}
						break;
					case 'Issue':
						if(isset($dta['issue_name']))
							{
								$name = $dta['issue_name'][0];
								$this->saveRDF($id, $name, 'Issue.name');
							}
						break;
					default:
						echo "OPS SAVE TYPE $type";
						exit;
				}
		}

	function export_proceeding($dt,$id,$tp='P')
		{
		$Metadata = new \App\Models\Base\Metadata();
		$RDF = new \App\Models\Rdf\RDF();
		$ABNT = new \App\Models\Metadata\Abnt();

		if (count($dt['data']) == 0) {
			return "OPS export_proceeding " . $id . '<br>';
		}
		$dta = $Metadata->metadata($dt);

		$this->saveData($id, 'Title', $dta);
		$this->saveData($id, 'Section',$dta);
		$this->saveData($id, 'Journal', $dta);
		$this->saveData($id, 'Authors', $dta);
		$this->saveData($id, 'Keywords', $dta);

		$this->saveData($id, 'Year', $dta);
		$this->saveData($id, 'Place', $dta);

		$this->saveData($id, 'Class', $dta);
		$this->saveData($id, 'Pages', $dta);
		//$this->saveRDF($id, json_encode($dta), 'name.json');

		$this->saveCSV($id);
		$this->saveData($id, 'Elastic', $dta);

		/* Formata para Artigo ABNT */
		$ABNT = new \App\Models\Metadata\Abnt();
		$name = $ABNT->abnt_proceeding($dta);
		$this->saveData($id, 'abnt', $name);
		$this->saveData($id, 'name', $name);

		return '';
		}

	/****************************************************************** ARTICLE / PROCEEDING */
	function export_article($dt, $id, $tp = 'A')
	{
		$Metadata = new \App\Models\Base\Metadata();
		$RDF = new \App\Models\Rdf\RDF();
		$ABNT = new \App\Models\Metadata\Abnt();

		if (count($dt['data']) == 0) {
			return "OPS export_proceeding " . $id . '<br>';
		}
		$dta = $Metadata->metadata($dt);

		$this->saveData($id, 'Title', $dta);
		$this->saveData($id, 'Section', $dta);
		$this->saveData($id, 'Journal', $dta);
		$this->saveData($id, 'Authors', $dta);
		$this->saveData($id, 'Keywords', $dta);

		if (isset($dta['issue_id'])) {
			$dti = $RDF->le($dta['issue_id'][0]);
			$dta['issue'] = $Metadata->metadata($dti);
			$this->saveData($id, 'Issue', $dta['issue']);
		}


		$this->saveData($id, 'Year', $dta);
		$this->saveData($id, 'Place', $dta);

		$this->saveData($id, 'Class', $dta);
		$this->saveData($id, 'Pages', $dta);
		//$this->saveRDF($id, json_encode($dta), 'name.json');

		$this->saveCSV($id);
		$this->saveData($id, 'Elastic', $dta);

		/* Formata para Artigo ABNT */
		$ABNT = new \App\Models\Metadata\Abnt();
		$name = $ABNT->abnt_article($dta);
		$this->saveData($id, 'abnt', $name);
		$this->saveData($id, 'name', $name);

		return '';
	}

	function export_person($dt, $id)
	{
		$sx = '';
		$name = $dt['concept']['n_name'];
		$name = nbr_author($name, 1);
		$name = '<a href="' . (PATH . '/v/' . $id) . '" class="author">' . $name . '</a>';
		$this->saveRDF($id, $name, 'name.nm');
		return $sx;
	}

	function export_journal($dt, $id)
	{
		$sx = 'JOURNAL';
		$name = $dt['concept']['n_name'];
		$name = nbr_author($name, 7);
		$name = '<a href="' . (PATH . '/v/' . $id) . '" class="author">' . $name . '</a>';
		$this->saveRDF($id, $name, 'name.nm');
		return $sx;
	}

	function export_bookChapter($dt,$id)
		{
		$Metadata = new \App\Models\Base\Metadata();
		$RDF = new \App\Models\Rdf\RDF();
		$ABNT = new \App\Models\Metadata\Abnt();

		if (count($dt['data']) == 0) {
			return "OPS export_proceeding " . $id . '<br>';
		}
		$dta = $Metadata->metadata($dt);
		$x = 0;

		$this->saveData($id, 'Title', $dta);
		//$this->saveData($id, 'Section', $dta);
		//$this->saveData($id, 'Journal', $dta);

		$this->saveData($id, 'Authors', $dta);
		$this->saveData($id, 'Keywords', $dta);
		if (isset($dta['issue_id'])) {
			$dti = $RDF->le($dta['issue_id'][0]);
			$dta['issue'] = $Metadata->metadata($dti);
			$this->saveData($id, 'Issue', $dta['issue']);
		}

		$this->saveData($id, 'Year', $dta);
		$this->saveData($id, 'Place', $dta);

		$this->saveData($id, 'Cover', $dta);

		$this->saveData($id, 'Class', $dta);
		$this->saveData($id, 'Pages', $dta);
		//$this->saveRDF($id, json_encode($dta), 'name.json');

		$this->saveCSV($id);
		$this->saveData($id, 'Elastic', $dta);

		/* Formata para Artigo ABNT */
		$ABNT = new \App\Models\Metadata\Abnt();
		$nameABNT = $ABNT->abnt_chapter($dta);
		$authors = '';
		if (isset($dta['Authors']))
			{
				$authors = $ABNT->authors($dta);
				$authors = trim($dta['authors']).'#';
				$authors = troca($authors, '$', ';');
				$authors = troca($authors,';#','');
				$authors = troca($authors, '#', '');
				$authors .= '. ';
			}

		if (isset($dta['title']))
			{
				$title = $dta['title'];
			} else {
				$title = '[sem título]';
			}

		$name = '<b>'.trim($title). '</b><br><i>'.$authors.'</i>';
		$this->saveData($id, 'abnt', $nameABNT);
		$this->saveData($id, 'name', $name);

		return "";
		}

	function export_book($dt, $id)
	{
		$Metadata = new \App\Models\Base\Metadata();
		$RDF = new \App\Models\Rdf\RDF();
		$ABNT = new \App\Models\Metadata\Abnt();

		if (count($dt['data']) == 0) {
			return "OPS export_proceeding " . $id . '<br>';
		}
		$dta = $Metadata->metadata($dt);

		$this->saveData($id, 'Title', $dta);
		//$this->saveData($id, 'Section', $dta);
		//$this->saveData($id, 'Journal', $dta);
		$this->saveData($id, 'Authors', $dta);
		$this->saveData($id, 'Keywords', $dta);

		if (isset($dta['issue_id'])) {
			$dti = $RDF->le($dta['issue_id'][0]);
			$dta['issue'] = $Metadata->metadata($dti);
			$this->saveData($id, 'Issue', $dta['issue']);
		}

		$this->saveData($id, 'Year', $dta);
		$this->saveData($id, 'Place', $dta);

		$this->saveData($id, 'Class', $dta);
		$this->saveData($id, 'Pages', $dta);
		//$this->saveRDF($id, json_encode($dta), 'name.json');

		$this->saveCSV($id);
		$this->saveData($id, 'Elastic', $dta);

		/* Formata para Artigo ABNT */
		$ABNT = new \App\Models\Metadata\Abnt();
		$name = $ABNT->abnt_book($dta);
		$this->saveData($id, 'abnt', $name);
		$this->saveData($id, 'name', $name);

		return "";
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


	function BookChapher($reg,$ISBN,$idb)
		{
			$RDF = new \App\Models\Rdf\RDF();
			$Language = new \App\Models\AI\NLP\Language();

			$json = $reg['n_name'];
			$chap = (array)json_decode($json);

			$w=0;
			foreach($chap as $id=>$line)
				{
					$line = (array)$line;
					$title = '';
					if (isset($line['title']))
						{
							$title = $line['title'];
							if (strpos($title, '{')) {
								$title = trim(substr($title, 0, strpos($title, '{')));
							}
						}

					$authors = (array)$line['autor'];

					$pgi = 0;
					$pgf = 0;
					if (isset($line['pag'])) $pgi = $line['pag'];
					if (isset($line['pagf'])) $pgi = $line['pagf'];

					/*********************** RDF */
					if (substr($title,0,1) == '=')
						{

						} else {
							$prefTerm = $ISBN;
							if ($pgi > 0) {
								$prefTerm .= '_p'.$pgi;
								$w++;
							}  else {
								$w++;
								$prefTerm .= '_p' . strzero($w,4);
							}

							$class = 'BookChapter';
							$idc = $this->RDF->RDF_concept($prefTerm, $class);
							$lang = $Language->getTextLanguage($title);

							$prop = 'hasTitle';
							$literal = $this->RDF->literal($title,$lang);
							$this->RDF->propriety($idc, $prop, 0, $literal);


							for($r=0;$r < count($authors);$r++)
								{
									$author = $authors[$r];
									$id_pe = $this->RDF->RDF_concept($author, 'Person');
									$prop = 'hasAuthor';
									$this->RDF->propriety($idc, $prop, $id_pe, 0);
								}


							//$literal = 0;
							$prop = 'hasBookChapter';
							$exec = $this->RDF->propriety($idb, $prop, $idc, 0);
						}
				}
			return('====>'.$title);
			//return $this->RDF->c($idc);
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
			$sx .= '<meta http-equiv="refresh" content="3;' . (PATH . COLLECTION . 'rdf/export/' . $url . '/' . ($lt + 1)) . '">';
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