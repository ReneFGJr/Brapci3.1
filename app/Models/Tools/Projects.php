<?php

namespace App\Models\Tools;

use CodeIgniter\Model;
use App\Models\Socials;

class Projects extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_tools.projects';
    protected $primaryKey       = 'id_prj';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_prj', 'prj_title', 'prj_description',
        'prj_own', 'updated_at'
    ];
    protected $typeFields    = [
        'hidden', 'string*', 'text',
        'user', 'up'
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

    function my_projects($d1,$d2,$d3)
    {
        $sx = lang('tools.my_projects');

        $this->path = PATH.COLLECTION.'/lattes';
        $this->path_back = PATH . COLLECTION . '/lattes';
        $Socials = new Socials();
        $user = $Socials->getuser();

        $sx .= h($d1);

        switch($d1)
            {
                case 'harvested':
                    $sx .= $this->harvested($d2,$d3);
                    break;
                case 'harvesting_new':
                    $ProjectsHarvesting = new \App\Models\Tools\ProjectsHarvesting();
                    $sx .= $ProjectsHarvesting->harvesting_new($d2);
                    $sx = metarefresh(PATH.COLLECTION.'/lattes/viewid/'.$d2);
                    break;
                case 'edit':
                    $sx .= form($this);;
                    break;
                case 'viewid':
                    $sx = $this->viewid($d2);
                    break;
                default:
                    $sx = tableview($this);
                    break;
            }
        return $sx;
    }

    function header_project($dt)
        {
            $sx = '';
            $sx .= bsc(h($dt['prj_title'],2),12);
            $sx .= '<p>' . $dt['prj_description'] . '</p>';
            $sx .= '<hr>';
            return $sx;
        }

    function harvested($id)
        {
        $ProjectsHarvesting = new \App\Models\Tools\ProjectsHarvesting();
        $dt = $ProjectsHarvesting->find($id);

        $idp = $dt['ph_project_id'];
        $sx = '';
        $dt = $this->find($idp);
        $sx .= $this->header_project($dt);

        $sx .= $ProjectsHarvesting->form($id);

        return $sx;
        }

    function viewid($id)
        {
            $ProjectsHarvesting = new \App\Models\Tools\ProjectsHarvesting();
            $sx = '';
            $dt = $this->find($id);
            $sx .= $this->header_project($dt);

            /************************* Harvestings */
            $sx .= bsc(h(lang('tools.harvesting'),3));
            $sx .= bsc($ProjectsHarvesting->list($dt['id_prj']),12);
            $sx .= bsc($ProjectsHarvesting->btn_harvesting_new($id),12);
            $sx = bs($sx);
            return $sx;
        }
}
