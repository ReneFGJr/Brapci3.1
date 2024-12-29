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

    function getActivities($id)
        {
            $Socials = new \App\Models\Socials();
            $user = get("user");
            $RSP = [];
            $RSP['results'] = [];
            $RSP['search'] = '';
            $works = [];
            if ($user != '')
                {
                    $dt_user = $Socials->validToken($user);
                    if (isset($dt_user['ID']))
                        {
                            $cp = 'ID, id_jnl, JOURNAL, IDJ, ISSUE, CLASS, SESSION, LEGEND, TITLE, AUTHORS, KEYWORDS, cover, id_a';
                            $dtw =
                                $this
                                ->select($cp)
                                ->join('brapci_elastic.dataset','a_v = ID')
                                ->where('a_user',$dt_user['ID'])
                                ->orderBy('id_a desc')
                                ->findAll(10);
                            foreach($dtw as $idw=>$linew)
                                {
                                    $ddd = [];
                                    $ddd['id'] = $linew['id_a'];
                                    $ddd['score'] = 1;
                                    $ddd['type'] = $linew['CLASS'];
                                    $ddd['year'] = $linew['YEAR'];
                                    $ddd['data'] = $linew;
                                }
                        }

                    $RSP['total'] = count($RSP['works']);
                } else {
                    $RSP['total'] = 0;
                }
            echo json_encode($RSP);
            exit;
        }

    function register($id)
    {
        $ip = IP();
        $user = get("user");
        if ($user != '')
            {
                $Social = new \App\Models\Socials();
                $us = $Social->validToken($user);
                if (isset($us['ID']))
                    {
                        $user = $us['ID'];
                    } else {
                        $user = -1;
                    }
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
