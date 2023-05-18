<?php

namespace App\Models\Guide\Manual;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'guide';
    protected $table            = 'guide';
    protected $primaryKey       = 'id_g';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_g', 'g_name', 'g_active'
    ];

    protected $typeFields    = [
        'hidden', 'string', 'sn'
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

    var $path = PATH.'/guide/manual';
    var $path_back = PATH . '/guide/manual';
    var $id = 0;

    function index($d1='',$d2='',$d3='',$d4='')
        {
            echo "=>$d1,$d2,$d3,$d4";
            switch($d1)
                {
                    case 'style':
                        $Style = new \App\Models\Guide\Manual\Style();
                        $sx = $Style->index($d2, $d3, $d4);
                        break;
                    case 'export':
                        $sx = $this->export($d2,$d3,$d4);
                        break;
                    case 'viewid':
                        $id = round('0' . $d2);
                        $sx = bs(bsc($this->view($id), 12));
                        break;
                    case 'edit':
                        $id = round('0'.$d2);
                        $this->id = $id;
                        $sx = bs(bsc(form($this),12));
                        break;
                    default:
                        $sx = bs(bsc(tableview($this),12));
                }
            return $sx;
        }

        function getGuide()
            {
                if (isset($_SESSION['guide']))
                    {
                        return $_SESSION['guide'];
                    }
                return 0;
            }

        function setGuide($id)
        {
            $_SESSION['guide'] = $id;
            return 0;
        }

        function export($d1,$d2,$d3)
            {
                $sx = '';
                $model = ['dataverse'];
                if ($d2 != '')
                    {
                        switch($d2)
                            {
                                case 'dataverse':
                                    $Content = new \App\Models\Guide\Manual\Export\Dataverse();
                                    $sx = $Content->index($d1);
                                    return $sx;
                                default:
                                    $sx = 'Export not implemented';
                                    break;
                            }
                    } else {
                        $menu = [];
                        foreach ($model as $name) {
                            $menu[PATH . '/guide/manual/export/' . $d1 . '/' . $name] = lang('guide.' . $name);
                        }
                        $sx .= menu($menu);
                    }
                $sx = bs(bsc($sx));
                return $sx;
            }


        function view($id)
            {
                $id = round('0'.$id);
                if ($id == 0)
                    {
                        $id = $this->getGuide();
                    }
                $dt = $this->find($id);
                if ($dt == '') { return ''; }

                $_SESSION['guide'] = $id;

                $sx = h($dt['g_name']);
                $sx .= '<hr>';
                $sx .= $this->bnt_export($id);
                $sx .= $this->bnt_style($id);
                $sx .= '<hr>';

                /* View content */
                $Content = new \App\Models\Guide\Manual\Content();
                $sx .= $Content->index('',$id);
                return $sx;
            }
        function bnt_export($id)
            {
                $sx = '<a href="'.PATH.'/guide/manual/export/'.$id.'" class="btn btn-outline-primary">'.bsicone('download').' Export</a>';
                return $sx;
            }

        function bnt_style($id)
        {
            $sx = '<a href="' . PATH . '/guide/manual/style/' . $id . '" class="btn btn-outline-primary ms-2">' . bsicone('text') . ' CSS Style</a>';
            return $sx;
        }
}
