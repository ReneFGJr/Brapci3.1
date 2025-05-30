<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class Download extends Model
{
    protected $DBGroup          = 'click';
    protected $table            = 'download';
    protected $primaryKey       = 'id_dw';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'dw_rdf', 'dw_ip', 'dw_user'
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

    function register($id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SESSION['user_id'])) {
            $user = $_SESSION['user_id'];
        } else {
            $user = 0;
        }
        $data['dw_rdf'] = $id;
        $data['dw_ip'] = $ip;
        $data['dw_user'] = $user;
        $this->insert($data);
    }

    function views($id)
    {
        $dt = $this->select('count(*) as total')->where('dw_rdf', $id)->first();
        return $dt['total'];
    }

}
