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
		if (isset($dt['title'])) {
			$title = trim(html_entity_decode($dt['title']));
			$title = trim(mb_strtolower($title));
			$tu = mb_strtoupper($title);
			$tu = mb_substr($tu, 0, 1);
			$te = mb_substr($title, 1);
			$title = $tu . $te;
		} else {
			$title = ':::none:::';
		}

		$tela = '';
		$tela .= '<span class="abtn-article">';
		if (isset($dt['authors'])) {
			$authors = $dt['authors'] . '.';
			$authors = troca($authors, '$$', '.');
			$authors = troca($authors, '$', ';');

			$tela .= $authors;
		}
		/**************** */
		if (isset($dt['issue_id'])) {
			$issueNR = $dt['issue_id'];
			$Issue = new \App\Models\Base\Issues();
			$dri = $Issue->le($issueNR);
			$jid = $dri['id_jnl'];
		} else {
			$dri = [];
			$jid = 0;
			return "[erro abnt]";
		}

		switch ($jid) {
			case 75:
				$tela .= '. ' . anchor(PATH . '/benancib' . '/v/' . $id, $title) . '. ';
				break;
			default:
				$tela .= '. ' . anchor(PATH . '/proceedings' . '/v/' . $id, $title) . '. ';
				break;
		}

		$tela .= '<i>In</i>: ';
		$tela .= mb_strtoupper($dri['jnl_name']);
		if ($dri['is_nr'] != '') {
			$tela .= ', ' . $dri['is_nr'] . '.';
		}
		if ($dri['is_place'] != '') {
			$tela .= ', ' . $dri['is_place'];
		}
		if ($dri['is_year'] != '') {
			$tela .= ', ' . $dri['is_year'];
		}

		$tela .= '. <b>Anais</b> [.] ';

		if ($dri['is_place'] != '') {
			$tela .= ' ' . $dri['is_place'];
		}
		if ($dri['is_editor'] != '') { {
				$tela .= ', ' . $dri['is_editor'];
			}
			if ($dri['jnl_editor'] != '') {
				$tela .= ', ' . $dri['jnl_editor'];
			} else {
				# none
			}
		}
		if ($dri['is_year'] != '') {
			$tela .= ', ' . $dri['is_year'];
		}

		if (isset($dt['pages'])) {
			$tela .= ', p ' . $dt['pages'];
		}
		$tela .= '.';

		/******** LIMPAR */
		$tela = troca($tela, ' ,', ',');
		$tela = troca($tela, ' .', '.');
		$tela = troca($tela, ';.', '.');
		while (strpos($tela, '..')) {
			$tela = troca($tela, '..', '.');
		}
		$tela = troca($tela, '[.]', '[...]');
		$tela .= '</span>';

		return $tela;
	}


	function abnt_book($dt)
	{
		$sx = '';
		$sx .= $this->authors($dt);
		if ($sx != '') {
			$sx .= ' ';
		}
		$sx .= '<b>' . $dt['title'] . '</b>. ';
		if (isset($dt['editora_local'])) {
			$sx .= $dt['editora_local'] . ': ';
		} else {
			$sx .= '[<i>S.l.</i>]: ';
		}
		if (isset($dt['editora'])) {
			$sx .= $dt['editora'] . '';
		} else {
			$sx .= '[<i>s.n.</i>]';
		}

		if (isset($dt['year'])) {
			$sx .= ', ' . $dt['year'] . '.';
		} else {
			$sx .= ', [????]' . '.';
		}
		$sx = troca($sx, '..', '.');
		return $sx;
	}

	function abnt_chapter($dt)
	{
		$sx = '';
		$sx .= $this->authors($dt);

		if (isset($dt['title'])) {
			if ($sx != '') {
				$sx .= ' ' . trim($dt['title']);
			} else {
				$title = trim($dt['title']);
				$pos = round(strpos($title, ' '));
				while (($pos < 4) and ($pos > 0)) {
					$title = substr($title, 0, $pos) . '_' . substr($title, $pos + 1, strlen($title));
					$pos = strpos($title, ' ');
				}
				if ($pos == 0) {
					$pos = strlen($title);
				}
				$sx1 = substr($title, 0, $pos);
				$sx2 = substr($title, $pos + 1, strlen($title));
				$title = mb_strtoupper($sx1) . ' ' . trim($sx2);
				$title = troca($title, '_', ' ');

				$sx .= trim($title);
			}
		}

		$sx .= '. In: ';
		if (isset($dt['books'])) {
			$sx .= $dt['books'];
		} else {
			$sx .= '[Fonte não localizada]';
		}
		return $sx;
	}

	function authors($dt)
	{
		$sx = '';
		$etal = false;
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
				$authors .= '; <i>et al.</i> ';
				$etal = true;
			}
			$sx .= $authors;
		}
		$sx = troca($sx, '..', '.');
		$sx = trim($sx);
		return $sx;
	}


	function abnt_article($dt)
	{
		if (!isset($dt['title'])) {
			$dt['title'] = '::none::';
		}
		$title = trim(html_entity_decode($dt['title']));
		$title = trim(mb_strtolower($title));
		$tu = mb_strtoupper($title);
		$tu = mb_substr($tu, 0, 1);
		$te = mb_substr($title, 1);
		$title = $tu . $te;

		$tela = '';
		$tela .= $this->authors($dt);
		$tela .= '. ' . $title;

		if (isset($dt['Journal'])) {
			$tela .= '. <b>' . nbr_title($dt['Journal'], 7) . '</b>';
		}
		/******************************** VOL */
		if (isset($dt['issue']['issue_vol']) > 0) {
			$nr = trim($dt['issue']['issue_vol']);
			if (strlen($nr) > 0) {
				if (strpos(' ' . $dt['issue']['issue_vol'], 'v.')) {
					$tela .= ', ' . $dt['issue']['issue_vol'];
				} else {
					$tela .= ', v.' . $dt['issue']['issue_vol'];
				}
			}
		}
		/******************************** NR **/
		if (isset($dt['legend']) and ($dt['legend'] != '')) {
			$tela .= '. ' . $dt['legend'];
		} else {

			if (isset($dt['issue']['Issue_nr']) > 0) {
				$nr = trim($dt['issue']['Issue_nr']);
				if (strlen($nr) > 0) {
					if (strpos(' ' . $dt['issue']['Issue_nr'], 'n.')) {
						$tela .= ', ' . $dt['issue']['Issue_nr'];
					} else {
						$tela .= ', n.' . $dt['issue']['Issue_nr'];
					}
				}
			}

			if (isset($dt['issue']['year'])) {
				$tela .= ', ' . trim($dt['issue']['year']);
			} else {
				$tela .= ', ' . '[????]';
			}

			if (isset($dt['pages'])) {
				$tela .= ', p ' . $dt['pages'];
			}
		}
		$tela .= '.';

		/******** LIMPAR */
		$tela = troca($tela, ' ,', ',');
		$tela = troca($tela, ';.', '.');
		$tela = troca($tela, ' .', '.');
		while (strpos($tela, '..')) {
			$tela = troca($tela, '..', '.');
		}
		//$tela .= cr();
		return $tela;
	}
}
