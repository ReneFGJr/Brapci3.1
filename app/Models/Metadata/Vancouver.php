<?php

namespace App\Models\Metadata;

use CodeIgniter\Model;

class Vancouver extends Model
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

	//https://usp.br/sddarquivos/arquivos/vancouver.pdf

	function show($dt, $type = 'A')
	{
		$tela = '';
		switch ($dt['Class']) {
			case 'Article':
				$tela = $this->vancouver_article($dt);
				return $tela;
				break;
			case 'Issue':
				if (isset($dt['publisher']))
					{
						$tela .= $dt['publisher'];
					}

				if (isset($dt['is_vol'])) {
					if ($dt['is_vol'] != 'ERRO') {
						$tela .= ', ' . $dt['is_vol'];
					}
				}
				if (isset($dt['is_nr'])) {
					if ($dt['is_nr'] != 'ERRO') {
						$tela .= '(' . $dt['is_nr'].')';
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
				$tela = $this->vancouver_book($dt);
				break;


		}
		return $tela;
	}
	function vancouver_proceeding($dt)
		{
		$id = $dt['ID'];
		if (isset($dt['title']))
			{
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
			$authors = $dt['authors'].'.';
			$authors = troca($authors,'$$','.');
			$authors = troca($authors,'$',';');

			$tela .= $authors;
		}
		/**************** */
		if (isset($dt['issue_id']))
		{
			$issueNR = $dt['issue_id'];
			$Issue = new \App\Models\Base\Issues();
			$dri = $Issue->le($issueNR);
			$jid = $dri['id_jnl'];
		} else {
			$dri = [];
			$jid = 0;
			return "[erro vancouver]";
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
		$tela .= mb_strtoupper($dri['jnl_name']);
		if ($dri['is_nr'] != '')
			{$tela .= ', '.$dri['is_nr'].'.'; }
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
		if ($dri['is_editor'] != '') {
			{
				$tela .= ', ' . $dri['is_editor'];
			} if ($dri['jnl_editor'] != '') {
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


	function vancouver_book($dt)
	{
		$sx = '';
		$sx .= $this->authors($dt);
		if ($sx != '') { $sx .= ' '; }
		if (isset($dt['title'])) {
			$sx .= '<b>' . $dt['title'] . '</b>. ';
		} else {
			$sx .= '<b>::Sem título::</b>';
		}

		if (isset($dt['publisher']))
			{
				$sx .= $dt['publisher'];
			} else {
				$sx .= '[<i>S.l.,s.n.</i>]: ';
			}

		if (isset($dt['year'])) {
			$sx .= ', '.$dt['year'] . '.';
		} else {
			$sx .= ', [????]' . '.';
		}
		$sx = troca($sx, '..','.');
		return $sx;
	}

	function vancouver_chapter($dt)
	{
		$sx = '';
		$sx .= $this->authors($dt);

		if (isset($dt['title'])) {
		if ($sx != '')
			{
				$sx .= ' '.trim($dt['title']);
			} else {
				$title = trim($dt['title']);
				$pos = round(strpos($title,' '));
				while (($pos < 4) and ($pos > 0))
					{
						$title = substr($title,0,$pos).'_'.substr($title,$pos+1,strlen($title));
						$pos = strpos($title, ' ');
					}
				if ($pos == 0)
					{
						$pos = strlen($title);
					}
					$sx1 = substr($title,0,$pos);
					$sx2 = substr($title,$pos+1,strlen($title));
					$title = mb_strtoupper($sx1).' '.trim($sx2);
					$title = troca($title,'_',' ');

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
		$authors = '';
		if (isset($dt['creator_author'])) {
			$total = count($dt['creator_author']);
			if ($total <= 3) {
				foreach ($dt['creator_author'] as $idk => $line) {
					if ($authors != '') {
						$authors .= '; ';
					}
					$authors .= nbr_author(ascii($line['name']), 3);
				}
			} else {
				if ($total > 0)
				{
					$authors .= nbr_author(ascii($dt['creator_author'][0]['name']), 3);
					$authors .= '; <i>et al.</i> ';
					$etal = true;
				}
			}
			$sx .= $authors;
		}
		$sx = troca($sx,'..','.');
		$sx = trim($sx);
		return $sx;
		}


	function vancouver_article($dt)
	{
		if (!isset($dt['title']))
			{
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

		if (isset($dt['publisher']))
			{
				$tela .= '. ' . nbr_title($dt['publisher'], 7);
			}

		/*********************************** ANO */
		if (isset($dt['year'])) {
			$tela .= '. ' . trim($dt['year']);
		} else {
			$tela .= '. ' . '[????]';
		}


		/******************************** VOL */
		if (isset($dt['Issue']['is_vol']) > 0) {
			$nr = trim($dt['Issue']['is_vol']);
			$tela .= ';' . trim(troca($dt['Issue']['is_vol'],'v.',''));
			}
		/******************************** NR **/
		if (isset($dt['Issue']['is_nr']) > 0) {
			$nr = trim($dt['Issue']['is_nr']);
			$tela .= '('. trim(troca($dt['Issue']['is_nr'],'n.','')).')';
		}

		if (isset($dt['pages'])) {
			$tela .= ':'.trim(troca($dt['pages'],'p.','')) ;
		}
		$tela .= '.';

		if (isset($dt['doi'])) {
			$tela .= ' doi:' . $dt['doi'];
		}
		$tela .= '.';

		/******** LIMPAR */
		$tela = troca($tela, ' ,', ',');
		$tela = troca($tela, ';.', '.');
		$tela = troca($tela, ' .', '.');
		while (strpos($tela, '..')) {
			$tela = troca($tela, '..', '.');
		}
		$tela .= cr();
		return $tela;
	}
}
