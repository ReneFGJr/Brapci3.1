<?php

namespace App\Models\Monitor;

use CodeIgniter\Model;

class Index extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'files';
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

	function index($d1 = '', $d2 = '', $d3 = '')
	{
		$sx = '';
		switch ($d1) {
			case 'status':
				$sx .= $this->identify($d1, $d2);
				break;
				/*************************************************************** get register */
			default:
				echo "OPS MONITR $d1,$d2";
				exit;
		}

		return $sx;
	}

	function verificarComputadores(array $listaIPs)
	{
		$resultados = [];

		foreach ($listaIPs as $ip) {
			// Tentamos abrir uma conexão na porta 80 (HTTP) ou 443 (HTTPS)
			$porta = 80;
			$timeout = 2; // Timeout de 2 segundos para a conexão

			$conexao = @fsockopen($ip, $porta, $errno, $errstr, $timeout);

			if ($conexao) {
				$resultados[$ip] = 'Ligado';
				fclose($conexao); // Fechar a conexão após o teste
			} else {
				$resultados[$ip] = 'Desligado ou inacessível';
			}
		}

		return $resultados;
	}

	function checkIP()
	{
		$RSP = [];
		$listaDeIPs = ['143.54.113.96', '143.54.112.86', '143.54.112.219', '143.54.112.91', '143.54.113.60', '143.54.113.131', '143.54.112.77', '143.54.113.15'];
		$resultados = $this->verificarComputadores($listaDeIPs);

		foreach ($resultados as $ip => $status) {
			array_push($RSP,"O computador com IP $ip está $status");
		}
		return $RSP;
	}
}
