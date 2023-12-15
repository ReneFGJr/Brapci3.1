<?php

namespace App\Models\Tools;

use App\Models\Socials;
use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '')
    {
        $sx = '';
        $Socials = new Socials();
        $user = $Socials->getuser();

        if ($user == 0) {
            $sx .= bsmessage(lang('brapci.user_not_loged'), 3);
            $sx = bs(bsc($sx, 12));
            return $sx;
        }

        $Projects = new \App\Models\Tools\Projects();
        $idp = $Projects->selected();

        $dt = $Socials->find($user);

        switch ($d1) {
            case 'openaire':
                $Openaire = new \App\Models\Tools\Openaire\Index();
                $sx .= $Openaire->index($d2,$d3, $d4, $d5);
                break;
            case 'projects':
                $sx .= view("Tools/welcome", $dt);
                $sx .= $Projects->my_projects();
                break;
            case 'nlp':
                $NLP = new \App\Models\AI\NLP\Index();
                $sx .= $NLP->index($d2, $d3, $d4, $d5);
                break;
            case 'lattes':
                $sx .= $this->lattes($d2, $d3, $d4, $d5);
                break;
            default:
                $sx .= $this->menu();
                break;
        }
        $sx = bs(bsc($sx, 12));

        return $sx;
    }

    function menu()
    {
        $menu = array();
        $menu['#Lattes Tools'] = lang('tools.zone_my_projects');
        $menu[URL . '/tools/projects'] = lang('tools.my_projects');
        $menu['#Lattes Tools'] = lang('tools.Lattes_tools');
        $menu[URL . '/tools/lattes'] = lang('tools.my_researchers');
        $menu[URL . '/tools/lattes/search'] = lang('tools.lattes_search');

        $menu['#' . lang('tools.Clean')] = lang('tools.clean_tools');
        $menu[URL . '/tools/nlp/clean'] = lang('tools.clean_tools');

        $menu['#' . lang('tools.NLP')] = lang('tools.nlp_tools');
        $menu[URL . '/tools/nlp'] = lang('tools.nlp_tools');



        $sx = menu($menu);
        return $sx;
    }

    function lattes($d1, $d2, $d3, $d4)
    {
        $Lattes = new \App\Models\Lattes\Index();
        return $Lattes->index($d1, $d2, $d3, $d4);
    }
}
