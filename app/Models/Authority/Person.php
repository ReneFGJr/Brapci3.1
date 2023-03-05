<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class Person extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'brapci_authority.authoritynames';
	protected $primaryKey           = 'id_a';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_a','a_genere','a_prefTerm'
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

	function viewid($id)
	{
		$AuthotityIds = new \App\Models\Authority\AuthotityIds();
		$AuthorityNames = new \App\Models\Authority\AuthorityNames();
		$Metadata = new \App\Models\Base\Metadata();
		$Affiliation = new \App\Models\Authority\Affiliation();

		$RDF = new \App\Models\Rdf\RDF();
		$da = $RDF->le($id);
		if ($da['concept']['cc_use'] > 0)
			{
				if (get("redirect")!='')
					{
						echo "OPS - redirect ".$id.'<='.get('redirect');
						exit;
					}

				$id2 = $da['concept']['cc_use'];
				$sx = metarefresh(PATH.'/autoridade/v/'.$id2.'?redirec='.$id);
				return $sx;
			}

		$dm = $Metadata->metadata($da, true);

		$dm['edit'] = '';
		$dm['edit'] = '<span onclick="newwin(\'' . PATH . '/popup/remissive/' . $id . '\',800,800);" class="handle">' . bsicone('list') . '</span>';

		/*********************** Affiliation */
		$dm['Affiliation'] = $Affiliation->show_list($dm);

		$fld = array('Gender', 'Imagem');
		foreach($fld as $idc=>$name)
			{
				if(!isset($dm[$name])) { $dm[$name] = ''; }
			}

		if ($dm['Imagem'] != '')
			{
				foreach($dm['Imagem'] as $id=>$path)
					{
						$dm['photo'] = '<img src="'.$path.'" class="img-thumb img-fluid">';
					}
			} else {
				$dm['photo'] = $this->image($dm['Gender']);
			}

		$dm['data'] = $da['data'];
		$dm['concept'] = $da['concept'];
		$dm['vdata'] = $RDF->view_data($da);

		//pre($dm);

		$sx = view('Authority/Person', $dm);
		return $sx;
	}

	function viewidX($id, $loop = 0)
	{
		$AuthotityIds = new \App\Models\Authority\AuthotityIds();
		$AuthorityNames = new \App\Models\Authority\AuthorityNames();

		$RDF = new \App\Models\Rdf\RDF();
		$da = $RDF->le($id);

		$use = $da['concept']['cc_use'];
		if ($use > 0) {
			if ($loop > 4) {
				echo "OPS - Falhar geral LOOP";
				exit;
			}
			return $this->viewid($use, ($loop++));
		}

		$name = $da['concept']['n_name'];
		$idc = $da['concept']['id_cc'];

		$dt = $this->where('a_brapci', $idc)->findAll();
		if (count($dt) == 0) {
			$dt['a_uri'] = '';
			$dt['a_use'] = 0;
			$dt['a_prefTerm'] = $name;
			$dt['a_lattes'] = '';
			$dt['a_orcid'] = '';
			$dt['a_master'] = '';
			$dt['a_brapci'] = $id;
			$dt['a_genere'] = 'X';
			$rsp = $AuthorityNames->insert($dt);
			$this->check_id($id);
		} else {
			$dt = $dt[0];
		}


		/************************************************************* HEADER */
		$tela = $this->person_header($dt, $da);

		/******************************************** RECHECK */
		if (get("act") == 'check') {
			$tela .= $this->check_id($id);
			return $tela;
		}
		if (get("act") == 'change') {
			$tela .= $this->change_id($id);
			return $tela;
		}


		/******************************************** CHANGE */
		//$this->


		/************************************************************* Lattes */
		$link0 = $AuthotityIds->brapciID($dt['a_brapci']);

		$link1 = '';

		if ($dt['a_brapci'] != 0) {
			$link = base_url(PATH . MODULE . '/index/lattes/import_lattes/' . trim($dt['a_lattes']) . '/');
			$link2 = '<a href="' . $link . '" target="_new' . $dt['a_lattes'] . '">';
			$link2 .= bsicone('import', 32);
			$link2 .= '</a>';
		} else {
			$link2 = '';
		}
		//brapci_200x200.png

		//} else {
		//			$tela .= anchor(base_url(PATH . MODULE. '/admin/authority/findid/' . $dt['a_brapci']));

		/****************** COLLABORATORS */
		$Collaborators = new \App\Models\Authority\Collaboration();
		$Collab = $Collaborators->collaborations($id);

		$tela .= bs(bsc(trim($link0 . ' ' . $link1 . ' ' . $link2), 12));

		/*************************************************** BRAPCI */
		if (($dt['a_brapci'] == 0) and (strpos($dt['a_uri'], 'brapci.inf.br'))) {
			$txt = $dt['a_uri'];
			while (strpos(' ' . $txt, '/') > 0) {
				$pos = strpos($txt, '/');
				$txt = substr($txt, $pos + 1, strlen($txt));
			}
			$sql = "update " . $this->table . " set a_brapci = $txt where id_a = " . $id;
			$this->query($sql);
			$dt['a_brapci'] = $txt;
		}
		return $tela;
	}

	function PersonPublications($id)
	{
		$LattesProducao = new \App\Models\Lattes\LattesProducao();
		$tela = $LattesProducao->producao($id);
		/*
			$RDF = new \App\Models\Rdf\RDF();
			$dt = $RDF->le($id,0,'brapci');
			$tela .= $RDF->view_data($dt);
			*/
		return $tela;
	}

