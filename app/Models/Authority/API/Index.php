<?php

namespace App\Models\Authority\API;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'index.phps';
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

    function getCPF($cpf='')
        {
            $AuthConcept = new \App\Models\Authority\API\AuthConcept();
            $dt = $AuthConcept->where('c_cpf',$cpf)->first();
            if ($dt != '')
                {
                    $id = $dt['id_c'];
                    $dt = $this->getid($id);
                }            
            return $dt;
        }

    function register($name,$class='Person',$source='', $prop_source='',$cpf='',$lattes='')
        {
            $RDF = new \App\Models\Rdf\RDF();
            $AuthName = new \App\Models\Authority\API\AuthName();
            $AuthConcept = new \App\Models\Authority\API\AuthConcept();
            $AuthResource = new \App\Models\Authority\API\AuthResource();

            $class = $RDF->getClass($class);
            $prop = $RDF->getClass('prefLabel');
            $idn = $AuthName->register($name,1,$cpf,$lattes);
            $idc = $AuthConcept->register($class,$idn);

            if ($source != '')
                {
                    $prop = $RDF->getClass($prop_source);
                    $AuthResource->register($idc, $prop, $source);
                }
            return $idc;
        }

        function search($n,$t)
            {
                $vpage = 20;
                $offset = get("offset");
                if ($offset == '')
                    {
                        $offset = 1;
                    }
                $RSP = [];

                $data = $this->search_base($n);

                /********** Calculos */
                $total = count($data);

                $RSP['pages'] = (round($total/$vpage)+1);
                $RSP['total'] = $total;
                $RSP['page'] = $offset;

                /********** DAdos */
                $RSP['item'] = $data;

                return $RSP;
            }

        function search_base($n)
            {
                $n = mb_strtoupper(ASCII($n));
                $AuthName = new \App\Models\Authority\API\AuthName();
                $flag = 'https://cip.brapci.inf.br/img/flags/flag-brazil.svg';
                $cp = 'id_an, id_c, an_name, c_use';
                $cp = '*';
                $dt = $AuthName
                    ->select($cp)
                    ->join('auth_concept', 'c_prefName = id_an')
                    ->like('an_name_asc',$n)
                    ->orderBy('an_name')
                    ->findAll();
                return $dt;
            }

        function getid($id)
            {
                $AuthName = new \App\Models\Authority\API\AuthName();
                $cp = '*';
                $RSP['id'] = $id;
                $dt = $AuthName
                    ->select($cp)
                    ->join('auth_concept', 'c_prefName = id_an')
                    ->where('id_c', $id)
                    ->first();
                $RSP = array_merge($RSP,$dt);
                return $RSP;
            }
}
