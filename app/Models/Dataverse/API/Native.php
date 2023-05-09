<?php

namespace App\Models\Dataverse\API;

use CodeIgniter\Model;

class Native extends Model
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

    /******************* SERVER */
    function getServerInfo($server, $token, $root)
    {
        //https://guides.dataverse.org/en/latest/api/native-api.html#show-dataverse-software-version-and-build-number
        /* Pegar a versão do Dataverse */
        $API = new \App\Models\Dataverse\API\Index();
        $url = $server . 'api/info/server';
        $dt['url'] = $url;
        $dt['api'] = '';
        $dt['apikey'] = $token;
        $txt = $API->curl($dt);
        return $txt;
    }

    function getDataverseVersion($server)
        {
        //https://guides.dataverse.org/en/latest/api/native-api.html#show-dataverse-software-version-and-build-number
        /* Pegar a versão do Dataverse */
        $API = new \App\Models\Dataverse\API\Index();
        $url = $server . 'api/info/version';
        $txt = $API->curl($url);
        return $txt;
        }

    function getDataverseRoot($server)
        {
            $API = new \App\Models\Dataverse\API\Index();
            $url = $server . 'api/dataverses/1';
            $dt['url'] = $url;
            $dt['api'] = '';
            $dt['apikey'] = '';
            $txt = $API->curl($dt);

            $txt = (array)json_decode($txt);
            $root = (array)$txt['data'];
            $root = $root['alias'];

            return $root;
        }

    function getDataverseInfo($server,$token,$root)
    {
        //https://guides.dataverse.org/en/latest/api/native-api.html#show-dataverse-software-version-and-build-number
        /* Pegar a versão do Dataverse */
        $API = new \App\Models\Dataverse\API\Index();
        $url = $server . 'api/info/server';
        $url = $server . 'api/dataverses/' . $root;

        $txt = $API->curl($url);
        return $txt;
    }

    function getCollections($server,$token='',$root='')
        {
            $dt['url'] = $server;
            $dt['api'] = 'api/dataverses/' . $root . '/contents';
            $dt['apikey'] = $token;

            $API = new \App\Models\Dataverse\API\Index();
            $Dataverse = new \App\Models\Dataverse\Index();
            //$txt = $API->curlExec($dt);
            $txt = $API->curl($dt);
            return $txt;
        }
}
