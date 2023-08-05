<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class LibraryPlace extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'library_place';
    protected $primaryKey       = 'id_lp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_lp', 'lp_labrary', 'lp_name',
        'lp_description', 'lp_cood_x', 'lp_cood_y'
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

    function listPlaces($lib)
        {
            $dt = $this
            ->where('lp_library',$lib)
            ->orderBy('lp_name')
            ->FindAll();
            return $dt;
        }
}
