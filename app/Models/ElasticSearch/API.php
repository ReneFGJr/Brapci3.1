<?php

namespace App\Models\ElasticSearch;

use CodeIgniter\Model;

class API extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'apis';
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
	protected $index = 'brapci2022';
	protected $server = '';
	protected $sz = 25;

	function __construct()
	{
		$this->server = getenv('app.elasticSearchURL');
	}

	function showList($dt)
	{
		$sx = '';
		foreach ($dt as $label => $value) {
			$sx .= bsc(lang('elastic.' . $label), 2, 'text-start text_50');
			$sx .= bsc(lang('elastic.' . $value) . '&nbsp', 10);
		}
		$sx = bs($sx);
		return $sx;
	}

	function call($path, $method = 'GET', $data = null)
	{

		if (strlen($this->index) == 0) {
			echo ('index needs a value');
			return (array());
		}

		$url = $this->server .  $path;
		$headers = array('Accept: application/json', 'Content-Type: application/json',);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


		switch ($method) {
			case 'GET':
				break;
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				break;
			case 'PUT':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				break;
			case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
		}

		$response = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		return json_decode($response, true);
	}

	public function status()
	{
		return $this->call('_cluster/health', 'GET');
	}

	public function settings()
	{
		return $this->call('_cluster/settings', 'GET');
	}

	function formTest()
	{
		$sx = '<hr>';
		$sx .= bsc('Server:', 2) . bsc($this->server, 10);
		$tela1 = h(lang('brapci.action'), 5);
		$tela1 .= form_open();
		$tela1 .= '<select name="action">';
		$tela1 .= '<option value="query">' . lang('brapci.elasticQuery') . '</option>';
		$tela1 .= '</select>';

		$tela2 = '';
		$tela1 .= form_close();
		/*********************** Query */
		$tela2 .= '<span class="small">' . lang('query') . '</span>';
		$tela2 .= '<textarea name="text" class="form-control"></textarea>';
		$tela2 .= '<input type="submit" value="Busca">';
		$tela2 .= '<hr>';
		$tela2 .= 'Ex:<pre>{"query":{"match_all":{}}}</pre>';
		$tela2 .= '{
				"query": {
				  "query_string": {
					"query": "arquivometria"
				  }
				}
			  }';
		$sx .= bsc($tela1, 3);
		$sx .= bsc($tela2, 9);
		$sx = bs($sx);
		return $sx;
	}
}