<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class Catalog extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'find_work';
    protected $primaryKey       = 'id_w';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_w','w_ID','w_TYPE',
        'w_TITLE',
        'w_AUTHORS',
        'w_YEAR',
        'w_PUBLISHER'
    ];

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

    function findISBN($ISBN)
        {
            $id = $ISBN['isbn13'];
            $dt = $this->where('w_ID',$id);
            return $dt;
        }
}
