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
        } else {

            switch ($d1) {
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
        }
        return $sx;
    }

    function menu()
    {
        $menu = array();
        $menu[URL . '/tools/'] = 'Home';
        $menu['#Lattes Tools'] = lang('tools.Lattes_tools');
        $menu[URL . '/tools/lattes'] = lang('tools.my_researchers');
        $menu['#' . lang('tools.NLP')] = lang('tools.nlp_tools');
        $menu[URL . '/tools/nlp'] = lang('tools.nlp_tools');
        $sx = menu($menu);
        return $sx;
    }

    function lattes($d1,$d2,$d3,$d4)
        {
            $sx = '';
            $sx .= h(lang('tools.Lattes_tools'),2);

            switch($d1)
                {
                    default:
                        $Projects = new \App\Models\Tools\Projects();
                        $sx .= $Projects->my_projects($d1,$d2,$d3,$d4);
                        break;
                }
            return $sx;
        }
}
