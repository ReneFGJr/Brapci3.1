<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesProducaoLivro extends Model
{
	protected $DBGroup              = 'lattes';
	protected $table                = 'lattesproducao_livro';
	protected $primaryKey           = 'id_lv';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_lv ', 'lv_author', 'lv_brapci_rdf',
		'lv_authors', 'lv_title', 'lv_ano',
		'lv_url', 'lv_doi', 'lv_issn',
		'lv_journal', 'lv_vol', 'lv_nr',
		'lv_place', 'lv_country', 'lv_event',
		'lv_natureza','lv_tipo'
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
		$dt = $this->select('count(*) as total, lv_author')
			->where('lv_author', $id)
			->groupBy('lv_author')
			->findAll();
		if (count($dt) > 0) {
			return $dt[0]['total'];
		}
		return 0;
	}

	function producao($id)
	{
		$tela = '';
		$dt = $this->where('lv_author', $id)->orderBy('lv_ano', 'desc')->findAll();
		$tela .= '<ol>';
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$tela .= '<li>' . $line['lv_authors'] . '. ' . $line['lv_title'] . '. ';
			$tela .= '<b>' . $line['lv_journal'] . '</b>';
			if (strlen($line['lv_vol']) > 0) {
				$tela .= ', ' . $line['lv_vol'];
			}
			if (strlen($line['lv_nr']) > 0) {
				$tela .= ', ' . $line['lv_nr'];
			}
			$tela .= ', ' . $line['lv_ano'];
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
		$arti = (array)$prod['LIVROS-E-CAPITULOS'];
		$arti = (array)$arti['LIVROS-PUBLICADOS-OU-ORGANIZADOS'];
		$arti = (array)$arti['LIVRO-PUBLICADO-OU-ORGANIZADO'];

		for ($r = 0; $r < count($arti); $r++) {
			$line = (array)$arti[$r];

			$dados = (array)$line['DADOS-BASICOS-DO-LIVRO'];
			$dados = (array)$dados['@attributes'];
			$p = array();
			$p['lv_tipo'] = $this->type($dados['TIPO']);
			$p['lv_natureza'] = $this->natureza($dados['NATUREZA']);
			$p['lv_author'] = $id;
			$p['lv_brapci_rdf'] = 0;
			$p['lv_ano'] = $dados['ANO'];
			$p['lv_doi'] = $dados['DOI'];
			$p['lv_title'] = $dados['TITULO-DO-LIVRO'];
			$p['lv_url'] = $dados['HOME-PAGE-DO-TRABALHO'];
			$p['lv_lang'] = $Lang->code($dados['IDIOMA']);
			$p['lv_country'] = $dados['PAIS-DE-PUBLICACAO'];

			$deta = (array)$line['DETALHAMENTO-DO-LIVRO'];
			$deta = (array)$deta['@attributes'];

			$p['lv_isbn'] = $deta['ISBN'];
			$vl = trim($deta['NUMERO-DE-VOLUMES']);
			$nr = trim($deta['NUMERO-DE-PAGINAS']);
			if ($vl != '') {
				$vl = 'v. ' . $vl;
			}
			if ($nr != '') {
				$nr = 'p. ' . $nr;
			}
			$p['lv_place'] = $deta['CIDADE-DA-EDITORA'];
			$p['lv_vol'] = $vl;
			$p['lv_nr'] = $nr;

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
			$p['lv_authors'] = $authn;
			$p['lv_author_total'] = count($auth);

			$rst = $this->where('lv_author', $id)
				->where('lv_title', $p['lv_title'])
				->where('lv_ano', $p['lv_ano'])
				->findAll();

			if (count($rst) == 0) {
				$this->insert($p);
			}
		}
		return 'ok';
	}

	function type($t)
		{
			switch($t)
				{
					case 'LIVRO_ORGANIZADO_OU_EDICAO':
						return "O";
						break;
					case 'LIVRO_PUBLICADO':
						return "L";
						break;
					default:
						echo "TIPO: $t";
						exit;
				}
		}
	function natureza($t)
	{
		switch ($t) {
			case 'NAO_INFORMADO':
				return "N";
				break;
			case 'ANAIS':
				return "E";
				break;
			case 'OUTRA':
				return "O";
				break;
			case 'LIVRO':
				return "L";
				break;
			case 'TEXTO_INTEGRAL':
				return "T";
				break;
			case 'COLETANEA':
				return "CL";
				break;
			default:
				echo "NATUREZA: $t";
				exit;
				break;
		}
	}
}