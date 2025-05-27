<?php

namespace App\Models\Guide\Software;

use CodeIgniter\Model;

class Index extends Model
{
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function index($act = '', $id = '')
    {
        $sx = '';
        $sx .= "$act,$id";
        $Software = new \App\Models\Guide\Software\Software();

        switch ($act) {
            case 'os':
                $OperationalSystem = new \App\Models\Guide\Software\OperationalSystem();
                $sx = $OperationalSystem->listOS();
                break;
            case 'steps_create':
                $sx = $Software->createSteps($id);
                break;
            case 'saveStep':
                $sx = $Software->saveStep($_POST);
                break;
            case 'save':
                if (!isset($_POST['id_s'])) {
                    $dd = $Software->where('s_name', $_POST['s_name'])
                        ->where('s_version', $_POST['s_version'])
                        ->first();
                    if ($dd == [])
                    {
                        $Software->set($_POST)->insert();
                    }
                } else {
                    $sx = $Software->saveSoftware($_POST);
                }
                $sx = $this->index('list');
                break;
            case 'create':
                $sx = $Software->createSoftware($id);
                break;
            case 'edit':
                $sx = $Software->createSoftware($id);
                break;
            case 'list':
                $sx = $Software->listSoftware();
                break;
            case 'view':
                $sx = $Software->viewSoftware($id);
                break;
            case 'details':
                $sx = $this->getSoftwareDetails($id);
                break;
            default:
                $sx = "Invalid action: $act";

                $menu = [];
                $menu[PATH . '/guide/software/list'] = lang('brapci.software');
                $menu[PATH . '/guide/software/os'] = lang('brapci.os');
                $sx .= menu($menu);
                $sx = bs(bsc($sx));

                break;
        }

        return $sx;
    }
}
