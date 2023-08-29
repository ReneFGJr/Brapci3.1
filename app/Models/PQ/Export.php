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

	function brapci()
		{
			$sx = '';
			$Bolsa = new \App\Models\PQ\Bolsas();
			$Bolsa
				->join('bolsistas','bb_person = id_bs')
				->join('modalidades','bs_tipo = id_mod')
				->where('bs_rdf_id > 0')
				->orderBy('bs_nome, bs_start');
			$dt = $Bolsa->FindAll();

			$RDF = new \App\Models\Rdf\RDF();
			for ($r=0;$r < count($dt);$r++)
				{
					$line = $dt[$r];
					$mod = $line['mod_sigla'].$line['bs_nivel'];
					$id_author = $line['bs_rdf_id'];
					$dti = substr($line['bs_start'],0,4);
					$dtf = substr($line['bs_finish'],0,4);
					$mod .= "($dti-$dtf)";
					$IDB = $RDF->RDF_concept($mod,'brapci:CnpqPQ');
					$prop = 'brapci:hasPQ';
					$RDF->propriety($id_author, $prop, $IDB);
				}
			$sx .= bsmessage('Export success!',1);

			return $sx;
		}
}
