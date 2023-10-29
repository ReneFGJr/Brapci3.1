<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFliteral extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_literal';
    protected $primaryKey       = 'id_n';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'n_name', 'n_lock', 'n_lang', 'n_md5'
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

    function register($name,$lang='pt_BR',$lock=1)
        {
            $Language = new \App\Models\AI\NLP\Language();
            $lang = $Language->normalize($lang);

            $dt = $this->where('n_name',$name)->findAll();
            if (count($dt) > 0)
                {
                    foreach($dt as $id=>$line)
                        {
                            if ($line['n_lang'] == $lang)
                                {
                                    $new = false;
                                    $ID = $line['id_n'];
                                }
                        }
                } else {
                    $new = true;
                }


            /*********************************************** NOVO */
            if ($new)
                {
                    $d['n_name'] = $name;
                    $d['n_lang'] = $lang;
                    $ID = $this->set($d)->insert();
                }
            return $ID;
        }
}
