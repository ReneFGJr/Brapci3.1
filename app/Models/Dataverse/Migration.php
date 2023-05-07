<?php

namespace App\Models\Dataverse;

use CodeIgniter\Model;

class Migration extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'migrations';
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

    function index($d1,$d2,$d3)
        {
        $sx = '';
        $Native = new \App\Models\Dataverse\API\Native();
        $Tree = new \App\Models\Dataverse\Tree();
        $Dataverse = new \App\Models\Dataverse\Index();
        $server = $Dataverse->getServer();
        $token  = $Dataverse->getToken();
        $root = $Native->getDataverseRoot($server);

        $sa = '';
        $sa .=  h('Migration',2);
        $sa .= '<tt>Server: <b>'.$server.'</b></tt>';
        $sa .= '<br>';
        $sa .= '<tt>Token : <b>' . $token . '</b></tt>';
        $sa .= '<br>';
        $sa .= '<tt>Root : <b>' . $root . '</b></tt>';
        $sx .= bsc($sa,12);

        $sx .= $Tree->getCollections($server,$token,$root);

            $sx = bs($sx);
            return $sx;
        }

    function get_all()
        {
            $url = '/api/search?q=a';
        }
}
