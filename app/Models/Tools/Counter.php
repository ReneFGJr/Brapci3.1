<?php

namespace App\Models\Tools;

use CodeIgniter\Model;

class Counter extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'guest';
    protected $primaryKey       = 'id_count';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_count', 'count_value', 'count_last_ip', 'count_value'];

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

    function counter($service='')
        {
            $dt = $_SERVER;
            $IP = IP();
            $dt = $this->where('count_service',$service)->first();

            if (!$dt)
                {
                    $dt['count_service'] = $service;
                    $dt['count_value'] = 0;
                    $dt['count_last_ip'] = '0.0.0.0';
                    $this->set($dt)->insert();
                }

            if ($dt['count_last_ip'] != $IP)
                {
                    $counter = $dt['count_value'];
                    $counter++;
                    $dd['count_value'] = $counter;
                    $dd['count_last_ip'] = $IP;
                    $this->set($dd)->where('count_service', $service)->update();
                }

            $dt['counter'] = $counter++;
            return $dt;
        }
}
