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

    function searchZ3950ByISBN($isbn)
    {
        // Configuração do servidor Z39.50
        $server = "z3950.bn.br:210/biblioteca";

        // Inicializa uma conexão YAZ
        $id = yaz_connect($server);

        if (!$id) {
            throw new Exception("Não foi possível conectar ao servidor Z39.50.");
        }

        // Configura o formato de apresentação (MARC21, XML, etc.)
        yaz_syntax($id, "usmarc");

        // Configura a consulta (campo 'isbn')
        $query = "@attr 1=7 " . $isbn; // Atributo '1=7' para busca por ISBN
        yaz_search($id, "rpn", $query);

        // Executa a busca
        yaz_wait();

        // Verifica se houve erro
        if (yaz_error($id) != "") {
            throw new Exception("Erro: " . yaz_error($id));
        }

        // Obtém o número de resultados
        $hits = yaz_hits($id);
        if ($hits == 0) {
            return "Nenhum resultado encontrado para o ISBN $isbn.";
        }

        // Recupera o primeiro registro
        $record = yaz_record($id, 1, "string");
        yaz_close($id);

        return $record;
    }
}
