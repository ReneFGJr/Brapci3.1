<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Collections extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_collections';
    protected $primaryKey       = 'id_collection';
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

    function list($type)
        {
            if ($type == 'json')
                {
                    $cp = 'collection_id, collection_name, collection_type, collection_seq';
                    $dt = $this
                        ->select($cp)
                        ->orderby('collection_seq')
                        ->findAll();
                    return(json_encode($dt));
                }
        }
}
