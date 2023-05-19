<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesProducaoCapitulo extends Model
{
	protected $DBGroup              = 'lattes';
	protected $table                = 'lattesproducao_capitulo';
	protected $primaryKey           = 'id_lvc';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_lvc ', 'lvc_author', 'lvc_brapci_rdf',
		'lvc_authors', 'lvc_title', 'lvc_ano',
		'lvc_url', 'lvc_doi', 'lvc_issn', 'lvc_isbn',
		'lvc_journal', 'lvc_vol', 'lvc_nr',
		'lvc_place', 'lvc_country', 'lvc_event',
		'lvc_natureza', 'lvc_tipo', 'lvc_livro_organizadores',
		'lvc_livro_title'
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

	function zerezima_dados_xml($id)
	{
		$this->where('lvc_author', $id)->delete();
		return true;
	}

	function resume($id)
	{
		$dt = $this->select('count(*) as total, lvc_author')
			->where('lvc_author', $id)
			->groupBy('lvc_author')
			->findAll();
		if (count($dt) > 0) {
			return $dt[0]['total'];
		}
		return 0;
	}

	function producao($id)
	{
		$tela = '';
		$dt = $this->where('lvc_author', $id)->orderBy('lvc_ano', 'desc')->findAll();
		$tela .= '<ol>';
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$tela .= '<li>' . $line['lvc_authors'] . '. ' . $line['lvc_title'] . '. ';
			$tela .= '<b>' . $line['lvc_journal'] . '</b>';
			if (strlen($line['lvc_vol']) > 0) {
				$tela .= ', ' . $line['lvc_vol'];
			}
			if (strlen($line['lvc_nr']) > 0) {
				$tela .= ', ' . $line['lvc_nr'];
			}
			$tela .= ', ' . $line['lvc_ano'];
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
		if (isset($xml['PRODUCAO-BIBLIOGRAFICA'])) {
			$prod = (array)$xml['PRODUCAO-BIBLIOGRAFICA'];
			if (isset($prod['LIVROS-E-CAPITULOS'])) {
				$arti = (array)$prod['LIVROS-E-CAPITULOS'];
				if (isset($arti['LIVROS-PUBLICADOS-OU-ORGANIZADOS'])) {
					if (isset($arti['CAPITULOS-DE-LIVROS-PUBLICADOS'])) {
						$arti = (array)$arti['CAPITULOS-DE-LIVROS-PUBLICADOS'];
						$arti = (array)$arti['CAPITULO-DE-LIVRO-PUBLICADO'];

						if ((count($arti) > 0) and (!isset($arti[0]))) {
							$arti2 = array();
							$arti2[0] = $arti;
							$arti = $arti2;
						}

						for ($r = 0; $r < count($arti); $r++) {
							$line = (array)$arti[$r];

							$dados = (array)$line['DADOS-BASICOS-DO-CAPITULO'];
							$dados = (array)$dados['@attributes'];
							$p = array();
							$p['lvc_tipo'] = $this->type($dados['TIPO']);
							$p['lvc_author'] = $id;
							$p['lvc_brapci_rdf'] = 0;
							$p['lvc_ano'] = $dados['ANO'];
							if (isset($dados['DOI'])) {
								$p['lvc_doi'] = $dados['DOI'];
							} else {
								$p['lvc_doi'] = '';
							}
							$p['lvc_title'] = $dados['TITULO-DO-CAPITULO-DO-LIVRO'];
							$p['lvc_url'] = $dados['HOME-PAGE-DO-TRABALHO'];
							$p['lvc_lang'] = $Lang->code($dados['IDIOMA']);
							$p['lvc_country'] = $dados['PAIS-DE-PUBLICACAO'];

							$deta = (array)$line['DETALHAMENTO-DO-CAPITULO'];
							$deta = (array)$deta['@attributes'];

							$p['lvc_isbn'] = $deta['ISBN'];
							$p['lvc_livro_title'] = $deta['TITULO-DO-LIVRO'];
							$p['lvc_livro_organizadores'] = $deta['ORGANIZADORES'];

							$vl = trim($deta['NUMERO-DE-VOLUMES']);
							$nr = "";
							if ($vl != '') {
								$vl = 'v. ' . $vl;
							}
							if ($nr != '') {
								$nr = 'p. ' . $nr;
							}
							$p['lvc_place'] = $deta['CIDADE-DA-EDITORA'];
							$p['lvc_vol'] = $vl;
							$p['lvc_nr'] = $nr;

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
							$p['lvc_authors'] = $authn;
							$p['lvc_author_total'] = count($auth);

							$rst = $this->where('lvc_author', $id)
								->where('lvc_title', $p['lvc_title'])
								->where('lvc_ano', $p['lvc_ano'])
								->findAll();

							if (count($rst) == 0) {
								$idp = $this->insert($p);
							} else {
								$idp = $rst[0]['id_lvc'];
							}

							/****************** KEYWORDS */
							if (isset($line['PALAVRAS-CHAVE'])) {
								$Keywords = new \App\Models\LattesExtrator\LattesKeywords();
								$dados = (array)$line['PALAVRAS-CHAVE'];
								$dados = (array)$dados['@attributes'];
								$Keywords->keyword_xml($idp, $dados, 'C');
							}
						}
					}
				}
			}
		}
		return 'ok';
	}

	function type($t)
	{
		switch ($t) {
			case 'Capítulo de livro publicado':
				return "CP";
				break;
			default:
				echo "TIPO: $t";
				exit;
		}
	}
	function natureza($t)
	{
		switch ($t) {
			default:
				echo "NATUREZA: $t";
				exit;
				break;
		}
	}
}
