<?php

namespace App\Models\Tools;

use CodeIgniter\Model;

class ProjectsAPIs extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_tools.api_tools_project';
    protected $primaryKey       = 'id_atp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [

        'id_atp', 'apt_at', 'apt_project', 'apt_active'
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

    function index($act,$id,$id2='',$id3='',$id4='')
        {
            switch($act)
                {
                    case 'lattes':
                        $Lattes = new \App\Models\Lattes\Index();
                        $sx = $Lattes->index($id2,$id,$id3,$id4);
                        break;
                    case 'active':
                        $Project = new \App\Models\Tools\Projects();
                        $prj = $Project->selected();
                        $this->register($prj,$id);
                        $sx = metarefresh(PATH.COLLECTION. '/project/select/'.$prj);
                        break;
                    default:
                        $sx = 'ACTION:'.$act.'='.$id;
                }
            return $sx;
        }

    function register($prj,$api)
        {
            $dt = $this
            ->where('apt_at',$api)
            ->where('apt_project',$prj)
            ->findAll();

            if (count($dt) == 0)
                {
                    $dt['apt_at'] = $api;
                    $dt['apt_project'] = $prj;
                    $dt['apt_active'] = 1;
                    $this->set($dt)->insert();
                } else {
                    $dt['apt_active'] = 1;
                    $this
                        ->set($dt)
                        ->where('apt_at', $api)
                        ->where('apt_project', $prj)
                        ->update();
                }
        }

    function services($prj)
        {
            $dt = $this
                ->join('brapci_tools.api_tools', 'apt_at = id_at','RIGHT')
                ->orderBy('apt_at, at_name')
                ->findAll();

            $sx = '';
            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    if ($line['id_atp'] == '')
                    {
                        $sc = h($line['at_name'], 3);
                        $sc .= '<p class="small">' . $line['at_desciption'] . '</p>';
                        $sx .= bsc($sc, 11);

                        $sc = '<a class="btn btn-outline-primary" href="' . PATH . COLLECTION . '/project/api/'.$line['id_at'].'/active">' . lang('brapci.active') . '</a>';
                        $sx .= bsc($sc, 1);

                    } else {
                        $link = '<a href="'.PATH.COLLECTION.'/project/api/'.$line['apt_project'].'/'.strtolower($line['at_name']).'">';
                        $linka = '</a>';
                        $sc = h($link.$line['at_name'].$linka, 3);
                        $sc .= '<p class="small">' . $line['at_desciption'] . '</p>';
                        $sx .= bsc($sc, 11);

                        $sc = lang('brapci.actived');
                        $sx .= bsc($sc, 1);
                    }

                }
            return $sx;
        }
}
