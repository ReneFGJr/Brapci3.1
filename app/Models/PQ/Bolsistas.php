<?php

namespace App\Models\PQ;

use CodeIgniter\Model;

class Bolsistas extends Model
{
	protected $DBGroup              = 'pq';
	protected $table                = 'bolsistas';
	protected $primaryKey           = 'id_bs';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_bs', 'bs_nome', 'bs_lattes','bs_rdf_id'
	];

	protected $typeFields        = [
		'hidden', 'string*','string:50*', 'integer'
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

	var $id = 0;
	var $path = '';
	var $path_back = '';


	function edit($id)
	{
		$this->id = $id;
		$this->path = PATH . '/popup/pq_bolsista_edit?id=' . $id . '&';
		$this->path_back = 'wclose';
		$sx = h(lang('pq.pq_editar'), 2);
		$sx .= form($this);

		$sx = bs(bsc($sx, 12));
		return ($sx);
	}

	function le($id)
	{
		$LattesExtrator = new \App\Models\LattesExtrator\Index();
		$Lattes = new \App\Models\Lattes\Index();

		$dt = $this->find($id);
		if ($dt == '')
			{
				echo "ERRO: registro de lattes vazio";
				return array();
			}
		$dt['bs_image'] = URL . '/img/genre/no_image_he.jpg';
		$dt['bs_content'] = 'Sem biografia identificada<br>';
		$dt['bs_content'] .= $LattesExtrator->btn_coletor($dt['bs_lattes']);
		$dt['bs_content'] .= $Lattes->link($dt['bs_lattes']);

		$dt['bs_brapci'] = anchor(PATH . COLLECTION . '/v/' . $dt['bs_rdf_id'], 'ver perfil na Brapci', 'class="btn btn-outline-primary"');
		$dt['bs_brapci'] = anchor(PATH . COLLECTION .  '/v/' . $dt['bs_rdf_id'], 'ver perfil na Brapci', 'class="btn btn-outline-primary"');

		return $dt;
	}

	function bolsista_list()
	{
		$RDF = new \App\Models\Rdf\RDF();
		$perfil_edit = true;

		$sx = '';
		$ord = get("order");
		if ($ord == '') { $ord = 'bs_nome'; }

		$dt = $this->orderBy($ord)->findAll();

		$sx .= h(lang('pq.total') . ': ' . count($dt), 6);
		$sx .= '<table class="table table-striped">';
		$sx .= '<tr class="small">
				<th width="3%">' . lang('pq.nr') . '</th>
				<th width="50%">' . '<a href="?order=bs_nome">' . lang('pq.bs_nome') . '</a></th>
				<th width="10%">' . '<a href="?order=bs_lattes">' . lang('pq.bs_lattes') . '</a></th>';

		if ($perfil_edit) {
			$sx .= '<th width="3%">' . onclick(PATH.'/popup/pq_bolsista_edit/0?id=0',800,400) . bsicone('plus') . '</a></th>';
		}
		$sx .= '</tr>' . cr();
		$nr = 0;

		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$linka = '</a>';
			$link = '<a href="' . PATH . MODULE . 'pq/viewid/' . $line['bs_lattes'] . '" class="text-secondary">*';
			$nr++;
			$sx .= '<tr>';
			$sx .= '<td>' . $nr . '</td>';
			$sx .= '<td>' . $link . $line['bs_nome'] . $linka . '</td>';
			$sx .= '<td>' . $link . $line['bs_lattes'] . $linka . '</td>';
			if ($perfil_edit) {
				$sx .= '<th width="3%">' . onclick(PATH.'/popup/pq_bolsista_edit/'. $line['id_bs'].'?id='.$line['id_bs'],800,400) . bsicone('edit') . '</span></th>';
			}
			$sx .= '</tr>';
			$sx .= cr();
		}
		$sx .= '</table>';
		return $sx;
	}
}
