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

	function ref($dt,$type='A')
		{
			$Class = $dt['Class'];
			/********************** Authors */
			$authors = $this->ref_authors($dt['Authors']);
			$title = $this->ref_title($dt['Title']);
			$legend = $this->ref_legend($dt['Issue']);
			$ref = $authors . '. ' . $title . '. ' . $legend;
			$ref = troca($ref,'..','.');
			return($ref);
		}
	function ref_legend($dt)
		{
			$dt = (array)$dt;
			$leg = '<b>'.$dt['journal']. '</b>';
			if (isset($dt['vol']))
				{
					$leg .= ', '.$dt['vol'];
				}
			if (isset($dt['nr'])) {
				$leg .= ', ' . $dt['nr'];
			}
			if (isset($dt['year'])) {
				$leg .= ', ' . $dt['year'];
			}
			return $leg;
		}

	function ref_title($dt,$lg_pref='pt')
		{
			$title = '[...]';
			foreach($dt as $lang=>$line)
				{
					$title = $line['0'];
					break;
				}
			return $title;
		}

	function ref_authors($dt)
	{
		$sx = '';
		$etal = false;

		if (isset($dt[0])) {
			$total = count($dt);
			$authors = '';
			if ($total <= 3) {
				foreach ($dt as $idk => $name) {
					if ($authors != '') {
						$authors .= '; ';
					}
					$authors .= nbr_author(ascii($name), 2);
				}
				$authors .= '. ';
			} else {
				foreach ($dt as $idk => $name) {
					$authors .= nbr_author(ascii($name), 2);
					$authors .= '; <i>et al.</i> ';
					break;
				}
				$etal = true;
			}
			$sx .= $authors;
		}
		$sx = troca($sx, '..', '.');
		$sx = trim($sx);
		return $sx;
	}

	function show($dt, $type = 'A')
	{
		switch($dt['Class'])
			{
				case 'Proceeding':
					$tela = $this->abnt_proceeding($dt);
					return $tela;
					break;
				case 'Article':
					$tela = $this->abnt_article($dt);
					return $tela;
					break;
				case 'Issue':
					$tela = '';
					if (isset($dt['publisher'])) {
						$tela .= '<b>'.$dt['publisher']. '</b>';
					}
					if (isset($dt['is_vol']))
						{
							if ($dt['is_vol'] != 'ERRO')
								{
									$tela .= ', v. '.$dt['is_vol'];
								}
						}
					if (isset($dt['is_nr'])) {
						if ($dt['is_nr'] != 'ERRO') {
							$tela .= ', n. ' . $dt['is_nr'];
						}
					}
					if (isset($dt['is_year'])) {
						if ($dt['is_year'] != 'ERRO') {
							$tela .= ', ' . $dt['is_year'];
						}
					}
				return $tela;
				break;
			}

		switch ($type) {
			case 'B':
				$tela = $this->abnt_book($dt);
				break;
			case 'E':
				$tela = $this->abnt_proceeding($dt);
				break;
			default:
				$tela = $type;
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

		if (isset($dri['legend']) and ($dri['lenged'] != ''))
			{
				$tela .= '. '.$dri['legend'];
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
		if (isset($dt['title']))
			{
				$sx .= '<b>' . $dt['title'] . '</b>. ';
			} else {
				$sx .= '<b>::Sem título::</b>';
			}

		if (isset($dt['publisher'])) {
			$sx .= $dt['publisher'];
		} else {
			$sx .= '[<i>S.l.,s.n.</i>]: ';
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

		if (isset($dt['creator_author'])) {
			$total = count($dt['creator_author']);
			$authors = '';
			if ($total <= 3) {
				foreach ($dt['creator_author'] as $idk => $line) {
					if ($authors != '') {
						$authors .= '; ';
					}
					$authors .= nbr_author(ascii($line['name']), 2);
				}
				$authors .= '. ';
			} else {
				foreach($dt['creator_author'] as $idk=>$line)
					{
						$authors .= nbr_author(ascii($line['name']), 2);
						$authors .= '; <i>et al.</i> ';
						break;
					}
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
		} else {
		if (isset($dt['Title']))
			{
				foreach($dt['Title'] as $lang=>$title)
					{
						$dt['title'] = $title;
						break;
					}
			}
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

		if (!isset($dt['legend']))
			{
				$Issue = (array)$dt['Issue'];
				$tela .= '. <b>'. (string)$Issue['journal'].'</b>';
				if ($Issue['vol'] != '') { $tela .= ', '. (string)$Issue['vol']; }
				if ($Issue['nr'] != '') { $tela .= ', ' . $tela .= (string)$Issue['nr']; }
				$tela .= (string)$Issue['year'];
			} else {
				$tela .= '. ' . troca($dt['legend'], $dt['publisher'], '<b>' . $dt['publisher'] . '</b>');
			}



		if (isset($dt['pages'])) {
			$tela .= ', p ' . $dt['pages'];
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
