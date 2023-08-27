<?php

namespace App\Models\Handle;

use CodeIgniter\Model;

class Server extends Model
{
    protected $DBGroup          = 'handle';
    protected $table            = 'server';
    protected $primaryKey       = 'id_s';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_s','s_handle','s_password',
        's_admpriv','s_home'
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

    function le($id)
        {
            return $this->get($id);
        }
    function get($id)
        {
            return $this->find($id);

        }
    function getHandle($handle)
        {
            $dt = $this->where('s_handle',$handle)->first();
            //echo $this->getlastquery();
            return $dt;
        }
}
