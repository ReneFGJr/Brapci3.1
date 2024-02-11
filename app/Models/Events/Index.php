<?php

namespace App\Models\Events;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'event';
    protected $primaryKey       = 'id_ev';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ev ', 'ev_name', 'ev_place',
        'ev_ative', 'ev_permanent', 'ev_data_start',
        'ev_data_end', 'ev_deadline', 'ev_url',
        'ev_description', 'ev_image', 'ev_count'
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

    function index($d1,$d2,$d3)
        {
            header('Access-Control-Allow-Origin: *');
            if (get("test") == '') {
                header("Content-Type: application/json");
            }
            switch($d1)
                {
                    default:
                        $this->events($d2);
                }
        }

    function events($type=1)
        {
            $dt = $this->findAll();
            return ($dt);
        }
}
