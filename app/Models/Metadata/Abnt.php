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

	function short($dt, $url = True)
	{
		$ref = '';
		#$URL = 'https://brapci.inf.br/index.php/res/v/';
		$URL = 'https://hdl.handle.net/20.500.11959/brapci/';

		$Class = $dt['Class'];
		$link = '<a href="'.$URL.$dt['ID'].'" target="_blank" class="link">';
		$linka = '</a>';

		switch($Class)
			{
				case 'Issue':
					return "ISSUE - Construção";
			}

		/******************************************************* */
		$TIT = [];
		$title = '';
		if (isset($dt['Title']))
			{
				$T = (array)$dt['Title'];
				if (isset($T['pt']))
					{
						$title = $T['pt'][0];
					} elseIf (isset($T['es'])) {
						$title = $T['es'][0];
					} elseif (isset($T['en'])) {
						$title = $T['en'][0];
					} elseif (isset($T['fr'])) {
						$title = $T['fr'][0];
					} else {
						foreach($T as $lang=>$t)
							{
								if ($title == '') { $title = $t; }
							}
					}
			} else {
				$title = '(sem título)';
			}

		$title = $link . $title . $linka;

		/********************** Authors */
		if (isset($dt['Authors']))
		{
			$authors = $this->ref_authors($dt['Authors']);

			$legend = $this->ref_legend($dt['Issue']);
			$ref = $authors . '. ' . $title . '. ' . $legend;
			if ($url) {
				$ref .= '. Acesso em: ' . date("d") . '/' . mes_abreviado(date("m")) . '/' . date("Y");
				$ref .= '. Disponível em: ' . '<a href="' . $URL . $dt['ID'] . '" target="_blank">' . $URL . $dt['ID'] . '</a>';
			}
			$ref = troca($ref, '..', '.');
			$ref = troca($ref, ', ,', ',');
		}



		if ($ref == '') { $ref = $link."Class: ".$Class.$linka; }
		return ($ref);
	}

	function ref($dt, $url = True)
	{
		#$URL = 'https://brapci.inf.br/index.php/res/v/';
		$URL = 'https://hdl.handle.net/20.500.11959/brapci/';

		$Class = $dt['Class'];

		/********************** Authors */
		$authors = $this->ref_authors($dt['Authors']);
		$title = $this->ref_title($dt['Title']);
		$legend = $this->ref_legend($dt['Issue']);
		$ref = $authors . '. ' . $title . '. ' . $legend;
		if ($url) {
			$ref .= '. Acesso em: ' . date("d") . '/' . mes_abreviado(date("m")) . '/' . date("Y");
			$ref .= '. Disponível em: ' . '<a href="' . $URL . $dt['ID'] . '" target="_blank">' . $URL . $dt['ID'] . '</a>';
		}
		$ref = troca($ref, '..', '.');
		$ref = troca($ref, ', ,', ',');
		return ($ref);
	}

	function ref_legend($dt)
	{
		$dt = (array)$dt;
		$leg = '<b>' . $dt['journal'] . '</b>';
		if (isset($dt['vol'])) {
			if (isset($dt['vol'])) {
				if ($dt['vol'] == sonumero($dt['vol'])) {
					$leg .= ', v. ' . $dt['vol'];
				} else {
					$leg .= ', ' . $dt['vol'];
				}
			}
		}
		if (isset($dt['nr'])) {
			if ($dt['nr'] != '') {
				if ($dt['nr'] == sonumero($dt['nr'])) {
					$leg .= ', v. ' . $dt['nr'];
				} else {
					$leg .= ', ' . $dt['nr'];
				}
			}
		}
		if (isset($dt['year'])) {
			$leg .= ', ' . $dt['year'];
		}
		return $leg;
	}

	function ref_title($dt, $lg_pref = 'pt')
	{
		$title = '[...]';
		foreach ($dt as $lang => $line) {
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
		switch ($dt['Class']) {
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
					$tela .= '<b>' . $dt['publisher'] . '</b>';
				}
				if (isset($dt['is_vol'])) {
					if ($dt['is_vol'] != 'ERRO') {
						$tela .= ', v. ' . $dt['is_vol'];
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
		# Norma 6023:2018
		$id = $dt['ID'];

		$tela = '';
		$tela .= $this->authors($dt);

		if (isset($dt['title'])) {
			$tela .= ' <b>' . $dt['title'] . '</b>. ';
		} else {
			$tela .= ' <b>::Sem título::</b>';
		}
		/**************** */
		$tela = trim($tela);

		$vol = '';
		$nr = '';
		$eve = '';
		$nrR = '';
		$year = '';
		$local = '';

		// SILVA, Maria A.; OLIVEIRA, João P. Bibliometria e ciência da informação: um estudo de caso.
		//In: CONGRESSO BRASILEIRO DE CIÊNCIA DA INFORMAÇÃO, 27., 2024, Recife.
		// Anais do XXVII Congresso Brasileiro de Ciência da Informação. Recife: ANCIB, 2024. p. 123-130.

		if (isset($dt['Issue']))
				{
					$vol = trim($dt['Issue']['is_vol']);
					$nr = trim($dt['Issue']['is_nr']);
					$year = trim($dt['Issue']['is_year']);
					$eve = trim($dt['Issue']['publisher']);
					$nrR = trim($dt['Issue']['is_vol_roman']);
				}

		$tela .= ' <i>In</i>: ';
		$tela .= mb_strtoupper($eve);
		if ($nr != '') {
			$tela .= ', ' . $nr . '.';
		}
		if ($year != '') {
			$tela .= ', ' . $year;
		}
		if ($local != '') {
			$tela .= ', ' . $local;
		}

		$tela .= '. <b>Anais</b> [.] ';
		$tela .= $nrR.' '.$eve;
		$tela .= ', ' . $year;

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
		if (isset($dt['title'])) {
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
				foreach ($dt['creator_author'] as $idk => $line) {
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
			if (isset($dt['Title'])) {
				foreach ($dt['Title'] as $lang => $title) {
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

		if (!isset($dt['legend'])) {
			if (isset($dt['Issue'])) {
				$Issue = (array)$dt['Issue'];
				$tela .= '. <b>' . (string)$Issue['journal'] . '</b>';
				if ($Issue['vol'] != '') {
					$tela .= ', ' . (string)$Issue['vol'];
				}
				if ($Issue['nr'] != '') {
					$tela .= ', ' . $tela .= (string)$Issue['nr'];
				}
				$tela .= (string)$Issue['year'];
			} else {
				$tela .= 'Erro ISSUE';
			}
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
