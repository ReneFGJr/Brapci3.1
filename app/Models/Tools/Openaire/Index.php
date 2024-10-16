<?php

namespace App\Models\Tools\Openaire;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'openaire';
    protected $table            = 'openaire';
    protected $primaryKey       = 'id_openaire';
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


    function index($d1,$d2,$d3,$d4)
        {
            $sx = '';
            $prj = 0;
            if (isset($_SESSION['project']['id']))
                {
                    $prj = round('0'. $_SESSION['project']['id']);
                }
            $sx .= bsc('OpenAire', 10);
            $sx .= bsc('<img src="'.URL.'/img/logo/openaire.png" class="img-fluid">',2);

            switch($d1)
                {
                    case 'result':
                        $sx .= $this->analysis($prj);
                        break;
                    default:
                        $sa = $this->resume($prj);
                        $sa .= '=>'.$d1;
                        $sx .= bsc($sa, 12);
                        break;
                }
            $sx = bs($sx);
            return $sx;
        }

    function analysis($id)
        {
            $LinkProvider = new \App\Models\Tools\Openaire\LinkProvider();
            return $LinkProvider->analysis($id);
        }

    function resume($id)
        {
            $LinkProvider = new \App\Models\Tools\Openaire\LinkProvider();
            return $LinkProvider->resume($id);
        }

    function import_doi($prj,$dois)
        {
            $sx = '';
            $LinkProvider = new \App\Models\Tools\Openaire\LinkProvider();
            $sx .= '<ul>';
            foreach($dois as $doi=>$id)
                {
                    $sx .= '<li>'.$doi.' '.$LinkProvider->register($doi,$prj).'</li>';
                }
            $sx .= '</ul>';
            $sx = bs(bsc($sx));
            return $sx;

        }

    function linksFromPid($doi)
        {

        }
}
