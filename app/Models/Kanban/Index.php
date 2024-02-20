<?php

namespace App\Models\Kanban;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'kanban';
    protected $table            = 'kanban';
    protected $primaryKey       = 'id_kb';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kb','kb_deadline'
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

    function index($d1,$d2)
        {
            $RSP = [];
            $RSP['status'] = '200';
            return $RSP;
        }

        function getUserApi($key)
            {
                $social = new \App\Models\Socials();
                $user = $social->where('us_apikey',$key)->first();
                return $user;
            }

        function get_schedule($us)
            {
                $key = get("apikey");
                $key = 'ff63a314d1ddd425517550f446e4175e';
                $user = $this->getUserApi($key);

                $userID = $user['id_us'];

                $dd = [];
                $cp = '*';
                $cp = 'id_kb as ID, kb_titulo as Task,
                        kb_description as Description,
                        kb_deadline as Deadline,
                        kb_prioridade as Prior';
                $dd['user'] = $user;
                $dd['backlog'] = $this
                    ->select($cp)
                    ->where('kb_user',$userID)
                    ->where('kb_etapa',0)
                    ->orderBy('kb_prioridade, kb_deadline')
                    ->findAll();
                $dd['todo'] = $this
                ->select($cp)
                    ->where('kb_user', $userID)
                    ->where('kb_etapa', 1)
                    ->orderBy('kb_prioridade, kb_deadline')
                    ->findAll();
                $dd['doing'] = $this
                ->select($cp)
                    ->where('kb_user', $userID)
                    ->where('kb_etapa', 2)
                    ->orderBy('kb_prioridade, kb_deadline')
                    ->findAll();
                $dd['review'] = $this
                ->select($cp)
                    ->where('kb_user', $userID)
                    ->where('kb_etapa', 3)
                    ->orderBy('kb_prioridade, kb_deadline')
                    ->findAll();
                echo json_encode($dd);
                exit;

            }
}
