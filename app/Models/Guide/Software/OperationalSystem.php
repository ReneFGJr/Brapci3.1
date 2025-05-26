<?php

namespace App\Models\Guide\Software;

use CodeIgniter\Model;

class OperationalSystem extends Model
{
    protected $DBGroup          = 'software';
    protected $table            = 'operation_system';
    protected $primaryKey       = 'id_os';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_os',
        'os_name',
        'os_description',
        'os_url',
        'os_version',
        'created_at'
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

    function viewOS($id = '')
    {
        $d = $this->select('id_s, s_name, s_description, s_url, s_version, created_at');
        $d->where('id_s', $id);
        $dt = [];
        $dt['software'] = $this->first();

        $sx = view('Software/OperationSystemView', $dt);
        return $sx;
    }

    function listOS($d1 = '', $d2 = '')
    {

        $d = $this->select('id_os, os_name, os_description, os_url, os_version, created_at');
        $dt = [];
        $dt['oses'] = $this->findAll();

        $sx = view('Software/OperationSystemList', $dt);
        return $sx;
    }
}
