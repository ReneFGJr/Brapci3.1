<?php

namespace App\Models\Z3950;

use CodeIgniter\Model;

class Index extends Model
{
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    private $server;

    public function __construct()
    {
        $this->server = "z3950.bn.br:210/biblioteca"; // Configurar o servidor Z39.50
    }

    public function searchByISBN(string $isbn)
    {

        if (function_exists('yaz_connect')) {
            echo "A extensão PHP-YAZ está ativa!";
        } else {
            echo "A extensão PHP-YAZ NÃO está ativa!";
            exit;
        }
        // Conectar ao servidor Z39.50
        $id = yaz_connect($this->server);

        if (!$id) {
            throw new Exception("Não foi possível conectar ao servidor Z39.50.");
        }

        // Definir o formato de resposta (ex.: MARC21)
        yaz_syntax($id, "usmarc");

        // Construir e executar a consulta por ISBN
        $query = "@attr 1=7 " . $isbn; // Atributo '1=7' para ISBN
        yaz_search($id, "rpn", $query);

        // Aguarda a execução da consulta
        yaz_wait();

        // Verifica erros na resposta
        if (yaz_error($id) != "") {
            throw new Exception("Erro: " . yaz_error($id));
        }

        // Obter o primeiro registro
        $record = yaz_record($id, 1, "string");

        // Fechar a conexão
        yaz_close($id);

        return $record;
    }
}
