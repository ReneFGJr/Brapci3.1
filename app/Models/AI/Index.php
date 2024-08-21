<?php

namespace App\Models\AI;

use CodeIgniter\Model;

class Index extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = '*';
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

	function reprocess_bug($subact)
	{
		$sx = '';
		$BUGS = new \App\Models\Functions\Bugs();
		$RDF = new \App\Models\Rdf\RDF();
		$RDFLiteral = new \App\Models\Rdf\RDFLiteral();
		$dt = $BUGS->recoverProblem($subact);
		foreach ($dt as $id => $line) {
			$idb = $line['id_bug'];
			$idc = $line['bug_v'];
			$dta = $RDF->le($idc);

			$name = $dta['concept']['n_name'];
			$idn = $dta['concept']['id_n'];

			if (sonumero($name) == '') {
				$name2 = nbr_author($name, 1);
				if ($name != $name2) {
					$dta['n_name'] = $name2;
					$RDFLiteral->set($dta)->where('id_n', $idn)->update();

					$sx .= $name.' => '.$name2.'<br>';
				} else {
					echo $name . 'none<br>';
				}

				$sx .= 'Update BUGs';
				$dta['bug_status'] = 2;
				$dta['bug_solution'] = 'Autonomo Bots v0.23.02.02';
				$dta['updated_at'] = date("Y-m-d H:i:s");
				$BUGS->set($dta)->where('id_bug', $idb)->update();
			}
		}
		$sx = bs(bsc($sx,12));
		return $sx;
	}

	function index($act = '', $subact = '', $d1 = '', $d2 = '')
	{
		switch ($act) {
			case 'chat':
				$API = new \App\Models\AI\Chatbot\Index();
				$RSP = $API->index($act, $subact, $d1, $d2);
				echo json_encode($RSP);
				exit;
				break;

			case 'authority':
				switch ($subact) {
					case 'nameLowerCase':
						$this->reprocess_bug($subact);
						break;
					case 'nameJUNIOR':
						$this->reprocess_bug($subact);
						break;
					default:
						$sx .= 'OPS AI authority';
						break;
				}
				break;
			case 'synthesize':
				$wiki = new \App\Models\TextToPeech\Index();
				$sx .= $wiki->index($subact, $d1, $d2);
				break;
			case 'wiki':
				$wiki = new \App\Models\AI\Wiki\Index();
				$sx .= $wiki->index($subact, $d1, $d2);
				break;
			case 'nlp':
				$NLP = new \App\Models\AI\NLP\Index();
				$sx .= $NLP->index($subact, $d1, $d2);
				break;
			case 'file':
				switch ($subact) {
					case 'process':
						$API = new \App\Models\AI\FILE\pdf;
						$sx = $API->show_files();
						return $sx;

					case 'upload':
						$API = new \App\Models\AI\FILE\upload;
						$sx = $API->upload($d1, $d2);
						return $sx;

					case 'pdf_to_text':
						$API = new \App\Models\AI\FILE\pdf;
						$sx = $API->pdf_to_html($d1, $d2);
						break;

					default:
						break;
				}
				break;

			default:
				$this->menu();
				exit;
				break;
		}

		return [];
	}

	function menu()
	{
		$menu = [];
		$menu['/api/ai/chat'] = 'Chat Ollama';
		$menu['/api/ai/authority'] = 'Authority';

		echo json_encode($menu);
		exit;

		return "";
	}
}
