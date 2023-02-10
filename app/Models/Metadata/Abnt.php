<?php

namespace App\Models\Metadata;

use CodeIgniter\Model;

class Abnt extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'abtns';
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

	function show($dt, $type = 'A')
	{
		switch ($type) {
			default:
				$tela = $this->abnt_article($dt);
		}
		return $tela;
	}
	function abnt_proceeding($dt)
		{
		$id = $dt['ID'];
		$title = trim(html_entity_decode($dt['title']));
		$title = trim(mb_strtolower($title));
		$tu = mb_strtoupper($title);
		$tu = mb_substr($tu, 0, 1);
		$te = mb_substr($title, 1);
		$title = $tu . $te;

		$tela = '';
		$tela .= '<span class="abtn-article">';
		if (isset($dt['Authors'])) {
			$total = count($dt['Authors']);
			$authors = '';
			if ($total <= 3) {
				for ($r = 0; $r < count($dt['Authors']); $r++) {
					if ($authors != '') {
						$authors .= '; ';
					}
					$authors .= nbr_author($dt['Authors'][$r], 2);
				}
				$authors .= '. ';
			} else {
				$authors .= nbr_author($dt['Authors'][0], 2);
				$authors .= '; <i>et al.</i>. ';
			}
			$tela .= $authors;
		}
		if (isset($dt['id_jnl'][0]))
			{
				$jid = $dt['id_jnl'][0];
			} else {
				$jid = 0;
			}
		switch($jid)
			{
				case 75:
					$tela .= '. ' . anchor(PATH . '/benancib' . '/v/' . $id, $title) . '. ';
					break;
				default:
					$tela .= '. ' . anchor(PATH . '/proceedings' . '/v/' . $id, $title) . '. ';
					break;
			}

		$tela .= '<i>In</i>: ';
		if (isset($dt['Issue']['Journal']))
		{
			$tela .= $dt['Issue']['Journal'];
			$tela .= ', ' . $dt['Issue']['Issue_nr'] . '. ';
		}

		$tela .= '<b>Anais</b> [.] ';

		if (isset($dt['Issue']['Year']))
			{

				$tela .= $dt['Issue']['Place'];
				$tela .= ', ' . $dt['Issue']['Year'];

			} else {
				if (isset($dt['issue']['Identifier'])) {
					$tela .= $dt['issue']['Identifier'];
				}

				if (isset($dt['issue']['place'])) {
					$tela .= ' ' . $dt['issue']['place'];
				}
			}


		if (isset($dt['pages'])) {
			$tela .= ', p ' . $dt['pages'];
		}
		$tela .= '.';

		/******** LIMPAR */
		$tela = troca($tela, ' ,', ',');
		$tela = troca($tela, ' .', '.');
		while (strpos($tela, '..')) {
			$tela = troca($tela, '..', '.');
		}
		$tela = troca($tela, '[.]', '[...].');
		$tela .= '</span>';
		return $tela;

		}
	function abnt_article($dt)
	{
		pre($dt,false);
		$title = trim(html_entity_decode($dt['title']));
		$title = trim(mb_strtolower($title));
		$tu = mb_strtoupper($title);
		$tu = mb_substr($tu, 0, 1);
		$te = mb_substr($title, 1);
		$title = $tu . $te;

		$tela = '';
		$tela .= '<span class="abtn-article">';
		if (isset($dt['Authors'])) {
			$total = count($dt['Authors']);
			$authors = '';
			if ($total <= 3) {
				for ($r = 0; $r < count($dt['Authors']); $r++) {
					if ($authors != '') {
						$authors = '; ';
					}
					$authors .= nbr_author($dt['Authors'][$r], 2);
				}
				$authors .= '. ';
			} else {
				$authors .= nbr_author($dt['Authors'][0], 2);
				$authors .= '; <i>et al.</i>. ';
			}
			$tela .= $authors;
		}
		$tela .= '. ' . $title;

		if (isset($dt['Journal']))
			{
				$tela .= '. <b>' . nbr_author($dt['Journal'], 7) . '</b>';
			}

		if (isset($dt['issue']['issue_vol']) > 0) {
			$tela .= ', ' . $dt['issue']['issue_vol'];
		}
		if (isset($dt['issue']['issue_nr']) > 0) {
			$tela .= ', ' . $dt['issue']['issue_nr'];
		}
		if (isset($dt['issue']['year']))
			{
				$tela .= ', ' . trim($dt['issue']['year']);
			} else {
				$tela .= ', ' . '[????]';
			}

		if (isset($dt['pages'])) {
			$tela .= ', p ' . $dt['pages'];
		}
		$tela .= '.';

		/******** LIMPAR */
		$tela = troca($tela, ' ,', ',');
		$tela = troca($tela, ' .', '.');
		while (strpos($tela, '..')) {
			$tela = troca($tela, '..', '.');
		}
		$tela .= '</span>'.cr();
		return $tela;
	}
}
