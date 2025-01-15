<?php

namespace App\Models\Editais;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'editais';
    protected $table            = 'editais';
    protected $primaryKey       = 'id_e';
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

    function openEditais()
        {
            $dt = $this
                ->join('agencias', 'e_agencia = id_ag')
                ->where('e_data_end < ', date('Y-m-d'))
                ->orwhere('e_data_end', '1900-01-01')
                ->findAll(40);
            $dd = [];
            $dd['editais'] = $dt;
            return $dd;
        }
}
