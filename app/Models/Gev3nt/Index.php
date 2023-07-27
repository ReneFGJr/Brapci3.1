<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'Gev3nt';
    protected $table            = 'events';
    protected $primaryKey       = 'id_ev';
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

    function index($d1='',$d2='',$d3='',$d4='',$d5='')
        {
            $sx = '';
            return $sx;
        }

    function events($type=0)
        {
                $dt = $this
                    ->join('event_sections','es_event = id_e')
                    ->where('es_active',$type)
                    ->orderBy('es_data, es_hora_ini')
                    ->findAll();
                return $dt;
        }
}
