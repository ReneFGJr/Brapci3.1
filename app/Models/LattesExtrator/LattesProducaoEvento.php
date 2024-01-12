<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesProducaoEvento extends Model
{
	protected $DBGroup              = 'lattes';
	protected $table                = 'lattesproducao_evento';
	protected $primaryKey           = 'id_le';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_le ', 'le_author', 'le_brapci_rdf',
		'le_authors', 'le_title', 'le_ano',
		'le_url', 'le_doi', 'le_issn',
		'le_journal', 'le_vol', 'le_nr',
		'le_place', 'le_country', 'le_event',
		'le_natureza', 'le_seq'
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
		$this->where('le_author', $id)->delete();
		return true;
	}

	function natureza($nat)
	{
		switch ($nat) {
			case 'COMPLETO':
				$nat = 'CP';
				break;
			case 'RESUMO':
				$nat = 'RS';
				break;
			case 'RESUMO_EXPANDIDO':
				$nat = 'RE';
				break;
			default:
				echo '==>' . $nat;
				exit;
				break;
		}
		return $nat;
	}

	function resume($id)
	{
		$dt = $this->select('count(*) as total, le_author')
			->where('le_author', $id)
			->groupBy('le_author')
			->findAll();
		if (count($dt) > 0) {
			return $dt[0]['total'];
		}
		return 0;
	}

	function csv($id)
	{
		set_time_limit(0);
		$cp = 'le_author, le_authors, le_title, le_ano, le_doi, le_issn, le_journal, le_natureza';
		$dt = $this
			->select($cp)
			->join('brapci_tools.projects_harvesting_xml', 'le_author  = hx_id_lattes')
			->where('hx_project', $id)
			->orderBy('le_seq')
			->findAll();


			pre($dt);



		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=brapci_tools_production_" . date("Ymd-His") . ".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo 'IDLATTES,AUTHORS,TITLE,YEAR,DOI,ISSN,JOURNAL,NATUREZA' . chr(13);

		foreach ($dt as $id => $line) {
			$sa = '';
			$sa .= '"' . $line['le_author'] . '",';
			$sa .= '"' . $line['le_authors'] . '",';
			$sa .= '"' . $line['le_title'] . '",';
			$sa .= '"' . $line['le_ano'] . '",';
			$sa .= '"' . $line['le_doi'] . '",';
			$sa .= '"' . $line['le_issn'] . '",';
			$sa .= '"' . $line['le_journal'] . '",';
			$sa .= '"' . $this->natureza($line['le_natureza']) . '",';
			$sa = troca($sa, chr(13), '');
			$sa = troca($sa, chr(10), '');
			$sx = $sa . chr(13);
			echo $sx;
		}
		exit;
	}

	function producao($id)
	{
		$tela = '';
		$dt = $this->where('le_author', $id)
			->orderBy('le_natureza, le_seq, le_ano', 'desc')
			->findAll();
		$sxt = '';
		$tela .= '<ol>';
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$xt = $line['le_natureza'];
			if ($xt != $sxt) {
				if (strlen($tela) > 0) {
					$tela .= '</ol>';
				}
				$tela .= h(lang('brapci.event_type_' . $line['le_natureza']), 3);
				$tela .= '<ol>';
				$sxt = $xt;
			}

			$tela .= '<li>' . $line['le_authors'] . '. ' . $line['le_title'] . '. ';
			$tela .= '<b>' . $line['le_journal'] . '</b>';
			if (strlen($line['le_vol']) > 0) {
				$tela .= ', ' . $line['le_vol'];
			}
			if (strlen($line['le_nr']) > 0) {
				$tela .= ', ' . $line['le_nr'];
			}
			$tela .= ', ' . $line['le_ano'];
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
			if (isset($prod['TRABALHOS-EM-EVENTOS'])) {
				$arti = (array)$prod['TRABALHOS-EM-EVENTOS'];
				$arti = (array)$arti['TRABALHO-EM-EVENTOS'];

				for ($r = 0; $r < count($arti); $r++) {
					if (!isset($arti[0])) {
						$art2[0] = $arti;
						$arti = array();
						$arti = $art2;
					}
					$line = (array)$arti[$r];
					$attr = $line['@attributes'];

					$dados = (array)$line['DADOS-BASICOS-DO-TRABALHO'];
					$dados = (array)$dados['@attributes'];
					$p = array();
					$p['le_author'] = $id;
					$p['le_brapci_rdf'] = 0;
					$p['le_ano'] = $dados['ANO-DO-TRABALHO'];
					if (isset($dados['DOI'])) {
						$p['le_doi'] = $dados['DOI'];
					} else {
						$p['le_doi'] = '';
					}
					$p['le_title'] = $dados['TITULO-DO-TRABALHO'];
					$p['le_url'] = $dados['HOME-PAGE-DO-TRABALHO'];
					$p['le_lang'] = $Lang->code($dados['IDIOMA']);
					$p['le_country'] = $dados['PAIS-DO-EVENTO'];
					$p['le_seq'] = $attr['SEQUENCIA-PRODUCAO'];

					$deta = (array)$line['DETALHAMENTO-DO-TRABALHO'];
					$deta = (array)$deta['@attributes'];

					$p['le_event'] = $deta['NOME-DO-EVENTO'];
					$p['le_isbn'] = $deta['ISBN'];
					$vl = trim($deta['VOLUME']);
					$nr = trim($deta['FASCICULO']);
					if ($vl != '') {
						$vl = 'v. ' . $vl;
					}
					if ($nr != '') {
						$nr = 'n. ' . $nr;
					}
					$p['le_place'] = $deta['CIDADE-DO-EVENTO'];
					$p['le_vol'] = $vl;
					$p['le_nr'] = $nr;

					$p['le_natureza'] = $this->natureza($dados['NATUREZA']);

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
					$p['le_authors'] = $authn;
					$p['le_author_total'] = count($auth);

					$rst = $this->where('le_author', $id)
						->where('le_title', $p['le_title'])
						->where('le_ano', $p['le_ano'])
						->where('le_event', substr($p['le_event'], 0, 250))
						->findAll();

					if (count($rst) == 0) {
						$idp = $this->insert($p);
					} else {
						$idp = $rst[0]['id_le'];
					}
					/****************** KEYWORDS */
					if (isset($line['PALAVRAS-CHAVE'])) {
						$Keywords = new \App\Models\LattesExtrator\LattesKeywords();
						$dados = (array)$line['PALAVRAS-CHAVE'];
						$dados = (array)$dados['@attributes'];
						$Keywords->keyword_xml($idp, $dados, 'E');
					}

				}
			}
		}
		return 'ok';
	}
}
