<?php

namespace App\Models\keywords;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'keywords';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    function index($d1='', $caID='')
    {
        $d1 = trim(strtolower($d1));
        $RSP = [];
        $RSP['status'] = '404';
        $RSP['message'] = 'Function not Found';
        $RSP['verb'] = $d1;

        switch ($d1) {
            case 'get':
                $RSP = $this->getKeywords($caID);
                $RSP['status'] = '200';
                $RSP['status_message'] = 'OK';
                break;
            default:
                $RSP['status'] = '404';
                $RSP['status_message'] = 'Function '.$d1.' not Found';
                break;
        }
        return $RSP;
    }

    function getKeywords($d1)
    {
        $RSP = [];
        $RPS['status'] = '200';
        $idz = get("idz");
        $IDs = explode(",",$idz);
        $RDFdata = new \App\Models\RDF2\RDFdata();

        $dt = $RDFdata
            ->select('d_r1, d_r2, id_n, n_name, n_lang')
            ->join('rdf_concept', 'd_r2 = id_cc', 'INNER')
            ->join('rdf_class', 'cc_class = id_c AND c_class = "Subject"', 'INNER')
            ->join('rdf_literal', 'cc_pref_term = id_n', 'INNER')
            ->whereIn('d_r1', $IDs)
            ->orderBy('d_r1', 'DESC')
            ->findAll();
        pre($dt);


        $Search = new \App\Models\ElasticSearch\Search();
        $dt = $Search->whereIn('ID',$IDs)->findAll();

        $kw = [];
        foreach($dt as $line) {
            $json = json_decode($line['json'],true);
            pre($json);
            $concept = $line['concept'];
            $kw[] = $concept;
        }
        $RSP['data'] = $kw;
        return $RSP;
    }
}
