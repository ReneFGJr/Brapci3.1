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
            $sx = '<div class="container-fluid" style="height: 120px"></div>' . cr();
            switch($subact)
                {
                    case 'repository':
                        switch($id)
                            {
                                case 'viewid':
                                    $sx .= breadcrumbs(array('Research Data' => '', 'Repository' => PATH. COLLECTION.'/data/repository', 'View' => PATH . COLLECTION . '/data/repository/viewid/' . $id2));
                                    $sx .= $this->repository_view($id2);
                                    break;
                                case 'edit':
                                    $sx .= $this->repository_edit($id2);
                                    break;
                                default:
                                    $sx .= $act . '-' . $subact;
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

    function repository_view($id)
        {
            $sx = '';
            $dt = $this->find($id);
            $sx .= '<span class="small">'.lang('data.repository').'</span>';
            $sx .= h($dt['rp_name'],1);
            $sx .= '<p>'.$dt['rp_description'].'</p>';

            $sx .= '<p>' . anchor($dt['rp_url'], $dt['rp_url'], 'target="_blank"') . '</p>';
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
