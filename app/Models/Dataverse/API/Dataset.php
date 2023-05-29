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
        $dt['api'] = '/api/datasets/:persistentId/?persistentId=hdl:' . $DOI;

        $dt = $API->curl($dt);
        //$dt = troca($dt, '"multiple":false', '"multiple":0');
        //$dt = troca($dt, '"multiple":true', '"multiple":1');
        $dta = json_decode($dt,true);
        $DV = [];

        /*************************** Title */
        $dtaf = $dta['data']['latestVersion']['metadataBlocks']['citation']['fields'];
        $DV['datasetVersion']['metadataBlocks']['citation']['fields'] = $dtaf;

        for($r=0;$r < count($DV['datasetVersion']['metadataBlocks']['citation']['fields']);$r++)
            {
                if($DV['datasetVersion']['metadataBlocks']['citation']['fields'][$r]['typeName'] == 'datasetContact')
                    {
                        pre($DV['datasetVersion']['metadataBlocks']['citation']['fields'][$r]['typeName']);
                    }
            }


        pre($DV);
        //"datasetContactEmail":{"typeName":"datasetContactEmail","multiple":false,"typeClass":"primitive","value":"cariniana@ibict.br"}}]}

        $sx = $this->createDataset(get("dataverse_d"), $DV);
        /***************************** */
        return $sx;
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
        //$dd['url'] = 'https://venus.brapci.inf.br/';
        $dd['api'] = 'api/dataverses/' . $PARENT . '/datasets';
        //$dd['apikey'] = '919d765c-b728-4875-b50e-dd4fb71b5e6b';
        $dd['url'] = get("url_d");
        $dd['apikey'] = get("apikey_d");
        $dd['dataverse'] = get("dataverse_d");

        #$rst = $API->curlExec($dd);
        $rst = $API->curlExec($dd);
        $sx = '<br><tt>'.$API->url.'</tt>';
        $rsp = json_decode($rst, true);

        return $sx;
    }
}
