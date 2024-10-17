<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class Events extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event';
    protected $primaryKey       = 'id_e';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_e',
        'e_name',
        'e_url',
        'e_description',
        'e_active',
        'e_logo',
        'e_sigin_until'
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
            $dt = $this->where('id_e',$id)->first();
            return $dt;
        }

    function open_event($user='')
        {
            $cp = '*';

            if ($user != '')
                {
                    $dt = $this
                    ->select($cp)
                    ->join('event_inscritos', '(ein_event = id_e) and (ein_user = ' . $user . ')', 'LEFT')
                    ->where("e_sigin_until >=  '". date("Y-m-d")."'")
                    ->where('e_active',1)
                    ->orderby('e_sigin_until')
                    ->findAll();
                } else {
                    $dt = $this
                        ->select($cp)
                        ->join('event_inscritos', '(ein_event = 0)', 'LEFT')
                        ->where("e_sigin_until >=  '" . date("Y-m-d") . "'")
                        ->where('e_active', 1)
                        ->orderby('e_sigin_until')
                        ->findAll();
                }
            return $dt;

        }
}
