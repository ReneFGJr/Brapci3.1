<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Wordcount extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'wordcounts';
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

	function wordcount($txt, $lang = '')
	{
		$sx = '';
		$txt = ascii(mb_strtolower($txt));
		$schar = array('-', '.', ',', '!', '?', '}', '{', '[', ']', '/', '\\', ':', ';', '(', ')', '<', '>', '+', '=', '_', '@', '#', '$', '%', '&', '*', '^', '~', '`', '|', ' ');
		$txt = str_replace($schar, ';', $txt);
		$txt = str_replace(' ', ';', $txt);

		$stop = array('and', 'or', 'the', 'from', 'on', 'to', 'a', 'an', 'of', 'in', 'for', 'by', 'is', 'are', 'was', 'were', 'as', 'at', 'be', 'been', 'but', 'it', 'its', 'this', 'that', 'these', 'those', 'can', 'will', 'could', 'should', 'would', 'have', 'has', 'had', 'do', 'does', 'did', 'have', 'has', 'had', 'having', 'with', 'without', 'what', 'when', 'where', 'why', 'how', 'who', 'whom', 'which', 'you', 'your', 'yours', 'yourself', 'yourselves', 'he', 'him', 'his', 'himself', 'she', 'her', 'hers', 'herself', 'it', 'its', 'itself', 'they', 'them', 'their', 'theirs', 'themselves', 'what', 'which', 'who', 'whom', 'this', 'that', 'these', 'those', 'am', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'having', 'do', 'does', 'did', 'doing', 'a', 'an', 'the', 'and', 'but', 'if', 'or', 'because', 'as', 'until', 'while', 'of', 'at', 'by', 'for', 'with', 'about', 'against', 'between', 'into', 'through', 'during', 'before', 'after', 'above', 'below', 'to', 'from', 'up', 'down', 'in', 'out', 'on', 'off', 'over', 'under', 'again', 'further', 'then', 'once', 'here', 'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than', 'too', 'very', 's', 't', 'can', 'will', 'just', 'don', 'should', 'now');
		foreach ($stop as $value) {
			$txt = str_replace(';' . $value . ';', ';', $txt);
		}

		//$txt = str_replace($stop,';',$txt);
		$txt = troca($txt, ' ', ';');
		$txt = troca($txt, ';;', ';');
		$txt = troca($txt, ';;', ';');
		$txt = troca($txt, ';;', ';');

		$w = explode(';', $txt);
		$n = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

		$sx .= '';
		for ($r = 0; $r < count($w); $r++) {
			$wd = trim($w[$r]);
			if ($wd != '') {

				/* Desloca */
				$id = 0;
				for ($q=1;$q < count($n) ;$q++) {
					$n[$q-1] = $n[$q];
					if ($id++ > 40) { echo "OPS"; exit; }

				}
				$n[20] = $wd;
				
				print_r($n);
				echo '<hr>';


				//$sx .= $w[$r].cr();
			}
		}
		return $sx;
	}
}
