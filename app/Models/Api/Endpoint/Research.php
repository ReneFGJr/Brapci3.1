<?php

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Research extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'researches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function amostra()
        {
            $t = get("total");
            $E = get("erro");
            $nivelConfianca = get("confianca");

            if ($E == '') { $E = 0.05; }
            if ($nivelConfianca == '') { $nivelConfianca = 95; }

            // Valor de Z baseado no nível de confiança
            // Z para 95% de confiança é 1.96.
            // Você pode ajustar para outros níveis de confiança conforme necessário
            $Z = 0;
            if ($nivelConfianca == 95) {
                $Z = 1.96;
            } elseif ($nivelConfianca == 99) {
                $Z = 2.58;
            } // Adicione mais níveis de confiança conforme necessário

            // Calcula o tamanho da amostra
            $n = ($Z ** 2 * $p * (1 - $p)) / ($E ** 2);

            // Retorna o tamanho da amostra arredondado para o próximo número inteiro
            return ceil($n);

        }
}
