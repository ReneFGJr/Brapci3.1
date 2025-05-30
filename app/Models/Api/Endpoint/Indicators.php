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

    function index($d1 = '', $d2 = '', $d3 = '',$d4='')
    {
        $RSP['status'] = '500';
        switch ($d1) {

            case 'index':
                $ProducaoJournalAno = new \App\Models\ICR\ProducaoJournalAno();
                $RSP = $ProducaoJournalAno->createIndex($d2);
                break;
            case 'ProducaoJournalAno':
                $ProducaoJournalAno = new \App\Models\ICR\ProducaoJournalAno();
                $RSP['data'] = $ProducaoJournalAno->get($d2);
                if ($RSP['data'] == [])
                    {
                        $RSP = $ProducaoJournalAno->createIndex($d2);
                        $RSP['data'] = $ProducaoJournalAno->get($d2);
                    }
                $Source = new \App\Models\Base\Sources();
                $RSP['journal'] = $Source->le($d3);
                $RSP['jid'] = $d2;
                $RSP['status'] = '200';
                $RSP['trabalhos'] = $ProducaoJournalAno->trabalhos;
                break;
            case 'ProducaoJournalAutores':
                if ($d3 == '') {
                    $d3 = date("Y") - 5;
                }
                if ($d4 == '') {
                    $d4 = date("Y");
                }
                $ProducaoAutores = new \App\Models\ICR\ProducaoAutores();
                $RSP['data'] = $ProducaoAutores->get($d2,$d3,$d4);
                $RSP['jid'] = $d2;
                $RSP['periodo'] = $d3.'-'.$d4;
                $RSP['authors_total'] = $ProducaoAutores->total;
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
