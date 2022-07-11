<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesProducao extends Model
{
	protected $DBGroup              = 'lattes';
	protected $table                = 'LattesProducao';
	protected $primaryKey           = 'id_lp';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_lp ', 'lp_author', 'lp_brapci_rdf',
		'lp_authors', 'lp_title', 'lp_ano',
		'lp_url', 'lp_doi', 'lp_issn',
		'lp_journal', 'lp_vol', 'lp_nr',
		'lp_place'
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

	function selo($total, $desc)
	{
		$sx = '';
		$sx .= '<div class="text-center p-1 mb-3" style="width: 100%; border: 1px solid #000; border-radius: 10px;  line-height: 80%;">';
		$sx .= '<span style="font-size: 16px;">' . $total . '</span>';
		$sx .= '<br>';
		$sx .= '<b style="font-size: 12px; ">' . $desc . '</b>';
		$sx .= '</div>';
		return $sx;
	}

	function resume($id)
	{
		$dt = $this->select('count(*) as total, lp_author')
			->where('lp_author', $id)
			->groupBy('lp_author')
			->findAll();
		if (count($dt) > 0) {
			return $dt[0]['total'];
		}
		return 0;
	}

	function producao($id)
	{
		$tela = '';
		$dt = $this->where('lp_author', $id)->orderBy('lp_ano', 'desc')->findAll();
		$tela .= '<ol>';
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$tela .= '<li>' . $line['lp_authors'] . '. ' . $line['lp_title'] . '. ';
			$tela .= '<b>' . $line['lp_journal'] . '</b>';
			if (strlen($line['lp_vol']) > 0) {
				$tela .= ', ' . $line['lp_vol'];
			}
			if (strlen($line['lp_nr']) > 0) {
				$tela .= ', ' . $line['lp_nr'];
			}
			$tela .= ', ' . $line['lp_ano'];
			$tela .= '</li>';
		}
		$tela .= '</ol>';
		return $tela;
	}

	function producao_xml($id)
	{
		$Lang = new \App\Models\Language\Lang();
		$LattesExtrator = new \App\Models\LattesExtrator\Index();
		$file = $LattesExtrator->fileName($id);
		if (!file_exists($file)) {
			echo "ERRO NO ARQUIVO " . $file;
			exit;
		}
		$xml = (array)simplexml_load_file($file);

		$xml = (array)$xml;
		$prod = (array)$xml['PRODUCAO-BIBLIOGRAFICA'];
		$arti = (array)$prod['ARTIGOS-PUBLICADOS'];
		$arti = (array)$arti['ARTIGO-PUBLICADO'];

		for ($r = 0; $r < count($arti); $r++) {
			$line = (array)$arti[$r];

			$dados = (array)$line['DADOS-BASICOS-DO-ARTIGO'];
			$dados = (array)$dados['@attributes'];
			$p = array();
			$p['lp_author'] = $id;
			$p['lp_brapci_rdf'] = 0;
			$p['lp_ano'] = $dados['ANO-DO-ARTIGO'];
			$p['lp_doi'] = $dados['DOI'];
			$p['lp_title'] = $dados['TITULO-DO-ARTIGO'];
			$p['lp_url'] = $dados['HOME-PAGE-DO-TRABALHO'];
			$p['lp_lang'] = $Lang->code($dados['IDIOMA']);

			$deta = (array)$line['DETALHAMENTO-DO-ARTIGO'];
			$deta = (array)$deta['@attributes'];

			$p['lp_journal'] = $deta['TITULO-DO-PERIODICO-OU-REVISTA'];
			$p['lp_issn'] = $deta['ISSN'];
			$vl = trim($deta['VOLUME']);
			$nr = trim($deta['FASCICULO']);
			if ($vl != '') {
				$vl = 'v. ' . $vl;
			}
			if ($nr != '') {
				$nr = 'n. ' . $nr;
			}
			$p['lp_place'] = $deta['LOCAL-DE-PUBLICACAO'];
			$p['lp_vol'] = $vl;
			$p['lp_nr'] = $nr;

			/****************** AUTHORES */
			$auth = (array)$line['AUTORES'];
			$authn = '';
			if (count($auth) == 1) {
				$autx = $auth;
				$auth = array();
				array_push($auth, $autx);
			}

			for ($ar = 0; $ar < count($auth); $ar++) {
				$aaa = (array)$auth[$ar];
				$authp = $aaa['@attributes'];
				if (strlen($authn) > 0) {
					$authn .= '; ';
				}
				$nome = (string)$authp['NOME-COMPLETO-DO-AUTOR'];
				$authn .= nbr_author($nome, 1);
			}
			$p['lp_authors'] = $authn;
			$p['lp_author_total'] = count($auth);

			$rst = $this->where('lp_author', $id)
				->where('lp_title', $p['lp_title'])
				->where('lp_ano', $p['lp_ano'])
				->where('lp_journal', $p['lp_journal'])
				->findAll();

			if (count($rst) == 0) {
				$this->insert($p);
			}
		}
		return 'ok';
	}
}