<?php

namespace App\Models\Handle;

use CodeIgniter\Model;

class Handle extends Model
{
    protected $DBGroup          = 'handle';
    protected $table            = 'handle';
    protected $primaryKey       = 'id_hdl';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_hdl','hdl_handle','hdl_prefix',
        'hdl_hs_admin','hdl_url','hdl_email',
        'hdl_desc'
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

    function register($hdl,$url,$email,$desc,$status,$type)
        {
            $Historic = new \App\Models\Handle\Historic();
            $dd['hdl_handle'] = $hdl;
            $dd['hdl_prefix'] = substr($hdl,0,strpos($hdl,'/'));
            $dd['hdl_hs_admin'] = $status;
            $dd['hdl_url'] = $url;
            $dd['hdl_email'] = $email;
            $dd['hdl_desc'] = $desc;

            $dt = $this->where('hdl_handle',$hdl)->first();
            if ($dt == '')
                {
                    $this->set($dd)->insert();
                }
            $dh = [];
            $dh['hh_hadle'] = $hdl;
            $dh['hh_description'] = 'CREATE ' . $url . cr() . 'email:' . $email . cr() . 'Description:' . $desc;
            $dh['hh_action'] = $type;
            $dg['hh_status'] = $status;
            $Historic->register($dh);
        }
}
