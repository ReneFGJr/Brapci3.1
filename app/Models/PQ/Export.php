<?php

namespace App\Models\PQ;

use CodeIgniter\Model;

class Export extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'exports';
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

	function lattes_prodiction()
	{
		$Bolsista = new \App\Models\PQ\Bolsistas();
		$dt = $Bolsista
			->join('brapci_lattes.LattesProducao', 'lp_author = bs_lattes')
			->findAll();
		$sx = 'PESQUISAOR;TIPO;LATTES;AUTORES;TITULO;ANO;URL;DOI;JOURNAL;LOCAL;VOL_NR'.CR();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$sx .= '"' . $line['bs_nome'] . '"' . ';';
			$sx .= '"ARTIGO"' . ';';
			$sx .= '"' . $line['bs_lattes'] . '";';
			$sx .= '"' . $line['lp_authors'] . '";';
			$sx .= '"' . troca($line['lp_title'],'"',''). '";';
			$sx .= $line['lp_ano'] . ';';
			$sx .= '"' . $line['lp_url'] . '";';
			$sx .= '"' . $line['lp_doi'] . '";';
			$sx .= '"' . $line['lp_journal'] . '";';
			$sx .= '"' . $line['lp_place'] . '";';

			$sx .= '"' . $line['lp_vol'] . ' ' . $line['lp_nr'] . '";';
			$sx .= cr();
		}

		/************************************************** Livro */
		$dt = $Bolsista
			->join('brapci_lattes.lattesproducao_livro', 'lv_author = bs_lattes')
			->findAll();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$sx .= '"' . $line['bs_nome'] . '"' . ';';
			$sx .= '"LIVRO"' . ';';
			$sx .= '"' . $line['bs_lattes'] . '";';
			$sx .= '"' . $line['lv_authors'] . '";';
			$sx .= '"' . troca($line['lv_title'], '"', '') . '";';
			$sx .= $line['lv_ano'] . ';';
			$sx .= '"' . $line['lv_url'] . '";';
			$sx .= '"' . $line['lv_doi'] . '";';
			$sx .= '"' . $line['lv_journal'] . '";';
			$sx .= '"' . $line['lv_place'] . '";';

			$sx .= '"";';
			$sx .= cr();
		}

		$dt = $Bolsista
			->join('brapci_lattes.lattesproducao_evento', 'le_author = bs_lattes')
			->findAll();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$sx .= '"' . $line['bs_nome'] . '"' . ';';
			$sx .= '"EVENTO"' . ';';
			$sx .= '"' . $line['bs_lattes'] . '";';
			$sx .= '"' . $line['le_authors'] . '";';
			$sx .= '"' . troca($line['le_title'], '"', '') . '";';
			$sx .= $line['le_ano'] . ';';
			$sx .= '"' . $line['le_url'] . '";';
			$sx .= '"' . $line['le_doi'] . '";';
			$sx .= '"' . $line['le_journal'] . '";';
			$sx .= '"' . $line['le_place'] . '";';

			$sx .= '"";';
			$sx .= cr();
		}

		$dt = $Bolsista
			->join('brapci_lattes.lattesproducao_capitulo', 'lvc_author = bs_lattes')
			->findAll();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$sx .= '"' . $line['bs_nome'] . '"' . ';';
			$sx .= '"CAPITULO"' . ';';
			$sx .= '"' . $line['bs_lattes'] . '";';
			$sx .= '"' . $line['lvc_authors'] . '";';
			$sx .= '"' . troca($line['lvc_title'], '"', '') . '";';
			$sx .= $line['lvc_ano'] . ';';
			$sx .= '"' . $line['lvc_url'] . '";';
			$sx .= '"' . $line['lvc_doi'] . '";';
			$sx .= '"' . $line['lvc_journal'] . '";';
			$sx .= '"' . $line['lvc_place'] . '";';

			$sx .= '"";';
			$sx .= cr();
		}


		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=file.csv");
		echo utf8_decode($sx);
		exit;

	}

	function brapci()
	{
		$sx = '';
		$Bolsa = new \App\Models\PQ\Bolsas();
		$Bolsa
			->join('bolsistas', 'bb_person = id_bs')
			->join('modalidades', 'bs_tipo = id_mod')
			->where('bs_rdf_id > 0')
			->orderBy('bs_nome, bs_start');
		$dt = $Bolsa->FindAll();

		$RDF = new \App\Models\Rdf\RDF();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$mod = $line['mod_sigla'] . $line['bs_nivel'];
			$id_author = $line['bs_rdf_id'];
			$dti = substr($line['bs_start'], 0, 4);
			$dtf = substr($line['bs_finish'], 0, 4);
			$mod .= "($dti-$dtf)";
			$IDB = $RDF->RDF_concept($mod, 'brapci:CnpqPQ');
			$prop = 'brapci:hasPQ';
			$RDF->propriety($id_author, $prop, $IDB);
		}
		$sx .= bsmessage('Export success!', 1);

		return $sx;
	}
}
