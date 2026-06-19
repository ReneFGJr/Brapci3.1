<?php

namespace App\Models\Lattes;

use CodeIgniter\Model;

class KnName extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'kn_name';
    protected $primaryKey       = 'id_knn';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'knn_idn',
        'knn_name',
    ];

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

    public function findByName(string $name): ?array
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }

        return $this->where('knn_name', $name)->first();
    }

    public function findByIdn(string $idn): ?array
    {
        $idn = trim($idn);
        if ($idn === '') {
            return null;
        }

        return $this->where('knn_idn', $idn)->first();
    }
}
