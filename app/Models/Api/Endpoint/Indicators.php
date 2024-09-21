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

class Indicators extends Model
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

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        $RSP['status'] = '500';
        switch ($d1) {

            case 'index':
                $ProducaoJournalAno = new \App\Models\ICR\ProducaoJournalAno();
                $RSP = $ProducaoJournalAno->createIndex(16);
                break;
            case 'ProducaoJournalAno':
                $ProducaoJournalAno = new \App\Models\ICR\ProducaoJournalAno();
                $RSP['data'] = $ProducaoJournalAno->get($d2);
                $Source = new \App\Models\Base\Sources();
                $RSP['journal'] = $Source->le($d3);
                $RSP['jid'] = $d2;
                $RSP['status'] = '200';
                break;

            default:
                $RSP['status'] = '400';
                $RSP['message'] = 'Verb not informed. User: indicator/production/$JID';
                break;
        }
        return $RSP;
    }
}
