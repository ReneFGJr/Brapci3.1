<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFPrefix extends Model
{
	var $DBGroup             		= 'rdf';
	protected $table                = PREFIX.'rdf_prefix';
	protected $primaryKey           = 'id_prefix ';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_prefix','prefix_ref','prefix_url','prefix_ativo'
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

function prefixn($dt)
	{
		$pre = trim($dt['prefix_ref']);
		$class = trim($dt['c_class']);
		if (strlen($class) > 0)
		{
			if (strlen($pre) > 0)
			{
				$sx = $pre.':'.$class;
			} else {
				$sx = $class;
			}
		} else {
			$sx = '<i>'.msg('none').'</i>';
		}
		return($sx);

	}	

	function prefixo($pre)
		{
			$ID = 0;	
			$dt = $this->where('prefix_ref',$pre)->find();

			if (count($dt) > 0)
				{
					$ID = $dt[0]['id_prefix'];
				} else {
					$data['prefix_ref'] = $pre;
					$this->insert($data);
					//echo 'Prefix não localizado - '.$pre;
					sleep(0.1);
					
					$dt = $this->where('prefix_ref',$pre)->find();
					print_r($dt);
					$ID = $dt[0]['id_prefix'];
				}
			return $ID;
		}

	function inport($url='')
		{
			$sx = '';
			$ID = 3;			
			$ID_file = 9;
			$URL = 'http://cedapdados.ufrgs.br';
			$IDP = 'hdl:20.500.11959/CedapDados/'.$ID.'/'.$ID_file;
			$url = $URL.'/api/access/datafile/:persistentId?persistentId='.$IDP;
			$lang = 'pt-BR';
			$dir = '.tmp';
			$file = md5($url);
			$filename = $dir.'/'.$file;

			/* Leitura do Arquivo */
			if (!is_dir($dir))	{ mkdir($dir); }
			if (file_exists($filename))
			{
				$txt = file_get_contents($filename);
			} else {
			/************************************* */
				$txt = file_get_contents($url);
				file_put_contents($filename,$txt);
			}
			$txt = str_replace(array('"'),array(''),$txt);
			$lns = explode(chr(10),$txt);
			$hd = explode(chr(9),$lns[0]);
			
			for ($r=01;$r < count($lns);$r++)
				{
					$ln = explode(chr(9),$lns[$r]);
					if (count($ln) > 1)
					{
						for ($y=0;$y < count($hd);$y++)
							{
								$dt[$hd[$y]] = $ln[$y];
							}

						$dz = $this->
									where('prefix_ref',$dt[$hd[0]])->findAll();
						
						if (isset($dz[0]))
							{

							} else {
								$this->insert($dt);
							}					
					}
				}
				$sx .= bsmessage('DataSet File inported',1);
				return $sx;
		}	
}
