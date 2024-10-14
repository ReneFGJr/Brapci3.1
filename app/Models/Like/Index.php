<?php

namespace App\Models\Like;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'liked';
    protected $table            = 'likes';
    protected $primaryKey       = 'id_lk';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_lk',
        'lk_user',
        'lk_id',
        'lk_status',
        'lk_update'
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

    function getLike($id, $user)
    {
        $RSP = [];
        $RSP['liked'] = 0;

        $Social = new \App\Models\Socials();
        $dtUser = $Social->where('us_apikey', $user)->First();

        if ($dtUser != []) {
            $userID = $dtUser['id_us'];
            $dt = $this
                ->where('lk_user', $userID)
                ->where('lk_id', $id)
                ->first();

            if ($dt)
                {
                    $RSP['liked'] = $dt['lk_status'];
                }
        }
        return $RSP;
    }

    function setID($id, $user)
    {
        $Social = new \App\Models\Socials();
        $dtUser = $Social->where('us_apikey', $user)->First();

        if ($dtUser != []) {
            $userID = $dtUser['id_us'];
            $dt = $this
                ->where('lk_user', $userID)
                ->where('lk_id', $id)
                ->first();

            $dd = [];
            $dd['lk_user'] = $userID;
            $dd['lk_id'] = $id;
            $dd['lk_status'] = '1';
            $dd['lk_update'] = date("Y-m-d") . 'T' . date('H:i:s');

            if ($dt == []) {
                $this->set($dd)->insert();
            } else {
                if ($dt['lk_status'] != 1) {
                    $this->set($dd)->where('id_lk', $dt['id_lk'])->update();
                }
            }
            $RSP['status'] = '200';
            $RSP['message'] = 'Success - Marked ' . $id;
        } else {
            $RSP['status'] = '500';
            $RSP['message'] = 'Usuário inválido (apikey)';
        }
        return $RSP;
    }

    function unsetID($id, $user)
    {
        $Social = new \App\Models\Socials();
        $dtUser = $Social->where('us_apikey', $user)->First();

        if ($dtUser != []) {
            $userID = $dtUser['id_us'];
            $dt = $this
                ->where('lk_user', $userID)
                ->where('lk_id', $id)
                ->first();

            $dd = [];
            $dd['lk_status'] = '0';
            $dd['lk_update'] = date("Y-m-d") . 'T' . date('H:i:s');

            if ($dt != []) {
                if ($dt['lk_status'] == 1) {
                    $this->set($dd)->where('id_lk', $dt['id_lk'])->update();
                }
            }
            $RSP['status'] = '200';
            $RSP['message'] = 'Success - Dismarked ' . $id;
        } else {
            $RSP['status'] = '500';
            $RSP['message'] = 'Usuário inválido (apikey)';
        }
        return $RSP;
    }

    function status()
    {
        $RSP = [];
        $RSP['post'] = $_POST;
        $RSP['get'] = $_POST;
        return $RSP;
    }
}
