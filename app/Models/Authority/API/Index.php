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

    function getCPF($cpf = '')
    {
        $AuthConcept = new \App\Models\Authority\API\AuthConcept();
        $dt = $AuthConcept->where('c_cpf', $cpf)->first();
        if ($dt != '') {
            $id = $dt['id_c'];
            $dt = $this->getid($id);
        }
        return $dt;
    }

    function register($name, $class = 'Person', $source = '', $prop_source = '', $cpf = '', $lattes = '')
    {
        $RDF = new \App\Models\Rdf\RDF();
        $AuthName = new \App\Models\Authority\API\AuthName();
        $AuthConcept = new \App\Models\Authority\API\AuthConcept();
        $AuthResource = new \App\Models\Authority\API\AuthResource();

        $class = $RDF->getClass($class);
        $prop = $RDF->getClass('prefLabel');
        $idn = $AuthName->register($name, 1, $cpf, $lattes);
        $idc = $AuthConcept->register($class, $idn);

        if (isset($_POST)) {
            $dt = $AuthConcept->find($idc);
            if (isset($_POST['cpf'])) {
                $dt['c_cpf'] = sonumero(get("cpf"));
            }
            if (isset($_POST['email'])) {
                $dt['c_email'] = get("email");
            }
            if (isset($_POST['email_alt'])) {
                $dt['c_email_alt'] = get("email_alt");
            }
            $AuthConcept->set($dt)->where('id_c', $idc)->update();
        }

        if ($source != '') {
            $prop = $RDF->getClass($prop_source);
            $AuthResource->register($idc, $prop, $source);
        }
        return $idc;
    }

    function search($n, $t)
    {
        $vpage = 20;
        $offset = get("offset");
        if ($offset == '') {
            $offset = 1;
        }
        $RSP = [];

        $n = get("term").$n;
        if (strlen($n) < 3)
            {
                $data = [];
                $RSP['message'] = 'Minimal 3 chars to search';
                $RSP['status'] = '500';
            } else {
                $data = $this->search_base($n);
                $RSP['status'] = '200';
                $RSP['message'] = 'OK';
            }

        /********** Calculos */
        $total = count($data);

        $RSP['pages'] = (round($total / $vpage) + 1);
        $RSP['total'] = $total;
        $RSP['page'] = $offset;

        /********** Dados */
        $RSP['item'] = $data;



        $dataC = $this->search_base($n,'CorporateBody');
        $RSP['corporate'] = $dataC;

        return $RSP;
    }

    function register_corporate($dt)
    {
        $idc = 0;
        $class = "CorporateBody";

        $RDF = new \App\Models\Rdf\RDF();
        $AuthName = new \App\Models\Authority\API\AuthName();
        $AuthConcept = new \App\Models\Authority\API\AuthConcept();
        $AuthResource = new \App\Models\Authority\API\AuthResource();

        $class = $RDF->getClass($class);
        $prop = $RDF->getClass('prefLabel');
        $name = nbr_author($dt['prefLabel'],7);

        $idn = $AuthName->register($name);
        $idc = $AuthConcept->register($class, $idn);

        if (isset($dt['prop']['acronym']))
            {
                $name = mb_strtoupper($dt['prop']['acronym']);
                $ida = $AuthName->register($name);
                $idac = $AuthConcept->register($class, $ida);
                $AuthConcept->remissive($idac, $idc);
            }

        if ($dt['prop'] != '') {
            foreach($dt['prop'] as $prop=>$vlr)
                {
                    if (is_array($vlr))
                        {
                            //echo $prop . '=>';
                            //pre($vlr,false);
                        } else {
                            $prop = $RDF->getClass($prop);
                            $AuthResource->register($idc, $prop, $vlr);
                        }
                }
        }
        return $idc;
    }

    function search_base($n,$class='Person')
    {
        /*************** Busca RDF */
        $RDF = new \App\Models\RDF2\RDF();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $name = get("term");

        $faceted = True;
        $idc = $RDFclass->getClass($class);
        $row = $RDFconcept->searchTerm($name,$idc,$faceted);

        return $row;
        exit;


        $n = mb_strtoupper(ASCII($n));
        $AuthName = new \App\Models\Authority\API\AuthName();
        $flag = 'https://cip.brapci.inf.br/img/flags/flag-brazil.svg';
        $cp = 'id_an, id_c, an_name, c_use';
        $cp = '*';
        $dt = $AuthName
            ->select($cp)
            ->join('auth_concept', 'c_prefName = id_an')
            ->like('an_name_asc', $n)
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
            ->join('brapci.rdf_class', 'auth_concept.c_class = rdf_class.id_c')
            ->where('auth_concept.id_c', $id)
            ->first();

        if ($dt=='')
            {
                $RSP['status'] = '404';
                $RSP['message'] = 'Registro não existe';
                return $RSP;
            }

        if ($dt['c_use'] != 0)
            {
                $idu = round($dt['c_use']);
                $dt = $this->getid($idu);
                $dt['remissive'] = $this->getRemissive($idu);
            }
        $RSP = array_merge($RSP, $dt);
        return $RSP;
    }
    function getRemissive($id)
        {
            if ($id == 0) { return []; }
            $AuthConcept = new \App\Models\Authority\API\AuthConcept();
            $dt = $AuthConcept
                ->join('auth_name','c_prefName = id_an')
                ->where('c_use',$id)
                ->findAll();
            return $dt;
        }
}