function remissive($id)
	{
		$AuthotityRemissive = new \App\Models\Authority\AuthotityRemissive();
		$sx = $AuthotityRemissive->remissive($id);
		return($sx);
	}

 function person_header($dt,$rdf)
	{
		$AuthorityNames = new \App\Models\Authority\AuthorityNames();
		$this->Socials = new \App\Models\Socials();
		$AuthotityIds = new \App\Models\Authority\AuthotityIds();
		$sx = '';
		$sx .= '<div class="col-md-2 text-right text-end" style="border-right: 4px solid #8080FF;">
				<tt style="font-size: 100%;">Person</tt>
				</div>';

		$name = $rdf['concept']['n_name'];
		$nameID = $rdf['concept']['id_cc'];

		/****************************************** Atualiza Lista */
		if ($dt['a_prefTerm'] != $name)
			{
				$du['a_prefTerm'] = $name;
				$AuthorityNames->set($du)->where('id_a', $dt['id_a'])->update();
				$dt['a_prefTerm'] = $name;
			}

		$sa = h($dt['a_prefTerm'].' <sup style="font-size: 0.5em;">('.$nameID.')</sup>',4);
		$sa .= $AuthotityIds->lattesID($dt['a_lattes'],30);
		if  ($this->Socials->getAccess("#ADM"))
			{
				$sa .= $this->btn_check($dt,30);
				$sa .= $this->btn_remissive($dt,30);
				$sa .= $this->btn_change_updade($dt,30);
			}

		if ($dt['a_brapci'] > 0)
			{
				$sa .= $this->remissive($dt['a_brapci']);
			}


		$sx .= bsc($sa,8);

		/*********************************************** Photo */

		$sx = bs($sx);
		return $sx;
	}
function btn_check($dt,$size=50)
	{
		if ($dt['a_brapci'] > 0)
		{
			$sx = '';
			$sx .= '<a href="'.(PATH. COLLECTION.'/v/'.$dt['a_brapci'].'?act=check').'" class="btn btn-xs btn-default" title="Check Remissive">';
			$sx .= bsicone('flag',$size);
			$sx .= '</a>';
		}
		return $sx;
	}

function btn_change_updade($dt,$size=50)
	{
		if ($dt['a_brapci'] > 0)
		{
			$sx = '';
			$sx .= '<a href="'.(PATH.COLLECTION .'/v/'.$dt['a_brapci'].'?act=change').'" class="btn btn-xs btn-default" title="Change Remissive">';
			$sx .= bsicone('change',$size);
			$sx .= '</a>';
		}
		return $sx;
	}

function btn_remissive($dt,$size=50)
	{
		if ($dt['a_brapci'] > 0)
		{
			$sx = '';
			$sx .= onclick(PATH.MODULE.'rdf/remissive_Person/'.$dt['a_brapci'],800,400);
			$sx .= bsicone('reload',$size);
			$sx .= '</span>';
		}
		return $sx;
	}

