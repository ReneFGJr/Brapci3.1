<?php

namespace App\Models\Wordpress;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'wordpress';
    protected $table            = 'sources';
    protected $primaryKey       = 'id_wp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_wp', 'wp_name', 'wp_version_1',
        'wp_version_2', 'wp_version_3', 'wp_acronic',
        'wp_package_url','wp_status'
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

    function index($d1,$d2,$d3,$d4)
        {
            $dt = [];
            switch($d1)
                {
                    case 'check':
                        return $this->check($d2,$d3,$d4);
                        break;
                    default:
                        $dt['timestamp'] = date("Y-m-dTH-m-s");
                        return json_encode($dt);
                }
        }

    function check($d2,$d3,$d4)
        {
            $ds = $this->where('wp_acronic',$d2)->first();

            if ($ds == '')
                {
                    $dt['system'] = 'not_found';
                    $dt['error'] = '404';
                    $dt['timestamp'] = date("Y-m-dTH-m-s");
                } else {
                    $dt['wp_package_url'] = URL.'/_repository/wordpress/';
                    $dt['details_url'] = URL . '/_repository/wordpress/update.html';
                    $dt['download_url'] = "https://rudrastyh.com/themes/misha-theme/2.0.zip";
                    $dt['version'] =$ds['wp_version_1'].'.'.$ds['wp_version_2'].'.'. $ds['wp_version_3'];
                    $dt['timestamp'] = date("Y-m-dTH-m-s");
                }
            return json_encode($dt);
        }
}
