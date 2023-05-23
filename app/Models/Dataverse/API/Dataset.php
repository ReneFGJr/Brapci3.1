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
        $dta = json_decode($dt);
        $dta = $dta->data;

        echo h("Enviando");

        $this->createDataset('group2',$dta);
        return $dta;
    }

    function createDataset($PARENT,$data)
    {
        $Dataverse = new \App\Models\Dataverse\Index();
        $server = $Dataverse->getServer();
        $token  = $Dataverse->getToken();

        $API = new \App\Models\Dataverse\API\Index();
        $dt['apikey'] = '919d765c-b728-4875-b50e-dd4fb71b5e6b';
        $dt['url'] = 'https://venus.brapci.inf.br/';
        $dt['api'] = 'api/dataverses/' . $PARENT . '/datasets';

        $DV = array();
        /* Protocolo */
        $DV['protocol'] = 'doi';
        //$DV['authority'] = '10.48472';
        $DV['authority'] = '10.5072/FK1/';

        $DV['publisher'] =  $data->publisher;
        $DV['productionDate'] = $data->latestVersion->productionDate;

        $DV['datasetVersion']['license']['name'] = 'CC BY 4.0';
        $DV['datasetVersion']['license']['uri'] = 'http://creativecommons.org/licenses/by/4.0';
        $DV['datasetVersion']['fileAccessRequest'] = false;

        $DV['datasetVersion']['metadataBlocks']['citation']['displayName'] = "Citation Metadata";
        $DV['datasetVersion']['metadataBlocks']['citation']['name'] = "citation";
        $DV['datasetVersion']['metadataBlocks']['citation']['fields'] = $data->latestVersion->metadataBlocks->citation->fields;

        $dt['json'] = json_encode($DV);
        $dt['method'] = 'POST';

        $dt = $API->curl($dt);
        $dta = json_decode($dt);

    }
}
