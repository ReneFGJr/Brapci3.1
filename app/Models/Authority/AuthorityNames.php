<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class AuthorityNames extends Model
{
	protected $DBGroup              = 'default';
	public $table                	= 'brapci_authority.authoritynames';
	protected $primaryKey           = 'id_a';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_a', 'a_prefTerm', 'a_class',
		'a_lattes', 'a_brapci', 'a_orcid',
		'a_uri', 'a_use', 'a_master',
		'a_country',
		'a_UF', 'updated_at'
	];

	protected $typeFields        = [
		'hidden', 'string:100', 'string:100',
		'string:100', 'string:100', 'string:100',
		'string:100', 'string:1', 'string:20',
		'string:2', 'string:2', 'string:2'
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

	function getBrapciId($nome)
	{
		$RDF = new \App\Models\Rdf\RDF();
		$RDFConcept = new \App\Models\Rdf\RDFConcept();
		$RDFLiteral = new \App\Models\Rdf\RDFLiteral();
		$RDFData = new \App\Models\Rdf\RDFData();
		$sx = 'Busca ' . $nome;
		$dt = $RDFLiteral->where('n_name', $nome)->findAll();
		$RDFConcept->select('id_cc,cc_use');
		$ids = array();
		for ($r = 0; $r < count($dt); $r++) {
			$dd = $RDFData->where('d_literal', $dt[$r]['id_n'])->findAll();

			for ($q = 0; $q < count($dd); $q++) {
				array_push($ids, $dd[$q]['d_r1']);
			}
		}

		for ($i = 0; $i < count($ids); $i++) {
			if ($i == 0) {
				$RDFConcept->where('id_cc', $ids[$i]);
			} else {
				$RDFConcept->orwhere('id_cc', $ids[$i]);
			}
		}
		$da = $RDFConcept->findAll();

		if (count($da) == 1) {
			if ($da[0]['cc_use'] > 0) {
				$id_brapci = $da[0]['cc_use'];
			} else {
				$id_brapci = $da[0]['id_cc'];
			}
			return $id_brapci;
		} else {
			$id_brapci = 0;
			for ($r = 0; $r < count($da); $r++) {
				$line = $da[$r];

				if ($id_brapci == 0) {
					if ($line['cc_use'] > 0) {
						$id_brapci = $line['cc_use'];
					} else {
						$id_brapci = $line['id_cc'];
					}
				} else {
					if ($line['cc_use'] > 0) {
						if ($line['cc_use'] != $id_brapci) {
							echo "AmBIGUO";
							pre($da);
							exit;
						}
					} else {
						if ($line['id_cc'] != $id_brapci) {
							echo "AmBIGUO 2";
							pre($da);
							exit;
						}
					}
				}
			}
		}
		return $id_brapci;
	}

	function summaryCreate()
	{
		$this->select('count(*) as total');
		$dt = $this->findAll();
		print_r($dt);
	}

	function get_id_by_name($name, $dt = array())
	{
		$name = trim($name);
		$this->where('a_prefTerm', $name);
		$dt = $this->findAll();
		return $dt;
	}

	function match($id)
	{
		$sx = '';
		$this->where('id_a', $id);
		$dt = $this->findAll();
		if (count($dt) > 0) {
			$line = $dt[0];
			if ($line['a_use'] > 0) {
				$id = $line['a_use'];
				$this->where('id_a', $id);
				$dt = $this->findAll();
			}

			$name = $dt[0]['a_prefTerm'];
			if (strpos($name, ' - ') > 0) {
				$name = substr($name, 0, strpos($name, ' - '));
			}
			$Match = new \App\Models\AI\Authority\Match();
			$Match->table = $this->table;
			$sx .= $Match->check($name);
		}

		/**************************************************/
		$ROR = new \App\Models\Authority\ROR();
		$ROR->search($name);

		return $sx;
	}



	function remissive($id)
	{
		$dt = $this->le($id);
		$id = $dt['id_a'];

		$this->where('a_use', $id);
		$this->orderBy('a_prefTerm', 'asc');
		$dt = $this->findAll();
		$sx = h('Remissivas', 4);
		$sx .= '<ul class="list-remissive-authority">';
		for ($r = 0; $r < count($dt); $r++) {
			$sx .= '<li>' . $dt[$r]['a_prefTerm'] . '</li>';
		}
		$sx .= '</ul>';
		return $sx;
	}

	function viewid($id)
	{
		$Country = new \App\Models\Authority\Country();
		$RDF = new \App\Models\Rdf\RDF();

		$dt = $this->le($id);
		if ($dt['a_brapci'] > 0) {
			$dr = $RDF->le($dt['a_brapci']);
			if ($dr['concept']['cc_use'] > 0)
				{
					$dt['a_brapci'] = $dr['concept']['cc_use'];
					$dt['a_prefTerm'] = $dr['concept']['n_name'];
					$dt['a_uri'] = $RDF->link($dr);
					$this->set($dt)->where('id_a', $id)->update();
				}

			$sx = h($dt['a_prefTerm'], 1);
			$sx .= bsmessage(lang('brapci.redirect_brapci'));
			$sx .= bsmessage(lang('brapci.wait'));
			//$sx .= metarefresh(PATH . 'res/v/' . $dt['a_brapci'],0);
			return $sx;
			exit;
		}

		/******************************************** Instituição */
		$sx = '';
		$sx .= bsc(lang('brapci.prefTerm'), 11, 'small');
		$sx .= bsc(lang('brapci.Country'), 1, 'small');
		$sx .= bsc(h($dt['a_prefTerm'], 3), 11);
		$country = $dt['a_country'] . $dt['a_UF'];
		$img = $Country->flag($dt['a_country']);
		$sx .= bsc($img, 1);
		$sx .= bsc('<hr>', 12);
		$sx .= bsc($this->remissive($id), 12);


		$sx .= bsc(
			$this->btn_lattes($dt) .
				$this->btn_brapci($dt) .
				$this->btn_orcid($dt),
			12
		);

		$sx = bs($sx);
		return $sx;
	}

	function header($dt)
		{
		$AuthotityIds = new \App\Models\Authority\AuthotityIds();
		$Country = new \App\Models\Place\Country\Index();
		$Photo = new \App\Models\Authority\Photo();

			$sx = '';
			$ids = 'Lattes | Brapci | OrcID | VIAF | ISNI';

			$sx .= h($dt['a_prefTerm'],1);
			$sx = bsc($sx,12,'mt-5');

			pre($dt,false);
			/********** IDS */
			$sx .= bsc($AuthotityIds->ids($dt), 9);
				// a_brapci
				// a_lattes
				// a_orcid
				// a_genere
				// a_use


			/********* FLAG COUNTRY */
			$sx .= bsc($Country->flag($dt['a_country'],'fluid'),1);

			/********** PHOTO */
			$sx .= bsc($Photo->image($dt),2);


			return $sx;
		}

	function le($id)
	{
		$this->where('id_a', $id);
		$dt = $this->findAll();
		$dt = $dt[0];
		if ($dt['a_use'] > 0) {
			$id = $dt['a_use'];
			$this->where('id_a', $id);
			$dt = $this->findAll();
			$dt = $dt[0];
		}
		return $dt;
	}

	function btn_lattes($dt)
	{
		$Lattes = new \App\Models\Lattes\Lattes();
		$sx = '';
		if ($dt['a_lattes'] != '') {
			$sx = $Lattes->link($dt);
		} else {
			$link = PATH . 'res/admin/authority/findid/' . $dt['id_a'];
			$sx = '<a href="' . $link . '" class="btn btn-outline-primary">' . lang('brapci.lattes_check') . '</a> ';
		}
		return $sx;
	}
	function btn_brapci($dt)
	{
		$sx = '';
		if ($dt['a_brapci'] != '') {
			$link = PATH . 'res/v/' . $dt['a_brapci'];
			$sx = '<a href="' . $link . '">';
			$sx .= '<img src="' . URL . 'img/logo/brapci_200x200.png" width="40" height="40" border=0>';
			$sx .= '</a> ';
		}
		return $sx;
	}
	function btn_orcid($dt)
	{
		$sx = '';
		if ($dt['a_orcid'] != '') {
			$sx = '<span class="btn btn-outline-primary">' . lang('brapci.ordid_link') . '</span> ';
		}
		return $sx;
	}

	function edit($id)
	{
		$this->id = $id;
		$this->path = base_url(PATH . COLLECTION .  '/edit/' . $id);
		if ($id > 0) {
			$this->path_back = base_url(PATH . COLLECTION .  '/viewid/' . $id);
		} else {
			$this->path_back = base_url(PATH . COLLECTION .  '/index/');
		}

		$tela = form($this);
		return $tela;
	}
}
