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
            case 'fix':
                $APIKEY = get("apikey");
                $idz = sonumero(get("idz"));
                $idfix = sonumero(get("idfix"));
                $RSP = $this->fixKeyword($idz, $idfix);
                break;
            case 'get':
                $RSP = $this->getKeywords($caID);
                $RSP['status'] = '200';
                $RSP['status_message'] = 'OK';
                $RSP['parame'] = $caID;
                break;
            default:
                $RSP['status'] = '404';
                $RSP['status_message'] = 'Function '.$d1.' not Found';
                break;
        }
        return $RSP;
    }

    function fixKeyword($id1,$id2)
        {
            $Socials = new \App\Models\Socials();
            $RDF = new \App\Models\RDF2\RDF();
            $RDFconcept = new \App\Models\RDF2\RDFconcept();

            $user = $Socials->validToken(get("apikey"));
            if ($user['status'] != '200') {
                $RSP = [];
                $RSP['status'] = '401';
                $RSP['message'] = 'Unauthorized';
                $RSP['data'] = $_POST;
                return $RSP;
            }
            $RSP = [];
            $d1 = $RDF->le_concept($id1);
            $d2 = $RDF->le_concept($id2);

            /******** Check 1 */
            if ($d1['concept']['status'] != '200') {
                $RSP['status'] = '404';
                $RSP['message'] = 'Keyword '.$id1.' not found';
                return $RSP;
            } else {
                if ($d1['concept']['id_cc'] != $d1['concept']['cc_use'])
                    {
                        $RSP['status'] = '500';
                        $RSP['message'] = 'Keyword ' . $id1 . ' already fixed';
                        return $RSP;
                    }
            }

            /******** Check 2 */
            if ($d2['concept']['status'] != '200') {
                $RSP['status'] = '404';
                $RSP['message'] = 'Keyword ' . $id1 . ' not found';
                return $RSP;
            } else {
                if ($d2['concept']['id_cc'] != $d2['concept']['cc_use'])
                    {
                        $RSP['status'] = '500';
                        $RSP['message'] = 'Keyword (master) is a remissive ' . $id2;
                        return $RSP;
                    }
            }

            $dd = [];
            $dd['cc_use'] = $id2;
            $RDFconcept->set($dd)->where('id_cc', $id1)->update();

            $RSP['status'] = '200';
            $RSP['message'] = 'Ops';
            $RSP['d1'] = $d1;
            $RSP['d2'] = $d2;
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
            ->orderBy('n_name', 'DESC')
            ->findAll();

        $kwTemp = [];
        $kw = [];
        foreach($dt as $line) {
            $IDkey = $line['d_r2'];
            $name = $line['n_name'];
            $lang = $line['n_lang'];

            if (!isset($kwTemp[$IDkey])) {
                $kwTemp[$IDkey] = 1;
                $concept = [
                    'ID' => $IDkey,
                    'name' => $name,
                    'lang' => $lang,
                ];
                array_push($kw, $concept);
            } else {
                $kwTemp[$IDkey]++;
            }
        }


        foreach($kw as $IDr=>$line) {
            $kw[$IDr]['count'] = $kwTemp[$line['ID']];
        }
        $RSP['data'] = $kw;
        return $RSP;
    }
}
