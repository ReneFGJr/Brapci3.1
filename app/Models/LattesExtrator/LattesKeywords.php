<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesKeywords extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'lattes_keywords';
    protected $primaryKey       = 'id_ky';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ky','ky_name','ky_lang'
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

    function register($kw,$lg='??')
        {
            $dt = $this->where('ky_name',$kw)->first();
            if ($dt =='')
                {
                    $dt['ky_name'] = $kw;
                    return $this->set($dt)->insert();
                } else {
                    return($dt['id_ky']);
                }
        }

    function keyword_xml($idp,$keys,$type)
        {
            $KeywordProduction = new \App\Models\LattesExtrator\LattesKeywordsProducao();
            foreach($keys as $id=>$kw)
                {
                    $kw = trim($kw);
                    if ($kw != '')
                        {
                            $idw = $this->register($kw);
                            $KeywordProduction->register($idp,$idw,$type);
                            echo $idw.'='.$kw;
                            exit;
                        }
                }
            pre($keys);
        }
}
