<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class Views extends Model
{
    protected $DBGroup          = 'click';
    protected $table            = 'views';
    protected $primaryKey       = 'id_a';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'a_user', 'a_IP', 'a_v', 'a_session'
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
        if (isset($_SESSION['user_id']))
            {
                $user = $_SESSION['user_id'];
            } else {
                $user = 0;
            }
        $session = get("session");

        $data['a_v'] = $id;
        $data['a_IP'] = $ip;
        $data['a_user'] = $user;
        $data['a_session'] = $session;
        $this->insert($data);
    }

    function views($id)
        {
            $dt = $this->select('count(*) as total')->where('a_v',$id)->first();
            return $dt['total'];
        }
}
