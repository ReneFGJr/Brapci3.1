<?php

namespace App\Models\ResearchData;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'research_data_repository';
    protected $primaryKey       = 'id_rp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_rp', 'rp_name', 'rp_description',
        'rp_url', 'rp_group'
    ];

    protected $typeFields    = [
        'hidden', 'string:100*', 'text',
        'string*', 'string'
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

    function index($act, $subact, $id, $id2)
        {
            $sx = $act.'-'. $subact;
            switch($subact)
                {
                    case 'repository':
                        switch($id)
                            {
                                case 'edit':
                                    $sx .= $this->repository_edit($id2);
                                    break;
                                default:
                                    $sx .= $this->repository_list();
                                    break;
                            }
                        break;
                    default:
                        $sx .= $this->menu();
                        break;
                }
            $sx = bs(bsc($sx,12));
            return $sx;
        }

    function repository_edit($id)
        {
            $this->id = $id;
            $this->path = PATH . COLLECTION . '/data/repository';
            $this->path_back = PATH . COLLECTION . '/data/repository';
            $sx = form($this);
            return $sx;
        }

    function repository_list()
        {
            $this->path = PATH . COLLECTION . '/data/repository';
            $sx = tableview($this);
            return $sx;
        }

    function menu()
    {

        $menu['#AI'] = 'Research Data';
        $menu[PATH . COLLECTION . '/data/repository'] = lang('data.repository');

        $sx = '';
        $sx .= MENU($menu);

        return $sx;
    }
}
