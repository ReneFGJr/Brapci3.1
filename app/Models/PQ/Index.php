<?php

namespace App\Models\PQ;

use CodeIgniter\Model;

class Index extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'indices';
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


	function subheader()
	{
		$sx = bsc('<h1>Base PQ/CNPq</h1>', 12);
		$sx = bs($sx);
		return $sx;
	}

	function index($d1, $d2, $d3, $d4)
	{
		$sx = '';

		switch ($d1) {
			case 'bolsa_edit':
				$Bolsa = new \App\Models\PQ\Bolsas();
				$id = get('id');
				$sx .= $Bolsa->edit($id);
				break;

			case 'export':
				$Export = new \App\Models\PQ\Export();
				$sx .= $Export->brapci();
				break;

			case 'export_production':
				$Export = new \App\Models\PQ\Export();
				$sx .= $Export->lattes_prodiction();
				break;

			case 'lattes_import':
				$Bolsistas = new \App\Models\PQ\Bolsistas();
				$nr = round('0'.$d2)+1;
				$dt = $Bolsistas->where('bs_lattes <> "NI"')->find($nr,1);
				if ($dt != '')
					{
						$sx .= h($dt['bs_nome'],4);
						$LattesExtrator = new \App\Models\LattesExtrator\Index();
						$sx .= $LattesExtrator->harvesting($dt['bs_lattes']);
						$sx .= metarefresh(PATH. '/pq/lattes_import/'.$nr,2);
					} else {
						$sx .= h("Fim do processamento");
						return $sx;
					}
				break;
			case 'import':
				$CNPQ = new \App\Models\PQ\CNPQ();
				$sx .= $CNPQ->crawler();
				break;

			case 'viewid':
				$sx .= $this->subheader();
				$sx .= $this->viewid($d2);
				break;
			case 'pq_bolsas':
				$sx .= $this->subheader();
				$sx .= $this->pq_bolsas();
				break;

			case 'pq_dataset':
				$sx .= $this->dataset();
				break;

			case 'pq_bolsasistas':
				$sx .= $this->subheader();
				$sx .= $this->pq_bolsistas();
				break;

			case 'pq_ano':
				$sx .= $this->subheader();
				$sx .= $this->pq_ano();
				break;
				break;

			case 'pq_vigentes':
				$sx .= $this->subheader();
				$sx .= $this->pq_vigentes($d2, $d3, $d4);
				break;
				break;

			default:
				$sx .= $this->subheader();
				$sx .= $this->resume();
		}
		//$sx = bs($sx);
		return $sx;
	}

	function btn_new($id)
		{
			$Bolsas = new \App\Models\PQ\Bolsas();
			$sx = $Bolsas->btn_new($id);
		}

	function viewid($id='')
	{
		$sx = '';
		$id_brapci = 0;

		$Bolsista = new \App\Models\PQ\Bolsistas();
		$dt = $Bolsista->where('bs_lattes',$id)->first();

		$Lattes = new \App\Models\Lattes\Index();
		$Lattes->checkId($id);

		if ($id <= 0) {
			return metarefresh(PATH.'/pq');
			//return redirect('pq:index');
		}
		$sx= $Lattes->viewid($id);

		$sx .= onclick(URL . '/popup/pq_bolsa_edit?id=0&pq=' . $id, 800, 600) . bsicone('plus', 16, 'float-end') . '</span>';

		if ($dt['bs_rdf_id'] == 0) {
			$Authority = new \App\Models\Authority\AuthorityNames();
			$nome = $dt['bs_nome'];
			$nome = nbr_author($nome, 1);

			$id_brapci = $Authority->getBrapciId($nome);

			if ($id_brapci > 0) {
				$dta['bs_rdf_id'] = $id_brapci;
				$Bolsista->set($dta)
					->where('id_bs', $id)
					->Orwhere('bs_lattes', $id)
					->update();
			}
			$dt = $Bolsista->where('bs_lattes', $id)->first();
		}
		if ($id_brapci > 0) {
			$RDF = new \App\Models\Rdf\RDF();
			$link = $RDF->link(array('id_cc' => $id_brapci), 'text-secondary');
			$link = substr($link, strpos($link, '"') + 1, strlen($link));
			$link = substr($link, 0, strpos($link, '"'));
		}
		return $sx;
	}

	function pq_bolsistas()
	{
		$Bolsista = new \App\Models\PQ\Bolsistas();
		$sx = '';
		$sx = $Bolsista->bolsista_list();
		return $sx;
	}

	function pq_bolsas()
	{
		$Bolsa = new \App\Models\PQ\Bolsas();
		$sx = '';
		$sx = $Bolsa->bolsista_list();
		return $sx;
	}
	function pq_ano()
	{
		$Bolsa = new \App\Models\PQ\Bolsas();
		$sx = '';
		$sx = $Bolsa->year_list(0);
		return $sx;
	}

	function pq_vigentes($d2, $d3, $d4)
	{
		$Bolsa = new \App\Models\PQ\Bolsas();
		$sx = '';
		$sx = $Bolsa->year_summary(1);
		$sx .= $Bolsa->year_list(1);
		return $sx;
	}

	function resume()
	{
		$this->Socials = new \App\Models\Socials();
		$Bolsa = new \App\Models\PQ\Bolsas();
		$Bolsista = new \App\Models\PQ\Bolsistas();

		$sx = $Bolsa->resume();

		$sx .= '<ul>';
		$sx .= '<li><a href="' . PATH . MODULE . 'pq/pq_bolsasistas' . '">' . lang('pq.bolsista') . '</a></li>';
		$sx .= '<li><a href="' . PATH . MODULE . 'pq/pq_vigentes' . '">' . lang('pq.bolsista_vigentes') . '</a></li>';

		$sx .= '<li><a href="' . PATH . MODULE . 'pq/pq_bolsas' . '">' . lang('pq.bolsista_list') . '</a></li>';
		$sx .= '<li><a href="' . PATH . MODULE . 'pq/pq_ano' . '">' . lang('pq.bolsista_ano_list') . '</a></li>';
		$sx .= '<li><a href="' . PATH . MODULE . 'pq/pq_renew' . '">' . lang('pq.bolsista_renovacao') . '</a></li>';
		$sx .= '<li><a href="' . PATH . MODULE . 'pq/pq_dataset' . '">' . lang('pq.bolsista_dataset') . '</a></li>';

		$sx .= '<li><a href="http://memoria2.cnpq.br/bolsistas-vigentes" target="_new">CNPq - ' . lang('pq.bolsista_ativos_cnpq') . '</a></li>';

		$url = 'http://plsql1.cnpq.br/divulg/RESULTADO_PQ_102003.prc_comp_cmt_links?V_COD_DEMANDA=200310&V_TPO_RESULT=CURSO&V_COD_AREA_CONHEC=60700009&V_COD_CMT_ASSESSOR=AC';
		$sx .= '<li><a href="'.$url.'" target="_new">CNPq - ' . lang('pq.bolsista_ativos_cnpq') . ' - Em Folha</a></li>';

		if ($this->Socials->getAccess("#ADM")) {
			$sx .= '<hr>';
			$sx .= '<li><a href="' . PATH . MODULE . 'pq/export_production' . '">' . lang('pq.export_production') . '</a></li>';
			$sx .= '<li><a href="' . PATH . MODULE . 'pq/export' . '">' . lang('pq.exportar') . '</a></li>';
			$sx .= '<li><a href="' . PATH . MODULE . 'pq/import' . '">' . lang('pq.import') . '</a></li>';
			$sx .= '<li><a href="' . PATH . MODULE . 'pq/lattes_import' . '">' . lang('pq.lattes_import') . '</a></li>';
		}
		$sx .= '</ul>';

		return $sx;
	}

	function dataset()
		{
			$Bolsas = new \App\Models\PQ\Bolsas();
			$sx = h("Dataset PQ");
			$sx .= $Bolsas->download();
			return $sx;
		}
}