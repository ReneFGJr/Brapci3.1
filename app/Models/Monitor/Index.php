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

	function verificarComputadores($ip)
	{
		$resultados = [];


		// Tentamos abrir uma conexão na porta 80 (HTTP) ou 443 (HTTPS)
		$porta = 80;
		$timeout = 2; // Timeout de 2 segundos para a conexão

		$conexao = @fsockopen($ip, $porta, $errno, $errstr, $timeout);
		if ($conexao) {
			return 'On';
		} else {
			// Tentativa de conexão via SMB (porta 445, Windows)
			if ($this->verificarPorta($ip, 445)) {
				return "On";
			}
			return 'Off';
		}
	}

	// Função auxiliar para verificar se uma porta específica está acessível
	function verificarPorta($ip, $porta, $timeout = 2)
	{
		$conexao = @fsockopen($ip, $porta, $errno, $errstr, $timeout);

		if ($conexao) {
			fclose($conexao); // Fechar a conexão após o teste
			return true;
		}

		return false;
	}

	function checkIP()
	{
		$RSP = [];
		$listaDeIPs = [
			'143.54.113.96' => 'Venus',
			'143.54.112.86' => 'Netuno',
			'143.54.112.219' => 'Jupyter',
			'143.54.112.91' => 'Saturno',
			'143.54.113.60' => 'NAS',
			'143.54.113.131' => 'TrueNAS',
			'143.54.112.77' => 'Desktop DELL',
			'143.54.113.15' => 'Desktop Rene'
		];

		foreach ($listaDeIPs as $IP => $server) {
			$status = $this->verificarComputadores($IP);

			$CHK = [];
			$CHK['ip'] = $IP;
			$CHK['server'] = $server;
			$CHK['status'] = $status;
			array_push($RSP, $CHK);
		}
		return $RSP;
	}
}
