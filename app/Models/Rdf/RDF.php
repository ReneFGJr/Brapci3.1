<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDF extends Model
{
	var $DBGroup              		= 'rdf';
	protected $table                = PREFIX . 'rdf_concept';
	protected $primaryKey           = 'id_cc';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_cc', 'cc_use'
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

	/*
		function rdf($d1='',$d2='',$d3='')
		{
			$RDF = new \App\Models\Rdf\RDF();
			$tela = $RDF->index($d1,$d2,$d3);
			return $tela;
		}
	*/

	function index($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '', $cab = '')
	{
		$sx = '';
		$data['title'] = 'RDF';
		$cab = '';
		$cab .= view('Brapci/Headers/header', $data);
		$cab .= view('Benancib/Headers/navbar', $data);

		$sx = '';

		switch ($d1) {
			/****************************************************************** CONCEPT */
			case 'view':
				switch($d2)
					{
						case 'pdf':
							$RDFPdf = new \App\Models\Rdf\RDFPdf();
							$RDFPdf->view_file($d3,$d4);
							exit;
							break;
						default:
							$sx .= $cab.bsmessage('What´s '.$d2,3);
							break;
					}
				break;
			/****************************************************************** CONCEPT */
			case 'v':
				$dt = $this->le($d2);
				$class = $dt['concept']['c_class'];

				$sx .= $cab;
				$sx .= bs(bsc(h($class, 1), 12));
				$sx .= $this->view_data($dt);;
			break;

			/****************************************************************** AJAX */
			case 'ajax':
			switch($d2)
				{
					case 'vc_create':
						$RDFRange = new \App\Models\Rdf\RDFRange();
						$d2 = get("reload");
						$d3 = get("vlr");
						$reg = get("reg");
						$prop = get("prop");
						$vlr = get("vlr");
						$IDC = $RDFRange->range_insert_value($d2, $vlr);
						$this->propriety($reg, $prop, $IDC);
						$sx = wclose();
						echo $sx;
						exit;
					case 'search':
						echo '==search=>'.$d4;
						$sx = view('Brapci/Headers/header', $data);
						$RDFFormVC = new \App\Models\Rdf\RDFFormVC();
						$sx = '';
						$sx .= $RDFFormVC->search($d3, $d4, '');
						echo $sx;
						exit;
						break;
					case 'set':
						$RDFFormVC = new \App\Models\Rdf\RDFFormVC();
						$sx = $RDFFormVC->ajax_save();
						return $sx;
						break;
					default:
						echo h($d2);
						exit;
				}
			break;
			/****************************************************************** CONCEPT */
			case 'concept':
				echo "NOT IMPLEMENTED";
				exit;
			break;

			/****************************************************************** VALUE */
			case 'data':
				$sx = $cab;
				$RDFForm = new \App\Models\Rdf\RDFForm();
				/* CRUD */
				switch ($d2) {
					case 'exclude':
						$sx = view('Brapci/Headers/header', $data);
						$RDFForm = new \App\Models\Rdf\RDFForm();
						$sx .= $RDFForm->exclude($d3, $d4);
						echo $sx;
						exit;
						break;
					case 'edit':
						$sx = view('Brapci/Headers/header', $data);
						$form_class = 0;
						$prop_name = $d3;
						$form_id = $d4;
						$register = $d5;

						$sx .= $RDFForm->edit($form_class, $prop_name, $form_id, $register);

						echo $sx; exit;
						break;
				}
				break;
			/******************************************************************* FORM */
			case 'form':
				$sx = $cab;
				$RDFForm = new \App\Models\Rdf\RDFForm();
				/* CRUD */
				switch($d2)
					{
						case 'editRDF':
							$RDF = new \App\Models\Rdf\RDF();
							$sx = view('Brapci/Headers/header', $data);
							$sx .= $RDF->form($d3);
							echo $sx;
							exit;
							break;
						case 'edit':
							$sx .= $RDFForm->form_ed($d3, $d4, $d5);
							break;
						case 'check':
							$sx .= $RDFForm->form_import($d3, $d4, $d5);
							break;
					}
				break;


			/******************************************************************* CLASS */
			case 'class':
				switch ($d2) {
					case 'edit':
						$sx .= breadcrumbs(array(
							'rdf.home' => PATH . MODULE . '/rdf/',
							'rdf.edit' => PATH . MODULE . '/rdf/edit'
						));
						$RDFClass = new \App\Models\Rdf\RDFClass();
						$sx .= $cab;
						$sx .= $RDFClass->edit($d3, $d4, $d5);
						break;
					case 'view':
						$sx .= $cab;
						$sx .= breadcrumbs(array(
							'rdf.home' => PATH . MODULE . '/rdf/',
							'rdf.view' => PATH . MODULE . '/rdf/class'
						));
						$RDFClass = new \App\Models\Rdf\RDFClass();
						$sx .= $RDFClass->view($d3);
						break;
					default:
						$sx .= $cab;
						$sx .= breadcrumbs(array(
							'rdf.home' => PATH . MODULE . '/rdf/',
							'rdf.class' => PATH . MODULE . '/rdf/class'
						));
						$RDFClass = new \App\Models\Rdf\RDFClass();
						$sx .= $RDFClass->list('C');
						break;
				}
				break;
				/******************************************************************* PROPERTY */
			case 'property':
				switch ($d2) {
					case 'edit':
						$sx .= breadcrumbs(array(
							'rdf.home' => PATH . MODULE . '/rdf/',
							'rdf.edit' => PATH . MODULE . '/rdf/edit'
						));

						$RDFClass = new \App\Models\Rdf\RDFClassProperty();
						$sx .= $cab;
						$sx .= $RDFClass->edit($d2, $d3, $d4, $d5);
						break;
					case 'view':
						$RDFClassProperty = new \App\Models\Rdf\RDFClassProperty();
						$sx .= $cab;
						$sx .= breadcrumbs(array(
							'rdf.home' => PATH . MODULE . '/rdf/',
							'rdf.view' => PATH . MODULE . '/rdf/property'
						));
						$RDFClass = new \App\Models\Rdf\RDFClass();
						$sx .= $RDFClassProperty->view($d3);
						break;
					default:
						$sx .= $cab;
						$sx .= breadcrumbs(array(
							'rdf.home' => PATH . MODULE . '/rdf/',
							'rdf.class' => PATH . MODULE . '/rdf/property'
						));
						$RDFClass = new \App\Models\Rdf\RDFClass();
						$sx .= $RDFClass->list('P');
						break;
				}
				break;
			default:
				$sx .= $cab;
				$sx .= breadcrumbs(array(
						'rdf.home' => PATH . MODULE . '/rdf/'
					));
				$sx .= $this->menu();
				break;
		}
		return $sx;
	}

	/********************************************************************************* MENU */
	function menu()
	{
		$Socials = new \App\Models\Socials();
		$Admin = $Socials->getAccess("#ADM");

		$sx = '';

		$sa = '';
		$sa .= h('rdf.MainMenu');

		if ($Admin) {
			$sa = h(lang('rdf.main_menu'), 3);
			$menu = array();
			$menu[PATH . COLLECTION . '/class'] =  lang('rdf.classes');
			$menu[PATH . COLLECTION . '/property'] =  lang('rdf.property');
			$sx .= bs($sa . bsc(menu($menu)));
		} else {
			$sx .= h(lang('rdf.guest_menu'), 3);
		}
		return $sx;
	}


	function xxx_index2($d1, $d2 = '', $d3 = '', $d4 = '', $d5 = '', $cab = '')
	{
		$sx = '';
		$type = get("type");




		switch ($d1) {
			case 'v':
				$dt = $this->le($d2);
				$class = $dt['concept']['c_class'];
				$sx .= h($class, 1);
				$sx = bs(bsc($sx));
				$sx .= $this->view_data($d2);
				break;
			case 'form_check':
				$sx = $cab;
				$RDFFormCheck = new \App\Models\Rdf\RDFFormCheck();
				$sx .= $RDFFormCheck->check($d2, $d3, $d4, $d5);
				$sx .= '<a href="' . PATH . COLLECTION . '/class/view/' . $d2 . '">' . lang('rdf.return') . '</a>';
				$sx .= metarefresh(PATH . COLLECTION . '/class/view/' . $d2);
				break;
			case 'class':
				$sx = $cab;
				echo '==class=>' . $d2;
				$RDFClass = new \App\Models\Rdf\RDFClass();
				$sx .= $RDFClass->list('C');
				break;
			case 'property':
				$sx = $cab;
				$RDFClass = new \App\Models\Rdf\RDFClass();
				$sx .= $RDFClass->list('P');
				break;
			case 'class_edit':
				$sx = $cab;
				$RDFClass = new \App\Models\Rdf\RDFClass();
				$sx .= $RDFClass->edit($d2, $d3, $d4, $d5);
				break;
			case 'formss':
				$sx = $cab;
				$RDFClassProperty = new \App\Models\Rdf\RDFClassProperty();
				$sx .= $RDFClassProperty->edit($d2, $d3, $d4, $d5);
				break;
			case 'ontology':
				$RDFOntology = new \App\Models\Rdf\RDFOntology();
				$sx = $cab;
				$sx .= breadcrumbs();
				$sx .= bs(bsc($RDFOntology->index($d2, $d3, $d4, $d5, $cab), 12));
				break;
			case 'export':
				$RDFExport = new \App\Models\Rdf\RDFExport();
				$sx = $cab;
				$sx .= $RDFExport->Export($d2, $d3, $d4, $d5);
				break;
			case 'remissive_Person':
				$sx .= $this->remissive($d2, $d3, $d4, $d5, $cab, 'Person');
				break;
			case 'remissive_CorporateBody':
				$sx .= $this->remissive($d2, $d3, $d4, $d5, $cab, 'CorporateBody');
				break;
			case 'remissive_Subject':
				$sx .= $this->remissive($d2, $d3, $d4, $d5, $cab, 'Subject');
				break;
			case 'set_pref_term':
				$RDFConcept = new \App\Models\Rdf\RDFConcept();
				$RDFConcept->set_pref_term($d2, $d3);
				$sx .= wclose();
				break;
			case 'check':
				$RDFChecks = new \App\Models\Rdf\RDFChecks();
				$sx .= $cab;
				$sx .= $RDFChecks->check_duplicate();
				$sx .= $RDFChecks->btn_return();
				break;
			case 'check_authors':
				$RDFChecks = new \App\Models\Rdf\RDFChecks();
				$sx .= $cab;
				$sx .= $RDFChecks->check_class("Person");
				$sx .= $RDFChecks->check_html('Person');
				$sx .= bs(bsc($RDFChecks->btn_return(), 12));
				break;
			case 'check_corporate_body':
				$RDFChecks = new \App\Models\Rdf\RDFChecks();
				$sx .= $cab;
				$sx .= $RDFChecks->check_class("CorporateBody");
				$sx .= $RDFChecks->check_html('CorporateBody');
				$sx .= bs(bsc($RDFChecks->btn_return(), 12));
				break;
			case 'check_subject':
				$RDFChecks = new \App\Models\Rdf\RDFChecks();
				$sx .= $cab;
				$sx .= $RDFChecks->check_class("Subject");
				$sx .= $RDFChecks->check_html('Subject');
				$sx .= bs(bsc($RDFChecks->btn_return(), 12));
				break;
			case 'check_loop';
				$RDFChecks = new \App\Models\Rdf\RDFChecks();
				$sx .= $cab;
				$sx .= $RDFChecks->check_loop();
				break;
			case 'exclude_concept':
				$sx = $cab;
				$sx .= $this->exclude_conecpt($d2, $d3);
				break;
			case 'form':
				$RDFForm = new \App\Models\Rdf\RDFForm();
				$sx = $cab;
				$sx .= $RDFForm->edit($d2, $d3, $d4, $d5);
				break;
			case 'forms':
				$RDFForm = new \App\Models\Rdf\RDFForm();
				$sx = $cab;
				$sx .= $RDFForm->forms($d1, $d2, $d3, $d4, $d5);
				break;
			case 'text':
				$RDFFormText = new \App\Models\Rdf\RDFFormText();
				$sx = $cab;
				$sx .= $RDFFormText->edit($d2);
				break;
			case 'inport':
				$sx = $cab;
				switch ($type) {
					case 'prefix':
						$this->RDFPrefix = new \App\Models\Rdf\RDFPrefix();
						$sx .= $this->RDFPrefix->inport();
						break;

					case 'class':
						$this->RDFClass = new \App\Models\Rdf\RDFClass();
						$sx .= $this->RDFClass->inport();
						break;
				}
				break;
				/************* Default */
			default:
				$sx = $cab;
				$sa = '';
				$sa .= '<ul>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/class') . '">' . lang('rdf.classes') . '</a></li>';
				$sa .= h(lang('rdf.import'), 3);
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/inport?type=prefix') . '">' . lang('Inport Prefix') . '</a></li>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/inport?type=class') . '">' . lang('Inport Class') . '</a></li>';
				$sa .= h(lang('rdf.check_do'), 3);
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/check') . '">' . lang('rdf.Check_class_duplicate') . '</a></li>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/check_loop') . '">' . lang('rdf.Check_loop') . '</a></li>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/check_authors') . '">' . lang('rdf.Check_authors') . '</a></li>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/check_corporate_body') . '">' . lang('rdf.Check_corporate_body') . '</a></li>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/check_subject') . '">' . lang('rdf.Check_subject') . '</a></li>';
				$sa .= h(lang('brapci.export_rdf'), 4);
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/export/index_authors') . '">' . lang('rdf.Export_authors.index') . '</a></li>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/export/index_subject') . '">' . lang('rdf.Export_subject.index') . '</a></li>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/export/index_corporatebody') . '">' . lang('rdf.Export_corporatebody.index') . '</a></li>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/export/index_journal') . '">' . lang('rdf.Export_journal.index') . '</a></li>';
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/export/index_proceeding') . '">' . lang('rdf.Export_proceeding.index') . '</a></li>';
				$sa .= h(lang('brapci.ontology'), 4);
				$sa .= '<li><a href="' . (PATH . MODULE . 'rdf/ontology') . '">' . lang('rdf.ontology') . '</a></li>';
				$sa .= '</ul>';
				$sx .= bs(bsc($sa, 12));
		}
		return $sx;
	}

	function ontology()
	{
		$OWL = new \App\Models\Rdf\RDFOntology();
		$sx = $OWL->index();
		$sx = bs($sx);
		return $sx;
	}

	function exclude($id)
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();
		$RDFData = new \App\Models\Rdf\RDFData();

		$RDFData->exclude($id);
		$RDFConcept->exclude($id);
	}

	function E404()
	{
		$sx = '<h1>' . 'ERROR: 404' . '</h1>';
		$sx .= '<p>' . lang('rdf.concept_was_deleted') . '</p>';
		$sx .= '<button onclick="history.back()">Go Back</button>';
		return ($sx);
	}

	function exclude_conecpt($id, $chk)
	{
		$sx = '';

		$check = md5(MODULE . $id);
		if ($check == $chk) {
			$this->exclude($id);
			$sx .= wclose();
			return $sx;
		}
		$dt = $this->le($id, 1);

		$sx .= 'class:' . $dt['concept']['c_class'];
		$sx .= '<br>';
		$sx .= h($dt['concept']['n_name'], 4);
		$sx .= '<hr>';
		/* Mostra mensagem de exclusão */
		$sx .= '<center>' . h(msg('find.rdf_exclude_confirm'), 4, 'text-danger') . '</center>';
		$sx .= '
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-default" onclick="wclose();" data-dismiss="modal">' . lang('find.cancel') . '</button>
			<a href="' . (PATH . MODULE . 'rdf/exclude_concept/' . $id . '/' . $check) . '" class="btn btn-warning" id="submt">' . lang('find.confirm_exclude') . '</a>
			</div>
		';
		return $sx;
	}


	function string_array($d, $t = 0, $sep = ';')
	{
		$sx = '';
		$max = count($d);

		if ($t == 1) {
			$max = $t;
		}
		for ($r = 0; $r < $max; $r++) {
			if (isset($d[$r])) {
				$line = $d[$r];
				if (strlen($line[2]) > 0) {
					$sx .= '<p>' . $line[2] . '</p>';
				} else {
					if (strlen($sx) > 0) {
						$sx .= $sep . ' ';
					}
					$dt['id_cc'] = $line[1];
					$sx .= $this->link($dt);
					$sx .= $this->c($line[1]);
					$sx .= '</a>';
				}
			}
		}
		return $sx;
	}

	function string($d, $t = 0, $sep = ';')
	{
		$sx = '';

		/* Retorna se for string */
		if (!is_array($d)) {
			return ($d);
		}
		/* Array vazia */
		if (count($d) == 0) {
			return '';
		}

		$max = count($d);
		if ($t == 1) {
			$max = $t;
		}
		for ($r = 0; $r < $max; $r++) {
			if (isset($d[$r])) {
				if (strlen($sx) > 0) {
					$sx .= $sep . ' ';
				}
				if (is_array($d[$r])) {
					$sx .= $this->c($d[$r][1]);
				} else {
					$sx .= $d[$r];
				}
			}
		}
		return $sx;
	}





	function le_class($id)
	{
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$dt = $RDFClass->le($id);
		return $dt;
	}

	function link($dt, $class = '')
	{
		$sx = '<a href="' . (URL . '$COLLECTION/v/' . $dt['id_cc']) . '" class="' . $class . '">';
		return $sx;
	}

	function href($dt, $class = '')
	{
		$sx = '<a href="' . (URL . '/' . COLLECTION . '/v/' . $dt['id_cc']) . '" class="' . $class . '">';
		return $sx;
	}


	function change($d1, $d2)
	{
		$RDFData = new \App\Models\Rdf\RDFData();
		$RDFData->change($d1, $d2);
	}

	function remissive($d2, $d3, $d4, $d5, $cab, $class = "Person")
	{
		$dt = $this->le($d2);
		$nome = $dt['concept']['n_name'];
		$sx = $cab;

		$nome = troca($nome, ',', '');
		$wd = explode(' ', $nome);
		$sa = '';
		for ($r = 0; $r < count($wd); $r++) {
			$sa .= '<a href="' . PATH . MODULE . 'rdf/remissive_' . $class . '/' . $d2 . '/' . $d3 . '?arg=' . $wd[$r] . '">' . $wd[$r] . '</a> ';
		}
		$sx .= bsc($sa, 12);

		/******************************* SAVE */
		$act = get("action");
		if ($act != '') {
			$d1x = get("id_cc");
			$d2x = get("id_use");

			if (is_array($d2x)) {
				for ($q = 0; $q < count($d2x); $q++) {
					$d2xa = $d2x[$q];
					$dt['cc_use'] = $d1x;
					$this->set($dt)->where('id_cc', $d2xa)->update();
					$this->change($d1x, $d2xa);
				}
				$sx = metarefresh(PATH . MODULE . 'rdf/remissive_' . $class . '/' . $d2 . '/' . $d3 . '?arg=' . get('arg'));
				return $sx;
			} else {
				if ($d2x != '') {
					$dt['cc_use'] = $d1x;
					$this->set($dt)->where('id_cc', $d2x)->update();
					$this->change($d1x, $d2x);
					$sx = metarefresh(PATH . MODULE . 'rdf/remissive_' . $class . '/' . $d2 . '/' . $d3 . '?arg=' . get('arg'));
					return $sx;
				}
			}
		}

		/* Classe */
		$id_class = $this->getClass($class);

		$nm = get("arg");
		if (strlen($nm) == 0) {
			$nm = $wd[0];
		}

		$this->join('rdf_name', 'cc_pref_term = id_n');
		$this->where('cc_class', $id_class);
		$this->where('cc_use', 0);
		$this->where('id_cc <> ' . $d2);
		$this->like('n_name', $nm);
		$this->orderBy('n_name');
		$dt = $this->FindAll();

		$sx .= form_open();
		$sx .= '<input type="text" name="arg" value="' . $nm . '" size="20" class="form-control">';
		$sx .= '<input type="hidden" name="id_cc" value="' . $d2 . '">';
		$sx .= count($dt) . ' names found';
		$sx .= '<select name="id_use[]" style="width: 100%;" size=12 multiple>';
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$sx .= '<option value="' . $line['id_cc'] . '">';
			$sx .= $line['n_name'];
			$sx .= '</option>';
		}
		$sx .= '</select>';
		$sx .= '<input type="submit" name="action" value="' . lang('Join') . '" class="btn btn-outline-primary">';
		$sx .= form_close();

		return $sx;
	}

	function show_index($class = '', $lt = '')
	{
		$sx = '';
		$dir = '../.tmp/indexes/' . $class;
		if (is_dir($dir)) {
			$indexes = scandir($dir);
			$sx .= '<style> .page-item:hover { background-color: #EEE;" } </style>' . cr();
			$sx .= '<nav aria-label="Index">';
			$sx .= '<ul class="pagination">';

			for ($r = 0; $r < count($indexes); $r++) {
				$file = trim($indexes[$r]);
				if (($file != 'index.php') and ($file != '.') and ($file != '..')) {
					$filename = $dir . '/' . $indexes[$r];
					if (file_exists($filename)) {
						$ltr = troca($file, 'index_', '');
						$ltr = troca($ltr, '.php', '');
						$link = PATH . MODULE . 'indexes/' . $class . '/' . $ltr;
						$sx .= '<li class="page-item text-center" style="width: 30px;">';
						$sx .= '<a href="' . $link . '">' . $ltr . '</a>';
						$sx .= '</li>';
					}
				}
			}
			$sx .= '</ul></nav>';

			if ($lt != '') {
				$filename = '../.tmp/indexes/' . $class . '/index_' . $lt . '.php';
				if (file_exists($filename)) {
					$sx .= file_get_contents($filename);
				} else {
					$sx = bsmessage('rdf.index.file.not.found - ' . $filename, 3);
				}
			}
		} else {
			$sx = bsmessage('rdf.index.dir.not.found - ' . $dir, 3);
		}
		$sx = bs(bsc($sx, 12));
		return $sx;
	}

	function list_indexes()
	{
		$dir = '../.tmp/indexes/';
		$ind = scandir($dir);
		$sx = h('rdf.indexes_list');
		$sx .= '<ul>';
		for ($r = 0; $r < count($ind); $r++) {
			if (is_dir($dir . $ind[$r])) {
				$dir_name = $ind[$r];
				if (($dir_name == '.') or ($dir_name == '..')) {
					$sx .= '';
				} else {
					$sx .= '<li>';
					$sx .= '<a href="' . PATH . MODULE . 'indexes/' . $dir_name . '" class="text-secondary">';
					$sx .= lang('rdf.index_' . $dir_name);
					$sx .= '</a>';
					$sx .= '</li>';
				}
			}
		}
		$sx .= '</ul>';
		$sx = bs(bsc($sx, 12));
		return $sx;
	}

	function btn_checkform($id)
	{
		$sx = '';
		$sx .= '<a href="' . PATH . COLLECTION . '/form_check/' . $id . '" class="btn btn-outline-primary">';
		$sx .= lang('rdf.form_check');
		$sx .= '</a>';
		return ($sx);
	}

	function btn_return($id = '', $class = '')
	{
		$this->Socials = new \App\Models\Socials();
		if ($id == '') {
			if ($this->Socials->getAccess("#ADM")) {
				$sx = '<a href="' . PATH . MODULE . 'rdf/" class="btn btn-outline-primary ' . $class . '">';
				$sx .= lang('brapci.return');
				$sx .= '</a>';
			} else {
				$sx = '<a href="' . PATH . MODULE . '" class="btn btn-outline-primary ' . $class . '">';
				$sx .= lang('brapci.return');
				$sx .= '</a>';
			}
			return $sx;
		}
		if (is_array($id)) {
			$id = $id['id_cc'];
		}
		$sx = '<a href="' . URL . '$COLLECTION/v/' . $id . '" class="btn btn-outline-primary ' . $class . '">';
		$sx .= lang('rdf.return');
		$sx .= '</a>';
		return $sx;
	}

	function form($id)
	{
		$RDFForm = new \App\Models\Rdf\RDFForm();
		$dt = $this->le($id);
		$class = $dt['concept']['c_class'];
		switch ($class) {
			case 'brapci_author':
				$tela = 'x';
			default:
				$tela = $RDFForm->form($id, $dt['concept']);
				break;
		}
		return $tela;
	}

	function recovery($dt, $fclass = '', $ido = 0)
	{
		$rsp = array();
		$fclass = trim($fclass);
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$class = trim($line['c_class']);
			if ($class == $fclass) {
				$id1 = round($line['d_r1']);
				$id2 = round($line['d_r2']);
				//echo '<br>=o=>'.$ido.'<br>=1=>'.$id1.'<br>=2=>'.$id2.'<br>';
				if ($ido == $id2) {
					array_push($rsp, array($line['d_r2'], $line['d_r1'], $line['n_name'], $line['d_p']));
				} else {
					array_push($rsp, array($line['d_r1'], $line['d_r2'], $line['n_name'], $line['d_p']));
				}
			}
		}
		return $rsp;
	}

	function remove($id, $class)
	{
		$RDFClass = new \App\Models\RDF\RDFClass();
		$RDFData = new \App\Models\RDF\RDFData();
		$class = $RDFClass->Class($class, false);
		$dt = $RDFData->where('d_r1', $id)->where('d_p', $class)->FindAll();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$idd = $line['id_d'];
			$d_r1 = $line['d_r1'] * (-1);
			$d_r2 = $line['d_r2'] * (-1);
			$d_p = $line['d_p'] * (-1);
			$RDFData->set(array('d_r1' => $d_r1, 'd_r2' => $d_r2, 'd_p' => $d_p))->where('id_d', $idd)->update();
		}
		$dt = $RDFData->where('d_r2', $id)->where('d_p', $class)->FindAll();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$idd = $line['id_d'];
			$d_r1 = $line['d_r1'] * (-1);
			$d_r2 = $line['d_r2'] * (-1);
			$d_p = $line['d_p'] * (-1);
			$RDFData->set(array('d_r1' => $d_r1, 'd_r2' => $d_r2, 'd_p' => $d_p))->where('id_d', $idd)->update();
		}
		return true;
	}


	function le_content($id)
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();
		$dt = $RDFConcept->le($id);
		$name = $dt['n_name'];
		return $name;
	}

	function le($id, $simple = 0)
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();

		$dt['concept'] = $RDFConcept->le($id);

		if ($simple == 0) {
			$RDFData = new \App\Models\Rdf\RDFData();
			$dt['data'] = $RDFData->le($id);
		}

		return ($dt);
	}

	function le_data($id)
	{
		$RDFData = new \App\Models\Rdf\RDFData();
		$dt['data'] = $RDFData->le($id);

		return ($dt);
	}

	function getClass($class)
	{
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$prop = $RDFClass->class($class);
		return $prop;
	}

	function find($sr = '', $class = '')
	{
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$RDFLiteral = new \App\Models\Rdf\RDFLiteral();
		$prop = $RDFClass->class($class);
		$id = $RDFLiteral->name($sr);
		return $id;
	}

	function get_literal($idc)
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();
		$dt = $RDFConcept->le($idc);
		$n_name = $dt['n_name'];
		return $n_name;
	}

	function get_content($dt = array(), $class = '')
	{
		$rst = array();
		$id = $dt['concept']['id_cc'];
		$dt = $dt['data'];
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			if ($line['c_class'] == $class) {
				if (trim($line['n_name']) != '') {
					array_push($rst, $line['n_name']);
				} else {
					if ($line['d_r1'] == $id) {
						array_push($rst, $line['d_r2']);
					} else {
						array_push($rst, $line['d_r1']);
					}
				}
			}
		}
		return $rst;
	}

	function c($id, $force = false)
	{
		if ($id == 0) {
			return "empty";
		}
		$dir = $this->directory($id, true);
		$file = $dir . 'name.nm';

		if ((file_exists($file)) and ($force == false)) {
			$tela = file_get_contents($file);
		} else {
			$RDFExport = new \App\Models\Rdf\RDFExport();
			$RDFExport->export($id, $force);
			if (file_exists($file)) {
				$tela = file_get_contents($file);
			} else {
				$tela = 'Content not found: ' . $id . '==' . $file . '<br>';
			}
		}

		$tela = troca($tela, '$COLLECTION', '/' . COLLECTION);
		return $tela;
	}

	function extract($dt = array(), $class = '')
	{
		return $this->recover($dt, $class);
	}
	function recover($dt = array(), $class = '')
	{
		$rst = array();
		$class = trim($class);
		$id = $dt['concept']['id_cc'];
		$dt = $dt['data'];

		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			if (trim($line['c_class']) == $class) {
				if (trim((string)$line['n_name']) != '') {
					array_push($rst, $line['n_name']);
				} else {
					if ($line['d_r1'] == $id) {
						array_push($rst, $line['d_r2']);
					} else {
						array_push($rst, $line['d_r1']);
					}
				}
			}
		}
		return $rst;
	}

	function info($id)
	{
		$sx = '';
		$id = round($id);
		$file = '.c/' . round($id) . '/.name';

		if (file_exists(($file))) {
			return file_get_contents($file);
		} else {
			return $this->export($id);
		}
		return '';
	}

	function export_index($class_name, $file = '')
	{
		$RDFData = new \App\Models\Rdf\RDFData();
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$RDFConcept = new \App\Models\Rdf\RDFConcept();

		$class = $RDFClass->Class($class_name);
		$rlt = $RDFConcept
			->join('rdf_name', 'cc_pref_term = rdf_name.id_n', 'LEFT')
			->select('id_cc, n_name, cc_use')
			->where('cc_class', $class)
			->where('cc_library', LIBRARY)
			->orderBy('n_name')
			->findAll();

		$flx = 0;
		$fi = array();
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$name = $line['n_name'];

			$upper = ord(substr(mb_strtoupper(ascii($name)), 0, 1));
			if ($flx != $upper) {
				$flx = $upper;
				$fi[$flx] = '';
			}
			$link = '<a href="' . (URL . '$COLLECTION/v/' . $line['id_cc']) . '">';
			$linka = '</a>';
			$fi[$flx] .= $link . $name . $linka . '<br>';
		}
		$s_menu = '<div id="list-example" class=""  style="position: fixed;">';
		$s_menu .= '<h5>' . lang($class_name) . '</h5>';
		$s_cont = '<div data-spy="scroll" data-target="#list-example" data-offset="0" class="scrollspy-example">';
		$cols = 0;
		foreach ($fi as $id_fi => $content) {
			//$s_menu .= '<a class="list-group-item list-group-item-action" href="#list-item-'.$id_fi.'">'.chr($id_fi).'</a>';
			//$s_menu .= '<a class="border-left" href="#list-item-'.$id_fi.'">'.chr($id_fi).'</a> ';

			$s_menu .= '<a class="border-left" href="#list-item-' . $id_fi . '"><tt>' . chr($id_fi) . '</tt></a> ';
			if (($cols++) > 6) {
				$cols = 0;
				$s_menu .= '<br>';
			}

			$s_cont .= '<h4 id="list-item-' . $id_fi . '">' . chr($id_fi) . '</h4>
						<p>' . $content . '</p>';
		}
		$s_menu .= '</div>';
		$s_cont .= '</div>';

		$sx = bsc('<div style="width: 100%;">' . $s_menu . '</div>', 1);
		$sx .= bsc($s_cont, 11);
		$sx .= '<style> body {  position: relative; } </style>';
		file_put_contents($file, $sx);
	}

	function export_all($d1 = '', $d2 = 0, $d3 = '')
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();

		$sx = '';
		$d2 = round($d2);
		$limit = 50;
		$offset = round($d2) * $limit;

		$sx .= '<h3>D1=' . $d1 . '</h3>';
		$sx .= '<h3>D2=' . $offset . '</h3>';

		$dt = $RDFConcept->select('id_cc')
			->where('cc_library', LIBRARY)
			->orderBy('id_cc')
			->limit($limit, $offset)
			->findAll($limit, $offset);
		$sx .= '<ul>';
		for ($r = 0; $r < count($dt); $r++) {
			$idc = $dt[$r]['id_cc'];
			$sx .= '<li>' . $this->export_id($idc) . '</li>';
		}
		$sx .= '</ul>';
		if (count($dt) > 0) {
			$sx .= metarefresh((PATH . 'export/rdf/' . (round($d2) + 1)), 2);
		} else {
			$sx .= bsmessage(lang('Export_Finish'));
		}
		$sx = bs(bsc($sx, 12));
		return $sx;
	}

	function export($id)
	{
		$sx = '';
		$id = round($id);
		if ($id > 0) {
			$dir = '.c/';
			if (!is_dir($dir)) {
				mkdir($dir);
			}
			$dir = '.c/' . round($id) . '/';
			if (!is_dir($dir)) {
				mkdir($dir);
			}
		} else {
			$sx .= 'ID [' . $id . '] inválido<br>';
		}

		/*************************************************************** EXPORT */
		$RDFData = new \App\Models\Rdf\RDFData();
		$RDFConcept = new \App\Models\Rdf\RDFConcept();

		$dt = $this->le($id);

		$class = $dt['concept']['c_class'];
		$txt_name = $dt['concept']['n_name'];

		/******************************************************* ARQUIVOS ********/
		$file_name = $dir . '.name';

		/********************************************************** VARIÁVEIS ****/
		$txt_journal = '';
		$txt_author = '';

		/********************************************************** WORK *********/
		switch ($class) {
			case 'Work':
				for ($w = 0; $w < count($dt['data']); $w++) {
					$dd = $dt['data'][$w];
					$dclass = $dd['c_class'];
					switch ($dclass) {
						case 'title':
							$txt_title = $dd['n_name'];
							break;

						case 'isWorkOf':
							$x = $this->le($dd['d_r2']);
							$txt_journal = $x['concept']['n_name'];
							break;

						case 'creator':
							$x = $this->le($dd['d_r2']);
							if (strlen($txt_author) > 0) {
								$txt_author .= '; ';
							}
							$txt_author .= $x['concept']['n_name'];
							break;
					}
				}
				break;
		}


		/*************************************************************** HTTP ****/
		if (substr($txt_name, 0, 4) == 'http') {
			$txt_name = '<a href="' . $txt_name . '" target="_new">' . $txt_name . '</a>';
		}

		/******************************************************** JOURNAL NAME  */
		if (strlen($txt_author) > 0) {
			$txt_name = $txt_author . '. ' . $txt_title . '. <b>[Anais...]</b> ' . $txt_journal . '.';
		}

		/******************************************************* SALVAR ARQUIVOS */
		if (strlen($txt_name) > 0) {
			file_put_contents($file_name, $txt_name);
		}

		$sx = $txt_name;
		return $sx;
	}

	function view_data($dt)
	{
		if (!is_array($dt)) {
			$dt = $this->le($dt);
		}
		$RDFdata = new \App\Models\Rdf\RDFData();
		$tela = $RDFdata->view_data($dt);
		return $tela;
	}


	function RDF_check_equivalent($to, $from)
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();
		$dt = $RDFConcept->select('id_cc, cc_class')->where('cc_class', $from)->findAll();
		if (count($dt) == 0) {
			return 0;
		} else {
			for ($r = 0; $r < count($dt); $r++) {
				$da['cc_class'] = $to;
				$RDFConcept->set($da);
				$RDFConcept->where('id_cc', $dt[$r]['id_cc'])->update();
			}
		}
	}


	function show_class($dt)
	{
		$prefix = '';
		$class = '';
		$prefix_url = '';
		if (strlen($dt['prefix_url']) > 0) {
			$prefix_url = $dt['prefix_url'];
		}
		if (strlen($dt['prefix_ref']) > 0) {
			$prefix = $dt['prefix_ref'] . ':';
		}
		if (strlen($dt['c_class']) > 0) {
			$class = $prefix . $dt['c_class'];
			$prefix_url .= '#' . $dt['c_class'];
		}
		if (strlen($prefix_url) > 0) {
			$class .= '<a href="' . $prefix_url . '" target="_new"><sup>(#)</sup></a>';
		}
		return $class;
	}

	/********************* REMISSIVAS */
	function conecpt($name, $class)
	{
		return $this->RDF_concept($name, $class);
	}
	function concept($name, $class)
	{
		return $this->RDF_concept($name, $class);
	}

	function put_literal($name, $lg = 'NaN', $force = 1)
	{
		$RDFLiteral = new \App\Models\Rdf\RDFLiteral();
		return $RDFLiteral->name($name, $lg, $force);
	}

	function RDF_concept($name, $class)
	{
		$RDPConcept = new \App\Models\Rdf\RDFConcept();

		$dt['Class'] = $class;
		$dt['Literal']['skos:prefLabel'] = $name;
		$idc = $RDPConcept->concept($dt);
		$tela = $idc;
		return $tela;
	}

	function literal($name, $lang, $idp = '', $prop = '')
	{
		if ($idp != '') {
			return $this->RDF_literal($name, $lang, $idp, $prop);
		} else {
			$RDFLiteral = new \App\Models\Rdf\RDFLiteral();
			return $RDFLiteral->name($name, $lang, true);
		}
	}

	function RDF_literal($name, $lang, $idp, $prop)
	{
		$idn = 0;
		$RDPLiteral = new \App\Models\Rdf\RDFLiteral();
		if (($prop != '') and ($idp > 0)) {
			$RDFData = new \App\Models\Rdf\RDFData();
			$idn = $RDFData->literal($idp, $prop, $name, $lang);
		}
		return $idn;
	}

	/* Igual ao propriety */
	function assoc($idp, $idt, $prop = '')
	{
		return $this->RDP_property($idp, $prop, $idt);
	}

	function propriety($idp, $prop = '', $resource = 0, $literal = 0)
	{
		return $this->RDP_property($idp, $prop, $resource, $literal);
	}

	function RDP_property($idp, $prop = '', $resource = 0, $literal = 0)
	{
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$RDFData = new \App\Models\Rdf\RDFData();
		$d = array();

		if (sonumero($prop) != $prop) {
			$prop = $RDFClass->class($prop);
		}
		$d['d_r1'] = $idp;
		$d['d_p'] = $prop;
		$d['d_library'] = LIBRARY;

		if ($resource > 0) {
			$d['d_r2'] = $resource;
			$d['d_literal'] = 0;
		}
		if ($literal > 0) {
			$d['d_r2'] = 0;
			$d['d_literal'] = $literal;
		}

		$rst = $RDFData->where('d_r1', $idp)->where('d_r2', $resource)->where('d_literal', $literal)->findAll();
		if (count($rst) == 0) {
			$RDFData->insert($d);
			return 1;
		}
		return 0;
	}




	function directory($id)
	{
		if ($id <= 0) {
			echo h('ERROR: directory ID invalid -> ' . $id, 3);
			exit;
		}
		/************************************************ */
		$idf = strzero($id, 8);
		$dir1 = '.c/' . substr($idf, 0, 2) . '/' . substr($idf, 2, 2) . '/' . substr($idf, 4, 2) . '/' . substr($idf, 6, 2) . '/';

		if (is_dir($dir1)) {
			return $dir1;
		}

		$dir2 = '/c/' . $id;
		if (is_dir($dir2)) {
			return $dir2;
		}
		$this->make_dir($dir1);
		return $dir1;
	}

	function make_dir($dir)
	{
		$paths = explode('/', trim($dir));
		$dr = '';
		for ($r = 0; $r < count($paths); $r++) {
			$subdir = trim($paths[$r]);
			if ($subdir != '') {
				$dr .= $subdir . '/';
				dircheck($dr);
			}
		}
		return true;
	}
}
