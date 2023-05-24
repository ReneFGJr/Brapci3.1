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
        $sx = '->'.$d2;
        $Native = new \App\Models\Dataverse\API\Native();
        $Dataset = new \App\Models\Dataverse\API\Dataset();
        $Tree = new \App\Models\Dataverse\Tree();
        $Dataverse = new \App\Models\Dataverse\Index();
        $server = $Dataverse->getServer();
        $token  = $Dataverse->getToken();

        $root = $Native->getDataverseRoot($server,$token);

        $sa = '';
        $sa .=  h('Migration',2);
        $sa .= '<tt>Server: <b>'.$server.'</b></tt>';
        $sa .= '<br>';
        $sa .= '<tt>Token : <b>' . $token . '</b></tt>';
        $sa .= '<br>';
        $sa .= '<tt>Root : <b>' . $root . '</b></tt>';
        $sx .= bsc($sa,12);

        $d2 = get("action");

        switch($d2)
            {
                case 'Download':
                    $doi = get("doi");
                    $sx .= h($doi);
                    echo h('================>'.$doi);
                    $sx .= $Dataset->getDataset($doi,$server,$token);
                    echo h("===FIM==");

                    break;
                default:
                    $sx .= $this->form_doi();
            }

            $sx = bs($sx);
            return $sx;
        }

    function form_doi()
        {
            $sx = '';
            $sx .= form_open();
            $sx .= form_label('Informe o número do DOI');
            $sx .= form_input('doi',get("doi"),['class'=>'full fw-form-control']);
            $sx .= form_submit('action','Download');
            $sx .= form_close();
            return $sx;
        }

    function getCollection()
        {
            $Tree = new \App\Models\Dataverse\Tree();
            $sx = $Tree->getCollections($server, $token, $root);
            return $sx;
        }


    function get_all()
        {
            $url = '/api/search?q=a';
        }
}
