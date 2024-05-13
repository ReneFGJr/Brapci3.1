<?php

namespace App\Models\Geo;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'geo_cidade';
    protected $primaryKey       = 'id_gc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_gc', 'gc_use', 'gc_name',
        'gc_lat', 'gc_long', 'gc_alt',
        'gc_type', 'gc_state', 'gc_conutry',
        'gc_code_hichart'
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

    function getCity($name)
    {
        $RSP = ['lat' => 0, 'long' => 0, 'altitude' => 0, 'name' => $name,'id'=>$name];
        $dt = $this
            ->where('gc_name', $name)
            ->orwhere('id_gc', $name)
            ->first();
        if ($dt == []) {
        } else {
            $RSP = [
                'lat' => $dt['gc_lat'],
                'long' => $dt['gc_long'],
                'altitude' => $dt['gc_alt'],
                'name' => $dt['gc_name'],
                'id' => $dt['id_gc']
            ];
        }
    return $RSP;
    }
}
