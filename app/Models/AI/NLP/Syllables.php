<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Syllables extends Model
{
	protected $DBGroup              = 'ai';
	protected $table                = 'ai_syllables';
	protected $primaryKey           = 'id_sy';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_sy', 'sy_syllable', 'sy_lang'
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

	var $lib = array();

	function syllabes_language($lang)
	{
		$this->select("sy_syllable");
		$dt = $this->where('sy_lang', $lang)->findAll();
		$w = array();
		//if (count($dt) == 0)
		{
			$this->learn_pt_br();
		}
		for ($r = 0; $r < count($dt); $r++) {
			$wd = trim($dt[$r]['sy_syllable']);
			$w[$wd] = 1;
		}
		return $w;
	}

	function syllables($txt, $lang)
	{
		$tela = '';
		$txt = troca($txt, array(
			'-', '(', ')', '[', ']', '&', '$', '?', '"', '.', '!',
			',', '“', '”', '/', '=', ':', ';', '@', '$', '%', '*',
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', "'",
		), ' ');

		$sy = $this->syllabes_language($lang);

		$txt = mb_strtolower($txt);
		$wd = explode(' ', $txt);

		$tela = h('brapci.result', 2);
		$tela .= '<ul>';
		for ($r = 0; $r < count($wd); $r++) {
			if (strlen(trim($wd[$r])) > 0) {
				$link = '<a href="' . base_url(PATH . 'ai/nlp/syllable/?dd1=' . $wd[$r] . '&dd2=' . $lang) . '" target="_new">';
				$linka = '</a>';
				$tela .= '<li>' . $link . $wd[$r] . $linka . ' => ' . $this->syllabe($wd[$r], $lang) . '</li>';
			}
		}
		$tela .= '</ul>';
		return $tela;
	}


	function syllabe($txt, $lang = 'en')
	{
		$rst = '';
		$lib = $this->syllabes_language($lang);
		ksort($lib);

		$wd = explode(' ', $txt);
		for ($q = 0; $q < count($wd); $q++) {
			$w = $wd[$q];

			$wo = $w;
			$w = ascii(mb_strtolower($w));
			$w = troca($w, 'ua', 'u-a');
			$w = troca($w, 'ss', 's-s');
			$w = troca($w, 'rr', 'r-r');
			$w = troca($w, 'xc', 'x-c');
			$w = troca($w, 'xs', 'x-s');
			$w = troca($w, 'sc', 's-c');
			$we = $w;
			$wa = trim(ascii($we));
			$vogais = array('a', 'e', 'i', 'o', 'u');

			for ($r = 0; $r < count($vogais); $r++) {
				$wa = troca($wa, $vogais[$r], '$');
			}
			$loop = 0;
			
			$sl = '';
			while (strlen($we) > 0) {
				$syl = '';
				foreach ($lib as $t => $v) {
					$candidato = troca($wa, '*', '');
					$candidato = substr($candidato, strlen($candidato)-strlen($t), strlen($t));

					if ($t == $candidato) {
						if (strlen($t) >= strlen($syl))
							{
								$syl = substr($we, strlen($we)-strlen($t), strlen($t));
								switch($syl)
									{
										case 'ao': $syl = 'o'; break;
										case 'poe': $syl = 'e'; break;
									}
							}
						
						
					}
				}
				$we = trim(substr($we, 0, strlen($we)-strlen($syl)));
				$wa = trim(substr($wa, 0, strlen($wa)-strlen($syl)));

				if (strlen($sl) > 0) {
					$sl = '-'.$sl;
				}
				$sl = $syl.$sl;
				$loop++;
				if ($loop > 20) {
					$sl = $we.'='.$sl;
					//echo 'ERRO: '.$we;
					break;
				}
			}
			$sl = troca($sl, '--', '-');
			$rst .= $sl.' ';			
		}

		$tela = $rst;
		return $tela;
	}


	/*****************************************************************************************/
	function auto_learn_en($txt)
	{
		$url = 'https://www.howmanysyllables.com/words/' . $txt;
		$txt = file_get_contents($url);
		return $txt;
	}
	function learn_pt_br()
	{
		//https://www.separarensilabas.com/index-pt.php
		$lang = 'pt-BR';
		$w = array(
			'$','$r-','$n','$s-',
			'$r', '$d', '$s', '$l',
			'b$', 'b$s', 'br$', 'br$d', 'b$r', 'b$l', 'br$l', 'b$n', 'bh$', 'br$n', 'b$m', 'bl$',
			'c$', 'cr$', 'c$n', 'c$s*', 'c$*u', '$*u', 'co$s', 'co*$s', 'c$$','c$r-',
			'd$', 'd$r-', 'd$*', 'd$n', 'd$s*', 'dr$s', 'd$s', 'd$l', 'd$m','d$-',
			'c$l', 'c$m', 'c$r', 'c$r*', 'c$*',	'c$s',
			'h$', 'h$s',
			'f$', 'fl$x', 'f$r-','f$r',
			'g$ns', 'g$n', 'g$m', 'g$', 'gu$', 'gu*$', 'gu$i', 'gr$', 'g$r', 'gr$s*', 'g$s', 'g$$',
			'k$',
			'l$i', 'l$', 'l$n', 'l$r', 'l$s', 'l$z', 'lh$', 'lh$m', 'lh$n',
			'n$', 'n$s', 'n$l', 'nh$', 'n$n', 'n$m', 'n$r', 'nh$u', 'n$s', 'n$u',
			'm$', '$m', 'm$s', 'm$s*', 'm$r', 'm$n', 'm$u', 'mi$', 'm$o', 'm$*is','m$$','$$',
			'p$', 'p$n', 'p$r', 'pr$', 'pl$', 'pl$s', 'ps$', 'pc$','p$s-','p$$',
			'qu*$n', 'qu$n', 'q$$','q$$r',
			'r$n', 'r$', 'r$r', 'r$s', 'ri$', 'r$', 'r$t', 'r$m', 'r$z','r$l',
			's$s', 's$', 's$r', 's$m', 's$n', '*s$r', '*s$l', 's$l', 's$r', 's$gn', 's$*','s$$',
			't$r','t$', 't$r-', 'tr$', 'tr$m', 'tr$s', 
			't$s', 't$z', 't$*', 't$u', 't$m', 't$n', 't$o', 't$ch', 't$l','t$x',
			'v$d', 'v$l', 'v$m', 'v$r', 'v$n', 'vr$s', 'vr$', 'v$s', 'v$z', 'v$',
			'z$', 'z$r', 'z$s', 'z$l',
			'x$m', 'x$n', 'x$', 'x$s'
		);
		for ($r = 0; $r < count($w); $r++) {
			$this->syllabe_check($w[$r], $lang);
		}
		return '';
	}

	function syllabe_check($s, $lang)
	{
		$s = mb_strtolower($s);
		$s = ascii($s);
		$lang = trim($lang);
		$tela = '';
		$data['sy_syllable'] = $s;
		$data['sy_lang'] = $lang;

		$this->where('sy_syllable', $s);
		$this->where('sy_lang', $lang);

		$dt = $this->findAll();

		if (count($dt) == 0) {
			$this->insert($data);
			$tela .= 'Inserido ' . $s;
		}
		return $tela;

		//$this->append($data);
	}
}
