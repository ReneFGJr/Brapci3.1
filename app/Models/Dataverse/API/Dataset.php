<?php

namespace App\Models\Dataverse\API;

use CodeIgniter\Model;

class Dataset extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'natives';
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

    function getDataset($DOI, $server, $key)
    {
        $Dataverse = new \App\Models\Dataverse\Index();
        $server = $Dataverse->getServer();
        $token  = $Dataverse->getToken();

        $API = new \App\Models\Dataverse\API\Index();
        $dt['apikey'] = $token;
        $dt['url'] = $server;
        $dt['api'] = '/api/datasets/:persistentId/?persistentId=doi:' . $DOI;

        $dt = $API->curl($dt);
        $dta = json_decode($dt,true);

        $DV['datasetVersion']['metadataBlocks'] = $dta['data']['latestVersion']['metadataBlocks'];

        $this->createDataset('group2', $DV);
        return $dta;
    }

    function createDataset($PARENT,$data)
    {
        $Dataverse = new \App\Models\Dataverse\Index();
        $server = $Dataverse->getServer();
        $token  = $Dataverse->getToken();

        $API = new \App\Models\Dataverse\API\Index();

        $dir = '../.tmp/dataverse/dataset/';
        dircheck($dir);
        $file = $dir . 'dataset-send.json';
        file_put_contents($file,json_encode($data));

        $dd['AUTH'] = true;
        $dd['POST'] = true;
        $dd['FILE'] = $file;
        $dd['url'] = 'https://venus.brapci.inf.br/';
        $dd['api'] = 'api/dataverses/' . $PARENT . '/datasets';
        $dd['apikey'] = '919d765c-b728-4875-b50e-dd4fb71b5e6b';

        echo "========= CRIANDO DATASET";

        #$rst = $API->curlExec($dd);
        $rst = $API->curl($dd);
        $rsp = json_decode($rst, true);

        pre($rsp);

        echo "FIM";
        exit;

    }
}
