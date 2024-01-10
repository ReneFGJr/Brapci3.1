<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class Search extends Model
{
    protected $DBGroup          = 'click';
    protected $table            = '_search';
    protected $primaryKey       = 'id_s';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_s', 's_date', 's_hour',
        's_query', 's_user', 's_page',
        's_type', 's_order', 's_session',
        's_total','s_ip'
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

    function register($q='',$total=0)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SESSION['user_id'])) {
            $user = $_SESSION['user_id'];
        } else {
            $user = 0;
        }
        $data['s_date'] = date("Y-m-d");
        $data['s_hour'] = date("H:i:s");
        $data['s_query'] = $q;
        $data['s_type'] = get("type");
        $data['s_order'] = get("type");
        $data['s_total'] = $total;
        $data['s_ip'] = $ip;
        $data['s_user'] = $user;
        $this->insert($data);
    }

    function views($id)
    {
        $dt = $this->select('count(*) as total')->where('a_v', $id)->first();
        return $dt['total'];
    }
}
