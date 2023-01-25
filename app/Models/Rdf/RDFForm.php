<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RdfForm extends Model
{
	protected $DBGroup              = 'rdf';
	protected $table                = 'rdf_form_class';
	protected $primaryKey           = 'id_sc';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_sc',
		'sc_class',
		'sc_group',

		'sc_ord',
		'sc_propriety',
		'sc_range',

		'sc_ativo',
		'sc_visible',
		'sc_library',
		'sc_global',


	];

	protected $typeFields        = [
		'hidden',
		'sql:id_c:c_class:rdf_class:c_type=\'C\' order by c_class*',
		'sql:gr_name:gr_name:rdf_form_groups*',

		'[1-99]',
		'sql:id_c:c_class:rdf_class:c_type=\'P\' order by c_class*',
		'sql:id_c:c_class:rdf_class:c_type=\'C\' order by c_class*',

		'sn*',
		'sn*',
		'string:100*',
		'sn*',

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

	function forms()
	{
		$dt = $this->select("*")
			->Join('rdf_class', 'sc_propriety = id_c', 'left')
			->Join('rdf_form_groups', 'gr_name = sc_group', 'left')
			->where('sc_library', LIBRARY)
			->OrWhere('sc_library', 0)
			->orderBy('gr_ord, sc_class, sc_group, c_class', 'asc')
			->FindAll();

		$sx = '';
		$xgr = '';
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$id = $line['sc_class'];
			$gr = $line['sc_group'];
			if ($gr != $xgr) {
				$sx .= '<h3>Grupo: ' . lang('rdf.' . $gr) . '</h3>';
				$xgr = $gr;
			}
			$link = onclick(PATH . MODULE . COLLECTION . '/rdf/form/edit/' . $line['id_sc'], 800, 500);
			$linka = '</span>';
			$act = '';
			$acta = '';
			if ($line['sc_ativo'] = 0) {
				$act = '<s>';
				$acta = '</s>';
			}
			$sx .= '<li>' . $act . $link . $line['c_class'] . $linka . $acta . ' (' . $line['sc_library'] . ')</li>';
		}
		return $sx;
	}

	function form($id, $dt)
	{
		$RDF = new \App\Models\Rdf\RDF();
		$class = $dt['cc_class'];

		if ($dt['cc_class'] < 0) {
			$sx = $RDF->E404();
			return $sx;
		}

		$this->form_import($class);

		if (get("action") == "DEL") {
			$check = md5($id . MODULE);
			$this->exclude($id, $check);
		}

		$sx = '';
		$js1 = '';
		$sx .= '<div class="small">Class</div>';
		$sx .= h($RDF->show_class($dt), 2, 'btn-primary [bn]');
		$sx .= '<a href="' . URL . MODULE . COLLECTION . '/v/' . $id . '" class="small">' . lang('rdf.return') . '</a>';
		$sx .= ' | ';
		$sx .= onclick(URL . MODULE . '/rdf/concept/exclude/' . $id, 800, 400, 'text-danger');
		$sx .= lang('rdf.delete') . '</span>';
		//$sx .= $RDF->link($dt,'btn btn-outline-primary btn-sm').'return'.'</a>';;


		/* complementos */
		switch ($class) {
			default:
				$cp = 'n_name, cpt.id_cc as idcc, d_p as prop, id_d, d_literal, id_n';
				$sqla = "select $cp from rdf_data as rdata
			INNER JOIN rdf_class as prop ON d_p = prop.id_c
			INNER JOIN rdf_concept as cpt ON d_r2 = id_cc
			INNER JOIN rdf_name on cc_pref_term = id_n
			WHERE d_r1 = $id and d_r2 > 0";
				$sqla .= ' union ';
				$sqla .= "select $cp from rdf_data as rdata
			LEFT JOIN rdf_class as prop ON d_p = prop.id_c
			LEFT JOIN rdf_concept as cpt ON d_r2 = id_cc
			LEFT JOIN rdf_name on d_literal = id_n
			WHERE d_r1 = $id and d_r2 = 0";
				/*****************/
				$sql = "select * from rdf_form_class
			INNER JOIN rdf_class as t0 ON id_c = sc_propriety
			LEFT JOIN (" . $sqla . ") as t1 ON id_c = prop
			LEFT JOIN rdf_class as t2 ON sc_propriety = t2.id_c
			LEFT JOIN rdf_form_groups ON sc_group = gr_name
			where sc_class = $class and (sc_library = " . LIBRARY . " OR sc_library = 0) and (sc_visible = 1)
			order by gr_ord, sc_ord, id_sc, t0.c_order";

				$rlt =  (array)$this->db->query($sql)->getResult();

				$sx .= '<table width="100%" cellpadding=5>';
				$js = '';
				$xcap = '';
				$xgrp = '';
				for ($r = 0; $r < count($rlt); $r++) {
					$line = (array)$rlt[$r];
					$grp = $line['sc_group'];
					if ($xgrp != $grp) {
						$sx .= '<tr>';
						$sx .= '<td colspan=3 class="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000;" align="center">';
						$sx .= lang('rdf.' . $grp);
						$sx .= '</td>';
						$sx .= '</tr>';
						$xgrp = $grp;
					}
					$cap = msg($line['c_class']);

					/************************************************************** LINKS EDICAO */
					$idc = $id; /* ID do conceito */
					$form_id = $line['id_sc']; /* ID do formulário */
					/* $class =>  ID da classe */

					$furl = (PATH . MODULE . COLLECTION . '/rdf/form/' . $class . '/' . $line['id_sc'] . '/' . $id);

					$class = trim($line['c_class']);
					$link = onclick(PATH . MODULE . '/rdf/data/edit/' . $class . '/' . $line['id_sc'] . '/' . $id, 800, 500, 'btn-primary rounded');
					//$link = onclick(PATH.MODULE.'rdf/form/'.$line['id_sc'],800,600,'btn-primary round');
					$linka = '</span>';
					$sx .= '<tr>';
					$sx .= '<td width="25%" align="right" valign="top">';

					if ($xcap != $cap) {
						$sx .= '<nobr><i>' . lang('rdf.' . $line['c_class']) . '</i></nobr>';
						$sx .= '<td width="1%" valign="top">' . $link . '&nbsp;+&nbsp;' . $linka . '</td>';
						$xcap = $cap;
					} else {
						$sx .= '&nbsp;';
						$sx .= '<td>-</td>';
					}
					$sx .= '</td>';

					/***************** Editar campo *******************************************/
					$sx .= '<td style="border-bottom: 1px solid #808080;">';
					if ($line['n_name'] != '') {
						$linkc = '<a href="' . (PATH . COLLECTION . '/v/' . $line['idcc']) . '" class="middle">';
						$linkca = '</a>';
						if (!isset($line['idcc'])) {
							$linr['idcc'] = '';
						}
						if ($line['idcc'] == '') {
							$linkc = '';
							$linkca = '';
						}

						switch ($line['c_class']) {
							case 'hasSummary':
								$txt = trim($line['n_name']);
								if (substr($txt,0,2)== '[{')
									{
										$RDFExport = new \App\Models\Rdf\RDFExport();
										$sx .= $RDFExport->BookChapher($line,$dt['n_name'],$id);
									} else {
										$sx .= $txt;
									}

								break;
							default:
								$sx .= $linkc . '' . $line['n_name'] . $linkca;

						}
					}

					/********************** Editar caso texto */
					$elinka = '</a>';
					if ($line['idcc'] == '') {
						$onclick = onclick(PATH . MODULE . '/rdf/text/' . $line['d_literal'], $x = 600, $y = 400, $class = "btn btn-outline-warning p-0 text-blue supersmall rounded");
						$elink = $onclick;
						$sx .= '&nbsp; ' . $elink . '&nbsp;ed&nbsp;' . $elinka;
						$sx .= '</span>';
					}

					/************* Excluir Texto/Conceito Associado */
					$onclick = onclick(PATH . MODULE . '/rdf/data/exclude/' . $line['id_d'], $x = 600, $y = 300, $class = "btn btn-outline-danger p-0 text-red supersmall rounded");
					$link = $onclick;
					$sx .= '&nbsp; ' . $link . '&nbsp;X&nbsp;' . $elinka;
					$sx .= '</span>';

					$sx .= '</td>';
					$sx .= '</tr>';
				}
				$sx .= '</table>';
				break;
		}
		return ($sx);
	}

	function form_ed($id, $class)
	{
		$act = get("form");
		if (($id == 0) and ($act == '')) {
			$_POST['sc_class'] = $class;
			$_POST['sc_library'] = LIBRARY;
		}

		$this->id = $id;
		$this->pre = 'rdf.';
		$this->path = PATH . COLLECTION . '/form';
		$this->path_back = 'wclose';
		$sx = form($this);
		return $sx;
	}

	function form_import($id_class, $force = false)
	{
		$RDF = new \App\Models\Rdf\Rdf();
		$RDFData = new \App\Models\Rdf\RDFData();
		$RDFConcept = new \App\Models\Rdf\RDFConcept();

		$dt = $RDFConcept
			->select('cc_class, d_p')
			->where('cc_class', $id_class)
			->join('rdf_data', 'id_cc = d_r1')
			->groupBy('cc_class, d_p')
			->FindAll();

		$tot = 0;
		for ($r = 0; $r < count($dt); $r++) {
			$dd['sc_class'] = $dt[$r]['cc_class'];
			$dd['sc_propriety']  = $dt[$r]['d_p'];
			$dd['sc_range'] = 0;
			$dd['sc_ativo'] = 1;
			$dd['sc_ord'] = 1;
			$dd['sc_library'] = LIBRARY;
			$dd['sc_group'] = '';
			$dd['sc_global'] = LIBRARY;

			$this->where('sc_propriety', $dt[$r]['d_p']);
			$this->where('sc_class', $dt[$r]['cc_class']);
			$this->where('sc_library', LIBRARY);
			$da = $this->findAll();

			if (count($da) == 0) {
				$this->set($dd)->insert();
				$tot++;
			}
		}
		if ($tot > 0) {
			$sx = bsmessage('Imported ' . $tot . ' forms', 1);
		} else {
			$sx = bsmessage('Nothing to import forms', 1);
		}
		/*************** RETURN */
		$sx .= '<a href="' . PATH . '/rdf/class/view/' . $id_class . '" class="btn btn-outline-primary">' . lang('rdf.return') . '</a>';
		$sx = bs(bsc($sx, 12));
		return $sx;
	}

	function le($id)
	{
		$dt = $this->find($id);
		return $dt;
	}

	function edit_form($id)
	{
		$this->id = $id;
		$this->path_back = 'close';
		$sx = form($this);
		return $sx;
	}

	function edit($form_class, $prop_name = '', $form_id = '', $register, $di4 = '', $di5 = '')
	{
		$Socials = new \App\Models\Socials();
		$sx = '';

		$dt = $this->le($form_id);

		/*************************** RANGE */
		$range = $dt['sc_range'];

		if ($range == 0) {
			if ($Socials->getAccess("#ADM")) {
				$id = $dt['id_sc'];
				$sx = metarefresh(PATH . MODULE . '/rdf/form/edit/' . $id . '?msg=range_not_found', 0);
				return $sx;
			} else {
				echo bsmessage("<font style='color: red'>RANGE not defined</font>", 3);

				if ($Socials->getAccess("#ADM")) {
					$sx = '<a href="' . PATH . MODULE . '/rdf/form/edit/' . $id . '?msg=range_not_found">';
					$sx .= 'EDIT';
					$sx .= '</a>';
				}
				return $sx;
			}
		}

		$RDFClass = new \App\Models\Rdf\RDFClass();
		$dr = $RDFClass->find($range);

		$range = $dr['c_class'];

		switch ($range) {
			case 'Image':
				$RDFFormImage = new \App\Models\Rdf\RDFFormImage();
				$sx .= $RDFFormImage->edit($form_class, $prop_name, $form_id, $register, $range);
				break;
			case 'Text':
				$RDFFormText = new \App\Models\Rdf\RDFFormText();
				$sx .= $RDFFormText->edit($form_class, $prop_name, $form_id, $register, $range);
				break;
			default:
				$RDFFormVC = new \App\Models\Rdf\RDFFormVC();
				$sx .= $RDFFormVC->edit($form_class, $prop_name, $form_id, $register, $range);
		}
		$sx = bs(bsc($sx, 12));
		return $sx;
	}

	function exclude($id, $ac = '')
	{
		$check = md5($id . MODULE);
		$RDFData = new \App\Models\Rdf\RDFData();
		$dt = $RDFData->find($id);

		$sx = '';
		$dd = array();

		/* Confirm */
		if ($ac == $check) {
			echo wclose();
			echo bsmessage(lang('Success'), 1);
			$RDFData->delete($id);
			exit;
		}

		/* Concept */
		if ($dt['d_r2'] > 0) {
			$RDFConcept = new \App\Models\Rdf\RDFConcept();
			$dd = $RDFConcept->le($dt['d_r2']);
			$sx .= '<div class="mt-2">' . lang('rdf.concept') . '</div>';
		}
		/* Text */
		if (($dt['d_r2'] == 0) and ($dt['d_literal'] > 0)) {
			$RDFLiteral = new \App\Models\Rdf\RDFLiteral();
			$dd = $RDFLiteral->find($dt['d_literal']);
			$sx .= '<div class="mt-2">' . lang('rdf.term') . '</div>';
		}
		/* Mostra Nome */
		if ($dd == '') {
			echo h(lang('rdf.404'), 1);
			echo h(lang('rdf.concept_not_found'), 3);
			exit;
		}

		$sx .= h($dd['n_name'], 3) . '<hr>';

		/* Mostra mensagem de exclusão */
		$sx .= '<center>' . h(lang('rdf.find.rdf_exclude_confirm'), 4, 'text-danger') . '</center>';
		$sx .= '
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-default" onclick="wclose();" data-dismiss="modal">' . lang('find.cancel') . '</button>
			<a href="' . (PATH . MODULE . '/rdf/data/exclude/' . $id . '/' . $check) . '" class="btn btn-warning" id="submt">' . lang('find.confirm_exclude') . '</a>
			</div>
		';
		/**************** fim ******************/
		return $sx;
	}

	function form_class_edit($id, $class = '')
	{
		$RDFPrefix = new \App\Models\Rdf\RDFPrefix();
		$sql = "
			SELECT id_sc, sc_class, sc_propriety, sc_ord, id_sc,
			t1.c_class as c_class, t2.prefix_ref as prefix_ref,
			t3.c_class as pc_class, t4.prefix_ref as pc_prefix_ref, sc_ativo,
			sc_group, sc_library, sc_visible
			FROM rdf_form_class
			INNER JOIN rdf_class as t1 ON t1.id_c = sc_propriety
			LEFT JOIN rdf_prefix as t2 ON t1.c_prefix = t2.id_prefix
			LEFT JOIN rdf_form_groups ON sc_group = gr_name

			LEFT JOIN rdf_class as t3 ON t3.id_c = sc_range
			LEFT JOIN rdf_prefix as t4 ON t3.c_prefix = t4.id_prefix

			where sc_class = $class
			AND ((sc_global =1 ) or (sc_library = 0) or (sc_library = " . LIBRARY . "))
			order by gr_ord, sc_ord";


		$rlt = (array)$this->db->query($sql)->getResult();
		$sx = '';
		$sx .= '<h4>' . msg("Form") . '</h4>';

		$xgr = '';

		/********************* Constroe a Tabela */
		for ($r = 0; $r < count($rlt); $r++) {
			$line = (array)$rlt[$r];

			/******** LINKS */
			$link = onclick(PATH . MODULE . '/rdf/form/edit/' . $line['id_sc'], 800, 500);
			$linka = '</span>';

			/******** HEADERS */
			$gr = $line['sc_group'];
			if ($gr != $xgr) {
				$sx .= bsc($gr, 12, 'h3');
				$xgr = $gr;
			}

			/******* DELETED */
			$style = "";
			if ($line['sc_ativo'] == 0) {
				$style = ' style=" text-decoration: line-through;" ';
			}
			/******************************************* ORDER */
			$ord = $line['sc_ord'];
			$ord .= ' ';

			/************************************* CLASS */
			$prop = $RDFPrefix->prefixn($line);

			/********************************* SPACENAME */
			$spacename = $link;
			$spacename .= '<b>' . ($line['c_class']) . '</b>' . ' (' . $prop . ')';
			$spacename .= $linka;
			/************************************* NAME **/


			/* RANGE */
			$dt = array();
			$dt['c_class'] = $line['pc_class'];
			$dt['prefix_ref'] = $line['pc_prefix_ref'];

			/* RANGE */
			$range = $RDFPrefix->prefixn($dt);
			$dt = array();
			if ($line['sc_visible'] == 1) {
				$enable = bsicone('eye');
			} else {
				$enable = bsicone('eye-closed');
			}

			$sx .= bsc($ord, 1, 'text-center', $style);
			$sx .= bsc($spacename, 5, '', $style);
			$sx .= bsc($range, 5, '', $style);
			$sx .= bsc($enable, 1, 'text-center', $style);
		}

		/************************************************************************ BOTOES */
		$btn_new = '';
		if ($class > 0) {
			//$link = onclick(PATH.MODULE.'rdf/formss/'.$id.'/0',800,600,"btn btn-outline-primary");
			$link = onclick(PATH . COLLECTION . '/form/edit/0/' . $class, 800, 500, "btn btn-outline-primary");
			$linka = '</span>';

			$btn_new = $link . lang('rdf.new_propriety_field') . $linka;
			$btn_new .= ' &nbsp; ';
		}
		/************** BTN */
		$btn_check = '<a href="' . PATH . MODULE . '/rdf/form/check/' . $id . '" class="btn btn-outline-primary">';
		$btn_check .= lang('rdf.check_form');
		$btn_check .= '</a>';

		$sr = bs(bsc($btn_new, 4) . bsc($btn_check, 4));
		$sx = $sr . bs($sx, 'container-fluid');

		return ($sx);
	}
}