function image($dt)
	{
		if (is_array($dt))
			{
				$genere = $dt['a_genere'];
			} else {
				$genere = substr($dt,0,1);
			}


		switch($genere)
			{
				case 'M':
				$file = 'img/genre/no_image_he.jpg';
				break;

				case 'F':
				$file = 'img/genre/no_image_she.jpg';
				break;

				default:
				$file = 'img/genre/no_image_she_he.jpg';
				break;
			}

		$img = URL.'/'.$file;
		$img = '<img src="'.$img.'" class="img-thumbnail img-fluid">';
		return $img;
	}

function change_id($id)
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();
		$RDF = new \App\Models\Rdf\RDF();
		$dt = $RDFConcept->select('cc_use, id_cc')->where('cc_use',$id)->findAll();
		for ($r=0;$r < count($dt);$r++)
			{
				$line = $dt[$r];
				$RDF->change($line['cc_use'],$line['id_cc']);
				echo '===>'.$line['id_cc'].'==>'.$line['cc_use'].'<br>';
			}
	}

function check_id($id)
	{
		$sx = '';
		$this->RDF = new \App\Models\Rdf\RDF();
		$dt = $this->RDF->le($id);
		$da = $this->where('a_brapci',$id)->findAll();
		if (count($da) == 0)
			{
				return "";
			}
		$da = $da[0];


		$sx .= h(lang('brapci.person_checklist'),4);
		$sx .= '<ul>';
		/********************************************** Check Genere */
		$sx .= '<li class="text-success">';
		$sx .= $this->check_genere($dt,$da);
		$sx .= '</li>';

		/********************************************** Check Loop */
		$sx .= '<li class="text-success">';
		$sx .= $this->check_loop_rdf($dt,$da);
		$sx .= '</li>';

		$sx .= '</ul>';

		$sx = bs(bsc($sx,12));
		return $sx;
	}

function check_loop_rdf($dt,$da)
	{

	}

function check_duplicate_rdf($dt,$da)
	{
		$RDFData = new \App\Models\Rdf\RDFData();
		$tot = $RDFData->check_duplicates();
		$sx = lang('brapci.check').' '.lang('brapci.duplicate');
		if ($tot > 0)
			{
				$sx .= '<span class="text-success"> <b>'.lang('brapci.update').' </b>('.$tot.')</span>';
			} else {
				$sx .= '<span class="text-danger"> <b>'.lang('brapci.bypass').'</b></span>';
			}
		return $sx;
	}

function check_genere($dt,$da)
	{
		$id = $da['id_a'];
		$dg = array('M'=>0,'F'=>0,'X'=>0);
		$gn = $this->RDF->recover($dt,'hasGender');
		for ($r=0;$r < count($gn);$r++)
			{
				$t = $this->RDF->c($gn[$r]);;
				$g = substr($t,0,1);
				if ($g == '')
					{
						print_r($gn[$r]);
						exit;
						$g = 'X';
					}
				if (isset($dg[$g]))
				{
					$dg[$g]++;
				}
			}

		if ($dg['M']+$dg['F']+$dg['X'] == 0)
			{
				echo "OPS";
				exit;
			}

		if (($dg['M'] > $dg['F']) and ($dg['M'] > $dg['X']))
			{
				$gt = 'M';
			}
		if (($dg['F'] > $dg['M']) and ($dg['F'] > $dg['X']))
			{
				$gt = 'F';
			}
		if ($dg['M'] == $dg['F'])
			{
				$gt = 'X';
			}
		$sx = lang('brapci.check').' '.lang('brapci.genere');
		if ($da['a_genere'] != $gt)
			{
				$this->set('a_genere',$gt);
				$this->where('id_a',$id)->update();
				$sx .= '<span class="text-success"> ['.$gt.'] <b>'.lang('brapci.update').'</b></span>';
			} else {
				$sx .= '<span class="text-danger"> ['.$gt.'] <b>'.lang('brapci.bypass').'</b></span>';
			}
		return $sx;
	}


}
