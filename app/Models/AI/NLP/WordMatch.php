<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class WordMatch extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'wordmatches';
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

	function show($txt)
		{
			$ln = explode(chr(10),$txt);
			$txt = '';
			for ($r=0;$r < count($ln);$r++)
				{
					if (strpos($txt,'<b'))
						{
							$txt .= $ln[$r]."\n";
						}
				}
			return $txt;
		}

	function analyse($txt,$vc=array())
		{
			$TextPrepare = new \App\Models\AI\NLP\TextPrepare();
			$rst = array();
			$txt = $TextPrepare->Text($txt);

			$w = array();
			foreach($vc as $t1=>$t2)
				{			
					$txt = troca($txt,' '.$t1.' ',' <b style="color: blue; font-size=5">'.$t2.'</b> ');
					$w[$t2] = 0;
				}
			$wt = array();
			foreach($w as $t1=>$v)
				{
				$ocorrencia = substr_count($txt,$t1);
				if ($ocorrencia > 0)
					{
						$w[$t1] = $ocorrencia;
						$wt[$t1] = $ocorrencia;
						//echo $t1.'-['.$ocorrencia.']<br>';
					}	
				}			
			return $txt;
		}
}
