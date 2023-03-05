<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class Affiliation extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'affiliations';
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

    function show_list($dm)
        {
            $RDF = new \App\Models\Rdf\RDF();
            $rsp = array();
            if (isset($dm['Affiliation']))
                {
                    foreach($dm['Affiliation'] as $id=>$ida)
                        {
                            $da = $RDF->le($ida);
                            $name = $da['concept']['n_name'];
                            $rsp[$name] = $ida;
                        }
                }
            $sx = '';
            foreach($rsp as $name=>$id)
                {
                    $sx .= '<li>'.anchor(PATH. '/autoridade/v/'.$id,$name). '</li>';
                }
            if ($sx != '') {
                $sx .= '. ';
            }
            return $sx;
        }
}
