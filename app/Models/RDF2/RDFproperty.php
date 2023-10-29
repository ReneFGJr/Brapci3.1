<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFproperty extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_class';
    protected $primaryKey       = 'id_c';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'c_class', 'c_equivalent', 'c_prefix',
        'c_type', 'c_description', 'c_url',
        'c_url_update', 'id_c', 'c_class_main'
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

    function register($Prefix, $Prop)
    {
        $RDFprefix = new \App\Models\RDF2\RDFprefix();
        $prefix = $RDFprefix->getPrefixID((string)$Prefix);
        $ClassID = 0;

        if ($prefix > 0) {
            $PropID = $this->getProperty($Prop, $prefix);
            if ($PropID == 0) {
                $d = [];
                $d['c_class'] = $Prop;
                $d['c_prefix'] = $prefix;
                $d['c_type'] = 'P';
                $d['c_description'] = '';
                $d['c_url'] = '';
                $d['c_url_update'] = date("Y-m-d");
                $d['c_class_main'] = 0;
                $d['c_equivalent'] = 0;
                $PropID = $this->set($d)->insert();
            }
        }
        return $PropID;
    }

    function getProperty($Prop, $Prefix = '')
    {
        $Prop = trim($Prop);
        $this->where('c_class', $Prop);
        if ($Prefix != '') {
            $this->where('c_prefix', $Prefix);
        }
        $dt = $this->first();

        if ($dt != null) {
            if ($dt['c_equivalent'] != 0) {
                return $dt['c_equivalent'];
            } else {
                return $dt['id_c'];
            }
        } else {
            return 0;
        }
    }

    function getProperties()
    {
        $cp = 'id_c as id, prefix_ref as prefix,c_class as Class,
                        c_type as Type, CONCAT(prefix_url,c_class) as url';
        //$cp = '*';
        $dt = $this
            ->select($cp)
            ->join('rdf_prefix', 'id_prefix = c_prefix')
            ->where('c_type', 'P')
            ->orderBy('c_class')
            ->findAll();
        return $dt;
    }
}
