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
		'bs_rdf_id', 'bs_nome', 'bs_lattes'
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

	function le($id)
		{
			$dt = $this->find($id);
			$dt['bs_image'] = URL.'/img/genre/no_image_he.jpg';
			$dt['bs_content'] = 'Sem biografia identificada';
			$dt['bs_brapci'] = anchor(PATH.'autoriry/v/'.$dt['bs_rdf_id'],'ver prefil na Brapci','class="btn btn-outline-primary"');

			return $dt;
		}

	function bolsista_list()
	{
		$RDF = new \App\Models\Rdf\RDF();
		$sx = '';
		$ord = get("order");

		$dt = $this->orderBy($ord)->findAll();

		$sx .= h(lang('pq.total') . ': ' . count($dt), 6);
		$sx .= '<table class="table table-striped">';
		$sx .= '<tr class="small">
				<th width="3%">' . lang('pq.nr') . '</th>
				<th width="50%">' . '<a href="?order=bs_nome">' . lang('pq.bs_nome') . '</a></th>
				<th width="10%">' . '<a href="?order=bs_lattes">' . lang('pq.bs_lattes') . '</a></th>
				</tr>' . cr();
		$nr = 0;

		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$linka = '</a>';
			$link = '<a href="' . PATH . MODULE . 'pq/viewid/?id=' . $line['id_bs'] . '" class="text-secondary">*';
			$nr++;
			$sx .= '<tr>';
			$sx .= '<td>' . $nr . '</td>';
			$sx .= '<td>' . $link . $line['bs_nome'] . $linka . '</td>';
			$sx .= '<td>' . $link . $line['bs_lattes'] . $linka . '</td>';
			$sx .= '</tr>';
			$sx .= cr();
		}
		$sx .= '</table>';
		return $sx;
	}
}
