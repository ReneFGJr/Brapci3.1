<?php

namespace App\Models\Password;

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

    function gerarSenha($tamanho = 8, $usarCaracteresEspeciais = true)
    {
        // Conjunto básico de caracteres: letras minúsculas, maiúsculas e dígitos.
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        // Se solicitado, adiciona caracteres especiais.
        if ($usarCaracteresEspeciais) {
            $caracteres .= '@#$%';
        }

        $senha = '';
        $totalCaracteres = strlen($caracteres);

        // Gera a senha selecionando caracteres aleatórios.
        for ($i = 0; $i < $tamanho; $i++) {
            $index = random_int(0, $totalCaracteres - 1);
            $senha .= $caracteres[$index];
        }

        return $senha;
    }
}
