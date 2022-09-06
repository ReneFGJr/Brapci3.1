<?php

namespace App\Models\AI\Skos;

use CodeIgniter\Model;

class VCconcepts_th extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'brapci_chatbot.vc_concepts_th';
    protected $primaryKey           = 'id_vc';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [
        'id_cth', 'cth_c', 'cth_th', 'cth_utl'
    ];

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

    function link($term,$th,$uri)
    {
        $dt = $this->where('cth_c', $term)->where('cth_th', $th)->findAll();
        if (count($dt) == 0)
            {
                $dd['cth_c'] = $term;
                $dd['cth_th'] = $th;
                $dd['cth_uri'] = $uri;
                $this->insert($dd);
            }
    }
}
