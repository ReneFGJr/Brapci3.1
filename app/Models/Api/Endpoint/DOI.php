<?php
/*
@category API
@package DOI Metadata
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2024 CC-BY
@access public/private/apikey
@example $URL/api/doi/?doi=101010/xxxxx
*/
namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class DOI extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'dois';
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

    function index($d1='',$d2='',$d3='')
        {
            $DOI = get("doi");
            $RSP['status'] = '500';

            switch($d1)
                {
                    default:
                        $DOI = '10.5281/zenodo.159501';
                        $RSP = $this->getDataCite($DOI);
                }
            return $RSP;
        }

    function getDataCite($doi)
        {
            $RSP = [];
            $url = "https://api.datacite.org/dois/$doi";
            try {
                $txt = file_get_contents($url);
                $DT = $this->metadataDataCite($txt);
                print($txt);
            } catch (Exception $e) {
                $RSP['status'] = '500';
                $RSP['message'] = $e->getMessage();
            } finally {
                $RSP['status'] = '500';
                $RSP['metadata'] = $txt;
            }
            return $RSP;
        }

        function metadataDataCite($json)
            {
                file_put_contents('./tmp/x.json', $json);
                $xml = (array)json_decode($json);
                pre($xml);
            }
}
