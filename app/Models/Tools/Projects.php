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

    function index($d1,$d2,$d3,$d4='',$d5='')
        {
            $this->path = PATH . COLLECTION . '/project';
            $this->path_back = PATH . COLLECTION ;

            $sx = "$d1, $d2, d3=$d3, d4=$d4, d5=$d5";
            switch($d1)
                {
                    case 'api':
                        $ProjectAPI = new \App\Models\Tools\ProjectsAPIs();
                        $sx .= $ProjectAPI->index($d3,$d2,$d4,$d5);
                        break;
                    case 'viewid':
                        $sx .= $this->view($d2,$d3);
                        break;
                    case 'select':
                        $this->selection($d2);
                        $sx .= $this->view($d2, $d3);
                        break;
                    case 'edit':
                        $this->id = $d2;
                        $sx .= bs(bsc(form($this),12));
                        break;
                }
            return $sx;
        }

    function view($id)
        {
            $ProjectAPI = new \App\Models\Tools\ProjectsAPIs();
            $dt = $this->find($id);
            $sx = view('Tools/Project/header',$dt);

            $sx .= $ProjectAPI->services($id);
            $sx = bs($sx);
            return $sx;
        }

    function selection($id)
        {
            $dt = $this->find($id);
            $_SESSION['project']['id'] = $dt['id_prj'];
            return $id;
        }

    function selected()
        {
            if(isset($_SESSION['project']['id']))
                {
                    return round($_SESSION['project']['id']);
                } else {
                    return 0;
                }
        }

    function my_projects($d1='',$d2='',$d3='')
    {
        $sx = bsc(h(lang('brapci.my_projects').'<hr>',3),12);

        $this->path = PATH.COLLECTION;
        $this->path_back = PATH . COLLECTION;
        $Socials = new Socials();
        $user = $Socials->getuser();

        /************************************** CARDS */
        $dt = $this->where('prj_own',$user)->orderBy('updated_at desc')->findAll();
        for($r=0;$r < count($dt);$r++)
            {
                $line = $dt[$r];
                $link = PATH . COLLECTION.'/project/select/'.$line['id_prj'];
                $txt = 'brapci.select';
                $sc = ''.cr();
                $sc .= '<div class="p-2" style="width: 100%; min-height: 200px; position: relative; border: 2px solid #3333; border-radius: 5px;">';
                $sc .= '<b>'.$line['prj_title'].'</b>';
                $sc .= '<p style="font-size: 0.7em;">'. $line['prj_description'].'</p>';
                $sc .= '<div style="position:absolute; bottom: 0px;">';
                $sc .= '<a href="' . $link . '" class="mb-2 btn btn-primary" style="width: 100%;">' . lang($txt) . '</a>';
                $sc .= '</div>';
                $sc .= '</div>'.cr();
                //prj_description
                $sx .= bsc($sc,3);
            }

        $link = PATH . COLLECTION . '/project/edit/0';
        $txt = 'brapci.project_new';
        $sc = '' . cr();
        $sc .= '<div class="p-2" style="min-height: 200px; position: relative; border: 2px solid #3333; border-radius: 5px">';
        $sc .= '<b>' . lang('brapci.new_project'). '</b>';
        $sc .= '<p style="font-size: 0.7em;">' . lang('brapci.new_project_info') . '</p>';
        $sc .= '<div style="position:absolute; bottom: 0px;">';
        $sc .= '<a href="' . $link . '" class="mb-2 btn btn-outline-danger" style="width: 100%;">' . lang($txt) . '</a>';
        $sc .= '</div>';
        $sc .= '</div>' . cr();
        //prj_description
        $sx .= bsc($sc, 3);

        return bs($sx);

        switch($d1)
            {
                case 'harvest':
                    $ProjectsHarvestingXML = new \App\Models\Tools\ProjectsHarvestingXML();
                    $sx .= $ProjectsHarvestingXML->getXML($d2);
                    break;
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
        $ProjectsHarvestingXML = new \App\Models\Tools\ProjectsHarvestingXML();
        $dt = $ProjectsHarvesting->find($id);

        $idp = $dt['ph_project_id'];
        $sx = '';
        $dt = $this->find($idp);
        $sx .= $this->header_project($dt);

        $sa = bsc(h('Harvesting'). $ProjectsHarvestingXML->btn_harvesting($idp),6);
        $sb = bsc($ProjectsHarvesting->form($id),6);

        $sx .= bs($sa.$sb);

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
