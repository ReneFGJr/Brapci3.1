<?php

namespace App\Models\DOI;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'persistent_indicador';
    protected $table            = 'persistent_id';
    protected $primaryKey       = 'id_pi';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pi ','pi_id', 'pi_url',
        'pi_json', 'pi_active', 'pi_status',
        'pi_citation', 'pi_creators', 'pi_title',
        'updated_at'
    ];

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

    function register_json($dtj)
        {
            $dt = (array)json_decode($dtj);

            $dt = (array($dt['data']));
            foreach($dt as $idd=>$data)
                {
                    $dta = array();
                    $dta['pi_json'] = $dtj;
                    $dta['pi_id'] = $data->id;
                    $dta['pi_title'] = $data->attributes->titles[0]->title;
                    $dta['pi_creators'] = '';
                    $authors = $data->attributes->creators;
                    foreach($authors as $ida=>$auth)
                        {
                            if ($dta['pi_creators'] != '') { $dta['pi_creators'] .= '; '; }
                            $dta['pi_creators'] .= (string)$auth->name;
                        };
                    $dta['pi_url'] = $data->attributes->url;
                    $dta['pi_status'] = $data->attributes->state;
                    $dta['pi_active'] = $data->attributes->isActive;
                    $dta['updated_at'] = date("Y-m-d H:i:s");

                    $dtr = $this->where('pi_id',$dta['pi_id'])->findAll();
                    if (count($dtr) == 0)
                        {
                            $this->set($dta)->insert();
                            $dtr = $this->where('pi_id', $dta['pi_id'])->findAll();
                        }
                }
            return $dta;

        }

    function recover_metadata($doi = '')
    {
        $DataCite = new \App\Models\DOI\APIDatacite();
        $dt = $DataCite->metadata($doi);

        return $dt;
    }

    function tombstone($d1='',$d2='', $d3 ='', $d4 ='', $d5 = '')
        {
            $sx = '';
            if (($d1 != '') and ($d2 != ''))
                {
                    $DOI = $d1.'/'.$d2;
                    if (strlen($d3) != '')
                        {
                            $DOI .= '/'.$d3;

                if (strlen($d4) != '') {
                    $DOI .= '/' . $d4;
                }

                if (strlen($d5) != '') {
                    $DOI .= '/' . $d5;
                }
                        }
                    $data = array();
                    $data = $this->recover_metadata($DOI);
                    $sx = view('DOI/tombstone', $data);
                }
            if (($d1 != '') and ($d2 == '')) {
                $data = array();
                $d1 = sonumero($d1);
                $data = $this->find($d1);
                $sx = view('DOI/tombstone', $data);
            }

            return $sx;
        }
}
