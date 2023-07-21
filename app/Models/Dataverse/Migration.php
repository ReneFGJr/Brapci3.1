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

    function indexFrom($d1, $d2, $d3)
    {
        $sx = '';
        $Native = new \App\Models\Dataverse\API\Native();
        $Dataset = new \App\Models\Dataverse\API\Dataset();
        $Tree = new \App\Models\Dataverse\Tree();
        $Dataverse = new \App\Models\Dataverse\Index();
        $server = $Dataverse->getServer();
        $token  = $Dataverse->getToken();

        if (($server == '') or ($token == '')) {
            $sx .= bsmessage('Server ou Token não informado');
            return $sx;
        }

        $sx .= $this->from_doi();

        $doi = get("doi");
        $source = get("url_o");

        if (($doi != '') and ($source != ''))
            {
                $sx .= h('Harvesting...',2);
                $sx .= $Dataset->getDataset2($doi, $source);
            }
        return $sx;
    }

    function from_doi()
    {
        $sx = h("Repatriação de Dados");
        $sx .= form_open();
        $sx .= form_label('Informe o número do DOI, ex: 10.57810/lattesdata/NSW6QE');
        $sx .= form_input('doi', get("doi"), ['class' => 'full fw-form-control']);

        $sx .= form_label('Origen do dado URL (do repositório: ex: https://lattesdata.cnpq.br/');
        $sx .= form_input('url_o', get("url_o"), ['class' => 'full fw-form-control']);

        $sx .= form_label('Comunidade Dataverse (Destino) ex: root');
        $sx .= form_input('dataverse_d', get("dataverse_d"), ['class' => 'full fw-form-control']);

        $sx .= form_submit('action', 'Migrate');
        $sx .= form_close();
        return $sx;
    }

    function index($d1,$d2,$d3)
        {
        $sx = '';

        $Native = new \App\Models\Dataverse\API\Native();
        $Dataset = new \App\Models\Dataverse\API\Dataset();
        $Tree = new \App\Models\Dataverse\Tree();
        $Dataverse = new \App\Models\Dataverse\Index();
        $server = $Dataverse->getServer();
        $token  = $Dataverse->getToken();

        if (($server == '') or ($token == ''))
            {
                $sx .= bsmessage('Server ou Token não informado');
                return $sx;
            }

        /***************************************** ROOT */
        $file = '../.tmp/dataverse/'.md5($server).'.root.json';
        dircheck('../.tmp/dataverse/');
        if (!file_exists($file))
            {
                $root = $Native->getDataverseRoot($server, $token);
                file_put_contents($file,$root);
                $root = json_decode($root,true);
            }
        $root = file_get_contents($file);

        /**************************************** MIGRATION */
        $d2 = get("action");

        $sa = '';
        $sa .=  h('Migration',2);
        $sa .= '<tt>Server: <b>'.$server.'</b></tt>';
        $sa .= '<br>';
        $sa .= '<tt>Token : <b>' . $token . '</b></tt>';
        $sa .= '<br>';
        $sa .= '<tt>Root : <b>' . $root . '</b></tt>';
        $sa .= '<br>';
        $sa .= '<tt>Action : <b>' . $d2 . '</b></tt>';
        $sx .= bsc($sa,12);


        switch($d2)
            {
                case 'Download':
                    $doi = get("doi");
                    $url_d = get("url_d");
                    $dataverse_d = get("dataverse_d");
                    $apikey_d = get("apikey_d");

                    if (($doi != '') and ($url_d != '') and ($dataverse_d != '') and ($apikey_d != ''))
                        {
                            $sx .= h($doi);
                            $sx .= $Dataset->getDataset($doi, $server, $token);
                        } else {
                            $sx = bsmessage('Dados incompletos',3);
                            $sx .= $this->form_doi();
                            return $sx;
                        }


                    break;

                default:
                    $sx .= $this->form_doi();
                    break;
            }

            $sx = bs($sx);
            return $sx;
        }

    function form_doi()
        {
            $sx = '';
            $sx .= form_open();
            $sx .= form_label('Informe o número do DOI, ex: 10.5072/FK2/QCVN58');
            $sx .= form_input('doi',get("doi"),['class'=>'full fw-form-control']);

            $sx .= form_label('Destino URL');
            $sx .= form_input('url_d', get("url_d"), ['class' => 'full fw-form-control']);

            $sx .= form_label('Destino Databerve (Name do Dataverse), ex: https://venus.brapci.inf.br/');
            $sx .= form_input('dataverse_d', get("dataverse_d"), ['class' => 'full fw-form-control']);

            $sx .= form_label('APIKEY Destino, ex: 919d765c-b728-4875-b50e-dd4fb71b5e6');
            $sx .= form_input('apikey_d', get("apikey_d"), ['class' => 'full fw-form-control']);

            $sx .= form_submit('action','Download');
            $sx .= form_close();
            return $sx;
        }

    function getCollection()
        {
            $server = '';
            $token = '';
            $root = '';
            $Tree = new \App\Models\Dataverse\Tree();
            $sx = $Tree->getCollections($server, $token, $root);
            return $sx;
        }


    function get_all()
        {
            $url = '/api/search?q=a';
        }
}
