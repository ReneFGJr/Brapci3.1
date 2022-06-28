<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class Subject extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'brapci_authority.AuthorityNames';
	protected $primaryKey           = 'id_a';
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

function viewid($id)
	{
		$AuthorityNames = new \App\Models\Authority\AuthorityNames();
		$Brapci = new \App\Models\Brapci\Brapci();
		$RDF = new \App\Models\Rdf\RDF();
		$da = $RDF->le($id);

		$use = $da['concept']['cc_use'];
		if ($use > 0) {
			$da = $RDF->le($use);
		}

		$name = $da['concept']['n_name'];
		$idc = $da['concept']['id_cc'];

		$dt = $this->where('a_brapci', $idc)->findAll();
		if (count($dt) == 0) {
			$dt['a_uri'] = 'https://brapci.inf.br/v/' . $id;
			$dt['a_use'] = 0;
			$dt['a_prefTerm'] = $name;
			$dt['a_lattes'] = '';
			$dt['a_orcid'] = '';
			$dt['a_master'] = '';
			$dt['a_brapci'] = $id;
			$dt['a_genere'] = 'X';
			$dt['a_class'] = $da['concept']['cc_class'];
			$rsp = $AuthorityNames->insert($dt);
			//$this->check_id($id);
		} else {
			$dt = $dt[0];
		}


		/************************************************************* HEADER */
		$tela = $this->subject_header($dt, $da);
		return $tela;
	}

	function subject_header($dt, $rdf)
	{
		$AuthorityNames = new \App\Models\Authority\AuthorityNames();
		$this->Socials = new \App\Models\Socials();
		$sx = '';
		$sx .= '<div class="col-md-2 text-right text-end" style="border-right: 4px solid #8080FF;">
				<tt style="font-size: 100%;">Subject</tt>        
				</div>';

		$name = $rdf['concept']['n_name'];
		$nameID = $rdf['concept']['id_cc'];

		/****************************************** Atualiza Lista */
		if ($dt['a_prefTerm'] != $name) {
			$du['a_prefTerm'] = $name;
			$AuthorityNames->set($du)->where('id_a', $dt['id_a'])->update();
			$dt['a_prefTerm'] = $name;
		}

		$sa = h($dt['a_prefTerm'] . '<sup>' . $nameID . '</sup>', 4);
		if ($this->Socials->getAccess("#ADM")) {
			//$sa .= $this->btn_check($dt,30);
			$sa .= $this->btn_remissive($dt, 30);
			//$sa .= $this->btn_change_updade($dt,30);
		}

		if ($dt['a_brapci'] > 0) {
			$sa .= $this->remissive($dt['a_brapci']);
		}


		$sx .= bsc($sa, 8);

		/*********************************************** Photo */
		$photo = $this->image($dt);
		$sx .= bsc($photo, 2);

		$sx = bs($sx);
		return $sx;
	}

	function btn_remissive($dt, $size = 50)
	{
		if ($dt['a_brapci'] > 0) {
			$sx = '';
			$sx .= onclick(PATH . MODULE . 'rdf/remissive_Subject/' . $dt['a_brapci'], 800, 400);
			$sx .= bsicone('loop', $size);
			$sx .= '</span>';
		}
		return $sx;
	}

	function image()
		{
			return "";
		}

	function remissive($id)
	{
		$AuthotityRemissive = new \App\Models\Authority\AuthotityRemissive();
		$sx = $AuthotityRemissive->remissive($id);
		return($sx);
	}	
}
